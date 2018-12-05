<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	session_start();
	include_once "function.php";
    // include_once "browse_header.php";

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
<title>Media browse</title>
<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript">

function blockCheck(blockCheck, blockYes) {
    if (document.getElementById(blockCheck).checked) {
        document.getElementById(blockYes).style.display = 'block';
    }
    else document.getElementById(blockYes).style.display = 'none';
}


</script>

<style>
</style>

</head>

<body>


<?php
include_once "browse_header.php";
?>


<p style="font-size:20px; color:blue;">Uploads</p>
<?php

if (isset($_POST["delete"])){
    $file_path = $_GET['file_path'];
    if (!unlink($file_path)){
        echo ("Error deteling $file_path");
    }
    else {
        $title = $_GET["title"];
        $file_path = $_GET['file_path'];
        echo ("$title has been deleted!");
        remove_file($file_path);
    }
}


$query = "SELECT * FROM media WHERE media.username = '$username' ORDER BY date_time desc ";
$result = mysql_query($query);
if (!$result){
    die ("Could not query the media table in the database: <br />". mysql_error());
}
?>

<table cellpadding="10">
<?php

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
    $file_path = $row["file_path"];
    $title = $row["title"];
    $date_time = $row["date_time"];
    $type = $row["type"];

    $form_id = "my_media_".$file_path;
    $block_id = "my_block_".$file_path;

?>
    <tr>
        <td width='120px' valign='top'>
            <div>
            <a href='media.php?file_path=<?php echo $file_path; ?>'><video width='120px'><source src=<?php echo $file_path;?> type=<?php echo $type; ?>></videl></a>
            </div>
        </td>


        <td valign='top' align='left'>
            <?php echo $title;?><br/>
            <?php echo $date_time;?><br/>
            <div>
                <FORM action="media_edit.php?file_path=<?php echo $file_path;?>" method="post">
                    <button name="edit" type="submit" value="<?php echo $file_path;?>">Edit</button>
                </FORM>
            </div>
        </td>


        <td valign='top'>
            <FORM action="media_block.php?file_path=<?php echo $file_path.'&title='.$title; ?>" method="post" id=<?php echo $form_id ;?> name=<?php echo $form_id ;?>>
                <button name="blocks" type="submit" value="<?php echo $file_path;?>">Blocks</button>
            </FORM>
        </td>

        <td valign='top'>
            <FORM action="media_download.php" method="post">
                <button name="download" type="submit" value="<?php echo $file_path;?>">Download</button>
            </FORM>
        </td>
        <td valign='top'>
            <FORM action="my_media.php?file_path=<?php echo $file_path.'&title='.$title; ?>" method="post">
                <button name="delete" type="submit" value="delete">Delete</button>
            </FORM>
        </td>
    </tr>
    <tr><td colspan="5"><hr></td></tr>

<?php
}
?>
</table>


</body>
</html>
