<?php
session_start();
include_once "function.php";



if(isset($_POST['download'])) {

    $file_url = $_POST['download'];
    $file_name = basename($file_url);

    if (!isset($_SESSION['username'])){
        header("Location: login.php?file_path=$file_url&index='Download'");
    }
    else{
        $username_download = $_SESSION['username'];
        $ip=$_SERVER['REMOTE_ADDR'];

        if (file_exists($file_url)) {

            //  CHECK DOWNLOAD BLOCKS
            $query = "SELECT * FROM media WHERE file_path='$file_url' ";
            $result = mysql_query($query);
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            $user_upload = $row['username'];
            $share_type = $row['share_type'];

            $check = check_download_block($file_url, $username_download, $user_upload, $share_type);
            
            if ($check == 0){
                echo "<p style='font-size:22px; color:green;'>You are not authorized to download this media. </p>";
            }
            else{
                insert_download($file_url, $username_download, $ip);
                // update_download_count($file_url);

    	        header('Content-Description: File Transfer');
    	        header('Content-Type: application/octet-stream');
    	        //    header('Content-Disposition: attachment; filename='.basename($file));
                header("Content-disposition: attachment; filename=\"".$file_name."\"");
        	    header('Content-Transfer-Encoding: binary');
        	    header('Expires: 0');
        	    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        	    header('Pragma: public');
        	    header('Content-Length: ' . filesize($file_url));
        	    ob_clean();
        	    flush();
        	    readfile($file_url);
        	    exit;
    	    }
        }
    }
}







?>
