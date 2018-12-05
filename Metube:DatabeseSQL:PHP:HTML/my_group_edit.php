<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	session_start();
	include_once "function.php";

    if (isset($_SESSION["username"])){
        $username=$_SESSION["username"];
        $nickname=$_SESSION['nickname'];
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
    /*background-image: url("http://www.uibrush.com/wp-content/uploads/2015/05/Sativa.jpg?a85c8e");*/
}
</style>

</head>

<body>


<?php

if (isset($_POST['add_topic'])){
    $topic_name = $_POST['topic_name'];
    $file_url = $_POST['file_url'];

    if (!empty($topic_name) && !empty($file_url)){
        $group_id = $_GET['group_id'];

        $file_url = multiexplode(array("?","&", "="),$file_url)[2];

        if(!file_exists($file_url)){
  	  	    $insert_error = "The file doesn't exist in MeTube! ";
  	    }
        else {
            $check_topic_exist = check_topic_exist($username, $group_id, $topic_name, $file_url);

            if ($check_topic_exist == 0){
                insert_group_topics($username, $group_id, $topic_name, $file_url);
            }
            else {
                $insert_error = 'The topic already exists! ';
            }
        }
    }
    else {
        if (empty($topic_name)){
            $insert_error_1 = "The topic name is required! ";
        }
        if (empty($file_url)){
            $insert_error_2 = "The file url is required! ";
        }
        $insert_error = $insert_error_1.$insert_error_2;
    }
}


if (isset($_POST['add_member'])){
    $group_id = $_GET['group_id'];

    $member_name = $_POST['member_name'];
    $member_arr = clean_str_to_arr($member_name);

    $insert_error = "The user(s) ";
    $insert_error_1 = " ";
    $insert_error_2 = " ";
    $insert_error_3 = " ";

    $tag = 0;
    foreach ($member_arr as $member){
        $check_user_valid = check_user_valid($member);
        $check_user_exist = check_user_exist(find_username($member), $group_id);
        // $check_user_block = check_user_block($username, $member);
        // $check_user_blockby = check_user_block($member, $username);
        $check_user_block = checkBlock($username, $member);
        $check_user_blockby = checkBlockBy($member, $username);

        if ($check_user_valid == 0){
            $tag = 1;
            $insert_error = $insert_error."&$member ";
        }
        else if ($check_user_exist == 1){
            $insert_error_1 = "The user $member is already in this group! " ;
        }
        else if ($check_user_block == 1) {
            $insert_error_2 = "The user $member is in your blocklist! " ;
        }
        else if ($check_user_blockby == 1) {
            $insert_error_3 = "You are blocked by the user $member! " ;
        }
        else {
            insert_group_users($group_id, find_username($member));
        }
    }
    if ($tag == 1){
        $insert_error = $insert_error."can not be found!";
    }
    else {
        $insert_error=" ";
    }
    $insert_error = $insert_error.$insert_error_1.$insert_error_2.$insert_error_3;
}


if (isset($_POST['leave_and_delete'])){
    $group_id = $_GET['group_id'];
    delete_group($group_id);
    header("Location: my_groups.php");
}


if (isset($_POST['remove_user'])){
    $group_id = $_GET['group_id'];
    $user = $_POST['remove_user'];
    delete_group_user($group_id, $user);
}


if (isset($_POST['remove_topic'])){
    $topic_id = $_POST['remove_topic'];
    delete_group_topic($topic_id);
}

?>


<?php
include_once "browse_header.php";
?>



<?php

$username = mysql_real_escape_string($username);
$group_id = $_GET['group_id'];

$query = "SELECT * FROM groups WHERE group_id = '$group_id' ";
$result = mysql_query($query);
$row = mysql_fetch_array($result, MYSQL_ASSOC);
$master = $row['master'];
$group_name = $row['group_name'];
// echo $group_name;

$query = "SELECT * FROM groups, group_users WHERE groups.group_id = group_users.group_id AND groups.group_id = '$group_id' ";
$result = mysql_query($query);

$members = array();
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
    if ($row['username'] != $master){
        array_push($members, $row['username']);
    }
}
$members = array_unique($members);

echo "<table cellpadding='5'><tr><td align='right' style='font-size:20px'>Group name &nbsp;&nbsp;</td> <td align='left' style='font-size:20px; color:blue;'>&nbsp;$group_name</td></tr>";
echo "<tr><td align='right' style='font-size:20px'> Master &nbsp;&nbsp;</td> <td align='left'>&nbsp;<a href='my_channel.php?channel_name=$master' style='text-decoration: none; font-size:20px; color:blue'>".find_nickname($master)."</a></td></tr>";
if (empty($members)){
    echo "<tr><td align='right' style='font-size:20px'> Members &nbsp;&nbsp;</td> <td align='left' style='font-size:16px; color:grey;'>&nbsp;None</td></tr>";
}
else {
    echo "<tr><td align='right' style='font-size:20px'> Members &nbsp;&nbsp;</td> <td align='left'>&nbsp;<a href='my_channel.php?channel_name=$members[0]' style='text-decoration: none; font-size:18px; color:blue'>".find_nickname($members[0])."</a></td>
              <td align='left' valign='top'><FORM action='my_group_edit.php?group_id=$group_id' method='post'>
                    <button name='remove_user' value='$members[0]' style='background-color:transparent;border-color:transparent;cursor:pointer;width:16px;height:16px;'>
                        <img src='http://findicons.com/files/icons/573/must_have/256/delete.png' style='background-color:transparent;border-color:transparent;cursor:pointer;width:16px;height:16px;'>
                    </button>
                  </FORM>
              </td>
          </tr>";
}
for($i=1; $i<sizeof($members); $i++){
    $user = $members[$i];
    echo "<tr><td align='right' style='font-size:20px'></td> <td align='left'>&nbsp;<a href='my_channel.php?channel_name=$user' style='text-decoration: none; font-size:18px; color:blue'>".find_nickname($user)."</a></td>
              <td align='left' valign='top'><FORM action='my_group_edit.php?group_id=$group_id' method='post'>
                <button name='remove_user' value='$user' style='background-color:transparent;border-color:transparent;cursor:pointer;width:16px;height:16px;'>
                    <img src='http://findicons.com/files/icons/573/must_have/256/delete.png' style='background-color:transparent;border-color:transparent;cursor:pointer;width:16px;height:16px;'>
                </button>
                </FORM>
              </td>
          </tr>";
}

