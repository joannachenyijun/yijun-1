<?php
include "mysqlClass.inc.php";


function isUserExist ($username, $email, $nickname){
    $query = "select * from account where username='$username'";
    $result = mysql_query( $query );
    if (mysql_num_rows($result) > 0 ){
        return 1;
    }

    $query = "select * from account where email='$email'";
    $result = mysql_query( $query );
    if (mysql_num_rows($result) > 0 ){
        return 2;
    }


    $query = "select * from account where nickname='$nickname'";
    $result = mysql_query( $query );
    if (mysql_num_rows($result) > 0 ){
        return 3;
    }

    return 0;
}


function username_check($username){
    $query = "select * from account where username='$username'";
    $result = mysql_query( $query );
    $row = mysql_fetch_row($result);
    // if (!$result)
    if (empty($row))
	{
	   return 0;
	}
	else{
        return 1;
	}

}


function email_check($email){
    $email = mysql_real_escape_string($email);
    $query = "select * from account where email='$email'";
    $result = mysql_query( $query );
    if (mysql_num_rows($result) == 0){
	   return 0;
	}
	else{
        return 1;
	}
}


function user_pass_check($email, $password){
    $email = mysql_real_escape_string($email);
    $password = mysql_real_escape_string($password);
    $query = "SELECT * FROM account where email='$email' ";
	$result = mysql_query( $query );

	if (!$result)
	{
	   die ("user_pass_check() failed. Could not query the database: <br />". mysql_error());
	}
	else{
        if (mysql_num_rows($result) == 0){
            return 1; //no such user
        }
        else {
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            if (strcmp($row['password'], $password)){
                return 2; //wrong password
            }
            else {
                return 0; // checked
            }
        }
	}
}


function getUserInfo($email, $password){
    $email = mysql_real_escape_string($email);
    $password = mysql_real_escape_string($password);

    $query = "select * from account where email='$email'";
    $result = mysql_query( $query );
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    return $row;
}


function updateNickname($nickname_new, $username){

    $query = sprintf("UPDATE account SET nickname = '%s' WHERE username = '%s'",
    mysql_real_escape_string($nickname_new),
    mysql_real_escape_string($username)
    );
    $result = mysql_query( $query );

}


function updatePassword($username, $password_new){
    $query = sprintf("UPDATE account SET password = '%s' WHERE username = '%s'",
    mysql_real_escape_string($password_new),
    mysql_real_escape_string($username)
    );
    $result = mysql_query( $query );
}


function updateEmail($username, $email_new){
    $query = sprintf("UPDATE account SET email = '%s' WHERE username = '%s'",
    mysql_real_escape_string($email_new),
    mysql_real_escape_string($username)
    );
    $result = mysql_query( $query );
}


function upload_error($result)
{
	//view erorr description in http://us2.php.net/manual/en/features.file-upload.errors.php
	switch ($result){
	case 1:
		return "UPLOAD_ERR_INI_SIZE";
	case 2:
		return "UPLOAD_ERR_FORM_SIZE";
	case 3:
		return "UPLOAD_ERR_PARTIAL";
	case 4:
		return "UPLOAD_ERR_NO_FILE";
	case 5:
		return "File has already been uploaded";
	case 6:
		return  "Failed to move file from temporary directory";
	case 7:
		return  "Upload file failed";
    case 8:
        return "The playlist has been created!";
    case 9:
        return "The file has been added to the playlist!";
    case 10:
        return "The file alreay exist in the playlist!";
    case 11:
        return "Group name/master is required! ";
    case 12:
        return "You can not share this video with this user! ";
    case 13:
        return "Successfully shared!";
    case 14:
        return "We only support .mp4 format!";
    case 15:
        return "No matched group found for you!";
    }

}


function getFullContact($username){
    $query = sprintf("SELECT account.username, account.nickname, account.email, account.fname, account.lname, account.gender, account.dob
                      FROM account, contact
                      WHERE contact.username_1 = '%s' AND account.username = contact.username_2",
    mysql_real_escape_string($username)
    );
    $result = mysql_query( $query );
    return $result;
}


function getRecentContact($username){
    $username = mysql_real_escape_string($username);
    $query = "SELECT message.date_time, account.username, account.nickname, account.email, account.fname, account.lname, account.gender, account.dob
              FROM account, message
              WHERE message.username_to = '$username' AND NOT message.username_from = '$username' AND account.username = message.username_from
              UNION
              SELECT message.date_time, account.username, account.nickname, account.email, account.fname, account.lname, account.gender, account.dob
              FROM account, message
              WHERE message.username_from = '$username' AND NOT message.username_to = '$username' AND account.username = message.username_to
               ";
    $result = mysql_query( $query );
    return $result;
}


function addContact($username, $contact){

    $contact = mysql_real_escape_string($contact);
    $query = "SELECT username from account WHERE nickname = '$contact' ";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $contact = $row['username'];

    $query = sprintf("INSERT INTO contact VALUES ('%s', '%s')",
    mysql_real_escape_string($username),
    mysql_real_escape_string($contact)
    );
    // $query = "insert into contact values ('$username','$contact')";
    $result = mysql_query( $query );
    return $result;
}


function deleteContact($username, $contact){
    $contact = mysql_real_escape_string($contact);
    $query = "SELECT username from account WHERE nickname = '$contact' ";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $contact = $row['username'];

    $query = sprintf("DELETE FROM contact WHERE username_1 = '%s' AND username_2 = '%s'",
    mysql_real_escape_string($username),
    mysql_real_escape_string($contact)
    );
    $result = mysql_query( $query );
    return $result;
}



