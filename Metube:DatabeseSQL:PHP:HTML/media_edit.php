<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	session_start();
	include_once "function.php";
    // include_once "browse_header.php";
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Media</title>

<script type="text/javascript">
function yesnoCheck() {
    if (document.getElementById('yesCheck').checked) {
        document.getElementById('ifYes').style.display = 'block';
    }
    else document.getElementById('ifYes').style.display = 'none';
}


</script>


<style>

.para{
    font-size: 22px;
}


</style>



</head>

<body>
<?php

if (isset($_SESSION["username"])){
    $username = $_SESSION["username"];
    $ip=$_SERVER['REMOTE_ADDR'];
}
else{
    $file_path = $_GET['file_path'];
    header("Location: login.php?file_path=$file_path");
}


if (isset($_POST["edit_submit"])){
    $file_path = $_GET['file_path'];
    $title = mysql_real_escape_string($_POST["title"]);
    $category = $_POST["category"];
    $channel_name = $_POST["channel"];
    $description_temp= $_POST["description"];
    $description = mysql_real_escape_string($description_temp);
    $tags = $_POST["tags"];
    $share_type = $_POST["yesno"];

    if(isset($_POST['allow_comment'])){
        $allow_comment = true;
    }
    else{
        $allow_comment = false;
    }

    if(isset($_POST['allow_rate'])){
        $allow_rate = true;
    }
    else{
        $allow_rate = false;
    }

    // UPDATE MEDIA INFO
    $query = "UPDATE media SET title='$title', category='$category', description='$description', tags='$tags', share_type='$share_type', allow_rate='$allow_rate', allow_comment='$allow_comment', date_time=NOW()
              WHERE file_path = '$file_path' ";
    $queryresult = mysql_query($query)
         or die("Insert into Media error in media_edit.php " .mysql_error());

    if($share_type == 0){
        $user_list = clean_str_to_arr($_POST["share_users"]);
        for ($i=0; $i<sizeof($user_list); $i++){
            $user_list[$i] = find_username($user_list[$i]);
        }
        // print_r($user_list);

        if (isset($_POST["share_groups"])){
            $group_name = $_POST["share_groups"];
            $group_name = mysql_real_escape_string($group_name);
        }
        if (isset($_POST['group_master'])){
            $group_master = find_username($_POST['group_master']);
            $group_master = mysql_real_escape_string($group_master);
        }

        if (empty($group_name) and empty($_POST['group_master'])){
            // do nothing
        }
        else if (empty($group_name) xor empty($_POST['group_master'])){
            $edit_result = "Group name/master is required! ";
        }
        else {
            $check = "SELECT * FROM groups, group_users WHERE groups.group_name='$group_name' AND groups.master='$group_master'
                      AND groups.group_id = group_users.group_id AND group_users.username = '$username' ";
            $check_result = mysql_query($check);
            if (mysql_num_rows($check_result) == 0){
                $edit_result = "No matched group is found! ";
            }
            else{
                $row = mysql_fetch_array($check_result, MYSQL_ASSOC);
                $group_id = $row['group_id'];
                $user_list = get_full_shared_users($user_list, $group_id);
            }
        }

        for ($i=0; $i<sizeof($user_list); $i++){
            $to = $user_list[$i];
            $to_nickname = find_nickname($to);
            $check_block = checkBlock($username, $to_nickname);
            $check_blockby = checkBlockBy($to_nickname, $username);

            if ($check_block == 1 or $check_blockby == 1){
                $edit_result = "You can not share this video with $to_nickname! ";
                unset($user_list[$i]);
            }
        }

        if (sizeof($user_list) > 0){
            share_media($username, $user_list, $file_path);
            // SEND MESSAGE TO NOTIFY FRIENDS
            foreach ($user_list as $to){
                $nickname = find_nickname($username);
                $subject = "$nickname shares a vedio with you !";
                $link = "http://webapp.cs.clemson.edu/~mlu87/metube_G5/media.php?file_path=$file_path";
                $message = "The user $nickname shares a vedio with you. Please check on this link $link " ;
                new_message($username, $to, $subject, $message);
            }
            $edit_result = "Successfully shared! ";
        }
    }

    // UPDATE keywords
    update_media_keywords($file_path, $title, $tags, $description, $category);
    $edit_succ = 1;

    // UPDATE CHANNEL
    if ($channel_name == "DEFAULT"){
        // do nothing
    }else {
        update_channel($channel_name, $file_path, $_SESSION['username']);
    }
}
else if (isset($_POST["cancel"])){
    $file_path = $_GET['file_path'];
    header("Location: my_media.php?file_path=$file_path");
}


