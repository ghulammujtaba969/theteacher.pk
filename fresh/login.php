<?php 

	define('TITLE_HEADER'		, 'School Management System');
	define("SITE_NAME"			, "SCHOOL MANAGEMENT SYSTEM");
	define("SITE_ADDRESS"		, "365-M Model Town, Lahore");
	define("COPY_RIGHTS"		, "EON IT SOLUTIONS");
	define("COPY_RIGHTS_ORG"	, "&copy; ".date("Y")." - All Rights Reserved.");
	define("COPY_RIGHTS_URL"	, "http://www.google.com/");
//---------------------------------------------------
include "functions/login_func.php";
//---------------------------------------------------
if(isset($_SESSION['LOGINIDA_SSS'])) {
header("Location: index.php");	
} else { 
$login_id = (isset($_POST['login_id']) && $_POST['login_id'] != '') ? $_POST['login_id'] : '';	
if (isset($_POST['login_id'])) {
	$result = cpanelLMSAuserLogin();
	if ($result != '') {
		$errorMessage = $result;
	}
}
//----------------------------------------------------------------------
echo'
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<title> '.TITLE_HEADER.' </title>

<link rel="shortcut icon" href="assets/img/favicon.png">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,500;0,600;0,700;1,400&display=swap">

<link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css">

<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="main-wrapper login-body">
<div class="login-wrapper">
<div class="container">
<div class="loginbox">
	<div class="login-left">
		<img class="img-fluid" src="assets/img/logo-white.png" alt="Logo">
	</div>
<div class="login-right">
<div class="login-right-wrap">
<h1>Login</h1>
<p class="account-subtitle">Access to our dashboard</p>';
//------------------------------------------------------------------------------------
if(isset($errorMessage)) {
	echo $errorMessage;
}
//------------------------------------------------------------------------------------
echo '

<form class="form-horizontal" id="frmLogin" name="frmLogin" method="post">

	<div class="form-group">
		<input class="form-control" type="text" id="login_id" name="login_id" value="'.$login_id.'" placeholder="Email">
	</div>

	<div class="form-group">
		<input class="form-control" d="inputPassword" name="login_pass" type="password" placeholder="Password">
	</div>
	
	<div class="form-group">
		<button class="btn btn-primary btn-block" type="submit">Login</button>
	</div>
</form>

<div class="text-center forgotpass"><a href="forgot-password.html">Forgot Password?</a></div>

	<div class="login-or">
		<span class="or-line"></span>
		<span class="span-or">or</span>
	</div>

<div class="social-login">
	<span>Login with</span>
	<a href="#" class="facebook"><i class="fab fa-facebook-f"></i></a><a href="#" class="google"><i class="fab fa-google"></i></a>
</div>

<div class="text-center dont-have">Don’t have an account? <a href="register.html">Register</a></div>
</div>
</div>
</div>
</div>
</div>
</div>


<script src="assets/js/jquery-3.5.1.min.js"></script>

<script src="assets/js/popper.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>

<script src="assets/js/script.js"></script>
</body>
</html>';
//----------------------------------------------------------------------
}
//----------------------------------------------------------------------
?>