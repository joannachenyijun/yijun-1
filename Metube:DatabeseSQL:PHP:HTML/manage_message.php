<!-- Mange Account -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<style type="text/css">
</style>
</head>
<body>

<?php

session_start();
include_once "function.php";
include_once "modules/account.php";

if (isset($_SESSION["username"])){
    $username=$_SESSION["username"];
    $nickname = $_SESSION["nickname"];
}
else{
    header('Location: index_header.php?unknowuser=1');
}


if(isset($_GET['username_from'])){
    $username = mysql_real_escape_string($_GET['username_from']);
    $_SESSION['username_from'] = mysql_real_escape_string($_GET['username_from']);
    $_SESSION['nickname_from'] = find_nickname($_SESSION['username_from']);
}

if(isset($_GET['username_to'])){
    $username = mysql_real_escape_string($_GET['username_to']);
    $_SESSION['username_to'] = mysql_real_escape_string($_GET['username_to']);
    $_SESSION['nickname_to'] = find_nickname($_SESSION['username_to']);
}

if(isset($_GET['subject'])){
    $subject = mysql_real_escape_string($_GET['subject']);
    $_SESSION['subject'] = mysql_real_escape_string($_GET['subject']);
}

if(isset($_GET['message'])){
    $message = mysql_real_escape_string($_GET['message']);
    $_SESSION['message'] = mysql_real_escape_string($_GET['message']);
}

if(isset($_GET['time'])){
    $time = $_GET['time'];
    $_SESSION['time'] = $_GET['time'];
}

if(isset($_POST['back'])){

    mark_as_read($_SESSION['username_from'], $_SESSION['username_to'], $_SESSION['subject'], $_SESSION['message'], $_SESSION['time']);
    header('Location: inbox.php');
}
else if(isset($_POST['reply'])){
    // echo "I am reply";
    mark_as_read($_SESSION['username_from'], $_SESSION['username_to'], $_SESSION['subject'], $_SESSION['message'], $_SESSION['time']);
    header('Location: reply_message.php');
}
else if(isset($_POST['delete'])){
    // echo "I am delete";
    delete_message($_SESSION['username_from'], $_SESSION['username_to'], $_SESSION['subject'], $_SESSION['message'], $_SESSION['time']);
    header('Location: inbox.php');
}
else if(isset($_POST['mark_as_unread'])){
    // echo "I am unread";
    mark_as_unread($_SESSION['username_from'], $_SESSION['username_to'], $_SESSION['subject'], $_SESSION['message'], $_SESSION['time']);
    header('Location: inbox.php');
}
else{
    // echo "I am else";
    mark_as_read($_SESSION['username_from'], $_SESSION['username_to'], $_SESSION['subject'], $_SESSION['message'], $_SESSION['time']);
}


include_once "browse_header.php"

?>

<form method="post" action= <?php echo "manage_message.php" ?> >

<table border="1">
    <tr>
        <td>Subject:&nbsp;&nbsp;<?php echo $subject; ?></td>
    </tr>
	<tr>
        <td >From:&nbsp;&nbsp;<?php echo $_SESSION['nickname_from']; ?><br /></td>
	</tr>
    <tr>
        <?php
            if ($_SESSION['username_from'] == $_SESSION['username_to']){
                $nickname_to = "me";
            }
            else {
                $nickname_to = find_nickname($_SESSION['username_to']);
            }
        ?>
        <td >To:&nbsp;&nbsp;<?php echo $nickname_to; ?><br /></td>
	</tr>
    <tr>
		<td ><p style="width: 600px; height: 200px;"><?php echo $message; ?></p><br /></td>
	</tr>
    <tr>
        <td><input name="reply" type="submit" value="Reply"><input name="delete" type="submit" value="Delete"><input name="mark_as_unread" type="submit" value="Mark as unread"><input name="back" type="submit" value="Back"><br /></td>
    </tr>
</table>
</form>



<body/>
<html/>
