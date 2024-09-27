<?php 

	include "dbsetting/lms_vars_config.php";
	include "dbsetting/classdbconection.php";
	$dblms = new dblms();
	include "functions/login_func.php";
	include "functions/functions.php";
	checkCpanelLMSALogin();
//----------------------------------------------------------------
include_once("include/header.php");
//----------------------------------------------------------------
$sqllms  = $dblms->querylms("SELECT * FROM ".ADMINS." rep WHERE emply_id = '".$_SESSION['LOGINIDA_SSS']."'  ");										
$rowstd = mysqli_fetch_array($sqllms);
//----------------------------------------------------------------------------

echo'

<div class="page-wrapper">
<div class="content container-fluid">

<div class="page-header">
<div class="row">
<div class="col">
<h3 class="page-title">Profile</h3>
<ul class="breadcrumb">
<li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
<li class="breadcrumb-item active">Profile</li>
</ul>
</div>
</div>
</div>

<div class="row">
<div class="col-md-12">
<div class="profile-header">
<div class="row align-items-center">
<div class="col-auto profile-image">
<a href="#">
<img class="rounded-circle" alt="User Image" src="'; if ($rowstd['emply_photo'] != null) { echo 'photos/Admin/'.$rowstd['emply_photo'].''; }else{ echo 'assets/img/user.jpg'; } echo'">
</a>
</div>
<div class="col ml-md-n2 profile-user-info">
<h4 class="user-name mb-0">  '.$rowstd['emply_fullname'].' </h4>
<h6 class="text-muted">'.get_admtypes($rowstd['emply_type']).'</h6>
<div class="user-Location"><i class="fas fa-map-marker-alt"></i>'.$rowstd['emply_postaladdress'].'</div>
</div>
<div class="col-auto profile-btn">
<a href="" class="btn btn-primary">
Edit
</a>
</div>
</div>
</div>
<div class="profile-menu">
<ul class="nav nav-tabs nav-tabs-solid">
<li class="nav-item">
<a class="nav-link active" data-toggle="tab" href="#per_details_tab">About</a>
</li>
<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#password_tab">Password</a>
</li>
<li class="nav-item">
<a class="nav-link" data-toggle="tab" href="#skills_tab">Skills</a>
</li>
</ul>
</div>
<div class="tab-content profile-tab-cont">

<div class="tab-pane fade show active" id="per_details_tab">

<div class="row">
<div class="col-lg-9">
<div class="card">
<div class="card-body">
<h5 class="card-title d-flex justify-content-between">
<span>Personal Details</span>
<a class="edit-link" data-toggle="modal" href="#edit_personal_details"><i class="far fa-edit mr-1"></i>Edit</a>
</h5>
<div class="row">
<p class="col-sm-3 text-muted text-sm-right mb-0 mb-sm-3">Name</p>
<p class="col-sm-9"> '.$rowstd['emply_fullname'].' </p>
</div>
<div class="row">
<p class="col-sm-3 text-muted text-sm-right mb-0 mb-sm-3">Date of Birth</p>
<p class="col-sm-9"> '.($rowstd['emply_dob']).' </p>
</div>
<div class="row">
<p class="col-sm-3 text-muted text-sm-right mb-0 mb-sm-3">Date of Join</p>
<p class="col-sm-9"> '.($rowstd['emply_joinsdate']).' </p>
</div>
<div class="row">
<p class="col-sm-3 text-muted text-sm-right mb-0 mb-sm-3">Email ID</p>
<p class="col-sm-9">'.($rowstd['emply_email']).'</p>
</div>
<div class="row">
<p class="col-sm-3 text-muted text-sm-right mb-0 mb-sm-3">Mobile</p>
<p class="col-sm-9">'.($rowstd['emply_phone']).'</p>
</div>
<div class="row">
<p class="col-sm-3 text-muted text-sm-right mb-0">Address</p>
<p class="col-sm-9 mb-0">'.$rowstd['emply_postaladdress'].'</p>
</div>
</div>
</div>
</div>

<div class="col-lg-3">

<div class="card">
<div class="card-body">
<h5 class="card-title d-flex justify-content-between">
<span>Account Status</span>
<a class="edit-link" href="#"><i class="far fa-edit mr-1"></i> Edit</a>
</h5>
<button class="btn btn-success" type="button"><i class="fe fe-check-verified"></i> Active</button>
</div>
</div>


<div class="card">
<div class="card-body">
<h5 class="card-title d-flex justify-content-between">
<span>Skills </span>
<a class="edit-link" href="#"><i class="far fa-edit mr-1"></i> Edit</a>
</h5>
<div class="skill-tags">
<span>Html5</span>
<span>CSS3</span>
<span>WordPress</span>
<span>Javascript</span>
<span>Android</span>
<span>iOS</span>
<span>Angular</span>
<span>PHP</span>
</div>
</div>
</div>