function checkContact($username, $contact){
    // echo strlen($contact);
    if(strlen($contact) == 0){
        return -1;
    }

    $contact = mysql_real_escape_string($contact);
    $query = "SELECT username from account WHERE nickname = '$contact' ";
    $result = mysql_query($query);

    if(mysql_num_rows($result) == 0){
        return 0; # the contact does not exists
    }
    else{
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $contact = $row['username'];
    }

    if($username == $contact){
        return 4; # the contact = self
    }


    $query = sprintf("SELECT * FROM block WHERE username_1 = '%s' AND username_2 = '%s'",
    mysql_real_escape_string($contact),
    mysql_real_escape_string($username)
    );
    $result = mysql_query($query);
    $row = mysql_fetch_array($result);
    if(!empty($row)){
        return 2; # you are blocked by the contact
    }

    $query = sprintf("SELECT * FROM block WHERE username_1 = '%s' AND username_2 = '%s'",
    mysql_real_escape_string($username),
    mysql_real_escape_string($contact)
    );
    $result = mysql_query($query);
    $row = mysql_fetch_array($result);
    if(!empty($row)){
        return 5; # you have blocked this user
    }

    $query = sprintf("SELECT * FROM contact WHERE username_1 = '%s' AND username_2 = '%s'",
    mysql_real_escape_string($username),
    mysql_real_escape_string($contact)
    );
    $result = mysql_query($query);
    $row = mysql_fetch_array($result);
    if(empty($row)){
        return 3; # the contact is not in the list
    }
    else{
        return 1; # the contact is in the list
    }

}


function getFriend($username){
    $query = sprintf("SELECT account.username, account.nickname, account.email, account.fname, account.lname, account.gender, account.dob
                      FROM account, friend
                      WHERE friend.username_1 = '%s' AND account.username = friend.username_2",
    mysql_real_escape_string($username)
    );
    $result = mysql_query( $query );
    return $result;
}


function checkFriend($username, $friend){
    $username = mysql_real_escape_string($username);
    $friend = mysql_real_escape_string($friend);

    $query = "SELECT username from account WHERE nickname = '$friend' ";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $friend = $row['username'];

    $query = "SELECT * FROM friend WHERE username_1 = '$username' AND username_2 ='$friend' ";
    $result = mysql_query($query);
    // $row = mysql_fetch_array($result);
    if(mysql_num_rows($result) == 0){
        return 3; # the contact is not a friend
    }
    else{
        return 1; # the contact is already a friend
    }
}


function addFriend($username, $friend){
    $username = mysql_real_escape_string($username);
    $friend = mysql_real_escape_string($friend);

    $query = "SELECT username from account WHERE nickname = '$friend' ";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $friend = $row['username'];

    $query = sprintf("INSERT INTO friend VALUES ('%s', '%s')",
    mysql_real_escape_string($username),
    mysql_real_escape_string($friend)
    );
    $result = mysql_query( $query );
    return $result;
}


function deleteFriend($username, $friend){
    $username = mysql_real_escape_string($username);
    $friend = mysql_real_escape_string($friend);

    $query = "SELECT username from account WHERE nickname = '$friend' ";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $friend = $row['username'];

    $query = sprintf("DELETE FROM friend WHERE username_1 = '%s' AND username_2 = '%s'",
    mysql_real_escape_string($username),
    mysql_real_escape_string($friend)
    );
    $result = mysql_query( $query );
    return $result;
}


function getBlock($username){
    $query = sprintf("SELECT * FROM account, block WHERE block.username_1 = '%s' AND block.username_2 = account.username",
    mysql_real_escape_string($username)
    );
    $result = mysql_query( $query );
    return $result;
}


function checkBlock($username, $block){
    $username = mysql_real_escape_string($username);
    $block = mysql_real_escape_string($block);
    $block = find_username($block);

    $query = "SELECT * FROM block WHERE username_1 = '$username' AND username_2 = '$block' ";
    $result = mysql_query($query);
    // $row = mysql_fetch_array($result);
    if(mysql_num_rows($result) == 0){
        return 3; # the contact is not in the block
    }
    else{
        return 1; # the contact is blocked
    }
}


function checkBlockBy($blockby, $username){
    $username = mysql_real_escape_string($username);
    $blockby = mysql_real_escape_string($blockby);
    $blockby = find_username($blockby);

    $query = "SELECT * FROM block WHERE username_1 = '$blockby' AND username_2 = '$username' ";
    $result = mysql_query($query);
    // $row = mysql_fetch_array($result);
    if(mysql_num_rows($result) == 0){
        return 3; # the contact is not in the block
    }
    else{
        return 1; # the contact is blocked
    }
}


function addBlock($username, $block){
    $block = mysql_real_escape_string($block);
    $query = "SELECT username from account WHERE nickname = '$block' ";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $block = $row['username'];

    $query = sprintf("INSERT INTO block VALUES ('%s', '%s')",
    mysql_real_escape_string($username),
    mysql_real_escape_string($block)
    );
    // $query = "insert into contact values ('$username','$contact')";
    $result = mysql_query( $query );
    return $result;
}


