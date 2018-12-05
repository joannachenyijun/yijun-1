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
<!-- <script src="Scripts/AC_ActiveX.js" type="text/javascript"></script>
<script src="Scripts/AC_RunActiveContent.js" type="text/javascript"></script> -->

<script type="text/javascript">
function shareCheck() {
    if (document.getElementById('shareCheck').checked) {
        document.getElementById('shareYes').style.display = 'block';
    }
    else document.getElementById('shareYes').style.display = 'none';

}

function plCheck() {
    if (document.getElementById('plCheck').checked) {
        document.getElementById('plYes').style.display = 'block';
    }
    else document.getElementById('plYes').style.display = 'none';

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
    $username_view = $_SESSION["username"];
    $nickname_view = $_SESSION['nickname'];
    $ip=$_SERVER['REMOTE_ADDR'];
}
else{
    $username_view = NULL;
    $nickname_view = NULL;
    $ip=$_SERVER['REMOTE_ADDR'];
    // echo $username_view;
}

if (isset($_POST['home'])){
    $file_path = $_GET['file_path'];
    header("Location: login.php?file_path=$file_path");
}


if (isset($_POST["submit_comment"])){
    if (!isset($_SESSION["username"])){
        $file_path = $_GET['file_path'];
        header("Location: login.php?file_path=$file_path&index='Comment'");
    }
    else {
        $content = $_POST["comment"];
        $file_path = $_GET['file_path'];
        if (empty($content)){
            $insert_error = "Comment can not be empty!";
        }
        else {
            insert_comments($file_path, $username_view, $content);
        }
    }
}


if (isset($_POST['share'])){

    if (!isset($_SESSION['username'])){
        $file_path = $_GET['file_path'];
        header("Location: login.php?file_path=$file_path&index='Share'");
    }

    if (!isset($_GET['file_path'])){
        $media_error = "No such file!";
    }
    else {
        $file_path = $_GET['file_path'];

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
            $edit_result = "Group name/master is required! ";
        }
        else {
            $check = "SELECT * FROM groups, group_users WHERE groups.group_name='$group_name' AND groups.master='$group_master'
                      AND groups.group_id = group_users.group_id AND group_users.username = '$username_view' ";

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

        // CHECK SHARE BLOCK
        for ($i=0; $i<sizeof($user_list); $i++){
            $to = $user_list[$i];
            $to_nickname = find_nickname($to);
            $check_block = checkBlock($username_view, $to_nickname);
            $check_blockby = checkBlockBy($to_nickname, $username_view);

            if ($check_block == 1 or $check_blockby == 1){
                $edit_result = "You can not share this video with $to_nickname! ";
                unset($user_list[$i]);
            }
        }

        if (sizeof($user_list) > 0){
            share_media($username_view, $user_list, $file_path);

            // SEND MESSAGE TO NOTIFY FRIENDS
            foreach ($user_list as $to){
                $nickname_view = find_nickname($username_view);
                $subject = "$nickname_view shares a vedio with you !";
                $link = "http://webapp.cs.clemson.edu/~mlu87/metube_G5/media.php?file_path=$file_path";
                $message = "The user $nickname_view shares a vedio with you. Please check on this link $link " ;
                new_message($username_view, $to, $subject, $message);
            }
            $edit_result = "Successfully shared! ";
        }
    }
}


// subscribe channel
if (isset($_POST['subscribe'])){
    if (!isset($_SESSION['username'])){
        $file_path = $_GET['file_path'];
        header("Location: login.php?file_path=$file_path&index='Subscribe'");
    }
    else {
        $channel_name = $_GET['channel'];
        insert_subscribe($channel_name, $_SESSION['username']);
        $channel_nickname = clean_str(find_nickname($channel_name));
        $sub = "You have successfully subscribed the channel:".$channel_nickname."!";
    }
}

if (isset($_POST['unsubscribe'])){
    $file_path = $_POST['unsubscribe'];
    $channel_name = $_GET['channel'];
    unsubscribe_channel($channel_name, $_SESSION['username']);
}


// subscribe user
if (isset($_POST['follow'])){
    if (!isset($_SESSION['username'])){
        $file_path = $_GET['file_path'];
        header("Location: login.php?file_path=$file_path&index='Follow'");
    }
    else {
        $channel_name = $_GET['channel'];
        insert_subscribe($channel_name, $_SESSION['username']);
        $edit_result = "You have successfully subscribed the channel:".find_nickname($channel_name)."!";
    }
}

if (isset($_POST['unfollow'])){
    $file_path = $_POST['unfollow'];
    $channel_name = $_GET['channel'];
    unsubscribe_channel($channel_name, $_SESSION['username']);
}


