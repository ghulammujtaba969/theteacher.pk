<?php
//----------------------------------------------------------------------------
	error_reporting(0);
	ob_start();
	ob_clean();
//----------------------------------------------------------------------------
	//date_default_timezone_set("Asia/Karachi");
	ini_set( 'date.timezone', 'Asia/Karachi' );
//----------------------------------------------------------------------------
if($_SERVER['HTTP_HOST']=="localhost" or $_SERVER['HTTP_HOST']=="127.0.0.1"){
//----------------------------------------------------------------------------	
//Local 
//----------------------------------------------------------------------------
	define('LMS_HOSTNAME'			, 'localhost');
	define('LMS_NAME'				, 'u921830511_lms');
	define('LMS_USERNAME'			, 'root');
	define('LMS_USERPASS'			, '');
//----------------------------------------------------------------------------
} else {
//----------------------------------------------------------------------------
// Live 
//$dblms->lastestid();	
//----------------------------------------------------------------------------	

	define('LMS_HOSTNAME'			, 'localhost');
	define('LMS_NAME'				, 'u921830511_lms');
	define('LMS_USERNAME'			, 'u921830511_lms_user');
	define('LMS_USERPASS'			, 'X3t1=69P8>Zc');

}
//----------------------------------------------------------------------------
	define('CATAGORIES'			   	, 'categories');
	define('SUB_CATAGORIES'	   		, 'sub_categories');
	define('CHILD_CATAGORIES'		, 'child_categories');
	define('USERS'					, 'users');	
	
	define('COURSES'				, 'elearn_courses');	
	define('PLANS'					, 'elearn_plans');	
	define('INQUIRY'				, 'elearn_inquiry');
	define('COUNTRIES'				, 'elearn_countries');
	

// =====================================================================
	

//--------------------------------------------------
	$ip	  			 = (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != '') ? $_SERVER['REMOTE_ADDR'] : '';
	$do	  			 = (isset($_REQUEST['do']) && $_REQUEST['do'] != '') ? $_REQUEST['do'] : '';
	$view 			 = (isset($_REQUEST['view']) && $_REQUEST['view'] != '') ? $_REQUEST['view'] : '';
	$page			 = (isset($_REQUEST['page']) && $_REQUEST['page'] != '') ? $_REQUEST['page'] : '';
	$Limit			 = (isset($_REQUEST['Limit']) && $_REQUEST['Limit'] != '') ? $_REQUEST['Limit'] : '';
//--------------------------------------------------
	define('LMS_IP'				, $ip);
	define('LMS_DO'				, $do);
	define('LMS_EPOCH'			, date("U"));
	define('LMS_VIEW'			, $view);
	define('TITLE_HEADER'		, 'The Teacher.pk');
	define("SITE_NAME"			, "The Teacher.pk");
	define("SITE_ADDRESS"		, "365-M Model Town, Lahore");
	define("DEVELOPED_BY"		, "Developed By");
	define("VERSION"			, "v 4.0");
	define("COPY_RIGHTS"		, "A&B SOLUTIONS");
	define("COPY_RIGHTS_ORG"	, "&copy; ".date("Y")." - All Rights Reserved.");
	define("COPY_RIGHTS_URL"	, "http://www.google.com/");
//--------------------------------------------------
?>