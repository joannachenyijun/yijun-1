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

<?php
    include_once "browse_header.php";

    if (isset($_POST['all'])){
        $result = getFullContact($_SESSION['username']);
        // $row = mysql_fetch_array($result);
        if(mysql_num_rows($result) == 0){
            echo "You don't have a contact!" ;
        }
        else{
            // $result = getFullContact($_SESSION['username']);
            echo "<table border='0' cellpadding='10'>";
            echo "<tr><td style='font-size:22px; color:green;'>Full Contacts</td></tr>";
            echo "<tr align = 'left'><th>Username</th><th>First Name</th><th>Last Name</th><th>Gender</th><th>DOB</th></tr>";
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
                $nick = $row['nickname'];
                $user = $row['username'];
                // $email = $row['email'];
                $fname = $row['fname'];
                $lname = $row['lname'];
                $gender = $row['gender'];
                $dob = $row['dob'];

                // <td align='left'>&nbsp;<a href='my_channel.php?channel_name=$user' style='text-decoration: none; font-size:18px; color:blue'>$user</a></td>
                echo
                "<tr>
                    <td align='left'>&nbsp;<a href='my_channel.php?channel_name=$user' style='text-decoration: none; font-size:18px; color:blue'>$nick</a></td>
                    <td>$fname</td>
                    <td>$lname</td>
                    <td>$gender</td>
                    <td>$dob</td>
                </tr>";
            }
            echo "</table>";
            echo"<br/><br/>";
        }
    }
    else if (isset($_POST['recent'])){
        $result = getRecentContact($_SESSION['username']);
        if(mysql_num_rows($result) == 0){
            echo "You haven't contact with anyone yet!";
        }
        else{
            // $result = getRecentContact($_SESSION['username']);
            // $row = mysql_fetch_array($result);
            echo "<table border='0' cellpadding='10'>";
            echo "<tr><td style='font-size:22px; color:green;'>Recnet Contacts</td></tr>";
            echo "<tr align = 'left'><th>Date</th><th>Username</th><th>First Name</th><th>Last Name</th><th>Gender</th><th>DOB</th></tr>";
            while ($row = mysql_fetch_array($result)){
                $date = $row['date_time'];
                $nick = $row['nickname'];
                // $email = $row['email'];
                $user = $row['username'];
                $fname = $row['fname'];
                $lname = $row['lname'];
                $gender = $row['gender'];
                $dob = $row['dob'];
                echo
                "<tr>
                <td>$date</td>
                <td align='left'>&nbsp;<a href='my_channel.php?channel_name=$user' style='text-decoration: none; font-size:18px; color:blue'>$nick</a></td>
                <td>$fname</td>
                <td>$lname</td>
                <td>$gender</td>
                <td>$dob</td>
                </tr>";
            }
            echo "</table>";
        }
    }

?>

<br/><br/>
<form method="post", action =<?php echo "contact_edit.php" ?> >
    <button name="edit" style='color:white; background-color:red; cursor:pointer;'>&nbsp;Edit Contacts</button>
</form>


</body>
</html>