include_once "browse_header.php";


// if(isset($_POST["edit"])) {
if(isset($_GET["file_path"])) {

    // $file_path = $_POST["edit"];
    $file_path = $_GET["file_path"];

	$query = "SELECT * FROM media WHERE file_path='$file_path'";
	$result = mysql_query( $query );
	$result_row = mysql_fetch_array($result, MYSQL_ASSOC);
    $title = $result_row['title'];
    $type = $result_row['type'];
}
?>

<p style="font-size:20px; color:blue;">Edit the media info</p>

<?php
if (isset($edit_succ)){
    echo "<p style='font-size:18px;'>The media info has been updated! </p>";
}

if(isset($edit_result)){
    echo "<p style='font-size:22px; color:red;'>$edit_result </p>";
}
?>

<table>
    <tr>
        <td width='400px' valign='top'>
            <div>
            <a href='media.php?file_path=<?php echo $file_path; ?>'><video width='320px'><source src =<?php echo $file_path; ?> type =<?php echo $type; ?> ></video></a>
            </div>
        </td>
    </tr>
</table>
<br/>

<FORM action="media_edit.php?file_path=<?php echo $file_path; ?>" method ="post" id="media_edit" name="media_edit">
<table border="0">
    <tr>
        <td ><textarea rows="1" cols="50" name="title" form="media_edit" placeholder="Title"></textarea></td>
        <td >Category&nbsp;&nbsp;
            <select name="category">
                <option value="music">Music</option>
                <option value="gaming">Gaming</option>
                <option value="movies">Movies</option>
                <option value="tv">TV shows</option>
                <option value="news">News</option>
                <option value="pets">Pets/Animals</option>
                <option value="comedy">Comedy</option>
            </select><br />
        </td>
    </tr>
    <tr>
        <td >
            <textarea rows="4" cols="50" name="tags" form="media_edit" placeholder="Tags"></textarea>
        </td>
        <td >Channel&nbsp;&nbsp;
            <select name="channel">
                <option value="DEFAULT"></option>
                <option value="Oriental_Theatre">Oriental Theatre</option>
                <option value="POP_Music">POP Music</option>
                <option value="Today">Today</option>
                <option value="The_Youth">The Youth</option>
                <option value="Vintage">Vintage</option>
            </select><br />
        </td>
    </tr>
    <tr>
        <!-- <td valign = "top"> Describe the media<input type="text" name="message" style="width: 300px; height: 200px; padding-top: 8px;"></td> -->
        <td>
            <textarea rows="11" cols="50" name="description" form="media_edit" placeholder="Description"></textarea>
        </td>
        <td >
            Add to playlist<br/>
            <textarea rows="1" cols="20" name="search_playlist" form="media_edit" placeholder="&#43;playlist name"></textarea>
            <br/><br/>

            Public <input type="radio" onclick="javascript:yesnoCheck();" name="yesno" id="noCheck" value=1>
            Private <input type="radio" onclick="javascript:yesnoCheck();" name="yesno" id="yesCheck" value=0>
            <div id="ifYes" style="display:none">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Only you can view <br/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea rows="1" cols="30" name="share_users" form="media_edit" placeholder="&#43;Share with contacts"></textarea><br/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea rows="1" cols="15" name="share_groups" id="share_groups" form="media_edit" placeholder="&#43;A group"></textarea>
                &nbsp;<textarea rows="1" cols="20" name="group_master" id="group_master" form="media_edit" placeholder="&#43;The group master"></textarea><br/>
            </div>
        </td>
    </tr>
    <tr>
        <td> <input type="checkbox" name="allow_comment" value="1" checked> Allow Comments ? </td>
    </tr>
    <tr>
        <td> <input type="checkbox" name="allow_rate" value="1" checked> Allow Users Rate This Video ? </td>
    </tr>
</table>

<p style="margin:0; padding:0">
<input value="Edit" name="edit_submit" type="submit" /><input value="Cancel" name="cancel" type="submit" />
</p>


</form>




<?php
  if(isset($media_error))
   {  echo "<div id='passwd_result'>".$media_error."</div>";}
?>

</body>
</html>
