<?php
//--------------------------------------------------
	include "dbsetting/lms_vars_config.php";
	include "dbsetting/classdbconection.php";
	$dblms = new dblms();
	// include "functions/login_func.php";
	// checkCpanelLMSALogin();
//--------------------------------------------------
if(isset($_SESSION['LOGINIDA_SSS'])) {
	header("Location: Dashboard.php");
//--------------------------------------------------	
} else { 
	header("Location: Home.php");
}
//--------------------------------------------------
?>