function deleteBlock($username, $block){

    $block = mysql_real_escape_string($block);
    $query = "SELECT username from account WHERE nickname = '$block' ";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $block = $row['username'];

    $query = sprintf("DELETE FROM block WHERE username_1 = '%s' AND username_2 = '%s'",
    mysql_real_escape_string($username),
    mysql_real_escape_string($block)
    );
    // echo $query;
    $result = mysql_query( $query );
    return $result;
}



function new_message($username, $to, $subject, $message){
    $query = sprintf("INSERT INTO message (subject, comment, date_time, isRead, username_from, username_to) VALUES ('%s', '%s', NOW(), false, '%s', '%s')",
    mysql_real_escape_string($subject),
    mysql_real_escape_string($message),
    mysql_real_escape_string($username),
    mysql_real_escape_string($to)
    );
    $result = mysql_query( $query );
    return $result;
    }



function getMessage($username){
    $query = " SELECT * FROM message WHERE username_to = '$username' ";
    $result = mysql_query( $query );
    return $result;
}


function getMessage_sent($username){
    $query = " SELECT * FROM message WHERE username_from = '$username' ";
    $result = mysql_query( $query );
    return $result;
}


function mark_as_read($username, $to, $subject, $message, $time){
    $query = "UPDATE message SET isRead = true
              WHERE username_from = '$username' AND comment = '$message' AND username_to = '$to' AND subject = '$subject' AND date_time='$time' ";
    $result = mysql_query( $query );
    return $result;
}


function mark_as_unread($username, $to, $subject, $message, $time){
    $query = "UPDATE message SET isRead = false
              WHERE username_from = '$username' AND comment = '$message' AND username_to = '$to' AND subject = '$subject' AND date_time ='$time' ";
    $result = mysql_query( $query );
    return $result;
}


function delete_message($username, $to, $subject, $message, $time){
    $query = "DELETE FROM message
              WHERE username_from = '$username' AND comment = '$message' AND username_to = '$to' AND subject = '$subject' AND date_time ='$time' ";
    $result = mysql_query( $query );
    return $result;
}

function find_user($user_list){
    $user_arr = array();

}



function get_full_shared_users($user_list, $group_id){
    $query = "SELECT * FROM group_users where group_id = '$group_id' ";
    $result = mysql_query($query);

    if (mysql_num_rows($result) > 0 ){
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            array_push($user_list, $row['username']);
        }
    }
    return $user_list;
}


function share_media($username, $user_list, $upfile){
    foreach ($user_list as $user){
        $query = "INSERT INTO media_shared (file_path, username_from, username_to, date_time )
                  VALUES ('$upfile', '$username', '$user', NOW())";
        $result = mysql_query($query);
    }
}


function download_blocks($upfile, $user_by, $user_blocked){
    $query = "INSERT INTO download_blocks (media_path, blocked_by, user_blocked)
              VALUES ('$upfile', '$user_by', '$user_blocked')";
    $result = mysql_query($query);
}


function view_blocks($upfile, $user_by, $user_blocked){
    $query = "INSERT INTO view_blocks (media_path, blocked_by, user_blocked)
              VALUES ('$upfile', '$user_by', '$user_blocked')";
    $result = mysql_query($query);
}


function clean_str_to_arr($str) {
   $str = preg_replace('/[^a-zA-Z0-9]+/', ' ', $str);
   // $arr = preg_split('/\n+/', $str);
   // $str = implode("\n", $arr);
   //
   // $arr = preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', $str, -1, PREG_SPLIT_NO_EMPTY);
   $arr = preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', $str, -1, PREG_SPLIT_NO_EMPTY);
   // // $str = implode(" ", $arr);
   // $arr = explode(" ", $str);

   // $arr = preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', $str, -1, PREG_SPLIT_NO_EMPTY);
   return $arr;
   // return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
}


function clean_str($str) {
   $str = preg_replace('/[^a-zA-Z0-9]+/', ' ', $str);
   return $str;
   // return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
}


function add_media_keywords($upfile, $title, $tags, $description, $category){
    $keywords = array();
    // $title_word = preg_split("/[\s,;!.?~:$%&*()]+/", $title);
    $title_arr = clean_str_to_arr($title);
    $tag_arr = clean_str_to_arr($tags);
    $description_arr = clean_str_to_arr($description);
    $category_arr = clean_str_to_arr($category);

    $keywords = array_merge($title_arr, $tag_arr, $description_arr, $category_arr);
    $keywords = implode(" ", $keywords); //convert array to str
    // $keywords = explode(" ", $keywords);

    $query = "INSERT INTO media_keywords VALUES ('$upfile', '$keywords')";
    $result = mysql_query($query);

    // return $keywords;
}


function update_media_keywords($file_path, $title, $tags, $description, $category){
    $keywords = array();
    // $title_word = preg_split("/[\s,;!.?~:$%&*()]+/", $title);
    $title_arr = clean_str_to_arr($title);
    $tag_arr = clean_str_to_arr($tags);
    $description_arr = clean_str_to_arr($description);
    $category_arr = clean_str_to_arr($category);
    $keywords = array_merge($title_arr, $tag_arr, $description_arr, $category_arr);
    $keywords = implode(" ", $keywords); //convert array to str
    // $keywords = explode(" ", $keywords);

    $query = "UPDATE media_keywords SET keywords = '$keywords' WHERE file_path = '$file_path' ";
    $result = mysql_query($query);
    // return $keywords;
}


