<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	session_start();
	include_once "function.php";

    if (isset($_SESSION["username"])){
        $username=$_SESSION["username"];
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
<!-- <script type="text/javascript">

</script> -->

<script type="text/javascript">
function topicCheck() {
    if (document.getElementById('topicCheck').checked) {
        document.getElementById('topicYes').style.display = 'block';
    }
    else document.getElementById('topicYes').style.display = 'none';

}

function memberCheck() {
    if (document.getElementById('memberCheck').checked) {
        document.getElementById('memberYes').style.display = 'block';
    }
    else document.getElementById('memberYes').style.display = 'none';

}
</script>

<style>
body {
    /*background-image: url("http://2.bp.blogspot.com/-KR-c__igRdE/VIMLjvkHtBI/AAAAAAAABqM/dT-V8PZKiKc/s1600/tumblr_mmrarytp3C1s9sbvwo7_1280.jpg");*/
}

#rcorners2 {
    border-radius: 10px;
    border: 1px solid #73AD21;
    padding: 4px;
    width: 350px;
}

}
</style>

</head>

<body>


<?php

if (isset($_POST['delete_comment'])){
    $discussion_id = $_GET['discussion_id'];
    $query =  "DELETE FROM group_discussion WHERE discussion_id='$discussion_id' ";
    $result = mysql_query($query);
}

if (isset($_POST['submit'])){
    $topic_id = $_GET['topic_id'];
    $comment = $_POST['add_comment'];
    $user = $_SESSION['username'];

    insert_group_discussion($topic_id, $user, $comment);
}

if (isset($_POST['cancel'])){
    if (isset($_GET['group_id'])){
        $group_id = $_GET['group_id'];
        header("Location: my_groups.php?group_id=$group_id");
    }
    else {
        header("Location: my_groups.php?");
    }
}

?>

<?php
include_once "browse_header.php";
?>


<?php

$username = mysql_real_escape_string($username);
$topic_id = $_GET['topic_id'];

$query = "SELECT * FROM group_topics WHERE topic_id = '$topic_id' ";
$result = mysql_query($query);
$row=mysql_fetch_array($result, MYSQL_ASSOC);
$topic_name = $row['topic_name'];
$file_path = $row['file_path'];
$file_title = find_media_title($file_path);

echo"<Table border='0' cellpadding='10'>
        <tr><td style='font-size:20px;' align='right'> Topic name </td><td style='font-size:18px; color:blue;'>&nbsp;&nbsp;$topic_name</td></tr>
        <tr><td style='font-size:20px;' align='right'> About </td>
            <td style='font-size:18px; color:blue;'><a href='media.php?file_path=$file_path' style='text-decoration: none; font-size:18px; color:blue'>&nbsp;&nbsp;$file_title</a></td>
        </tr>
        <tr><td style='font-size:20px;' align='right'> Discusson Board </td></tr>";

$query = "SELECT * FROM group_discussion, group_topics WHERE group_discussion.topic_id = '$topic_id' AND group_discussion.topic_id=group_topics.topic_id ";
$result = mysql_query($query);
if (mysql_num_rows($result)==0){
    echo "<tr><td style='font-size:18px;color:grey'> &nbsp;&nbsp;&nbsp; None </td></tr>";
}
else {
    while ($row=mysql_fetch_array($result, MYSQL_ASSOC)){
        $user = $row['username'];
        $comment = $row['comment'];
        $date_time = $row['date_time'];
        $discussion_id = $row['discussion_id'];

?>
        <tr><td align='right' valign='bottom'>&nbsp;&nbsp;<a href='my_channel.php?channel_name=<?php echo $user;?>' style='text-decoration: none; font-size:18px; color:blue'><?php echo find_nickname($user);?></a></td>
            <td style='font-size:15px;' valign="bottom">&nbsp;&nbsp; <?php echo $date_time; ?></td>
            <td style='font-size:15px;' align='left' valign='bottom'><span id='rcorners2'><?php echo $comment; ?></span></td>
            <?php
                if ($user == $username){
                    echo "<td align='left' valign='bottom'>
                            <FORM action='topics.php?topic_id=$topic_id&discussion_id=$discussion_id' method='post'>
                                <button name='delete_comment' style='background-color:transparent; border-color:transparent; cursor: pointer;'>
                                    <img src='https://cdn2.iconfinder.com/data/icons/e-business-helper/240/627249-delete3-512.png' height='19'/>
                                </button>
                            </FORM>
                         </td>";
                }
            ?>
        </tr>
<?php
    }
}
echo "</Table>";

// echo "<p id='rcorners2'>Rounded corners!</p>";

// if (isset($insert_error)){
//     echo "<p style='font-size:18px; color:'blue';>$insert_error</p>";
// }
echo "<br/></br>";

echo "<table>
        <FORM action='topics.php?topic_id=$topic_id' method='post'>
        <tr>
            <td style='font-size:22px; color:blue;' valign='top'>&nbsp;&nbsp;&nbsp;".find_nickname($username)."</td>
            <td valign='bottom'>
                &nbsp;&nbsp;<textarea name='add_comment' rows='4' cols='50' style='font-size:14px;' placeholder='Add comments...'></textarea>
            </td>
        </tr>
        <tr>
            <td></td>
            <td valign='bottom'>
                &nbsp;&nbsp;
                <button name='submit' value='1' >Submit</button>
                <button name='cancel' value='0' >Cancel</button>
            </td>
        </tr>
        </FORM>
      </table>"

?>





</body>
</html>