// ADD TO PLAYLIST
if (isset($_POST['addPlaylist'])){
    if (!isset($_SESSION['username'])){
        $file_path = $_GET['file_path'];
        header("Location: login.php?file_path=$file_path&index='Add to playlist'");
    }
    else {
        $media_error = array();
        $media_result = array();

        $file_path = $_GET['file_path'];
        $playlist_name = $_POST['playlist'];
        $playlist_arr = clean_str_to_arr($playlist_name);

        foreach ($playlist_arr as $playlist){
            $check = check_playlist_exist($playlist, $_SESSION['username']);

            if ($check == 0){
                array_push($media_result, "The playlist ".$playlist." has been created!");
                insert_playlist($playlist, $_SESSION['username'], $file_path);
            }
            else {
                $check_media = check_media_in_playlist($playlist, $_SESSION['username'], $file_path);
                if ($check_media == 0){
                    array_push($media_result, "Added to the playlist: ".$playlist);
                    insert_playlist($playlist, $_SESSION['username'], $file_path);
                }
                else if ($check_media == 1){
                    array_push($media_result, "This media is already in the playlist: ".$playlist);
                }
                else if ($check_media == 2){
                    array_push($media_result, "Added to the playlist: ".$playlist);
                    remove_playlist($playlist, $_SESSION['username']);
                    insert_playlist($playlist, $_SESSION['username'], $file_path);
                }
            }
        }
    }
}


if (isset($_POST['favorites'])){
    if (!isset($_SESSION['username'])){
        $file_path = $_GET['file_path'];
        header("Location: login.php?file_path=$file_path&index='Give A Like'");
    }
    else {
        $file_path = $_GET['file_path'];
        insert_favorites($file_path, $_SESSION['username']);
    }
}


if (isset($_POST['dislike'])){
    $file_path = $_POST['dislike'];
    remove_from_favorites($_SESSION['username'], $file_path);
}



if (isset($_POST['rating'])){
    $file_path = $_GET['file_path'];
    if (!isset($_SESSION["username"])){
        header("Location: login.php?file_path=$file_path&index='Rating'");
    }
    else {
        $score = $_POST['score'];
        if ($score != "DEFAULT"){
            $check_rating = check_user_rating($_SESSION['username'], $file_path);

            if ($check_rating == 0){
                insert_rating($score, $_SESSION['username'], $file_path);
            }
            else {
                update_rating($score, $_SESSION['username'], $file_path);
            }
        }
    }
}

if (isset($_POST['remove_comment'])){
    $comment_id = $_POST['remove_comment'];
    delete_media_comment($comment_id);
}


include_once "browse_header.php";



if(isset($edit_result)){
    echo "<p style='font-size:22px; color:red;'>$edit_result </p>";
}



if(!empty($media_result)){
    foreach ($media_result as $r){
        echo "<p style='font-size:20px; color:green;'>$r</p>";
        // echo "<br/><br/><br/>";
    }
}


