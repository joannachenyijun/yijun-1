<!-- Mange Account -->
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

echo "<table border='0' align = 'left' cellpadding='5'>";
$result = getMessage($_SESSION['username']);
// $row = mysql_fetch_array($result);
if(mysql_num_rows($result)==0){
    echo "<tr><th style='font-size:22px; color:green;'>Unread</th></tr>";
    echo "<tr><td style='color:grey;'>&nbsp;&nbsp;&nbsp;None</td></tr>" ;
}
else{
    echo "<tr><th style='font-size:22px; color:green;'>Unread</th></tr>";
    echo "<tr><td colspan='5'><hr/></td></tr>";
    $result = getMessage($_SESSION['username']);

    while ($row = mysql_fetch_array($result)){
        $username_from = $row['username_from'];
        $username_to = $row['username_to'];

        $comment = ' ';
        $message = $row['comment'];
        if($row['isRead'] == true){
            continue;
        }
        // echo strlen($row['comment']);
        if(strlen($row['comment']) >= 30){
            $comment = substr($row['comment'], 0, 30);
            // echo $comment;
        }
        else{
            $comment = $row['comment'];
        }

        if($row['username_from'] == $row['username_to']){
            $nickname_from = 'me';
        }
        else{
            // find display name
            $nickname_from = find_nickname($username_from);
        }

        $subject = $row['subject'];
        $time = $row['date_time'];

        echo
        "<tr>
        <td><a href = 'manage_message.php?username_to=$username_to&username_from=$username_from&subject=$subject&message=$message&time=$time' style='text-decoration: none;'> ".$nickname_from."</a></td>
        <td><a href = 'manage_message.php?username_to=$username_to&username_from=$username_from&subject=$subject&message=$message&time=$time' style='text-decoration: none;'> ".$subject."</a></td>
        <td><a href = 'manage_message.php?username_to=$username_to&username_from=$username_from&subject=$subject&message=$message&time=$time' style='text-decoration: none;'> ".$comment."</a></td>
        <td><a href = 'manage_message.php?username_to=$username_to&username_from=$username_from&subject=$subject&message=$message&time=$time' style='text-decoration: none;'> ".$time."</a></td>
        </tr>";
        // "<br/>";
    }
}



$result = getMessage($_SESSION['username']);
$row = mysql_fetch_array($result);
if(mysql_num_rows($result) == 0){
    echo "<tr><th style='font-size:22px; color:green;'>Read</th></tr>";
    echo "<tr><td style='color:grey;'>&nbsp;&nbsp;&nbsp;None</td></tr>" ;
}
else{
    echo "<tr><th style='font-size:22px; color:green;'>Read</th></tr>";
    echo "<tr><td colspan='5'><hr/></td></tr>";

    $result = getMessage($_SESSION['username']);

    while ($row = mysql_fetch_array($result)){
        $username_from = $row['username_from'];
        $username_to = $row['username_to'];

        $comment = ' ';
        if($row['isRead'] == false){
            continue;
        }
        if(strlen($row['comment']) >= 30){
            $message = $row['comment'];
            $comment = substr($row['comment'], 0, 30);
        }
        else{
            $message = $row['comment'];
            $comment = $row['comment'];
        }
        if($row['username_from'] == $row['username_to']){
            $nickname_from = 'me';
        }
        else{
            // $username = $row['username_from'];
            // find display name
            $nickname_from = find_nickname($username_from);
        }

        $subject = $row['subject'];
        $time = $row['date_time'];
        echo
        "<tr>
        <td><a href = 'manage_message.php?username_to=$username_to&username_from=$username_from&subject=$subject&message=$message&time=$time' style='text-decoration: none;'> ".$nickname_from."</a></td>
        <td><a href = 'manage_message.php?username_to=$username_to&username_from=$username_from&subject=$subject&message=$message&time=$time' style='text-decoration: none;'> ".$row['subject']."</a></td>
        <td><a href = 'manage_message.php?username_to=$username_to&username_from=$username_from&subject=$subject&message=$message&time=$time' style='text-decoration: none;'> ".$comment."</a></td>
        <td><a href = 'manage_message.php?username_to=$username_to&username_from=$username_from&subject=$subject&message=$message&time=$time' style='text-decoration: none;'> ".$row['date_time']."</a></td>
        </tr>";
        // "<br/>";
    }
}
echo "</table>";
?>


<body/>
<html/>
