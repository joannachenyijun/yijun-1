<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
    session_start();
	include_once "function.php";
    include_once "modules/account.php";

    // if (isset($_GET['file_path'])){
    //     $file_path = $_GET['file_path'];
    //     echo $file_path;
    // }
?>

<?php

if(isset($_POST['forget_password'])){
    // if (!empty($_POST['email_login'])){
    //     $email_login =  $_POST['email_login'];
    //     header("Location: edit_account.php?email_login=$email_login");
    // }
    // else {
    //     $login_error = "The email can not be empty!";
    // }
    $login_error = "Please contact us: \n mowengator@gmail.com/352-226-6030 \n Thank you! ";
}
elseif(isset($_POST['login'])) {
    $_POST['email_login'] = preg_replace('/\s+/', '', $_POST['email_login']);
	if($_POST['email_login'] == "" || $_POST['password_login'] == "") {
		$login_error = "One or more fields are missing.";
	}
	else {
		$check = user_pass_check($_POST['email_login'],$_POST['password_login']); // Call functions from function.php
		if($check == 1) {
			$login_error = "User ".$_POST['email_login']." is not found.";
		}
		elseif($check==2) {
			$login_error = "Incorrect password.";
		}
		else if($check==0){
            $_SESSION['email'] = $_POST['email_login'];
            $row = getUserInfo($_POST['email_login'], $_POST['password_login']);
			$_SESSION['username']=$row['username']; //Set the $_SESSION['username']
            $_SESSION['password'] = $row['password'];
            $_SESSION['nickname'] = $row['nickname'];

            if (isset($_GET['file_path'])){
                $file_path = $_GET['file_path'];
                header("Location: media.php?file_path=$file_path");
            }
            else{
                header('Location: browse.php');
            }
		}
	}
}
elseif(isset($_POST['register'])) {
    $_POST['email'] = filter_var ($_POST['email'], FILTER_SANITIZE_STRING);
    $_POST['email'] = preg_replace('/\s+/', '', $_POST['email']);

    if(empty($_POST['username'])){  # check username
        $register_error = "Username is required";
    }
    else if(strlen($_POST['username']) > 30){
        $register_error = "Username is too long!";
    }
    else if(strlen($_POST['nickname']) > 30){
        $register_error = "Nickname is too long!";
    }
    else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){  # check email
        $register_error = "Email address is not valid.<br>";
    }
	else if( $_POST['password'] != $_POST['password_rept']) { # check password
		$register_error = "Passwords don't match. Try again?";
	}
	else {

        if(empty($_POST['nickname'])){
            // check nickname
            $_POST['nickname'] = $_POST['username'];
        }

	    $check = isUserExist($_POST['username'], $_POST['email'], $_POST['nickname']);
		if($check == 0){
			//create a new user
			$user = new Account();
			$user->create($_POST['username'],$_POST['nickname'],$_POST['email'],$_POST['fname'],$_POST['lname'],$_POST['password'],$_POST['gender'],$_POST['dob']);
            $_SESSION['myAccount'] = serialize($user);
			$_SESSION['username']=$_POST['username'];
            $_SESSION['nickname']=$_POST['nickname'];
            $_SESSION['email']=$_POST['email'];
            $_SESSION['password']=$_POST['password'];

            if (isset($_GET['file_path'])){
                $file_path = $_GET['file_path'];
                header("Location: media.php?file_path=$file_path");
            }
            else{
                header('Location: browse.php');
            }
		}
		else if($check == 1){
			$register_error = "The User already exists. Please user a different username.";
		}
        else if ($check == 2){
        	$register_error = "The User already exists. Please user a different email.";
        }
        else if ($check == 3){
            $register_error = "The User already exists. Please user a different nickname.";
        }
	}
}
elseif(isset($_POST['cancel'])){
    header('Location: index_header.php');
}

?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Media browse</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript" src="js/jquery-latest.pack.js"></script>
<script type="text/javascript">

function indexDropdown(){
    document.getElementById("indexDropdown").classList.toggle("show");
}

</script>


<style>

.para{
    font-size: 20px;
}

.button_blue {
    background-color: #2B65EC;
    border: none;
    color: white;
    padding: 7px 7px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 2px 1px;
    cursor: pointer;
}
.button {
    /*background-color: #4CAF50;*/
    background-color:transparent;
    border-color:transparent;
    border: none;
    cursor: pointer;
}


/* Dropdown Button */
.dropbtn {
    /*background-color: #4CAF50;*/
    background-color:transparent;
    border-color:transparent;
    border: none;
    cursor: pointer;
}

/* Dropdown button on hover & focus */
.dropbtn:hover, .dropbtn:focus {
    /*background-color: #3e8e41;*/
}

/* The container <div> - needed to position the dropdown content */
.dropdown {
    position: relative;
}

/* Dropdown Content (Hidden by Default) */
.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 120px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

/* Links inside the dropdown */
.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