function word_cloud(){
    $word_cloud = array();
    $query = "SELECT * FROM media_keywords";
    $result = mysql_query($query);
    if (isset($result)){
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
            $keywords = explode(" ", $row["keywords"]);
            $word_cloud = array_merge($word_cloud, $keywords);
        }
    }

    return $word_cloud;
}


function display_media($result, $max_rows){
    $col_num = 0;
    $row_num = 0;
    echo "<table>";
    echo "<tr>";
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        $file_path = $row["file_path"];
        $title = $row["title"];
        $date_time = $row["date_time"];
        $type = $row["type"];
        $view_count = $row["view_count"];
        $user_upload = $row["username"];

        if ($col_num > 2){
            echo "</tr>";
            echo "<tr>";
            $row_num += 1;
            $col_num = 0;
        }

        if ($row_num >= $max_rows){
            echo "</tr>";
            break 1;
        }

        echo "<td width='250px' height='250px' valign='top'><div>
                <a href='media.php?file_path=$file_path'><video width='300px'><source src ='$file_path' type ='$type'></video></a>
                <br/>$title <br/>".
                find_nickname($user_upload)." <br/>
                $view_count views  &#183 $date_time
              </td><td>&nbsp;&nbsp;</td></div>";

        $col_num += 1;
    }
    // echo "</tr>";
    echo "</table>";
}

function display_search($path_target_arr, $max_rows){
    $col_num = 0;
    $row_num = 0;
    echo "<table>";
    echo "<tr>";
    foreach ($path_target_arr as $file_path){
        $query = "SELECT * FROM media WHERE file_path = '$file_path' ";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $title = $row["title"];
        $date_time = $row["date_time"];
        $type = $row["type"];
        $view_count = $row["view_count"];
        $user_upload = $row["username"];

        if ($col_num > 2){
            echo "</tr>";
            echo "<tr>";
            $row_num += 1;
            $col_num = 0;
        }

        if ($row_num >= $max_rows){
            echo "</tr>";
            break 1;
        }

        echo "<td width='250px' height='250px' valign='top'><div>
                <a href='media.php?file_path=$file_path'><video width='300px'><source src ='$file_path' type ='$type'></videl></a>
                <br/>$title <br/>".
                find_nickname($user_upload)." <br/>
                $view_count views  &#183 $date_time
              </td><td>&nbsp;&nbsp;</td></div>";

        $col_num += 1;
    }
    echo "</table>";
}


function basic_search($input_arr){
    $num = sizeof($input_arr);
    // $target_num = max(floor($num*0.9), 1);
    $target_num = 1;
    $path_arr = array();
    $path_target_arr = array();

    $query_all_media = "SELECT file_path FROM media";
    $result = mysql_query($query_all_media);
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        $path_arr[$row["file_path"]] = 0;
    }

    foreach($input_arr as $input){
        $query = "SELECT file_path FROM media_keywords
                  WHERE keywords LIKE '%$input%'";
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0){
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
                $path_arr[$row["file_path"]] += 1;
            }
        }
    }

    foreach($path_arr as $path => $n){
        if($n >= $target_num){
            array_push($path_target_arr, $path);
        }
    }

    return $path_target_arr;
}


function search_title ($input_arr){
    $num = sizeof($input_arr);
    // $target_num = max(floor($num*0.5), 1);
    $target_num = 1;
    $path_arr = array();
    $path_target_arr = array();

    $query_all_media = "SELECT file_path FROM media";
    $result = mysql_query($query_all_media);
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        $path_arr[$row["file_path"]] = 0;
    }

    foreach($input_arr as $input){
        $query = "SELECT file_path FROM media
                  WHERE title LIKE '%$input%'";
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0){
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
                $path_arr[$row["file_path"]] += 1;
            }
        }
    }

    foreach($path_arr as $path => $n){
        if($n >= $target_num){
            array_push($path_target_arr, $path);
        }
    }

    return $path_target_arr;
}



function advanced_search($title, $category, $type, $size, $time_before, $time_after, $time_order, $description){
    if ($time_before < $time_after){
        $result = "Please reset the Uploaded Time !";
        return $result;
    }

    $size = $size*1024*1024;

    if ($type == "DEFAULT"){
        if ($time_order == 'desc'){
            $query = "SELECT file_path FROM media WHERE category = '$category' AND size <= '$size'
            AND DATE(date_time) <= '$time_before' AND DATE(date_time) >= '$time_after'
            ORDER BY date_time desc ";
        }
        else {
            $query = "SELECT file_path FROM media WHERE category = '$category' AND size <= '$size'
            AND DATE(date_time) <= '$time_before' AND DATE(date_time) >= '$time_after'
            ORDER BY date_time  ";
        }
    }
    else {
        if ($time_order == 'desc'){
            $query = "SELECT file_path FROM media WHERE category = '$category' AND size <= '$size'
            AND DATE(date_time) <= '$time_before' AND DATE(date_time) >= '$time_after' AND type = '$type'
            ORDER BY date_time desc ";
        }
        else {
            $query = "SELECT file_path FROM media WHERE category = '$category' AND size <= '$size'
            AND DATE(date_time) <= '$time_before' AND DATE(date_time) >= '$time_after' AND type = '$type'
            ORDER BY date_time ";
        }
    }

    $result = mysql_query($query);

    if (mysql_num_rows($result) == 0){
        $result = "No Result Found!" ;
        return $result;
    }

    $file_path_arr = array();
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        array_push($file_path_arr, $row["file_path"]);
    }

// search based on description (keywords)
    $description_arr = clean_str_to_arr($description);
    $path_desp_arr = basic_search($description_arr);

