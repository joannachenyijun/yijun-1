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
<title>Media browse</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript">

</script>

<style>

</style>

</head>

<body>

<?php
$file_path = $_GET["file_path"];
$file_name = basename($file_path);
$title = $_GET["title"];


if (isset($_POST['submit'])){
    if (!empty($_POST['block_view'])){
        $block_view_arr = $_POST['block_view'];
        foreach($block_view_arr as $blocked_username){
            $check = "SELECT * FROM view_blocks WHERE file_path = '$file_path' AND blocked_by = '$username' AND blocked_username = '$blocked_username' ";
            $check_results = mysql_query($check);
            if (mysql_num_rows($check_results) == 0){
                insert_view_block($file_path, $username, $blocked_username);
            }
        }
        // update_media_share_type($file_path);
    }

    if (!empty($_POST['block_download'])){
        $block_download_arr = $_POST['block_download'];
        foreach($block_download_arr as $blocked_username){
            $check = "SELECT * FROM download_blocks WHERE file_path = '$file_path' AND blocked_by = '$username' AND blocked_username = '$blocked_username' ";
            $check_results = mysql_query($check);
            if (mysql_num_rows($check_results) == 0){
                insert_download_block($file_path, $username, $blocked_username);
            }
        }
        // update_media_share_type($file_path);
    }

    if (!empty($_POST['unblock_view'])){
        $unblock_view_arr = $_POST['unblock_view'];
        foreach($unblock_view_arr as $unblocked_username){
            $check = "SELECT * FROM view_blocks WHERE file_path = '$file_path' AND blocked_by = '$username' AND blocked_username = '$unblocked_username' ";
            $check_results = mysql_query($check);
            if (mysql_num_rows($check_results) > 0){
                remove_view_block($file_path, $username, $unblocked_username);
            }
        }
    }

    if (!empty($_POST['unblock_download'])){
        $unblock_download_arr = $_POST['unblock_download'];
        foreach($unblock_download_arr as $unblocked_username){
            $check = "SELECT * FROM download_blocks WHERE file_path = '$file_path' AND blocked_by = '$username' AND blocked_username = '$unblocked_username' ";
            $check_results = mysql_query($check);
            if (mysql_num_rows($check_results) > 0){
                remove_download_block($file_path, $username, $unblocked_username);
            }
        }
    }

    if(isset($_POST['block_view_add'])){
        $blocked_nickname = $_POST['block_add'];
        $blocked_username = find_username($blocked_nickname);

        $check = "SELECT * FROM account WHERE username = '$blocked_username'";
        $check_results = mysql_query($check);
        if (mysql_num_rows($check_results) > 0){
            insert_view_block($file_path, $username, $blocked_username);
            // update_media_share_type($file_path);
        }
        else{
            $block_error = "The user ".$blocked_nickname." doesn't exist!";
        }

    }

    if(isset($_POST['block_download_add'])){
        $blocked_nickname = $_POST['block_add'];
        $blocked_username = find_username($blocked_nickname);

        $check = "SELECT * FROM account WHERE username = '$blocked_username'";
        $check_results = mysql_query($check);
        if (mysql_num_rows($check_results) > 0){
            insert_download_block($file_path, $username, $blocked_username);
            // update_media_share_type($file_path);
        }
        else{
            $block_error = "The user ".$blocked_nickname." doesn't exist!";
        }
    }

// if the view_block is not empty, then share_type will be updated
    update_media_share_type($file_path);

}
else if (isset($_POST['cancel'])){
    header('Location: my_media.php');
}



?>


<?php
include_once "browse_header.php";
?>

<p style="font-size:20px;">The block list:&nbsp; <span style="font-size:20px; color:blue;"><?php echo $title; ?></span> </p>

<?php

$query_view = "SELECT * FROM view_blocks WHERE blocked_by='$username' AND file_path='$file_path'";
$result_view = mysql_query($query_view);

// echo "<div>";

echo "<FORM action='media_block.php?file_path=$file_path&title=$title' method='post' >";
echo "<table>";
    echo "<tr><th align=right style='font-size:18px;'>Views:</th></tr>";

