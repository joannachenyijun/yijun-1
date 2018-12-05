<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	session_start();
	include_once "function.php";
    // include_once "browse_header.php"
?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Media browse</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript" src="js/jquery-latest.pack.js"></script>
</head>


<body>


<!-- <div id='upload_result'>
<?php
	if(isset($_REQUEST['result']) && $_REQUEST['result']!=0)
	{
		echo upload_error($_REQUEST['result']);
	}
?>
</div>
<br/><br/> -->


<?php

if (isset($_SESSION["username"])){
    $username = $_SESSION["username"];
    $nickname = $_SESSION["nickname"];
}
else{
    header("Location: index_header.php?index='explore the metube!'");
}

if (isset($_GET["advanced_search"])){
    if (isset($_POST["cancel"])){
        header('Location: browse.php');
    }
}

include_once "browse_header.php";

echo "<div id='upload_result'>";
if(isset($_REQUEST['result']) && $_REQUEST['result']!=0){
    echo "<p style='font-size:22px; color:red;'>".upload_error($_REQUEST['result'])."</p>" ;
}
echo "</div>";

$word_cloud = word_cloud();
// print_r($word_cloud);

if (isset($_POST["search"])){
    $input_str = $_POST["search"];
    $input_arr = clean_str_to_arr($input_str);
    $path_target_arr = basic_search($input_arr);

    if (sizeof($path_target_arr) <= 0){
        $search_error = "No Result for ".$_POST['search']."!";
    }
    else{
        $max_rows = sizeof($path_target_arr);
        display_search($path_target_arr, $max_rows);
    }

}
elseif (isset($_GET["advanced_search"])){
    if(isset($_POST["submit"])){
        $title = $_POST["search_title"];
        $category = $_POST["search_category"];
        $type = $_POST["search_type"];
        $size = $_POST["search_size"];
        $time_before = $_POST["search_before"];
        $time_after = $_POST["search_after"];
        $time_order = $_POST["search_order"];
        $description = $_POST["search_description"];

        $target_files_adv = advanced_search($title, $category, $type, $size, $time_before, $time_after, $time_order, $description);

        if (is_string($target_files_adv)){
            $search_error = $target_files_adv;
        }
        else{
            $max_rows = sizeof($target_files_adv);
            display_search($target_files_adv, $max_rows);
        }
    }
}
elseif (isset($_GET["category"])){
    $category = $_GET["category"];
    $category_upper = strtoupper($category);
    echo "<p><a href='browse.php?category=$category' style='text-decoration:none; font-size:20px'>$category_upper</a></p>";

    $result_category_search = category_search($_GET["category"]);
    if (!isset($result_category_search)){
        $search_error = "No Media found for ".$category_upper."!";
    }
    else{
        $max_rows = mysql_num_rows($result_category_search);
        display_media($result_category_search, $max_rows);
    }
}
elseif (isset($_GET["id"])){
    $id = $_GET["id"];
    $id_str = str_replace('_', ' ', $id);
    echo "<p><a href='browse.php?id=$id_str' style='text-decoration:none; font-size:20px'>$id_str</a></p>";

   if ($id == 'Watch_It_Again'){
       $result = user_most_viewed($_SESSION["username"]);
       $path_target_arr = array();
       if (mysql_num_rows($result)>0){
           while ($row = mysql_fetch_array($result)){
               array_push($path_target_arr, $row["file_path"]);
           }
       }

       // Most viewed
       $result = user_most_recently_viewed($_SESSION["username"]);
       if (mysql_num_rows($result)>0){
           while ($row = mysql_fetch_array($result)){
               array_push($path_target_arr, $row["file_path"]);
           }
       }
       $path_target_arr = array_unique($path_target_arr);

       if (empty($path_target_arr)){
           echo "No viewed meida!";
       }
       else{
        //    $max_rows = mysql_num_rows($result);
           $max_rows = sizeof($path_target_arr);
           display_search($path_target_arr, $max_rows);
       }
   }
   else if ($id = 'Recommended'){
       $user_interests = user_interests($_SESSION["username"]);
       if (empty($user_interests)){
           echo "No recommendation!";
       }
       else {
           $path_target_arr = basic_search($user_interests);
           $max_rows = sizeof($path_target_arr);
           display_search($path_target_arr, $max_rows);
       }
   }
   else if ($id = 'Recently_Uploaded'){
       $query = "SELECT *, (DATE(NOW()) - DATE(date_time)) AS uploaded_time FROM media
                 WHERE YEAR(NOW()) = YEAR(date_time) AND (DATE(NOW()) - DATE(date_time)) <10 ORDER BY date_time desc ";
       $result = mysql_query($query);
       if (!isset($result)){
           $search_error = "No media is uploaded recently!";
       }
       else{
           $max_rows = mysql_num_rows($result);
           display_media($result, $max_rows);
       }
   }
   else if ($id = 'Top_Trending'){
       $query = "SELECT * FROM media ORDER BY view_count desc";
       $result = mysql_query($query);
       if (!isset($result)){
           $search_error = "No media is uploaded recently!";
       }
       else{
           $max_rows = mysql_num_rows($result);
           display_media($result, $max_rows);
       }
   }
}
else {   // for main page: recommend, recently view....
    $max_rows = 1;
    if (isset($_SESSION["username"])){
        $username = $_SESSION["username"];
        $nickname = $_SESSION['nickname'];
        echo "<p style='font-size:20px; color:green;'> Welcome $nickname!</p>";
    }

    if (isset($_SESSION["username"])){
        echo "<p><a href='browse.php?id=Watch_It_Again' style='text-decoration:none; font-size:20px'>Watch It Again</a></p>";

        // Most Recently viewed
        $result = user_most_viewed($_SESSION["username"]);
        $path_target_arr = array();
        if (mysql_num_rows($result)>0){
            while ($row = mysql_fetch_array($result)){
                array_push($path_target_arr, $row["file_path"]);
            }
        }
        // Most viewed
        $result = user_most_recently_viewed($_SESSION["username"]);
        if (mysql_num_rows($result)>0){
            while ($row = mysql_fetch_array($result)){
                array_push($path_target_arr, $row["file_path"]);
            }
        }
        $path_target_arr = array_unique($path_target_arr);

        if (empty($path_target_arr)){
            echo "No viewed meida!";
        }
        else{
            display_search($path_target_arr, $max_rows);
        }
        echo "<hr/>";

        echo "<p><a href='browse.php?id=Recommended' style='text-decoration:none; font-size:20px'>Recommended</a></p>";
        $user_interests = user_interests($_SESSION["username"]);
        if (empty($user_interests)){
            echo "No recommendation!";

        }
        else {
            $path_target_arr = basic_search($user_interests);
            display_search($path_target_arr, $max_rows);
        }
        echo "<hr/>";

    }

    echo "<p><a href='browse.php?id=Recently_Uploaded' style='text-decoration:none; font-size:20px'>Recently Uploaded</a></p>";
    $query = "SELECT *, (DATE(NOW()) - DATE(date_time)) AS uploaded_time FROM media
              WHERE YEAR(NOW()) = YEAR(date_time) AND (DATE(NOW()) - DATE(date_time)) <10 ORDER BY date_time desc ";
    $result = mysql_query($query);
    if (mysql_num_rows($result)==0){
        $search_error = "No media is uploaded within 10 days!";
    }
    else{
        display_media($result, $max_rows);
    }
    echo "<hr/>";


    echo "<p><a href='browse.php?id=Top_Trending' style='text-decoration:none; font-size:20px'>Top Trending</a></p>";
    $query = "SELECT * FROM media ORDER BY view_count desc";
    $result = mysql_query($query);
    if (mysql_num_rows($result)==0){
        $search_error = "None!";
    }
    else{
        display_media($result, $max_rows);
    }
    echo "<hr/>";
}

?>


<?php
  if(isset($search_error))
   {  echo "<div id='search_result' style='font-size:20px'>".$search_error."</div>";}
?>



</body>
</html>
