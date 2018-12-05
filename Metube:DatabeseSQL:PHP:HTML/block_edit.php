<!-- <link rel="stylesheet" type="text/css" href="css/default.css" /> -->
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

<?php
 // include_once "browse_header.php"
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

<?php

if(isset($_POST['cancel'])){
    header('Location: my_account.php');
}


// include_once "browse_header.php";


if(isset($_POST['add'])){

    $check_superclass = checkContact($_SESSION['username'], $_POST['block_add']);
    if($check_superclass == -1){
        $edit_error = "The username is required! ";
    }
    else if($check_superclass == 0){
        $edit_error = "The user is not found! ";
    }
    else if($check_superclass == 4){
        $edit_error = "You can not block yourself! ";
    }
    else{
        $check_subclass = checkBlock($_SESSION['username'], $_POST['block_add']);
        if($check_subclass == 1){
            $edit_error = " He is already in your blocked list! ";
        }
        else{
            addBlock($_SESSION['username'], $_POST['block_add']);
            deleteContact($_SESSION['username'], $_POST['block_add']);
            $check = checkFriend($_SESSION['username'], $_POST['block_add']);
            if($check == 1){
                deleteFriend($_SESSION['username'], $_POST['block_add']);
            }
            $edit_error = "The user is blocked! ";
        }
    }

}
else if(isset($_POST['unblock'])){
    $check_superclass = checkContact($_SESSION['username'], $_POST['block_rm']);
    if($check_superclass == -1){
        $edit_error = "The username is required! ";
    }
    else if($check_superclass == 0){
        $edit_error = "The user doesn't exists! ";
    }
    else if($check_superclass == 2){
        $edit_error = " The contact cannot be put back to your contact/friend list! It is possible that you are blocked by this user ";
    }
    else if($check_superclass == 4){
        $edit_error = "You can not unblock yourself! ";
    }
    else{
        $check_subclass = checkBlock($_SESSION['username'], $_POST['block_rm']);
        if($check_subclass == 3){
            $edit_error = "The user is not in your block list! ";
        }
        else{
            deleteBlock($_SESSION['username'], $_POST['block_rm']);
            addContact($_SESSION['username'], $_POST['block_rm']);
            $edit_error = "The user is not blocked now! ";
        }
    }
}
else if(isset($_POST['delete'])){
    $check_superclass = checkContact($_SESSION['username'], $_POST['block_rm']);
    if($check_superclass == -1){
        $edit_error = "The username is required! ";
    }
    else if($check_superclass == 0){
        $edit_error = "The user doesn't exists! ";
    }
    else if($check_superclass == 4){
        $edit_error = "You can not unblock yourself! ";
    }
    else{
        $check_subclass = checkBlock($_SESSION['username'], $_POST['block_rm']);
        if($check_subclass == 3){
            $edit_error = "The user is not in your block list! ";
        }
        else{
            deleteBlock($_SESSION['username'], $_POST['block_rm']);
            $edit_error = "The user is deleted! ";
        }
    }
}

?>



<form method="post" action="<?php echo "block_edit.php"; ?>">
    <p><b>Manage Your Blocked List<b/><p/>
    <hr>

    <table border='0' cellpadding='10'>
    	<tr>
    		<td >Add a new block:</td>
    		<td ><input class="text" type="text" name="block_add" placeholder="&#43;Username"><br /></td>
    	</tr>
        <tr>
            <td><input name="add" type="submit" value="Block"><input name="cancel" type="submit" value="Cancel"><br /></td>
        </tr>
        <tr>
    		<td >Unblock/Delete a contact:</td>
    		<td ><input class="text" type="text" name="block_rm" placeholder="&#43;Username"><br /></td>
    	</tr>
        <tr>
            <td><input name="unblock" type="submit" value="Unblock"><input name="delete" type="submit" value="Delete"><input name="cancel" type="submit" value="Cancel"><br /></td>
        </tr>
    </table>
    <br/><br/>
</form>



<!-- <form method="post", action =<?php echo "my_account.php" ?> >
    <input name="back" type="submit" value="Back to my account">
</form> -->


<?php
  if(isset($edit_error))
   {  echo "<div id='block_result' style='font-size:18px; color:blue'>".$edit_error."</div>";}
?>


</body>
</html>