/* Change color of dropdown links on hover */
.dropdown-content a:hover {background-color: #f1f1f1}

/* Show the dropdown menu (use JS to add this class to the .dropdown-content container when the user clicks on the dropdown button) */
.show {display:block;}

</style>
</head>

<body>


<table>
    <tr>
        <td>
            <div class="dropdown">
                <button onclick="indexDropdown()" class="dropbtn">
                    <img src="https://cdn1.iconfinder.com/data/icons/android-user-interface-vol-1/16/38_-_menu_bar_lines_option_list_hamburger_web-512.png" alt="menu" style="width:35px;height:30px;">
                </button>
                <div id="indexDropdown" class="dropdown-content">
                    <a href="index_header.php">Home</a>
                    <a href="login.php">Sign in</a>
                    <a href="login.php">Register</a>
                    <hr>
                    <a href="browse.php?category=<?php echo "music"?>">Music</a>
                    <a href="browse.php?category=<?php echo "gaming"?>">Gaming</a>
                    <a href="browse.php?category=<?php echo "movies"?>">Movies</a>
                    <a href="browse.php?category=<?php echo "tv_shows"?>">TV shows</a>
                    <a href="browse.php?category=<?php echo "news"?>">News</a>
                    <a href="browse.php?category=<?php echo "pets"?>">Pets</a>
                    <a href="browse.php?category=<?php echo "comedy"?>">Comedy</a>
                </div>
            </div>
        </td>
        <td>
            <img src="http://2.bp.blogspot.com/-LhBQ7vIrmBQ/VqxzdUlkf8I/AAAAAAAADm8/NtKKb8HaPWk/s400/logo_metube.jpg" alt="MeTube" style="width:70px;height:30px;"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </td>
        <td>
            <form action="browse.php" method="post">
                <input type="text"  name="search"  placeholder="Search" style ="width:300pt; height:20pt; font-size:16px" >
                <!-- <input type="botton" name="submit" background = "icon/search.png"> -->
                <button type="submit" style="background-color:transparent; border-color:transparent;">
                    <img src="http://www.pic4ever.com/images/2mpe5id.gif" height="19"/>
                </button>
            </form>
        </td>
        <td>
            &nbsp;&nbsp;&nbsp;&nbsp;
        </td>
        <td>
             <form action="advanced_search.php" method="post">
              	<!-- <input type="submit"  name="advanced_search" value ="Advanced Search"> -->
                <button class="button_blue">Advanced Search</button>
             </form>
        </td>
    </tr>
</table>

<hr/>

<?php
if (isset($_GET['index'])){
    $arr = clean_str_to_arr($_GET['index']);
    $str = implode(" ",$arr);

    echo "<p style ='font-size:22px; color:blue;'>Please Login/Register to $str !</p>";

}
?>

<table border="0" cellpadding="30">
<tr>
<td  valign = "top" align="left">

<!-- <body> -->
    <p style="font-size:18px; color:blue;"> Returning User? Sign in to your account<p>

    <?php
    if (isset ($_GET['file_path'])){
        $file_path = $_GET['file_path'];
    ?>
    <form method="post" action="<?php echo "login.php?file_path=$file_path"; ?>">
     <?php
     }else{
     ?>
     <form method="post" action="<?php echo "login.php"; ?>">
     <?php
     }
     ?>
        <P>
        <LABEL for="email">Email:</LABEL>
            <input type="text" name="email_login" id="email_login"> <br>
        <LABEL for="password">Password:</LABEL>
        	<input  type="password" name="password_login" id="password_login"> <input type="submit" name="forget_password" id="forget_password" style="cursor: pointer; color:red;" value="Forget password?"><br>
        <input name="login" type="submit" value="Login"><input name="reset" type="reset" value="Reset"><input name="cancel" type="submit" value="Cancel"><br /><br />
        <?php
        if(isset($login_error)){
            echo "<div id='login_result' style='font-size:15px; color:red;'>".$login_error."</div>";
        }
        ?>
        </p>
    </form>
</td>


<td valign = "top" align="left">

<p style="font-size:18px; color:blue;"> New User? Create an account <p>
<!-- <form action="login.php" method="post"> -->
<?php
if (isset ($_GET['file_path'])){
    $file_path = $_GET['file_path'];
?>
<form method="post" action="<?php echo "login.php?file_path=$file_path"; ?>">
 <?php
 }else{
 ?>
 <form method="post" action="<?php echo "login.php"; ?>">
 <?php
 }
 ?>
    <P>
    <LABEL for="username">Username:</LABEL>
        <input type="text" name="username" id="username"> <br>
    <LABEL for="nickname">Nickname:</LABEL>
        <input type="text" name="nickname" id="nickname"> <br>
    <LABEL for="email">Email:</LABEL>
        <input type="text" name="email" id="email"> <br>
    <LABEL for="fname">First Name:</LABEL>
        <input type="text" name="fname" id="fname"> <br>
    <LABEL for="lname">Last Name:</LABEL>
        <input type="text" name="lname" id="lname"> <br>
    <LABEL for="password">Create Password:</LABEL>
	    <input  type="password" name="password" id="password"> <br>
    <LABEL for="password_rept">Repeat Password:</LABEL>
        <input type="password" name="password_rept" id="password_rept"> <br>
    <LABEL for="dob">Date of Birth</LABEL>
        <input type="date" name="dob" id="dob"><br>
    Gender:
        <input type="radio" name="gender" value="male" >Male
        <input type="radio" name="gender" value="female">Female
        <input type="radio" name="gender" value="prefer not to say" checked >Prefer Not to Say <br /><br />

	<input name="register" type="submit" value="Register"><input name="reset" type="reset" value="Reset"><input name="cancel" type="submit" value="Cancel"><br /><br />
    <?php
      if(isset($register_error)){
          echo "<div id='register_error_result' style='font-size:15px; color:red;'>".$register_error."</div>";
      }
    ?>

    </P>
</form>

</td>
</tr>
<table>



</body>
</html>