if(isset($_GET['file_path'])) {

	$query = "SELECT * FROM media WHERE file_path='".$_GET['file_path']."'";
	$result = mysql_query( $query );
	$result_row = mysql_fetch_array($result, MYSQL_ASSOC);

    $file_path = $result_row['file_path'];
    $filename = $result_row['filename'];
    $title = $result_row['title'];
    $category =$result_row['category'];
    $type = $result_row['type'];
    $description = $result_row['description'];

    $allow_comment = $result_row['allow_comment'];
    $allow_rate = $result_row['allow_rate'];
    $share_type = $result_row['share_type'];

    $view_count = $result_row["view_count"];
    $user_upload = $result_row["username"];
    $date_time = $result_row["date_time"];


    // CHECK VIEW BLOCK
    $check = check_view_block($file_path, $username_view, $user_upload, $share_type);
    if ($check == 0){
        echo "<p style='font-size:22px; color:green;'>You are not authorized to view this media. </p>";
    }
    else{
        if($type != "video/mp4"){
            echo "<p style='font-size:18px; color:red;'>We only support .mp4 format!</p>";
        }
        else {
?>          <object id="QuickTimePlayer" classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0" align="middle" width="600" height="350" >
                <param name="src" value="<?php echo $filename;?>" />
                <PARAM name="href" value="<?php echo $filename;?>"/ >
                <param name="controller" value="true" />
                <param name="autoplay" value="true" />
                <param name="scale" value="aspect" />
                <param name="pluginspage" value="http://www.apple.com/quicktime/download/" />
                <embed src="<?php echo $file_path;?>"
                    href = "<?php echo $file_path;?>"
                    width="600" height="350"
                    pluginspage=http://www.apple.com/quicktime/download/
                    align="middle" autoplay="true"
                    bgcolor="black" >
                </embed>
            </object>
<?php
        }
        $avg_rating = cal_media_rating($file_path);
?>
        <table border="0">
        <tr>
           <?php
           if ($avg_rating[1] == 0){
               echo "<p class='para'>$title&nbsp;&nbsp;(&hearts; <span style='color:grey;'><small>&ndash; &nbsp;from $avg_rating[1]</small></span>)</p>";
           }
           else {
               echo "<p class='para'>$title&nbsp;&nbsp;(&hearts; $avg_rating[0]&nbsp; <span style='color:grey; font-size:12'><small>from $avg_rating[1]</small></span>)</p>";
           }
           ?>
           <!-- <p class="para"><?php echo $title;?>&nbsp;&nbsp;(&hearts; 4.3)</p> -->
        </tr>
        <tr>
           <?php
            $nick_upload = find_nickname($user_upload);
            echo "<td valign='top'>
                   $nick_upload &nbsp;&nbsp; $view_count viewers  &#183 $date_time
                 </td>";


            if (!isset($_SESSION['username'])){
                echo "<td align='left'><FORM action='media.php?file_path=$file_path&channel=$user_upload' method='post'>
                       <button name='favorites' type='submit' value=$file_path style='color:blue;cursor:pointer;'>Like</button>&nbsp;
                       <button name='follow' type='submit' value=$file_path style='color:white; background-color:blue;cursor:pointer;'>Follow</button>
                      </FORM></td>";
            }
            else {
                $check_favorite = check_user_favorites($file_path, $_SESSION['username']);
                $check_follow = check_user_subscribe($user_upload, $_SESSION['username']);
                if ($check_favorite == 0 && $check_follow == 0 ){
                     echo "<td align='left'><FORM action='media.php?file_path=$file_path&channel=$user_upload' method='post'>
                            <button name='favorites' type='submit' value=$file_path style='color:blue;cursor:pointer;'>Like</button>&nbsp;
                            <button name='follow' type='submit' value=$file_path style='color:white; background-color:blue;cursor:pointer;'>Follow</button>
                           </FORM></td>";
                }
                else if ($check_favorite == 0 && $check_follow == 1) {
                    echo "<td align='left'><FORM action='media.php?file_path=$file_path&channel=$user_upload' method='post'>
                           <button name='favorites' type='submit' value=$file_path style='color:blue; cursor:pointer;'>Like</button>&nbsp;
                           <button name='unfollow' type='submit' value=$file_path style='color:white; background-color:grey;cursor:pointer;'>Followed</button>
                          </FORM></td>";
                }

                else if ($check_favorite == 1 && $check_follow == 0) {
                    echo "<td align='left'><FORM action='media.php?file_path=$file_path&channel=$user_upload' method='post'>
                           <button name='dislike' type='submit' value=$file_path style='color:grey;cursor:pointer;'>Liked</button>&nbsp;
                           <button name='follow' type='submit' value=$file_path style='color:white; background-color:blue;cursor:pointer;'>Follow</button>
                          </FORM></td>";

                }
                else if ($check_favorite == 1 && $check_follow == 1) {
                    echo "<td align='left'><FORM action='media.php?file_path=$file_path&channel=$user_upload' method='post'>
                           <button name='dislike' type='submit' value=$file_path style='color:grey;cursor:pointer;' >Liked</button>&nbsp;
                           <button name='unfollow' type='submit' value=$file_path style='color:white; background-color:grey;cursor:pointer;'>Followed</button>
                          </FORM></td>";
                }
            }
            ?>
            <td align="left">
                <!-- CHECK DOWNLOAD BLOCKS  -->
                <FORM action="media_download.php" method="post">
                    <button name="download" type="submit" value="<?php echo $file_path;?>" style='cursor:pointer;'>Download</button>
                </FORM>
            </td>
        </tr>
        <?php
        $channel_name = check_media_channel($file_path, $user_upload);
        if ($channel_name == "DEFAULT"){
            $channel_name = $user_upload;

            if (!isset($_SESSION['username'])){
                echo "<FORM action='media.php?file_path=$file_path&channel=$channel_name' method='post'>";
                echo "<tr><td valign='top' style='font-size:18px; color:green;'>".find_nickname($channel_name)."&nbsp;
                <button name = 'subscribe' style='color:white; background-color:red; cursor:pointer;'>subscribe</button>
                </td></tr>";
                echo "</FORM>";
            }
            else {
                $check = check_user_subscribe($channel_name, $_SESSION['username']);
                if ($check == 0){
                    echo "<FORM action='media.php?file_path=$file_path&channel=$channel_name' method='post'>";
                    echo "<tr><td valign='top' style='font-size:18px; color:green;'>".find_nickname($channel_name)."&nbsp;
                    <button name = 'subscribe' style='color:white; background-color:red; cursor:pointer;'>subscribe</button>
                    </td></tr>";
                    echo "</FORM>";
                }
                else {
                    echo "<FORM action='media.php?file_path=$file_path&channel=$channel_name' method='post'>";
                    echo "<tr><td valign='top' style='font-size:18px; color:green;'>".find_nickname($channel_name)." &nbsp;
                    <button name = 'unsubscribe' style='color:white; background-color:grey; cursor:pointer;'>subscribed</button>
                    </td></tr>";
                    echo "</FORM>";
                }
            }
       }
       else {
           if (!isset($_SESSION['username'])){
               echo "<FORM action='media.php?file_path=$file_path&channel=$channel_name' method='post'>";
               echo "<tr><td valign='top' style='font-size:18px; color:green;'>".clean_str($channel_name)." &nbsp;
               <button name = 'subscribe' style='color:white; background-color:red; cursor:pointer;'>subscribe</button>
               </td></tr>";
               echo "</FORM>";
           }
           else {
               $check = check_user_subscribe($channel_name, $_SESSION['username']);
               if ($check == 0){
                   echo "<FORM action='media.php?file_path=$file_path&channel=$channel_name' method='post'>";
                   echo "<tr><td valign='top' style='font-size:18px; color:green;'>".clean_str($channel_name)." &nbsp;
                   <button name = 'subscribe' style='color:white; background-color:red; cursor:pointer;'>subscribe</button>
                   </td></tr>";
                   echo "</FORM>";
               }
               else {
                   echo "<FORM action='media.php?file_path=$file_path&channel=$channel_name' method='post'>";
                   echo "<tr><td valign='top' style='font-size:18px; color:green;'>".clean_str($channel_name)." &nbsp;
                   <button name = 'unsubscribe' style='color:white; background-color:grey; cursor:pointer;'>subscribed</button>
                   </td></tr>";
                   echo "</FORM>";
               }
           }
       }
       ?>
       <tr>
           <td><textarea readonly cols='30' style="font-size:15px;"><?php echo $description; ?></textarea></td>
       </tr>
       <tr>
           <td colspan="4"><hr/></td>
       </tr>
       <tr>
           <td >
               Share <input type="radio" onclick="javascript:shareCheck();" name="shareCheck" id="shareCheck" value=1>
               &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;
               Add to playlist <input type="radio" onclick="javascript:plCheck();" name="plCheck" id="plCheck" value=1>
           </td>
           <td >
               <FORM action="media.php?file_path=<?php echo $file_path;?>" method="post" name="media">
               <?php
                if ($allow_rate == true){
                    echo "<button name='rating' class='button' value = 1 style='font-size:20px; color:red; background-color:transparent; border-color:transparent;cursor:pointer;cursor:pointer;' >&#43;</button>" ;
                    echo "<select name='score' style='font-size:14px;'>
                        <option value='DEFAULT'>Rating</option>
                        <option value=1>1</option>
                        <option value=2>2</option>
                        <option value=3>3</option>
                        <option value=4>4</option>
                        <option value=5>5</option>
                    </select><br />";
                }
                else {
                    echo "<button name='rating' class='button' value = 1 style='font-size:20px; color:grey; background-color:transparent; border-color:transparent;cursor:pointer;cursor:pointer;' disabled>&#43;</button>" ;
                    echo "<select style='font-size:14px;' disabled>
                        <option value='DEFAULT'>Rating</option>
                        <option value=1>1</option>
                        <option value=2>2</option>
                        <option value=3>3</option>
                        <option value=4>4</option>
                        <option value=5>5</option>
                    </select><br />";
                }

              ?>
              </FORM>
           </td>
       </tr>
       <?php
       if (isset($_SESSION["username"])){
       ?>
       <tr>
       <td>
       <FORM action="media.php?file_path=<?php echo $file_path;?>" method="post" name="media" id="media">
       <div id="shareYes" style="display:none">
           <textarea rows="1" cols="30" name="share_users" id='share_users' form="media" placeholder="&#43;Share with contacts"></textarea><br/>
           <!-- <textarea rows="1" cols="20" name="share_groups" id='share_groups' form="media" placeholder="&#43;Share with groups"></textarea><br/> -->
           <textarea rows="1" cols="15" name="share_groups" id='share_groups' form="media" placeholder="&#43;A group"></textarea>
           <textarea rows="1" cols="20" name="group_master" id='group_master' form="media" placeholder="&#43;The group master"></textarea>
           <br/>
           <button name="share" id="share" type="submit" value="share" style='cursor:pointer;'>Share</button><button name="cancel" type="submit" value="Cancel">Cancel</button>
       </div>
       <div id="plYes" style="display:none">
           <textarea rows="1" cols="30" name="playlist" form="media" placeholder="&#43;Playlist Name"></textarea><br/>
           <button name="addPlaylist" id="addPlaylist" type="submit" value="addPlaylist" style='cursor:pointer;'>Add</button><button name="cancel" type="submit" value="Cancel">Cancel</button>
       </div>
       </FORM>
       </td>
       </tr>
       <?php
       }
       else {
       ?>
       <tr>
       <td>
       <FORM action="media.php?file_path=<?php echo $file_path;?>" method="post" name="media">
       <div id="shareYes" style="display:none">
           <p style="font-size:20px; color:blue">Please Login/Register to share this video!</P>
           <button name="home" id="submit" type="submit" value="submit">OK!</button><button name="cancel" type="submit" value="Cancel">No,later!</button>
       </div>
       <div id="plYes" style="display:none">
           <p style="font-size:20px; color:blue">Please Login/Register to manage your playlist!</P>
           <button name="home" id="submit" type="submit" value="submit" style='cursor:pointer;'>OK!</button><button name="cancel" type="submit" value="Cancel" style='cursor:pointer;'>No,later!</button>
       </div>
       </FORM>
       </td>
       </tr>
       <?php
        }
       ?>
       <FORM action="media.php?file_path=<?php echo $file_path;?>" method="post" name="add_comment" id="add_comment">
       <tr>
           <?php
           if ($allow_comment){
            ?>
           <td colspan="4">
               <textarea rows="12" cols="60" name="comment" form="add_comment" placeholder="Add a public comment" style='font-size:15px;'></textarea><br/>
               <button name="submit_comment" id="submit_comment" type="submit" value="submit" style='cursor:pointer;'>Submit</button><button name="cancel" type="submit" value="Cancel" style='cursor:pointer;'>Cancel</button>
           </td>
           <?php
           if (isset($insert_error)){
               echo "<td> <p style='font-size:18px;'>$insert_error</p></td>";
           }
           ?>
           <?php
            }
            else {
            ?>
            <td colspan="4">
                <textarea readonly rows="2" cols="60" name="comment" form="media" placeholder="The author closed the comment board!" style='font-size:15px;'></textarea>
            </td>

            <?php
            }
            ?>
       </tr>
       <tr><td colspan='4'>&nbsp;</td></tr>
       <tr><td colspan='4'><hr></td></tr>
       </FORM>
   </table>

   <?php
    $media_comments = media_comments($file_path);
    if (mysql_num_rows($media_comments)==0){
        //    do nothing
    }
    else {
        echo "<br/>";
        echo "<table border='0' cellpadding='2'>";
        // echo "<tr><td colspan='3'><hr></td></tr>";
        while ($row = mysql_fetch_array($media_comments, MYSQL_ASSOC)){
               $user = $row['username'];
               $comment = $row['comment'];
               $time = $row['date_time'];
               $comment_id = $row['comment_id'];
    ?>
        <tr>
            <td >
                <span style="font-size:24px; color:blue;"><?php echo find_nickname($user);?>&nbsp;&nbsp;</span><span style="font-size:15px;"><?php echo $time;?>&nbsp;&nbsp;</span>
            </td>
        </tr>
        <tr>
            <td valign='bottom'>
                <span style="font-size:19px;"><?php echo $comment;?>&nbsp;&nbsp;</span>
            </td>
            <?php
            if (isset($_SESSION['username'])){
                if ($user = $_SESSION['username']){
                    echo "<td align='left' valign='top'>
                            <FORM action='media.php?file_path=$file_path' method='post'>
                                <button name='remove_comment' value='$comment_id' style='background-color:transparent;border-color:transparent;cursor:pointer;width:16px;height:16px;'>
                                    <img src='https://cdn2.iconfinder.com/data/icons/e-business-helper/240/627249-delete3-512.png' style='background-color:transparent;border-color:transparent;cursor:pointer;width:16px;height:16px;'>
                                </button>
                            </FORM>
                         </td>";
                }
            }
            ?>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;</td>
        </tr>
       <?php
          }
        // echo "<tr><td colspan='3'><hr style='color:grey;'/></td></tr>";
        echo "</table>";
       }
       update_media_view($file_path, $username_view, $ip);
       update_view_count($file_path);
   }
}
?>


</body>
</html>
