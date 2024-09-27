<?php
class dblms {
	private $lms_hostname;
	private $lms_username;
	private $lms_password;
	private $lms_database;
	private $connectlms;
	private $select_dblms;

public function __construct() {

	$this->lms_hostname = LMS_HOSTNAME;
	$this->lms_username = LMS_USERNAME;
	$this->lms_password = LMS_USERPASS;
	$this->lms_database = LMS_NAME;

}

public function open_connectionlms() {
	
	try	{

		$this->connectlms 	= mysqli_connect($this->lms_hostname, $this->lms_username, $this->lms_password, $this->lms_database) or die (print "Class Database: Error while connecting to DB (link)");
		
		
		//$this->select_dblms = mysql_select_db($this->lms_database) or die (print "Class Database: Error while selecting DB");
	}
	
	catch(exception $e)	{
		return $e;
	}

}

public function close_connectionlms() {

	try	{
		mysqli_close($this->connectlms);
	}

	catch(exception $e)	{
		return $e;
	}

}

public function querylms($sqllms) {

	try	{

		$this->open_connectionlms();
		$sqllms = mysqli_query($this->connectlms, $sqllms);

	}

	catch(exception $e)	{
		return $e;
	}

	return $sqllms;
	$this->close_connectionlms();
}


//------------ Get Latest Inserted id ---
public function lastestid() {
	$lastid = mysqli_insert_id($this->connectlms);
	return $lastid;
	$this->close_connectionlms();
}
}

//---------------ORG------------------------------------------------------
?>