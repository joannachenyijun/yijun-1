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

<!-- <link rel="stylesheet" type="text/css" href="css/default.css" /> -->

<body>
<?php
// session_start();
// include_once "function.php";


if(isset($_POST['cancel'])){
    header('Location: my_account.php');
}

// include_once "browse_header.php";

if(isset($_POST['add'])){
    $check = checkContact($_SESSION['username'], $_POST['contact_add']);
    if($check == -1){
        $edit_error = "The username is required! ";
    }
    else if($check == 0){
        $edit_error = "The user is not found! ";
    }
    else if($check == 1){
        $edit_error = " The contact already exists! ";
    }
    else if($check == 2){
        $edit_error = " The contact cannot be added! It is possible that you are blocked by this user ";
    }
    else if($check == 4){
        $edit_error = "You can not add yourself! ";
    }
    else if ($check == 5){
        $edit_error = "You have blocked this user! ";
    }
    else{
        $edit_error = "The user is added to your contacts ! ";
        $result = addContact($_SESSION['username'], $_POST['contact_add']);
    }

}
else if(isset($_POST['delete'])){
    // echo "Are you sure you want to delete this contact?"
    $check = checkContact($_SESSION['username'], $_POST['contact_del']);
    if($check == -1){
        $edit_error = "The contact name is required! ";
    }
    else if($check == 0){
        $edit_error = "The contact doesn't exists! ";
    }
    elseif($check == 3){
        $edit_error = "The user is not in your contact list! ";
    }
    else if($check == 4){
        $edit_error = "You can not delete yourself! ";
    }
    else{
        // $result = deleteContact($_SESSION['username'], $_POST['contact_del']);
        $check_friend = checkFriend($_SESSION['username'], $_POST['contact_del']);
        if($check_friend == 1){
            deleteFriend($_SESSION['username'], $_POST['contact_del']);
        }

        $check_block = checkBlock($_SESSION['username'], $_POST['contact_del']);
        if($check_block == 1){
            deleteBlock($_SESSION['username'], $_POST['contact_del']);
        }
        $edit_error = "The user is deleted! ";
        $result = deleteContact($_SESSION['username'], $_POST['contact_del']);
    }
}

?>



<form method="post" action="<?php echo "contact_edit.php"; ?>">
    <p><b>Manage Your Contacts<b/><p/>
    <hr>

    <table width="100%">
    	<tr>
    		<td width="20%">Add a new contact:</td>
    		<td width="80%"><input class="text" type="text" name="contact_add" placeholder="&#43;Username"><br /></td>
    	</tr>
        <tr>
            <td><input name="add" type="submit" value="Add"><input name="cancel" type="submit" value="Cancel"><br /></td>
        </tr>
        <tr>
    		<td width="20%">Delete a contact:</td>
    		<td width="80%"><input class="text" type="text" name="contact_del" placeholder="&#43;Username"><br /></td>
    	</tr>
        <tr>
            <td><input name="delete" type="submit" value="Delete" ><input name="cancel" type="submit" value="Cancel"><br /></td>
        </tr>
    </table>
    <br/><br/>
</form>



<!-- <form method="post", action =<?php echo "my_account.php" ?> >
    <input name="back" type="submit" value="Back to my account">
</form> -->

<?php
  if(isset($edit_error))
   {  echo "<div id='contact_result' style='font-size:18px; color:blue'>".$edit_error."</div>";}
?>

</body>
</html>
