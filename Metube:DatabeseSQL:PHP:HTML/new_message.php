<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
messageInput
{
width: 300px;
height: 200px;
/*margin-top: 0px;
margin-left: 5px;
padding-top:0px;
vertical-align:top;*/
}
</style>
</head>
<body>

<?php
session_start();


include_once "function.php";
include_once "modules/account.php";

if (isset($_SESSION["username"])){
    $username = $_SESSION["username"];
    $nickname = $_SESSION["nickname"];
}
else{
    header("Location: index_header.php?index='Send_message'");
}


if(isset($_POST['cancel'])){
    header('Location: browse.php');
}
else if(isset($_POST['send'])){

    $check_to = check_user_valid($_POST['to']);

    if ($check_to == 0){
        $error = "The user doesn't exist! The message cannot be sent!";
    }
    else{
        $check_blockby = checkBlockBy($_POST['to'], $_SESSION['username']);
        // echo $check;
        if($check_blockby == 1){
            $error = "The message cannot be sent! It is possible you are blocked by the user ! ";
        }
        else if($check_blockby == 3){
            $_POST['to'] = find_username($_POST['to']);
            new_message($_SESSION['username'], $_POST['to'], $_POST['subject'], $_POST['message']);
            $error = "The message has been sent! ";
        }
    }
}
// else if(isset($_POST['save'])){
//     draft($_SESSION['username'], $_POST['to'], $_POST['subject'], $_POST['message']);
// }

include_once "browse_header.php"

?>

<form method="post" action="new_message.php">

<table border="1" cellpadding='5'>
    <tr>
        <td bgcolor="grey"><font color="white">New Message<font/></td>
	<tr>
        <td >To&nbsp;&nbsp;<input type="text" name="to" style="font-size:15px"><br /></td>
	</tr>
    <tr>
		<td >Subject&nbsp;&nbsp;<input type="text" name="subject" style="font-size:15px"><br /></td>
	</tr>
    <tr>
		<td >
            <!-- <input type="text" name="message" style="width: 300px; height: 200px;"><br /> -->
            <textarea name="message" rows='10' cols='50' style="font-size:15px"></textarea>
        </td>
        <!-- <td ><input type="text" name="message" class="messageInput"><br /></td> -->
	</tr>
    <tr>
        <!-- <td><input name="send" type="submit" value="Send"><input name="save" type="submit" value="Save"><input name="cancel" type="submit" value="Cancel"><br /></td> -->
        <td><input name="send" type="submit" value="Send"><input name="cancel" type="submit" value="Cancel"><br /></td>
    </tr>
</table>
</form>



<?php
  if(isset($error))
   {  echo "<div id='new_message_result' style='font-size:18px; color:blue;'>".$error."</div>";}
?>


<body/>
<html/>
