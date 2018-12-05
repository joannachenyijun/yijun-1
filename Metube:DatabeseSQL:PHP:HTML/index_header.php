<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php

    if (isset($_GET["logout"])){
        session_start();
        unset($_SESSION["username"]);
        unset($_SESSION["email"]);
        unset($_SESSION["nickname"]);
        unset($_SESSION["password"]);
        header("Location: index_header.php");
    }

    if (isset($_GET["advanced_search"])){
        if (isset($_POST["cancel"])){
            header('Location: browse.php');
        }
    }

	include_once "function.php";
    // $_SESSION["username"] = "";

    // echo $_SESSION["username"];
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Media browse</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript" src="js/jquery-latest.pack.js"></script>
<script type="text/javascript">

function indexDropdown(){
    document.getElementById("indexDropdown").classList.toggle("show");
}

</script>


<style>

.para{
    font-size: 20px;
}

.button_blue {
    background-color: #2B65EC;
    border: none;
    color: white;
    padding: 7px 7px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 2px 1px;
    cursor: pointer;
}
.button {
    /*background-color: #4CAF50;*/
    background-color:transparent;
    border-color:transparent;
    border: none;
    /*color: white;
    padding: 5px 5px;*/
    /*text-align: center;*/
    /*text-decoration: none;*/
    /*display: inline-block;*/
    /*font-size: 16px;
    margin: 2px 1px;*/
    cursor: pointer;
}


/* Dropdown Button */
.dropbtn {
    /*background-color: #4CAF50;*/
    background-color:transparent;
    border-color:transparent;
    /*color: white;*/
    /*padding: 16px;*/
    /*font-size: 16px;*/
    border: none;
    cursor: pointer;
}

/* Dropdown button on hover & focus */
.dropbtn:hover, .dropbtn:focus {
    /*background-color: #3e8e41;*/
}

/* The container <div> - needed to position the dropdown content */
.dropdown {
    position: relative;
    /*display: inline-block;*/
}

/* Dropdown Content (Hidden by Default) */
.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 120px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

/* Links inside the dropdown */
.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

/* Change color of dropdown links on hover */
.dropdown-content a:hover {background-color: #f1f1f1}

/* Show the dropdown menu (use JS to add this class to the .dropdown-content container when the user clicks on the dropdown button) */
.show {display:block;}

</style>
</head>

<body>



<table>
    <tr>
        <td>
            <div class="dropdown">
                <button onclick="indexDropdown()" class="dropbtn">
                    <img src="https://cdn1.iconfinder.com/data/icons/android-user-interface-vol-1/16/38_-_menu_bar_lines_option_list_hamburger_web-512.png" alt="menu" style="width:35px;height:30px;">
                </button>
                <div id="indexDropdown" class="dropdown-content">
                    <a href="index_header.php">Home</a>
                    <a href="login.php">Sign in</a>
                    <a href="login.php">Register</a>
                    <hr>
                    <a href="index_header.php?category=<?php echo "music"?>">Music</a>
                    <a href="index_header.php?category=<?php echo "gaming"?>">Gaming</a>
                    <a href="index_header.php?category=<?php echo "movies"?>">Movies</a>
                    <a href="index_header.php?category=<?php echo "tv_shows"?>">TV shows</a>
                    <a href="index_header.php?category=<?php echo "news"?>">News</a>
                    <a href="index_header.php?category=<?php echo "pets"?>">Pets</a>
                    <a href="index_header.php?category=<?php echo "comedy"?>">Comedy</a>
                </div>
            </div>
        </td>
        <td>
            <img src="https://lh3.googleusercontent.com/M5xLaQ7FIVKL6t_h9XubU9-Lh7SDxhnPKTZpT7wihzHoHYKM4hVgEEgNIJYbVDGkexw=w300" alt="MeTube" style="width:55px;height:45px; background-color:transparent; border-color:transparent;cursor:pointer;"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </td>
        <td>
            <form action="index_header.php" method="post">
                <input type="text"  name="search"  placeholder="Search" style ="width:300pt; height:20pt; font-size:16px" >
                <!-- <input type="botton" name="submit" background = "icon/search.png"> -->
                <button type="submit" style="background-color:transparent; border-color:transparent;">
                    <img src="http://www.pic4ever.com/images/2mpe5id.gif" height="19"/>
                </button>
            </form>
        </td>
        <td>
            &nbsp;&nbsp;&nbsp;&nbsp;
        </td>
        <td>
             <form action="advanced_search.php" method="post">
              	<!-- <input type="submit"  name="advanced_search" value ="Advanced Search"> -->
                <button class="button_blue">Advanced Search</button>
             </form>
        </td>
    </tr>
</table>

<hr/>


<!-- ========================================================================================= -->
<!-- ========================================================================================= -->


<?php


if (isset($_GET["unknowuser"])){
    // $search_error = "Please Login/Register to manage your account ! ";
    echo "<p style ='font-size:30px; color:blue;'>Please Login/Register to manage your account !</p>";
}


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

    // $path_target_arr = basic_search($_POST["search"]);
    //
    // if (sizeof($path_target_arr) <= 0){
    //     $search_error = "No Result for ".$_POST['search']."!";
    // }
    // else{
    //     $max_rows = sizeof($path_target_arr);
    //     display_search($path_target_arr, $max_rows);
    // }
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
        display_media($result_category_search,$max_rows);
    }
}
elseif (isset($_GET["id"])){
    $id = $_GET["id"];
    $id_str = str_replace('_', ' ', $id);
    echo "<p><a href='browse.php?id=$id_str' style='text-decoration:none; font-size:20px'>$id_str</a></p>";

   if ($id = 'Recently_Uploaded'){
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
    if (isset($_SESSION["username"])){
        echo "<p><a href='index_header.php?id=recommended' style='text-decoration:none; font-size:20px'>Recommended</a></p>";
        echo "<hr/>";
    }

    echo "<p><a href='index_header.php?id=recently_uploaded' style='text-decoration:none; font-size:20px'>Recently Uploaded</a></p>";
    $query = "SELECT *, (DATE(NOW()) - DATE(date_time)) AS uploaded_time FROM media
              WHERE YEAR(NOW()) = YEAR(date_time) AND (DATE(NOW()) - DATE(date_time)) <10 ORDER BY date_time desc ";
    $result = mysql_query($query);
    if (!isset($result)){
        $search_error = "No media is uploaded recently!";
    }
    else{
        // $max_rows = mysql_num_rows($result);
        display_media($result,1);
    }
    echo "<hr/>";


    echo "<p><a href='index_header.php?id=Top_Trending' style='text-decoration:none; font-size:20px'>Top Rending</a></p>";
    $query = "SELECT * FROM media ORDER BY view_count desc LIMIT 6";
    $result = mysql_query($query);
    if (!isset($result)){
        $search_error = "No media is uploaded recently!";
    }
    else{
        display_media($result,1);
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