</div>
</div>
<div class="row">
	<div class="col-12 col-sm-12">
	<label>My Skill Levels are: <span class="badge badge-primary" style="user-select: auto;">New</span></label>
	<br/>
		<div class="input-group mb-3 base" style="justify-content: space-evenly;align-items: center;">
			<div class="input-group-prepend">
				<span style="padding-right: 37px;" class="input-group-text" id="inputGroup-sizing-default">English Writer: </span>
			</div>
				&nbsp;
				&nbsp;
				&nbsp;
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="0" name="eng_writing_skill">
				<span class="new-control-indicator"></span> None
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="25" name="eng_writing_skill">
				<span class="new-control-indicator"></span>Basic
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="50" name="eng_writing_skill">
				<span class="new-control-indicator"></span>Medium
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="75" name="eng_writing_skill">
				<span class="new-control-indicator"></span>Expert
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="100" name="eng_writing_skill">
				<span class="new-control-indicator"></span>Advance
				</label>
			</div>
		</div>
		<div class="input-group mb-3 base" style="justify-content: space-evenly;align-items: center;">
			<div class="input-group-prepend">
				<span style="padding-right: 37px;" class="input-group-text" id="inputGroup-sizing-default">English Writer: </span>
			</div>
				&nbsp;
				&nbsp;
				&nbsp;
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="0" name="ur_writing_skill">
				<span class="new-control-indicator"></span> None
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="25" name="ur_writing_skill">
				<span class="new-control-indicator"></span>Basic
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="50" name="ur_writing_skill">
				<span class="new-control-indicator"></span>Medium
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="75" name="ur_writing_skill">
				<span class="new-control-indicator"></span>Expert
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="100" name="ur_writing_skill">
				<span class="new-control-indicator"></span>Advance
				</label>
			</div>
		</div>
		<div class="input-group mb-3 base" style="justify-content: space-evenly;align-items: center;">
			<div class="input-group-prepend">
				<span style="padding-right: 37px;" class="input-group-text" id="inputGroup-sizing-default">English Writer: </span>
			</div>
				&nbsp;
				&nbsp;
				&nbsp;
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="0" name="video_editing_skill">
				<span class="new-control-indicator"></span> None
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="25" name="video_editing_skill">
				<span class="new-control-indicator"></span>Basic
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="50" name="video_editing_skill">
				<span class="new-control-indicator"></span>Medium
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="75" name="video_editing_skill">
				<span class="new-control-indicator"></span>Expert
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="100" name="video_editing_skill">
				<span class="new-control-indicator"></span>Advance
				</label>
			</div>
		</div>
		<div class="input-group mb-3 base" style="justify-content: space-evenly;align-items: center;">
			<div class="input-group-prepend">
				<span style="padding-right: 37px;" class="input-group-text" id="inputGroup-sizing-default">English Writer: </span>
			</div>
				&nbsp;
				&nbsp;
				&nbsp;
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="0" name="graphic_designing_skill">
				<span class="new-control-indicator"></span> None
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="25" name="graphic_designing_skill">
				<span class="new-control-indicator"></span>Basic
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="50" name="graphic_designing_skill">
				<span class="new-control-indicator"></span>Medium
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="75" name="graphic_designing_skill">
				<span class="new-control-indicator"></span>Expert
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="100" name="graphic_designing_skill">
				<span class="new-control-indicator"></span>Advance
				</label>
			</div>
		</div>
		<div class="input-group mb-3 base" style="justify-content: space-evenly;align-items: center;">
			<div class="input-group-prepend">
				<span style="padding-right: 37px;" class="input-group-text" id="inputGroup-sizing-default">English Writer: </span>
			</div>
				&nbsp;
				&nbsp;
				&nbsp;
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="0" name="manage_skills">
				<span class="new-control-indicator"></span> None
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="25" name="manage_skills">
				<span class="new-control-indicator"></span>Basic
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="50" name="manage_skills">
				<span class="new-control-indicator"></span>Medium
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="75" name="manage_skills">
				<span class="new-control-indicator"></span>Expert
				</label>
			</div>
			<div class="n-chk">
				<label class="new-control new-radio square-radio radio-primary">
				<input type="radio" class="new-control-input" value="100" name="manage_skills">
				<span class="new-control-indicator"></span>Advance
				</label>
			</div>
		</div>
	</div>
</div>
</div>


<div id="password_tab" class="tab-pane fade">
	<div class="card">
		<div class="card-body">
			<h5 class="card-title">Change Password</h5>
			<div class="row">
				<div class="col-md-10 col-lg-6">
					<form>
						<div class="form-group">
							<label>Old Password</label>
							<input type="password" class="form-control">
						</div>
						<div class="form-group">
							<label>New Password</label>
							<input type="password" class="form-control">
						</div>
						<div class="form-group">
							<label>Confirm Password</label>
							<input type="password" class="form-control">
						</div>
						<button class="btn btn-primary" type="submit">Save Changes</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="skills_tab" class="tab-pane fade">
	<div class="card">
		<div class="card-body">
			<h5 class="card-title">Change Password</h5>
			<div class="row">
				<div class="col-md-10 col-lg-6">
					<form>
						<div class="form-group">
							<label>Old Password</label>
							<input type="password" class="form-control">
						</div>
						<div class="form-group">
							<label>New Password</label>
							<input type="password" class="form-control">
						</div>
						<div class="form-group">
							<label>Confirm Password</label>
							<input type="password" class="form-control">
						</div>
						<button class="btn btn-primary" type="submit">Save Changes</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

</div>
</div>
</div>

</div>';
//---------------------------------------------------
include_once "include/footer.php";
//---------------------------------------------------
?>