// search based on title
    $title_arr = clean_str_to_arr($title);
    $path_title_arr = search_title($title_arr);

    $path_target_arr = array_merge($path_desp_arr, $path_title_arr);

    foreach ($path_target_arr as $path){
        if (in_array($path, $file_path_arr)){
            continue;
        }
        else {
            array_push($file_path_arr, $path);
        }
    }
    return $file_path_arr;
}


function category_search($category){
    $query = "SELECT * FROM media WHERE category = '$category'";
    $result = mysql_query($query);

    return $result;
}


function update_view_count($file_path){

    $query = "SELECT COUNT(*) AS view_count FROM media_view WHERE file_path = '$file_path'";
    $result = mysql_query($query);
    $row = mysql_fetch_row($result, MYSQL_ASSOC);
    $view_count = $row["view_count"];

    $query = "UPDATE media SET view_count = '$view_count' WHERE file_path = '$file_path' ";
    $result = mysql_query($query);
    return $result;


}


function update_media_view($file_path, $username_view, $ip){
    $query = "INSERT INTO media_view (file_path, username, date_time, ip) VALUES ('$file_path', '$username_view', NOW(), '$ip')";
    $result = mysql_query($query);

    return $result;
}


function user_interests($username){
    $word_arr = array();
    $query = "SELECT DISTINCT media_keywords.keywords FROM media_keywords, media_view
              WHERE media_view.username = '$username' AND media_view.file_path = media_keywords.file_path";

    $result = mysql_query($query);
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        array_push($word_arr, $row["keywords"]);
    }
    $word_str = implode(" ", $word_arr);
    $word_arr = explode(" ", $word_str);

    return $word_arr;
}


function insert_download($file_path, $username_download, $ip){
    $query = "INSERT INTO media_download (file_path, username, date_time, ip)
              VALUES ('$file_path', '$username_download', NOW(), '$ip') ";
    $result = mysql_query($query);

    return $result;
}


function check_playlist_exist($playlist_name, $username){
    $playlist_name = mysql_real_escape_string($playlist_name);
    $username = mysql_real_escape_string($username);

    $query = "SELECT * FROM playlist WHERE username = '$username' AND playlist_name = '$playlist_name'";
    $result = mysql_query( $query );

    if (mysql_num_rows($result) == 0){
        return 0; //the playlist doesn't exist
    }
    else {
        return 1;
    }
}


function insert_playlist($playlist_name, $username, $file_path){
    $playlist_name = mysql_real_escape_string($playlist_name);
    $username = mysql_real_escape_string($username);
    $file_path = mysql_real_escape_string($file_path);

    $query = "INSERT INTO playlist (playlist_name, username, file_path) VALUES ('$playlist_name', '$username', '$file_path')";
    $result = mysql_query( $query );
}


function remove_playlist($playlist_name, $username){
    $playlist_name = mysql_real_escape_string($playlist_name);
    $username = mysql_real_escape_string($username);

    $query = "DELETE FROM playlist WHERE playlist_name='$playlist_name' AND username='$username' ";
    $result = mysql_query( $query );
}


function check_media_in_playlist($playlist_name, $username, $file_path){
    $query = "SELECT * FROM playlist, media WHERE playlist.username='$username' AND playlist.playlist_name='$playlist_name' AND media.file_path = playlist.file_path ";
    $result = mysql_query($query);

    if (mysql_num_rows($result) == 0){  # no media in this playlist
        return 2;
    }

    $query = "SELECT * FROM playlist WHERE username='$username' AND playlist_name='$playlist_name' AND file_path = '$file_path' ";
    $result = mysql_query($query);

    if (mysql_num_rows($result) == 0){  # this media is not in this playlist
        return 0;
    }
    else {
        return 1;
    }


}






function check_user_favorites($file_path, $username){
    $file_path = mysql_real_escape_string($file_path);
    $username = mysql_real_escape_string($username);

    $query = "SELECT * FROM favorites WHERE username = '$username' AND file_path = '$file_path'";
    $result = mysql_query( $query );

    if (mysql_num_rows($result) == 0){
        return 0; //the playlist doesn't exist
    }
    else {
        return 1;
    }
}


function insert_favorites($file_path, $username){
    $username = mysql_real_escape_string($username);
    $file_path = mysql_real_escape_string($file_path);

    $query = "INSERT INTO favorites (username, file_path) VALUES ('$username', '$file_path')";
    $result = mysql_query( $query );
}

function remove_from_favorites($username, $file_path){
    $username = mysql_real_escape_string($username);
    $file_path = mysql_real_escape_string($file_path);

    $query = "DELETE FROM favorites WHERE username = '$username' AND file_path='$file_path'";
    $result = mysql_query($query);
}


function check_comment($allow_comment){
    $query = "SELECT * FROM media";
    if($allow_comment = 'FALSE'){
            return 0; # does not allow comment
        }
        else{
            return 1;
        }

}

function get_comment_list($allow_comment = TRUE,$file_path){
    $query = "SELECT * FROM comments VALUES ('$comment_id','$comment','$username') ";
    $result = mysql_query( $query );
    return $result;
}


function add_comment($comment_id,$comment,$username){
     $query = "INSERT INTO comments VALUES ('$comment_id','$comment','$username')";
     $result = mysql_query( $query );
    return $result;

}



function edit_comment($comment_id,$comment,$username){
 $query = "UPDATE comments VALUES ('$comment')";
     $result = mysql_query( $query );
    return $result;

}



