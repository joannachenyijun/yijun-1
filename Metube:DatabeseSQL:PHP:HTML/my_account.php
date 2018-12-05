<!-- Mange Account -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	session_start();
	include_once "function.php";
    include_once "modules/account.php";
    // include_once "browse_header.php"
?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Media browse</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />

</head>


<body>

<?php
// session_start();
//
// include_once "function.php";
// include_once "modules/account.php";

if (isset($_SESSION["username"])){
    $username = $_SESSION["username"];
    $nickname = $_SESSION["nickname"];
}
else{
    header('Location: index_header.php?unknowuser=1');
}

?>

<?php
 include_once "browse_header.php";
?>

<h2>Hi, <?php echo $_SESSION['nickname'];?>! To manage your information and preferences, please use the options below<h2/>
<hr>
<!-- <h3>Edit your sign-in information<h3/> -->
<!-- <hr> -->

<table>
  <form method="post" action="<?php echo "edit_account.php?id=edit_email"; ?>">
        <tr><th align=right style="font-size:20px;"> Sign-in information </th></tr>
        <tr>
            <td align=right style="font-size:18px;">Sign-in Email</td>
            <td align=left style="font-size:16px; color:blue;"><?php echo $_SESSION['email']?>&nbsp;<button name = "edit_email">Edit email</button></td>
        </tr>
  </form>
  <form method="post" action="<?php echo "edit_account.php?id=edit_nickname"; ?>">
        <tr>
            <td align=right style="font-size:18px;">Nickname</td>
            <td align=left style="font-size:16px;color:blue;"><?php echo $_SESSION['nickname']?> &nbsp;<button name="edit_username" >Edit nickname</button></td>
        </tr>
  </form>
  <form method="post" action="<?php echo "edit_account.php?id=edit_password"; ?>">
      <tr>
          <td align=right style="font-size:18px;">Password </td><td align=left><button name="edit_password">Change password</button></td>
      </tr>
  </form>

<tr>
    <td>&nbsp;&nbsp;&nbsp;</td>
</tr>

  <!-- <form method="post" action="<?php echo "contact.php"; ?>"> -->
<tr><th align=right style="font-size:20px;"> Contact information </th></tr>
<tr>
    <td align=right style="font-size:18px;">Contact</td>
    <td align=left >
        <div>
        <form method="post" action="<?php echo "contact.php"; ?>">
            <button name="all" value="1">All</button>&nbsp;
            <button name="recent" value="1">Recents</button>&nbsp;
        </form>
        <form method="post" action="<?php echo "contact_edit.php"; ?>">
            <button name="edit_contact_list" >Edit</button>
        </form>
        <div>
    </td>
</tr>
<tr>
    <td align=right style="font-size:18px;">Friends</td>
    <td align=left>
        <form method="post" action="<?php echo "friend.php"; ?>">
            <button name="friend_list" >All</button>&nbsp;
        </form>
        <form method="post" action="<?php echo "friend_edit.php"; ?>">
            <button name="edit_friend_list" >Edit</button>
        </form>
    </td>
</tr>
<tr>
    <td align=right style="font-size:18px;">Blocks</td>
    <td align=left>
        <form method="post" action="<?php echo "block.php"; ?>">
            <button name="block_list" >All</button>&nbsp;
        </form>
        <form method="post" action="<?php echo "block_edit.php"; ?>">
             <button name="eidt_block_list" >Edit</button>
        </form>
     </td>
</tr>

</table>


<body/>
<html/>