if (mysql_num_rows($result_view) <= 0){
    echo "<td align='right'><p style='font-size:16px; color:grey;'>None </p></td>";
}
else {
    while ($row = mysql_fetch_array($result_view, MYSQL_ASSOC)){
        $blocked_username = $row["blocked_username"];
?>
        <tr>
            <td align='right'>
                <p style='font-size:16px'><?php echo find_nickname($blocked_username); ?></p>
            </td>
            <td>
                <!-- <FORM action="media_block.php?file_path=<?php echo $file_path.'&title='.$title; ?>" method="post" > -->
                    <input type="checkbox" name="unblock_view[]" value="<?php echo $blocked_username;?>">unblock
                <!-- </FORM> -->
            </td>
        </tr>
<?php
   }
}
// echo "</table>";
?>



<?php
$query_download = "SELECT * FROM download_blocks WHERE blocked_by='$username' AND file_path='$file_path'";
$result_download = mysql_query($query_download);

// echo "<table>";
    echo "<tr><th align=right style='font-size:18px;'>Downloads:</th></tr>";

if (mysql_num_rows($result_download) <= 0){
    echo "<td align='right'><p style='font-size:16px; color:grey;'>None </p></td>";
}
else {
    while ($row = mysql_fetch_array($result_download, MYSQL_ASSOC)){
        $blocked_username = $row["blocked_username"];
?>
        <tr>
            <td align='right'>
                <p style='font-size:16px'><?php echo find_nickname($blocked_username); ?></p>
            </td>
            <td>
                <!-- <FORM action="media_block.php?file_path=<?php echo $file_path.'&title='.$title; ?>" method="post" > -->
                    <input type="checkbox" name="unblock_download[]" value="<?php echo $blocked_username;?>">unblock
                <!-- </FORM> -->
            </td>
        </tr>

<?php
   }
}
// echo "</table>";
?>


<?php
$result_contact = getFullContact($username);

// echo "<table>";
    echo "<tr><th align=right style='font-size:18px;'>Add to block:</th></tr>";
    $count = 1;
    while ($row = mysql_fetch_array($result_contact, MYSQL_ASSOC)){
        $contact = $row["username"];
?>
        <tr>
            <td align='right'>
                <p style="font-size:16px;"><?php echo find_nickname($contact); ?></p>
            </td>
            <td>
                <input id = "chk1_<?php echo $count ;?>"  type="checkbox" onclick="document.getElementById('chk2_<?php echo $count ;?>').checked = this.checked;" name="block_view[]" value="<?php echo $contact;?>">block view
            </td>
            <td>
                <input id="chk2_<?php echo $count ;?>"  type="checkbox"  name="block_download[]" value="<?php echo $contact;?>">block download
            </td>
        </tr>
<?php
    $count += 1;
    }
?>


<?php
$result_contact_block = getBlock($username);

// echo "<table>";
    // echo "<tr><th align=right style='font-size:18px;'>Add to block:</th></tr>";
    // $count = 1;
    while ($row = mysql_fetch_array($result_contact_block, MYSQL_ASSOC)){
        $contact_block = $row["username"];
?>
        <tr>
            <td align='right'>
                <p style="font-size:16px;"><?php echo find_nickname($contact_block); ?></p>
            </td>
            <td>
                <input id = "chk1_<?php echo $count ;?>"  type="checkbox" onclick="document.getElementById('chk2_<?php echo $count ;?>').checked = this.checked;" name="block_view[]" value="<?php echo $contact_block;?>">block view
            </td>
            <td>
                <input id="chk2_<?php echo $count ;?>"  type="checkbox"  name="block_download[]" value="<?php echo $contact_block;?>">block download
            </td>
        </tr>
<?php
    $count += 1;
    }
?>
       <tr><td>&nbsp;</td></tr>
       <tr>
           <td align='right'><input class="text"  type="text" name="block_add" placeholder="&#43;Username" style='font-size:15px;' ></td>
           <td><input id = "chk1_<?php echo $count ;?>" type="checkbox"  onclick="document.getElementById('chk2_<?php echo $count ;?>').checked = this.checked;"  name="block_view_add" value=1>block view</td>
           <td><input id = "chk2_<?php echo $count ;?>" type="checkbox" name="block_download_add" value=1>block download</td>
       </tr>
</table>


<table>
    <tr><td>
    <!-- <FORM action="media_block.php?file_path=<?php echo $file_path.'&title='.$title; ?>" method="post" > -->
        <input type="submit" name="submit" value="Submit"><input type="reset" name="reset" value="Reset"><input type="submit" name="cancel" value="Cancel">
    <!-- </FORM> -->
    </tr></td>
</table>
</FORM>

<?php
if (isset($block_error)){
    echo "<p style='font-size:20px; color:green;'>$block_error</p> ";
}
?>


</body>
</html>
