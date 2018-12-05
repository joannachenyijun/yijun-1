<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	session_start();
	include_once "function.php";
    include_once "modules/account.php";
    include_once "browse_header.php"
?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Media browse</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />

<SCRIPT TYPE="text/javascript">
    function popup(mylink, windowname) {
        if (! window.focus)return true;
        var href;
        if (typeof(mylink) == 'string') href=mylink;
        else href=mylink.href;
        window.open(href, windowname, 'width=400,height=600,scrollbars=yes');
        return false;
    }
</SCRIPT>

</head>


<body>

<?php
// session_start();
// include_once "function.php";
// include_once "modules/account.php";
// include_once "browse_header.php";



$result = getMessage_sent($_SESSION['username']);
// $row = mysql_fetch_array($result);
if(mysql_num_rows($result) == 0){
    echo "No email has been sent." ;
    echo"<br/><br/>";
}
else{
    $result = getMessage_sent($_SESSION['username']);
    echo "<table border='0' align = 'left' cellpadding='5'>";
    while ($row = mysql_fetch_array($result)){
        $username_to = $row['username_to'];
        $username_from = $row['username_from'];

        $comment = ' ';
        $message = $row['comment'];
        if(strlen($row['comment']) >= 30){
            $comment = substr($row['comment'], 0, 30);
        }
        else{
            $comment = $row['comment'];
        }
        if($row['username_from'] == $row['username_to']){
            // $username = 'me';
            $nickname_to = 'me';
        }
        else{
            $username_to = $row['username_to'];
            $nickname_to = find_nickname($username_to);
        }

        $subject = $row['subject'];
        $time = $row['date_time'];
        echo
        "<tr>
        <td><a href = 'manage_message.php?username_to=$username_to&username_from=$username_from&subject=$subject&message=$message&time=$time' style='text-decoration: none;'> To: &nbsp ".$nickname_to."</a></td>
        <td><a href = 'manage_message.php?username_to=$username_to&username_from=$username_from&subject=$subject&message=$message&time=$time' style='text-decoration: none;'> ".$row['subject']."</a></td>
        <td><a href = 'manage_message.php?username_to=$username_to&username_from=$username_from&subject=$subject&message=$message&time=$time' style='text-decoration: none;'> ".$comment."</a></td>
        <td><a href = 'manage_message.php?username_to=$username_to&username_from=$username_from&subject=$subject&message=$message&time=$time' style='text-decoration: none;'> ".$row['date_time']."</a></td>
        </tr>";
        // "<br/>";
    }
    // echo"<br>";
    echo "</table>";
    // echo"<br/><br/>";
}

?>




<body/>
<html/>
