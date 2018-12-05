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

?>


<?php

$username = mysql_real_escape_string($username);
if (isset($_GET['playlist'])){
    $playlist_name = $_GET['playlist'];
    echo "<p style='font-size:20px; color:blue;'>$playlist_name<p>";
    echo "<br/><br/>";
    
    $query = "SELECT * FROM playlist, media WHERE playlist.username='$username' AND playlist.playlist_name='$playlist_name'
              AND media.file_path = playlist.file_path ";
    $result = mysql_query($query);

    if (mysql_num_rows($result) == 0){
        echo "<p style='font-size:16px; color:grey;'>&nbsp;&nbsp;None<p>";
    }
    else {
        $max_rows = mysql_num_rows($result);
        display_media($result, $max_rows);
    }
}
else {
    $query = "SELECT DISTINCT playlist_name FROM playlist WHERE username='$username' ";
    $result = mysql_query($query);

    echo "<table border='0'><tr><th align='left' style='font-size:22px; color:blue;'>My playlists</th></tr><tr><td>&nbsp;</td></tr>";
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        $playlist_name = $row['playlist_name'];
    ?>
        <tr>
            <td align='center'>
                <a href = "my_playlist.php?playlist=<?php echo $playlist_name ?>" style="text-decoration: none; font-size:18px; color:black"> <?php echo $playlist_name ?></a>
            </td>
            <td >
                <FORM action ="my_playlist.php" method="post">
                    <button name = "remove" value = <?php echo $playlist_name ?>>Remove</button>
                </FORM>
            </td>
        </tr>
        <tr><td>&nbsp;&nbsp;</td>
        </tr>

    <?php
    }
    echo "<tr>
            <FORM action ='my_playlist.php' method='post' name='my_playlist' id='my_playlist'>
            <td valign='bottom'>
                <button name='create_playlist' style='font-size:14px;'>Create a new playlist</button>
            </td >
            <td valign='bottom' colspan='3'>
                <textarea rows='1' cols='30' name='new_playlist' id='new_playlist' form='my_playlist' placeholder='&#43;Playlist name'></textarea>
            </td >
            </FORM>";
    echo "</tr> ";
    if (isset($insert_error)){
        echo "<tr>
              <td>&nbsp;</td>
              <td valign='bottom'><p style='font-size:16px; color:'blue';>$insert_error</p></td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>





</body>
</html>