function delete_comment($comment_id,$comment,$username){
    $query = "DELETE FROM comments VALUES ('$comment_id','$comment','$username')";
    $result = mysql_query( $query );
    return $result;
}


function check_Subscribe($subscribe_id,$channel_name,$username){

   $query = "SELECT * FROM subscribe VALUES('$subscribe_id','$channel_name','$username')";
  if(empty($row)){
        return 3;
    }
    else{
        return 1;
    }

}


function check_channel_exist($channel_name){
    $query = "SELECT * FROM account WHERE username='$channel_name' ";
    $result = mysql_query( $query );

    if (mysql_num_rows($result) == 0){
        return 0;
    }
    else {
        return 1;
    }
}


function insert_channel($channel_name, $file_path){
    $channel_name = mysql_real_escape_string($channel_name);
    $file_path = mysql_real_escape_string($file_path);

    $query = "INSERT INTO channels VALUES ('$channel_name', '$file_path') ";
    $result = mysql_query( $query );
    return $result;
}


function update_channel($channel_name, $file_path, $username){
    $channel_name = mysql_real_escape_string($channel_name);
    $file_path = mysql_real_escape_string($file_path);
    $username = mysql_real_escape_string($username);

    $check = "SELECT * FROM channels WHERE file_path = '$file_path' ";
    if (mysql_num_rows(mysql_query($check)) <= 1){
        insert_channel($channel_name, $file_path);
    }
    else {
        $query = "DELETE FROM channels WHERE file_path = '$file_path' ";
        $result = mysql_query($query);

        insert_channel($channel_name, $file_path);
        insert_channel($username, $file_path);
    }
}

function check_media_channel($file_path, $user_upload){
    $file_path = mysql_real_escape_string($file_path);

    $query = "SELECT channel_name FROM channels WHERE file_path='$file_path' AND NOT channel_name = '$user_upload' ";
    $result = mysql_query($query);

    if (mysql_num_rows($result) == 0){
        return "DEFAULT";
    }
    else {
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        return $row['channel_name'];
    }
}

function check_user_subscribe($channel_name, $username){
    if (!isset($username)){
        return 0;
    }
    else {
        $channel_name = mysql_real_escape_string($channel_name);
        $username = mysql_real_escape_string($username);

        $query = "SELECT * FROM subscribe WHERE channel_name = '$channel_name' AND username='$username' ";
        $result = mysql_query($query);

        if (mysql_num_rows($result) == 0){
            return 0;
        }
        else {
            return 1;
        }
    }

}



function insert_subscribe($channel_name, $username){
    $channel_name = mysql_real_escape_string($channel_name);
    $username = mysql_real_escape_string($username);

    $query = "INSERT INTO subscribe VALUES ('$channel_name', '$username')";
    $result = mysql_query($query);
}

function unsubscribe_channel($channel_name, $username){
    $channel_name = mysql_real_escape_string($channel_name);
    $username = mysql_real_escape_string($username);

    $query = "DELETE FROM subscribe WHERE channel_name = '$channel_name' AND username = '$username' ";
    $result = mysql_query($query);
}


function subscribe_channel($subscribe_id,$channel_name,$username){
   $query = "INSERT INTO subscribe VALUES('$subscribe_id','$channel_name','$username')";
   $result = mysql_query( $query );
    return $result;
}

function get_user_subscribe_list($username){
     $query = "SELECT * FROM subscribe WHERE username = '$username'";
     $result = mysql_query( $query );
     return $result;
}

function insert_view_block($file_path, $username, $blocked_username){
    $query = "INSERT INTO view_blocks VALUES ('$file_path', '$username', '$blocked_username')";
    $result = mysql_query( $query );
    return $result;
}

function insert_download_block($file_path, $username, $blocked_username){
    $query = "INSERT INTO download_blocks VALUES ('$file_path', '$username', '$blocked_username')";
    $result = mysql_query( $query );
    return $result;
}

function remove_view_block($file_path, $username, $unblocked_username){
    $query = "DELETE FROM view_blocks WHERE file_path = '$file_path' AND blocked_by='$username' AND blocked_username = '$unblocked_username' ";
    $result = mysql_query( $query );
    return $result;
}

function remove_download_block($file_path, $username, $unblocked_username){
    $query = "DELETE FROM download_blocks WHERE file_path = '$file_path' AND blocked_by='$username' AND blocked_username = '$unblocked_username' ";
    $result = mysql_query( $query );
    return $result;
}

function update_media_share_type($file_path){

    $file_path = mysql_real_escape_string($file_path);

    $check = "SELECT * FROM view_blocks where file_path = '$file_path' ";
    $check_result = mysql_query($check);
    if (mysql_num_rows($check_result) >0 ){
        $query = "UPDATE media SET share_type=0 WHERE file_path='$file_path' ";
        $result = mysql_query( $query );
    }
}



function check_view_block($file_path, $username_view, $user_upload, $share_type){
    if ($share_type == 1){
        return 1; // public
    }
    else {
        $query_block = "SELECT * FROM view_blocks WHERE file_path = '$file_path' AND blocked_username = '$username_view' ";
        $result = mysql_query($query_block);
        if (mysql_num_rows($result) > 0){ // the viewer is in the block view list
            return 0;
        }
        return 1;
    }
}


