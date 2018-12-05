<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	session_start();
	include_once "function.php";

    if (isset($_SESSION["username"])){
        $username=$_SESSION["username"];
        $nickname = $_SESSION["nickname"];
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


<!-- <link rel="stylesheet" type="text/css" href="css/default.css" /> -->
<?php

if(isset($_POST['cancel'])){
    header('Location: my_account.php');
}

// include_once "browse_header.php";


if(isset($_POST['add'])){

    $check_superclass = checkContact($_SESSION['username'], $_POST['friend_add']);
    // echo $check;
    if($check_superclass == -1){
        $edit_error = "The username is required! ";
    }
    else if($check_superclass == 0){
        $edit_error = "The user is not found! ";
    }
    else if($check_superclass == 2){
        $edit_error = " The user cannot be added as a friend! It is possible that you are blocked by this user ";
    }
    else if($check_superclass == 4){
        $edit_error = "You can not be a friend of yourself! ";
    }
    else if ($check_superclass == 5){
        $edit_error = "You have blocked this user! ";
    }
    else{
        $check_subclass = checkFriend($_SESSION['username'], $_POST['friend_add']);
        if($check_subclass == 1){
            $edit_error = " He is already your friend! ";
        }
        else{
            $edit_error = " The user is your friend now! ";
            $result = addFriend($_SESSION['username'], $_POST['friend_add']);
            if(!($check_superclass == 1)){
                $result = addContact($_SESSION['username'], $_POST['friend_add']);
            }
        }
    }
}
else if(isset($_POST['unfriend'])){
    // echo "Are you sure you want to delete this contact?"
    $check_superclass = checkContact($_SESSION['username'], $_POST['friend_del']);
    if($check_superclass == -1){
        $edit_error = "The username is required! ";
    }
    else if($check_superclass == 0){
        $edit_error = "The user doesn't exists! ";
    }
    else if($check_superclass == 4){
        $edit_error = "You can not delete yourself! ";
    }
    else{
        $check_subclass = checkFriend($_SESSION['username'], $_POST['friend_del']);
        if($check_subclass == 3){
            $edit_error = "The user is not your friend yet! ";
        }
        else{
            $edit_error = "The user is not your friend now! ";
            $result = deleteFriend($_SESSION['username'], $_POST['friend_del']);
        }
    }
}
else if(isset($_POST['delete'])){
    // echo "Are you sure you want to delete this contact?"
    $check_superclass = checkContact($_SESSION['username'], $_POST['friend_del']);
    if($check_superclass == -1){
        $edit_error = "The username is required! ";
    }
    else if($check_superclass == 0){
        $edit_error = "The user doesn't exists! ";
    }
    else if($check_superclass == 4){
        $edit_error = "You can not delete yourself! ";
    }
    else{
        $check_subclass = checkFriend($_SESSION['username'], $_POST['friend_del']);
        if($check_subclass == 3){
            $edit_error = "The user is not your friend! ";
        }
        else{
            $result = deleteFriend($_SESSION['username'], $_POST['friend_del']);
            $result = deleteContact($_SESSION['username'], $_POST['friend_del']);
            $edit_error = "The user is removed from your contacts! ";
            // $result = deleteBlock($_SESSION['username'], $_POST['friend_del']);
        }
    }
}

?>



<form method="post" action="<?php echo "friend_edit.php"; ?>">
    <p><b>Manage Your Friend List<b/><p/>
    <hr>

    <table border='0' cellpadding='5'>
    	<tr>
    		<td >Add a new friend:</td>
    		<td ><input class="text" type="text" name="friend_add" placeholder="&#43;Username"><br /></td>
    	</tr>
        <tr>
            <td><input name="add" type="submit" value="Add"><input name="cancel" type="submit" value="Cancel"><br /></td>
        </tr>
        <tr>
    		<td >Delete a friend:</td>
    		<td ><input class="text" type="text" name="friend_del" placeholder="&#43;Username"><br /></td>
    	</tr>
        <tr>
            <td><input name="unfriend" type="submit" value="Unfriend"><input name="delete" type="submit" value="Delete"><input name="cancel" type="submit" value="Cancel"><br /></td>
        </tr>
    </table>
    <br/><br/>
</form>


<!-- <form method="post", action =<?php echo "my_account.php" ?> >
    <input name="back" type="submit" value="Back to my account">
</form> -->


<?php
  if(isset($edit_error))
   {  echo "<div id='passwd_result' style='font-size:18px; color:blue;'>".$edit_error."</div>";}
?>



</body>
</html>
