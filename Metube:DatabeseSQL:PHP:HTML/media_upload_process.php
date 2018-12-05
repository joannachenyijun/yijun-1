<?php
session_start();
include_once "function.php";

/******************************************************
*
* upload document from user
*
*******************************************************/

if (isset($_SESSION["username"])){
    $username=$_SESSION["username"];
    $nickname=$_SESSION['nickname'];
}
else{
    header('Location: index_header.php?unknowuser=1');
}

if(isset($_POST['cancel'])){
    header("Location: browse.php");
}


//Create Directory if doesn't exist
if(!file_exists('uploads/'))
	mkdir('uploads/', 0757);
$dirfile = 'uploads/'.$username.'/';
if(!file_exists($dirfile))
	mkdir($dirfile,0755);
	chmod( $dirfile,0755);
    if ($_FILES["file"]["type"] != "video/mp4"){
        $result = 14;
    }
	else if($_FILES["file"]["error"] > 0 )
	{ 	$result=$_FILES["file"]["error"];} //error from 1-4
	else
	{
		$upfile = $dirfile.urlencode($_FILES["file"]["name"]);

	  if(file_exists($upfile))
	  {
	  	$result="5"; //The file has been uploaded.
	  }
	  else{
			if(is_uploaded_file($_FILES["file"]["tmp_name"]))
			{
				if(!move_uploaded_file($_FILES["file"]["tmp_name"],$upfile))
				{
					$result="6"; //Failed to move file from temporary directory
				}
				else /*Successfully upload file*/
				{
					//insert into media table
                    $title = mysql_real_escape_string($_POST["title"]);
                    $category = $_POST["category"];
                    $channel_name = $_POST['channel'];
                    $description_temp= $_POST["description"];
                    $description = mysql_real_escape_string($description_temp);
                    $tags = $_POST["tags"];
                    $share_type = $_POST["yesno"];
                    // $size = $_POST["MAX_FILE_SIZE"];
                    $size = $_FILES["file"]["size"];
                    // echo gettype($size);

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


                    // $query_set = "SET DEFINE OFF";
                    $query = "INSERT INTO media(filename, username, type, file_path, title, category, description, date_time, allow_rate, allow_comment, share_type, tags, size)
                              VALUES ('".urlencode($_FILES["file"]["name"])."', '$username', '".$_FILES["file"]["type"]."', '$upfile', '$title', '$category', '$description', NOW(), '$allow_rate', '$allow_comment', '$share_type', '$tags', '$size')";
                    $queryresult = mysql_query($query)
                        or die("Insert into Media error in media_upload_process.php " .mysql_error());

					$result="0";
					chmod($upfile, 0644);

                    // media share
                    $user_list = clean_str_to_arr($_POST["share_users"]);
                    for ($i=0; $i<sizeof($user_list); $i++){
                        $user_list[$i] = find_username($user_list[$i]);
                    }

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
                        $result = 11;
                    }
                    else {
                        $check = "SELECT * FROM groups, group_users WHERE groups.group_name='$group_name' AND groups.master='$group_master'
                                  AND groups.group_id = group_users.group_id AND group_users.username = '$username' ";

                        $check_result = mysql_query($check);
                        if (mysql_num_rows($check_result) == 0){
                            $result = 15;
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
                            $result = 12;
                            unset($user_list[$i]);
                        }
                    }

                    if (sizeof($user_list) > 0){
                        share_media($username, $user_list, $upfile);
                        // SEND MESSAGE TO NOTIFY FRIENDS
                        foreach ($user_list as $to){
                            $nickname = find_nickname($username);
                            $subject = "$nickname shares a vedio with you !";
                            $link = "http://webapp.cs.clemson.edu/~mlu87/metube_G5/media.php?file_path=$upfile";
                            $message = "The user $nickname shares a vedio with you. Please check on this link $link " ;
                            new_message($username, $to, $subject, $message);
                        }
                        $result=13;
                    }

                    add_media_keywords($upfile, $title, $tags, $description, $category);

                    // ADD THE FILE INTO THE CHANNEL
                    if ($channel_name == "DEFAULT"){
                        // do nothing
                    }
                    else {
                        insert_channel($channel_name, $upfile);
                    }

                    // ADD THE FILE INTO THE USER CHANNEL
                    insert_channel($username, $upfile);


                    // ADD TO PLAYLIST
                    if (!empty($_POST['addPlaylist'])){
                        $file_path = $upfile;
                        $playlist_name = $_POST['addPlaylist'];
                        $playlist_arr = clean_str_to_arr($playlist_name);

                        foreach ($playlist_arr as $playlist){
                            $check = check_playlist_exist($playlist, $_SESSION['username']);

                            if ($check == 0){
                                // array_push($media_result, "The playlist ".$playlist." has been created!");
                                $result_playlist="8";
                                insert_playlist($playlist, $_SESSION['username'], $file_path);
                            }
                            else {
                                $check_media = check_media_in_playlist($playlist, $_SESSION['username'], $file_path);
                                if ($check_media == 0){
                                    // array_push($media_result, "Added to the playlist: ".$playlist);
                                    $result_playlist="9";
                                    insert_playlist($playlist, $_SESSION['username'], $file_path);
                                }
                                else if ($check_media == 1){
                                    $result_playlist="10";
                                    // array_push($media_result, "This media is already in the playlist: ".$playlist);
                                }
                                else if ($check_media == 2){
                                    $result_playlist="9";
                                    // array_push($media_result, "Added to the playlist: ".$playlist);
                                    remove_playlist($playlist, $_SESSION['username']);
                                    insert_playlist($playlist, $_SESSION['username'], $file_path);
                                }
                            }
                        }
                    }
				}
			}
			else
			{
					$result="7"; //upload file failed
			}
		}
	}
    // echo $result_playlist;

	//You can process the error code of the $result here.
    // echo $result;
?>


<meta http-equiv="refresh" content="0;url=browse.php?result=<?php echo $result;?>">
