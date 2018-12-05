<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Comment system using php and mysql</title>
</head>
<body>
<form name="comment" method="post" action="comment.php" onSubmit="return validation()">
<table width="500" border="0" cellspacing="3" cellpadding="3" style="margin:auto;">
  <tr>
    <td align="right" id="one">Name :<span style="color:#F00;">*</span></td>
    <td><input type="text" name="namename" id="tnameid"></td>
  </tr>
  <tr>
    <td align="right" id="one">Work :<span style="color:#F00;">*</span></td>
    <td><input type="text" name="job" id="tjobid"></td>
  </tr>
  <tr>
    <td align="right" id="one"></td>
    <td><textarea name="message" id="tmessageid"></textarea></td>
  </tr>
  <tr>
  <td align="right" id="one"></td>
  <td><input type="submit" name="submit" id="submit" value="Submit Comment"></td>
  </tr>
</table>
</form>
</body>
</html>


<?php
include("config.php");
if(isset($_POST['submit']))
{
 $name=$_POST['namename'];
 $job=$_POST['job'];
 $message=$_POST['message'];
 $insert=mysql_query("insert into comment
                (name,job,message)values
                ('$name','$job','$message')")or die(mysql_error());
 header("Location:index.php");
 }
?>

<?php
include("config.php");
$select=mysql_query("select * from comment");
while($row=mysql_fetch_array($select))
{
 echo "<div id='sty'>";
 echo "<img src='files/fav icon.png'"."' width='50px' 
                                                height='50px' 
                                                align='left' />";
 echo "<div id='nameid'>".$row['name']."</div>";
 echo "<div id='msgid'>".$row['message']."</div>";
 echo "</div><br />";
}
?>


