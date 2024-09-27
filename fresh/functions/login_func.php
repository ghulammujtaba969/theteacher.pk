<?php
//----------------------------------------------------------------------------
session_start();
//----------------------------------------------------------------------------
//**********Admin Area Login checking ***********************/
function checkCpanelLMSALogin() {
// if the session id is not set, redirect to login page
	if(!isset($_SESSION['LOGINIDA_SSS'])) {
		header("Location: login.php");
		exit;
	}
	// For admin logout
	if(isset($_GET['logout'])) {
		panelLMSALogout();
	}
}

//***************Function for admin login*********************
function cpanelLMSAuserLogin() {

	require_once ("dbsetting/lms_vars_config.php");
	require_once ("dbsetting/classdbconection.php");
	require_once ("functions/functions.php");
	$dblms = new dblms();
//******* if we found an error save the error message in this variable**********
	$errorMessage = '';
	$emply_user   = cleanvars($_POST['login_id']);
	$emply_pass1  = cleanvars($_POST['login_pass']);
	$emply_pass3  = md5($emply_pass1);

//*************** first, make sure the adminname & password are not empty******
	if($emply_user == '') {
		$errorMessage = 'You must enter your User Name';
	} else if ($emply_pass3 == '') {
		$errorMessage = 'You must enter the User Password';
	} else {
// **************Check the admin name and password exist*****************
		$sqllms	= $dblms->querylms("SELECT * FROM ".USERS."
											 WHERE emply_username 	= '".$emply_user."' 
											 AND emply_userpass		= '".$emply_pass3."' 
											 AND emply_status		= '1' 
											 AND emply_access 		= '1' LIMIT 1");
//************** if the admin name and password exist then **************** 	
if (mysqli_num_rows($sqllms) == 1) {
//--------------------------------------------------------------------
	$row = mysqli_fetch_array($sqllms); 
//--------------------------------------------------------------------
$remarks = 'Login to Software From Credentials';
//--------------------------------------------------------------------
		$sqllms	= $dblms->querylms("INSERT INTO ".ADMINS_HISTORY." (
													id_emply								, 
													ip_address								,
													computer_browser						,
													dated									,
													remarks
													)
													VALUES
													(
													'".$row['emply_id']."'					, 
													'".$ip."'								, 
													'".$obj->showInfo('browser') . $obj->showInfo('version')."'	, 
													'".date("Y-m-d H:i:s")."'								, 
													'".cleanvars($remarks)."'						
														)"
													);
//*********** Store admin id into session ************************		
	$_SESSION['LOGINIDA_SSS']   	   	  = $row['emply_id'];
	$_SESSION['LOGINUSERA_SSS'] 	 	  = $row['emply_username'];
	$_SESSION['LOGINFNAMEA_SSS']  	      = $row['emply_fullname'];
	$_SESSION['LOGINFATHERNAME_SSS']      = $row['emply_fathername'];
	$_SESSION['LOGINCNIC_SSS']	  	      = $row['emply_cnic'];
	$_SESSION['LOGINPHONE_SSS']   	      = $row['emply_phone'];
	$_SESSION['LOGINEMAIL_SSS']  	      = $row['emply_email'];
	$_SESSION['LOGINTYPE_SSS']  	      = $row['emply_type'];
	$_SESSION['LOGINCAMPUS_SSS']  	      = $row['id_campus'];
// ***************Login time when the admin login **************


//**************Store into session url  Last page visit*******************  
	header("Location: index.php");

	
	} else {

//********** admin name and password dosn't much *******************
	$errorMessage = '<span style="color: red; text-align:center;"><p> Invalid User Name or Password.</p></span>';
	}		
}

return $errorMessage;
//mysqli_close($link);
}

//****************Logout Function for admin site *******************************
function panelLMSALogout() {
	if (isset($_SESSION['LOGINIDA_SSS'])) {
		unset($_SESSION['LOGINIDA_SSS']);
		unset($_SESSION['LOGINUSERA_SSS']);
		unset($_SESSION['LOGINFNAMEA_SSS']);
		unset($_SESSION['LOGINTYPE_SSS']);
		unset($_SESSION['LOGINFINANCE_SSS']);
		unset($_SESSION['LOGINTUTORS_SSS']);
		session_destroy();
	}
	header("Location: login.php");
	exit;
}
?>