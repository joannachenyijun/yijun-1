<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	session_start();
	include_once "function.php";
    // include_once "browse_header.php";

    if (isset($_SESSION["username"])){
        $username=$_SESSION["username"];
    }
    else{
        header('Location: index_header.php?unknowuser=1');
    }
?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- <title>Media browse</title> -->
<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript">

</script>

<style>
</style>

</head>

<body>


<?php
include_once "browse_header.php";
?>


<?php

if (isset($_POST['remove'])){
    $playlist_name = $_POST['remove'];
    remove_playlist($playlist_name, $username);
}

if (isset($_POST['create_playlist'])){
    $file_path = "NULL";
    $playlist_name = $_POST['new_playlist'];
    if (empty($playlist_name)){
        $insert_error = "Playlist name is required!";
    }
    else {
        $query = "SELECT * FROM playlist WHERE username='$username' AND playlist_name='$playlist_name'";
        if(mysql_num_rows(mysql_query($query)) > 0){
            $insert_error = "The Playlist $playlist_name already exists!";
        }
        else{
            insert_playlist($playlist_name, $_SESSION['username'], $file_path);
        }
    }
}

if (isset($_POST['dislike'])){
    $file_path = $_POST['dislike'];
    remove_from_favorites($_SESSION['username'], $file_path);
}

?>


<?php


$username = mysql_real_escape_string($username);

echo "<table border='0'><tr><th align='left' style='font-size:22px; color:blue;'>My favorites</th></tr><tr><td>&nbsp;</td></tr>";

// $query = "SELECT DISTINCT favorites.file_path AS file_path, media.title AS title FROM favorites, media WHERE favorites.username='$username' AND media.file_path = favorites.file_path  ";
// $result = mysql_query($query);

$query = "SELECT * FROM favorites, media WHERE favorites.username='$username' AND media.file_path = favorites.file_path  ";
$result = mysql_query($query);
$max_rows = mysql_num_rows($result);

// display_media($result, mysql_num_rows($result));


$col_num = 1;
$row_num = 0;
echo "<table cellpadding='5'>";
echo "<tr>";
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
    $file_path = $row["file_path"];
    $title = $row["title"];
    $date_time = $row["date_time"];
    $type = $row["type"];
    $view_count = $row["view_count"];
    $user_upload = $row["username"];

    if ($col_num > 3){
        $col_num = 0;
        echo "<tr>";
        $row_num += 1;
    }

    echo "<td width='250px' height='250px' valign='top'><div>
            <a href='media.php?file_path=$file_path'><video width='300px'><source src ='$file_path' type ='$type'></video></a>
            <br/>$title <br/>
            $user_upload <br/>
            $view_count views  &#183 $date_time
            <FORM action='my_favorites.php' method='post'>
                  <button name='dislike' type='submit' value='$file_path' style='color:white; background-color:red;'>Dislike</button>&nbsp;
            </FORM>
          </td>
          <td>&nbsp;&nbsp;</td></div>";

    if ($col_num > 3){
        echo "</tr>";
    }

    $col_num += 1;
    // echo "&nbsp;&nbsp;&nbsp;";
    if ($row_num >= $max_rows){
        break;
    }
}
// echo "</tr>";
echo "</table>";












?>


</body>
</html>
