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



if(isset($_POST['cancel'])){
    header('Location: inbox.php');
}
else if(isset($_POST['send'])){
    $check = checkBlockBy($_SESSION['username_to'], $_SESSION['username_from']);
    // echo $check;
    if($check == 1){
        $error = "The message cannot be sent! It is possible you are blocked by the user ! ";
    }
    else if($check == 3){
        new_message($_SESSION['username'], $_SESSION['username_from'], $_SESSION['subject'], $_POST['message']);
    }
}

include_once "browse_header.php";

?>


<table border="1">
    <tr>
        <td>Subject:&nbsp;&nbsp;<?php echo $_SESSION['subject']; ?></td>
    </tr>
	<tr>
        <?php
            if($_SESSION['username'] == $_SESSION['username_from']){
                // $username_from = "me";
                $nickname_from = "me";
            }else{
                $username_from=$_SESSION['username_from'];
                $nickname_from = find_nickname($username_from);
            }
        ?>
        <td >From:&nbsp;&nbsp;<?php echo $nickname_from; ?><br /></td>
	</tr>
    <tr>
        <td><textarea name="message" rows='6' cols='90' style="font-size:15px" readonly><?php echo $_SESSION['message']; ?></textarea></td>
	</tr>
</table>


<form method="post" action= <?php echo "reply_message.php" ?> >
<table border="1">
    <tr>
        <!-- <td><input name = "message" type="text" style="width: 600px; height: 200px;"></td> -->
        <td><textarea name="message" rows='10' cols='90' style="font-size:15px"></textarea></td>
    </tr>
    <tr>
        <td><input name="send" type="submit" value="Send"><input name="cancel" type="submit" value="Cancel"><br /></td>
    </tr>
</table>
</form>


<?php
  if(isset($error))
   {  echo "<div id='new_message_result' style='font-size:16px; color:blue;'>".$error."</div>";}
?>


<body/>
<html/>