function check_download_block($file_path, $username_download, $user_upload, $share_type){
    $query_block = "SELECT * FROM download_blocks WHERE file_path = '$file_path' AND blocked_username = '$username_download' ";
    $result = mysql_query($query_block);
    if (mysql_num_rows($result) > 0){ // the viewer is in the block download list
        return 0;
    }
    else {
        return 1;
    }
}


function user_most_viewed($username){
    $query = "SELECT *, count(*) AS num FROM media, media_view
              WHERE media_view.username = '$username' AND media_view.file_path = media.file_path
              GROUP BY media_view.file_path
              ORDER BY num desc ";
    $result = mysql_query($query);

    return $result;
}


function user_most_recently_viewed($username){
    $query = "SELECT *, MAX(media_view.date_time) AS view_time FROM media, media_view
              WHERE media_view.file_path = media.file_path AND media_view.username='$username'
              GROUP BY media_view.file_path ORDER BY view_time desc ";

    $result = mysql_query($query);

    return $result;
}


function media_most_viewed(){
    $query = "SELECT *, count(*) AS num FROM media, media_view
              WHERE media_view.file_path = media.file_path
              GROUP BY media.file_path
              ORDER BY num desc ";
    $result = mysql_query($query);

    return $result;
}


function media_most_recently_viewed(){
    $query = "SELECT *, media_view.date_time AS view_time FROM media, media_view
              WHERE media_view.file_path = media.file_path
              ORDER BY view_time desc ";
    $result = mysql_query($query);

    return $result;
}


function find_media_title($file_path){
    $file_path = mysql_real_escape_string($file_path);
    $query = "SELECT * FROM media where file_path='$file_path'";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $title = $row['title'];

    return $title;
}

function multiexplode ($delimiters,$string) {
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}

function find_media_path($file_url){
    $file_url = multiexplode(array("?","&", "="),$file_url)[2];
    return $file_url;
}


function remove_file($file_path){

    $file_path = mysql_real_escape_string($file_path);

    $query = "DELETE FROM media_view WHERE file_path = '$file_path'";
    $result = mysql_query($query);

    $query = "DELETE FROM media_download WHERE file_path = '$file_path'";
    $result = mysql_query($query);

    $query = "DELETE FROM comments WHERE file_path = '$file_path'";
    $result = mysql_query($query);

    $query = "DELETE FROM channels WHERE file_path = '$file_path'";
    $result = mysql_query($query);

    $query = "DELETE FROM favorites WHERE file_path = '$file_path'";
    $result = mysql_query($query);

    $query = "DELETE FROM media_keywords WHERE file_path = '$file_path'";
    $result = mysql_query($query);

    $query = "DELETE FROM download_blocks WHERE file_path='$file_path'";
    $result = mysql_query($query);

    $query = "DELETE FROM view_blocks WHERE file_path='$file_path'";
    $result = mysql_query($query);

    $query = "DELETE FROM playlist WHERE file_path='$file_path'";
    $result = mysql_query($query);

    $query = "DELETE FROM rating WHERE file_path='$file_path'";
    $result = mysql_query($query);

    $query = "DELETE FROM group_discussion WHERE topic_id
              IN (SELECT topic_id FROM (
                  SELECT group_discussion.topic_id AS topic_id FROM group_discussion, group_topics
                  WHERE group_discussion.topic_id=group_topics.topic_id AND group_topics.file_path = '$file_path') AS c )";
    $result = mysql_query($query);

    $query = "DELETE FROM group_topics WHERE file_path='$file_path' ";
    $result = mysql_query($query);

    $query = "DELETE FROM media WHERE file_path='$file_path'";
    $result = mysql_query($query);
}


function media_comments($file_path){
    $file_path = mysql_real_escape_string($file_path);

    $query = "SELECT * FROM comments WHERE file_path = '$file_path' ORDER BY date_time desc ";
    $result = mysql_query($query);

    return $result;
}


function insert_comments($file_path, $username, $content){
    $file_path = mysql_real_escape_string($file_path);
    $username = mysql_real_escape_string($username);
    $content = mysql_real_escape_string($content);

    $query = "INSERT INTO comments (file_path, username, comment, date_time) VALUES ('$file_path', '$username', '$content', NOW() )";
    $result = mysql_query($query);
}

function delete_media_comment($comment_id){
    $query = "DELETE FROM comments WHERE comment_id = '$comment_id' ";
    $result = mysql_query($query);
}


function check_group_exist($username, $group_name){
    $group_name = mysql_real_escape_string($group_name);
    $username = mysql_real_escape_string($username);

    $query = "SELECT * FROM groups WHERE group_name = '$group_name' AND master = '$username' ";
    $result = mysql_query($query);

    if (mysql_num_rows($result) == 0){
        return 0;
    }
    else {
        return 1;
    }
}

function insert_groups($username, $group_name){
    $group_name = mysql_real_escape_string($group_name);
    $username = mysql_real_escape_string($username);

    $query = "INSERT INTO groups (group_name, master) VALUES ('$group_name', '$username') ";
    $result = mysql_query($query);

    $query = "INSERT INTO group_users (group_id, username)
              SELECT group_id, master FROM groups WHERE master='$username' AND group_name='$group_name' ";
    $result = mysql_query($query);
}


function check_topic_exist($username, $group_id, $topic_name, $file_url){
    $username = mysql_real_escape_string($username);
    $topic_name = mysql_real_escape_string($topic_name);
    $file_url = mysql_real_escape_string($file_url);

    $query = "SELECT * FROM group_topics WHERE master='$username' AND group_id = '$group_id' AND topic_name = '$topic_name' AND file_path = '$file_url' ";
    $result = mysql_query($query);

    if (mysql_num_rows($result) == 0){
        return 0;
    }
    else {
        return 1;
    }
}

