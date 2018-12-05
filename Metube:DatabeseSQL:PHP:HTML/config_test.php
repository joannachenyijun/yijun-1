<?
$servername="mysql1.cs.clemson.edu";

$dbusername="metube_G5";
$dbpassword="TIVOLII19891223ls$";

$dbname="metube_G5";



////////////////////////////////////////
////// DONOT EDIT BELOW  /////////
///////////////////////////////////////

connecttodb($servername,$dbname,$dbusername,$dbpassword);
function connecttodb($servername,$dbname,$dbuser,$dbpassword)
{
global $link;
$link=mysql_connect ("$servername","$dbuser","$dbpassword");
if(!$link){die("Could not connect to MySQL");}
mysql_select_db("$dbname",$link) or die ("could not open db".mysql_error());
}
?>