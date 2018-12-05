<link rel="stylesheet" type="text/css" href="css/default.css" />
<?php
session_start();

include_once "function.php";
include_once "modules/account.php";


// if (isset($_GET['email_login'])){
//     $email_login = $_GET['email_login'];
//     $temp_pass = mt_rand(10000, 99999);
//
//     $to = $email_login;
//     $subject = "Reset your password! ";
//     $headers = "From: metube_G5";
//
//     $msg = "Your temporary password is $temp_pass! \n Please reset your password ASAP, Thank you!";
//
//     mail($to, $subject, $msg, $headers);
// }

if (isset($_SESSION["username"])){
    $username = $_SESSION["username"];
    $nickname = $_SESSION["nickname"];
}
else{
    header('Location: index_header.php?unknowuser=1');
}


if(isset($_POST['cancel'])) {
    header('Location: my_account.php');
}

if (isset($_GET["id"])){
    if ($_GET["id"] == "edit_email" ){
        if(isset($_POST['cancel'])) {
            header('Location: my_account.php');
        }

        if(isset($_POST['submit'])) {
            if(empty($_POST['email'])){
                $edit_result = "Email cannot be empty!";
            }
            else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                $edit_result = "Email address is not valid.<br>";
            }
            else{
                $check = email_check($_POST['email']);
                if($check == 0){
                    updateEmail($_SESSION['username'], $_POST['email']);
                    $_SESSION['email'] = $_POST['email'];
                    $edit_result = " The log-in email has been updated!";
                    // header('Location: my_account.php');
                }
                else{
                    $edit_result = "Email address already exists. Please use another one!<br>";
                }
            }
        }
    ?>

        <form method="post" action="<?php echo "edit_account.php?id=edit_email"; ?>">

        <p><b>Email<b/><p/>
        <hr>
        <p> Change your email to login <p/>

        <table width="100%">
        	<tr>
        		<td width="20%">New Email:</td>
        		<td width="80%"><input class="text" type="text" name="email"><br /></td>
        	</tr>
            <tr>
                <td><input name="submit" type="submit" value="Save"><input name="cancel" type="submit" value="Cancel"><br /></td>
            </tr>
        </table>
        </form>
    <?php
    }
    else if ($_GET["id"] == "edit_nickname"){

        if(isset($_POST['cancel'])) {
            header('Location: my_account.php');
        }

        if(isset($_POST['submit'])) {
            $nickname_new = $_POST['nickname'];

            if(empty($nickname_new)){
                $edit_result = "Nickname is empty!";
            }
            else if(strlen($nickname_new) > 30){
                $edit_result = "Nickrname is too long!";
            }
        	else {
                $nickname_new = mysql_real_escape_string($nickname_new);
                $query = "SELECT * FROM account WHERE nickname = '$nickname_new' ";
                $check = mysql_query($query);

                if (mysql_num_rows($check) > 0){
                    $edit_result = "This nickname already exists. Please use another one!<br>";
                }
                else {
                    updateNickname($nickname_new, $_SESSION['username']);
                    $_SESSION['nickname']=$nickname_new;
                    $edit_result = "You nickname has been updated! ";
                    // header('Location: my_account.php');
                }
        	}
        }

    ?>
        <form method="post" action="<?php echo "edit_account.php?id=edit_nickname"; ?>">

        <p><b>Nickname<b/><p/>
        <hr>
        <p> Update your nickname <p/>

        <table width="100%">
        	<tr>
        		<td width="20%">New Nickname:</td>
        		<td width="80%"><input class="text"  type="text" name="nickname"><br /></td>
        	</tr>
            <tr>
                <td><input name="submit" type="submit" value="Save"><input name="cancel" type="submit" value="Cancel"><br /></td>
            </tr>
        </table>
        </form>

    <?php
    }
    else if ($_GET["id"] == "edit_password"){
        if(isset($_POST['cancel'])) {
            header('Location: my_account.php');
        }

        if(isset($_POST['submit'])) {
            // $check = user_pass_check($_SESSION['username'], $_POST['password']);
            $check = user_pass_check($_SESSION['email'], $_POST['password']);
            if($check==2) {
                $edit_result = "Incorrect password. Please try again!";
            }
            else if($check==0){
                updatePassword($_SESSION['username'], $_POST['password_new']);
                $edit_result = 'Your password has been reset! ';
                // header('Location: my_account.php');
            }
        }
    ?>
        <form method="post" action="<?php echo "edit_account.php?id=edit_password"; ?>">

        <p><b>Password<b/><p/>
        <hr>
        <p> Change your password to login <p/>

        <table width="100%">
        	<tr>
        		<td width="20%">Current Password:</td>
                <td width="80%"><input  type="password" name="password"><br /></td>
        		<!-- <td width="80%"><input class="text"  type="text" name="password"><br /></td> -->
        	</tr>
            <tr>
        		<td width="20%">New Password:</td>
        		<td width="80%"><input  type="password" name="password_new"><br /></td>
        	</tr>
            <tr>
                <td><input name="submit" type="submit" value="Save"><input name="cancel" type="submit" value="Cancel"><br /></td>
            </tr>
        </table>
        </form>

    <?php
    }
}
?>


<?php
  if(isset($edit_result))
   {  echo "<div id='account_result' style='color:red;'>".$edit_result."</div>";}

?>
