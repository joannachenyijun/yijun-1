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

if (isset($_POST['unsubscribe'])){
    $channel_name = $_POST['unsubscribe'];
    // echo $channel_name;
    // echo $_POST['unsubscribe'];
    unsubscribe_channel($channel_name, $username);
}

// if (isset($_POST['subscribe'])){
//     $channel_name = $_POST['new_channel'];
//     if ($channel_name == "DEFAULT") {
//         $insert_error = "The channel name is required!";
//     }
//     else {
//         $query = "SELECT * FROM subscribe WHERE channel_name='$channel_name' AND username = '$username'";
//         if (mysql_num_rows(mysql_query($query)) >0 ){
//             $insert_error = "The channel $channel_name has already been subscribed!";
//         }
//         else {
//             insert_subscribe($channel_name, $username);
//         }
//     }
// }


if (isset($_POST['browse_channel'])){
    $channel_name = $_POST['public_channel'];
    if ($channel_name == "DEFAULT") {
        $insert_error = "The channel name is required!";
    }
    else {
        $query = "SELECT * FROM subscribe WHERE channel_name='$channel_name' AND username = '$username'";
        if (mysql_num_rows(mysql_query($query)) >0 ){
            $insert_error = "The channel".find_nickname($channel_name)."has already been subscribed!";
        }
        else {
            insert_subscribe($channel_name, $username);
        }
    }
}


if (isset($_POST['search_channels'])){
    $channel_nickname = $_POST['private_channel'];
    $channel_name = find_username($channel_nickname);
    // echo $channel_name;

    if ($channel_nickname == "DEFAULT") {
        $insert_error = "The channel name is required!";
    }
    else {

        $check_channel_exist = check_channel_exist($channel_name);

        if ($check_channel_exist == 0){
            $insert_error = "The channel ".$channel_nickname." doesn't exist!";
        }
        else {
            $query = "SELECT * FROM subscribe WHERE channel_name='$channel_name' AND username = '$username'";
            if (mysql_num_rows(mysql_query($query)) >0 ){
                $insert_error = "The channel ".$channel_nickname." has already been subscribed!";
            }
            else {
                insert_subscribe($channel_name, $username);
            }
        }
    }
}


?>


<?php

$username = mysql_real_escape_string($username);
if (isset($_GET['channel_name'])){
    $channel_name = $_GET['channel_name'];
    $public_channel=array("Oriental_Theatre", "POP_Music", "Today", "The_Youth", "Vintage");

    if (in_array($channel_name, $public_channel)){
        $channel_nickname = $channel_name ;
        $channel_nickname = clean_str($channel_nickname);
    }else {
        $channel_nickname = find_nickname($channel_name);
    }

    // echo "<p style='font-size:20px; color:blue;'>$playlist_name<p>";
    echo "<p style='font-size:20px;'>&sect;&nbsp;Welcome The Channel &nbsp <span style='font-size:22px; color:blue;'>".$channel_nickname."</span> </p>";
    echo "<br/><br/>";

    $query = "SELECT * FROM channels, media WHERE channels.channel_name ='$channel_name' AND media.file_path = channels.file_path ";
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
    $query = "SELECT DISTINCT channel_name FROM subscribe WHERE username='$username' ";
    $result = mysql_query($query);
    $public_channel=array("Oriental_Theatre", "POP_Music", "Today", "The_Youth", "Vintage");

    echo "<table border='0'><tr><th align='left' style='font-size:22px; color:blue;'>My channels</th></tr><tr><td>&nbsp;</td></tr>";
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        $channel_name = $row['channel_name'];
        if (in_array($row['channel_name'], $public_channel)){
            $channel_nickname = $row['channel_name'];
            $channel_nickname = clean_str($channel_nickname);
        }else {
            $channel_nickname = find_nickname($channel_name);
        }
        // echo $channel_name;
        // echo $row['channel_name'];

    ?>
        <tr>
            <td align='center'>
                <a href = "my_channel.php?channel_name=<?php echo $channel_name; ?>" style="text-decoration: none; font-size:18px; color:black"> <?php echo $channel_nickname ?></a>
            </td>
            <td >
                <FORM action ="my_channel.php" method="post">
                    <button name = "unsubscribe" value=<?php echo $channel_name; ?> >Unsubscribe</button>
                </FORM>
            </td>
        </tr>
        <tr><td>&nbsp;&nbsp;</td>
        </tr>

    <?php
    }
    echo "<tr><td>&nbsp;&nbsp;</td></tr>";
    echo "<FORM action ='my_channel.php' method='post' name='my_channel' id='my_channel'>";
    echo "<tr>
            <td valign='bottom'>
                <div>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <select name='public_channel' style='font-size:16px;'>
                    <option value='DEFAULT'>Browse public channels</option>
                    <option value='Oriental_Theatre'>Oriental Theatre</option>
                    <option value='POP_Music'>POP Music</option>
                    <option value='Today'>Today</option>
                    <option value='The_Youth'>The Youth</option>
                    <option value='Vintage'>Vintage</option>
                </select>
                <button name='browse_channel'>
                    <img src='https://cdn4.iconfinder.com/data/icons/wirecons-free-vector-icons/32/add-128.png' height='16'/>
                </button>
                </div>
            </td >";
    echo "</tr> ";
    echo "<tr><td>&nbsp;&nbsp;</td></tr>";
    echo "<tr>
            <td valign='bottom'>
                <div>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type='text' style='font-size:16px;' placeholder='Search channels' name='private_channel'>
                <button name='search_channels'>
                    <img src='https://cdn4.iconfinder.com/data/icons/ionicons/512/icon-ios7-search-strong-128.png' height='16'/>
                </button>
                </div>
            </td >";
    echo "</tr> ";
    echo "</FORM>";
    if (isset($insert_error)){
        echo "<tr>
              <td valign='bottom'><p style='font-size:18px; color:red;'>&nbsp;&nbsp;$insert_error</p></td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>





</body>
</html>