function insert_group_topics($username, $group_id, $topic_name, $file_url){
    $username = mysql_real_escape_string($username);
    $topic_name = mysql_real_escape_string($topic_name);
    $file_url = mysql_real_escape_string($file_url);

    $query = "INSERT INTO group_topics (topic_name, group_id, master, date_time, file_path)
              VALUES ('$topic_name', '$group_id', '$username', NOW(), '$file_url')";
    $result = mysql_query($query);
}



function check_user_valid($nickname){
    $username = mysql_real_escape_string($nickname);
    $query = "SELECT * FROM account WHERE nickname='$nickname'";
    $result = mysql_query($query);

    if (mysql_num_rows($result) == 0){
        return 0;
    }
    else {
        return 1;
    }
}



function check_user_exist($username, $group_id){
    $username = mysql_real_escape_string($username);
    $query = "SELECT * FROM group_users WHERE username='$username' AND group_id='$group_id' ";
    $result = mysql_query($query);

    if (mysql_num_rows($result) == 0){
        return 0;
    }
    else {
        return 1;
    }
}



function insert_group_users($group_id, $username){
    $username = mysql_real_escape_string($username);

    $query = "INSERT INTO group_users VALUES ('$group_id', '$username')";
    $result = mysql_query($query);
}


function delete_group($group_id){
    $query = "DELETE FROM group_users WHERE group_id = '$group_id' ";
    $result = mysql_query($query);

    $query = "DELETE FROM group_discussion WHERE topic_id
              IN (SELECT topic_id FROM (
                  SELECT group_discussion.topic_id AS topic_id FROM group_discussion, group_topics
                  WHERE group_discussion.topic_id=group_topics.topic_id AND group_topics.group_id = '$group_id') AS c )";
    $result = mysql_query($query);

    $query = "DELETE FROM group_topics WHERE group_id = '$group_id' ";
    $result = mysql_query($query);

    $query = "DELETE FROM groups WHERE group_id = '$group_id' ";
    $result = mysql_query($query);
}


function delete_group_user($group_id, $username){
    // $query = "DELETE FROM group_users WHERE group_id = '$group_id' AND username='$username' ";
    // $result = mysql_query($query);

    $query = "DELETE FROM group_discussion WHERE group_discussion.username='$username' AND topic_id
              IN (SELECT topic_id FROM (
                  SELECT group_discussion.topic_id AS topic_id FROM group_discussion, group_topics
                  WHERE group_discussion.topic_id=group_topics.topic_id AND group_topics.group_id = '$group_id') AS c )";
    $result = mysql_query($query);

    $query = "DELETE FROM group_users WHERE group_id = '$group_id' AND username = '$username' ";
    $result = mysql_query($query);
}

function delete_group_topic($topic_id){
    $query = "DELETE FROM group_discussion WHERE topic_id='$topic_id'";
    $result = mysql_query($query);

    $query = "DELETE FROM group_topics WHERE topic_id = '$topic_id'";
    $result = mysql_query($query);
}


function insert_group_discussion($topic_id, $username, $comment){
    $username = mysql_real_escape_string($username);
    $comment = mysql_real_escape_string($comment);

    $query = "INSERT INTO group_discussion (topic_id, username, comment, date_time) VALUES ('$topic_id', '$username', '$comment', NOW())";
    $result = mysql_query($query);
}

function check_user_rating($username, $file_path){
    $username = mysql_real_escape_string($username);
    $file_path = mysql_real_escape_string($file_path);

    $query = "SELECT * FROM rating WHERE username='$username' AND file_path = '$file_path' ";
    if (mysql_num_rows(mysql_query($query)) > 0){
        return 1;
    }
    else {
        return 0;
    }
}


function insert_rating($score, $username, $file_path){
    $username = mysql_real_escape_string($username);
    $file_path = mysql_real_escape_string($file_path);

    $query = "INSERT INTO rating (file_path, username, rating) VALUES ('$file_path', '$username', '$score')";
    $result = mysql_query($query);

}


function update_rating($score, $username, $file_path){
    $username = mysql_real_escape_string($username);
    $file_path = mysql_real_escape_string($file_path);

    $query = "UPDATE rating SET rating='$score' WHERE username = '$username' AND file_path = '$file_path' ";
    $result = mysql_query($query);
}


function cal_media_rating($file_path){
    $file_path = mysql_real_escape_string($file_path);
    $query = "SELECT file_path, COUNT(username) as num, SUM(rating) as total FROM rating WHERE file_path = '$file_path' ";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);

    if ($row['num'] == 0 ){
        return array(0, 0);
    }
    else {
        $num = $row['num'];
        $total = $row['total'];
        $avg = $total/(float)$num;

        return array(number_format((float)$avg, 1, '.', ''), $num);
    }
}

function find_nickname ($username){
    $username = mysql_real_escape_string($username);
    $query = "SELECT * FROM account WHERE username = '$username' ";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $nickname = $row['nickname'];

    return $nickname;
}

function find_username ($nickname){
    $nickname = mysql_real_escape_string($nickname);
    $query = "SELECT * FROM account WHERE nickname = '$nickname' ";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $username = $row['username'];

    return $username;
}

?>