// display topics
$query = "SELECT * FROM group_topics WHERE group_id = '$group_id' ORDER BY date_time ";
$result = mysql_query($query);

echo "<tr><td align='right' style='font-size:20px'> Topics &nbsp;&nbsp;";
if (mysql_num_rows($result) == 0){
    echo "<td valign='bottom' style='font-size:16px; color:grey;'> None </td>";
}
else {
    $idx = 0;
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $topic_id = $row['topic_id'];
        $topic_name = $row['topic_name'];
        $file_path = $row['file_path'];
        $file_title = find_media_title($file_path);
        if ($idx < 1){
            echo "  <td align='left'>&nbsp;<a href='topics.php?topic_id=$topic_id' style='text-decoration: none; font-size:18px; color:blue'>$topic_name</a></td>
                    <td align='left'>&nbsp;<a href='media.php?file_path=$file_path' style='text-decoration: none; font-size:18px; color:blue'>$file_title</a></td>
                    <td align='left' valign='top'><FORM action='my_group_edit.php?group_id=$group_id' method='post'>
                      <button name='remove_topic' value='$topic_id' style='background-color:transparent;border-color:transparent;cursor:pointer;width:16px;height:16px;'>
                          <img src='http://findicons.com/files/icons/573/must_have/256/delete.png' style='background-color:transparent;border-color:transparent;cursor:pointer;width:16px;height:16px;'>
                      </button>
                      </FORM>
                    </td>
                  </tr>";
            $idx += 1;
        }
        else {
            echo "<tr>
                    <td align='right' style='font-size:20px'></td>
                    <td align='left'>&nbsp;<a href='topics.php?topic_id=$topic_id' style='text-decoration: none; font-size:18px; color:blue'>$topic_name</a></td>
                    <td align='left'>&nbsp;<a href='media.php?file_path=$file_path' style='text-decoration: none; font-size:18px; color:blue'>$file_title</a></td>
                    <td align='left' valign='top'><FORM action='my_group_edit.php?group_id=$group_id' method='post'>
                      <button name='remove_topic' value='$topic_id' style='background-color:transparent;border-color:transparent;cursor:pointer;width:16px;height:16px;'>
                          <img src='http://findicons.com/files/icons/573/must_have/256/delete.png' style='background-color:transparent;border-color:transparent;cursor:pointer;width:16px;height:16px;'>
                      </button>
                      </FORM>
                    </td>
                  </tr>";
        }
    }
}

echo "</table>";
echo "<br/><br/>";
?>

<TABLE>
    <tr><td style="font-size:18px;" >
        &nbsp;&nbsp;&nbsp;Add topics <input type="radio" onclick="javascript:topicCheck();" name="topicCheck" id="topicCheck" value=1>
    </td></tr>
    <tr><td style="font-size:18px;">
        <FORM action='my_group_edit.php?group_id=<?php echo $group_id; ?>' method='post' name='edit_topic' id='edit_topic'>
        <div id="topicYes" style="display:none">
            <textarea rows="2" cols="30" name="topic_name" id='topic_name' form="edit_topic" placeholder="&#43; Topic name" style="font-size:14px;"></textarea><br/>
            <textarea rows="2" cols="20" name="file_url" id='file_url' form="edit_topic" placeholder="&#43; Media url" style="font-size:14px;"></textarea><br/>
            <button name="add_topic" id="add_topic" type="submit" value="add_topic">Add</button><button name="cancel" type="submit" value="Cancel">Cancel</button>
        </div>
        </FORM>
    </td></tr>
</TABLE>

<TABLE>
    <tr><td style="font-size:18px;" >
        &nbsp;&nbsp;&nbsp;Add members <input type="radio" onclick="javascript:memberCheck();" name="memberCheck" id="memberCheck" value=1>
    </td></tr>
    <tr><td style="font-size:18px;">
        <FORM action='my_group_edit.php?group_id=<?php echo $group_id; ?>' method='post' name='edit_member' id='edit_member'>
        <div id="memberYes" style="display:none">
            <textarea rows="3" cols="30" name="member_name" form="edit_member" placeholder="&#43; Username" style="font-size:14px;"></textarea><br/>
            <button name="add_member" id="add_member" type="submit" value="add_member">Add</button><button name="cancel" type="submit" value="Cancel">Cancel</button>
        </div>
        </FORM>
    </td></tr>
</TABLE>
<br/></br/>

<FORM action='my_group_edit.php?group_id=<?php echo $group_id; ?>' method='post'>
     &nbsp; &nbsp; <button name='leave_and_delete' type='submit' value=1 style='font-size:16px; color:black; background-color:red;'>Leave And Delete</button>
</FORM>


<?php

if (isset($insert_error)){
    echo "<p style='font-size:18px; color:red;'>$insert_error</p>";
}

 ?>


</body>
</html>
