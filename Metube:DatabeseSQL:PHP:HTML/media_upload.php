<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Media Upload</title>


<script type="text/javascript">
function yesnoCheck() {
    if (document.getElementById('yesCheck').checked) {
        document.getElementById('ifYes').style.display = 'block';
    }
    else document.getElementById('ifYes').style.display = 'none';

}

</script>

</head>


<body>



<?php
if (isset($_SESSION["username"])){
    $username = $_SESSION["username"];
    $nickname = $_SESSION['nickname'];
}
else{
    header("Location: index_header.php?index='Upload_files'");
}

?>


<form method="POST" action="media_upload_process.php" enctype="multipart/form-data" id="media_upload_process" >

    <table border="0">
        <tr>
            <td bgcolor="grey" colspan="2"><font color="white">Upload A Media<font/></td>
        <tr>
            <td ><textarea rows="1" cols="50" name="title" form="media_upload_process" placeholder="Title"></textarea></td>
            <td >Category&nbsp;&nbsp;
                <select name="category">
                    <option value="music">Music</option>
                    <option value="gaming">Gaming</option>
                    <option value="movies">Movies</option>
                    <option value="tv_shows">TV shows</option>
                    <option value="news">News</option>
                    <option value="pets">Pets/Animals</option>
                    <option value="comedy">Comedy</option>
                </select><br />
            </td>
        <tr>
            <td >
                <textarea rows="4" cols="50" name="tags" form="media_upload_process" placeholder="Tags"></textarea>
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
                <textarea rows="11" cols="50" name="description" form="media_upload_process" placeholder="Description"></textarea>
            </td>
            <td >
                Add to playlist<br/>
                <!-- <input type="text" name="search_playlist" placeholder="&#43;playlist name"> -->
                <textarea rows="1" cols="20" name="addPlaylist" form="media_upload_process" placeholder="&#43;playlist name"></textarea>
                <br/><br/>
            <!-- </td>
            <td > -->
                Public <input type="radio" onclick="javascript:yesnoCheck();" name="yesno" id="noCheck" value=1>
                Private <input type="radio" onclick="javascript:yesnoCheck();" name="yesno" id="yesCheck" value=0>
                <div id="ifYes" style="display:none">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Only you can view <br/>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea rows="1" cols="30" name="share_users" form="media_upload_process" placeholder="&#43;Share with contacts"></textarea><br/>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea rows="1" cols="15" name="share_groups" form="media_upload_process" placeholder="&#43;A group"></textarea>
                    &nbsp;<textarea rows="1" cols="20" name="group_master" form="media_upload_process" placeholder="&#43;The group master"></textarea><br/>
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

    <!-- <input  align="top" type="text" name="message" style="width: 300px; height: 200px;" value="Describe the media"> -->
    <!-- $maxfilesize=1024*1024*10; -->
    <input type="hidden" name="MAX_FILE_SIZE" value="31457280" />
    Add a Media: <label style="color:#663399"><em> (Each file limit 30M)</em></label><br/>
    <input name="file" type="file" size="50" />
    <!-- <input name="thumbnails" type="file" size="50" /> -->

    <input value="Upload" name="submit" type="submit" /><input value="Cancel" name="cancel" type="submit" />
    </p>


 </form>

</body>
</html>
