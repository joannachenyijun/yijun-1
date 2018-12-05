<?php
include_once "mysqlClass.inc.php";
// include_once "function.php";
class Account {
    private $username;
    private $nickname;
	private $email;
	private $fname;
	private $lname;
    private $password;
	private $gender;
	private $dob;
    function create($username, $nickname, $email, $fname, $lname, $password, $gender, $dob){
        $query = sprintf("insert into account values ('%s','%s','%s','%s','%s','%s','%s','%s')",
        mysql_real_escape_string($username),
        mysql_real_escape_string($nickname),
        mysql_real_escape_string($email),
        mysql_real_escape_string($fname),
        mysql_real_escape_string($lname),
        mysql_real_escape_string($password),
        mysql_real_escape_string($gender),
        mysql_real_escape_string($dob));
    	$result = mysql_query( $query );
        if($result){
            return 1;
        }
        else{
            die ("Could not insert into the database: <br />". mysql_error());
        }
    }
    // public function GetUsername() {
	// 	return $this->username;
	// }
    //
    // public function GetEmail() {
	// 	return $this->email;
	// }
    //
    // public function GetPassword() {
	// 	return $this->password;
	// }
    //
    // public function UpdateUsername($username_old, $username_new){
    //     $query = sprintf("UPDATE account SET username = '%s' WHERE username = '%s'",
    //     mysql_real_escape_string($username_new),
    //     mysql_real_escape_string($username_old)
    // );
    // }
}
 ?>
