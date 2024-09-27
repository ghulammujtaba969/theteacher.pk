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
$eng_writing_skill = getskills($rowstd['eng_writing_skill']);
$ur_writing_skill = getskills($rowstd['ur_writing_skill']);
$video_editing_skill = getskills($rowstd['video_editing_skill']);
$graphic_designing_skill = getskills($rowstd['graphic_designing_skill']);
$manage_skills = getskills($rowstd['manage_skills']);
$eg_wt_sk = $eng_writing_skill; 
$ur_wt_sk = $ur_writing_skill; 
$vd_ed_sk = $video_editing_skill; 
$gh_dg_sk = $graphic_designing_skill; 
$manage_sk = $manage_skills; 

if ($eng_writing_skill == 0) {
    $eng_writing_skill = 100;
    $eg_wt_sk = 'N/A'; 
}
if ($ur_writing_skill == 0) {
    $ur_writing_skill = 100;
    $ur_wt_sk = 'N/A'; 
}
if ($video_editing_skill == 0) {
    $video_editing_skill = 100;
    $vd_ed_sk = 'N/A'; 
}
if ($graphic_designing_skill == 0) {
    $graphic_designing_skill = 100;
    $gh_dg_sk = 'N/A'; 
}
if ($manage_skills == 0) {
    $manage_skills = 100;
    $manage_sk = 'N/A'; 
}

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
<a class="nav-link" data-toggle="tab" href="#history_tab">History</a>
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


</div>


<div id="history_tab" class="tab-pane fade">
	<div class="card">
		<div class="card-body">
			<h5 class="card-title">My History</h5>
			<div class="row">
				<div class="col-md-10 col-lg-12">';
					$sqllmsactivity  = $dblms->querylms("SELECT * FROM ".ADMINS." rep 
										INNER JOIN ".ADMINS_HISTORY." e ON e.id_emply = rep.emply_id 
										WHERE e.id_emply = '".$rowstd['emply_id']."' 
										ORDER BY e.id DESC LIMIT 20");
					echo'
					<table class="color-table table-bordered primary-table table table-hover">
					<tr>
						<th> Sr# </th>
						<th> IP address </th>
						<th> Browser </th>
						<th> Dated </th>
						<th> Remarks </th>
					</tr>';
					//------------------------------------------------
					$srno = 0;
					//------------------------------------------------
					while($rowstd = mysqli_fetch_array($sqllmsactivity)) {
					//------------------------------------------------
					$srno++;
					//-----------------------------------------------------------------
					$origDate 	= $rowstd['dated']; 
					$newDate 	= date("h:i a", strtotime($origDate));
					$timeago 	= get_timeago(strtotime($rowstd['dated']));
					//------------------------------------------------
					echo '
					<tr >  
						<td align="center">'.$srno.'</td>
						<td>'.$rowstd['ip_address'].'</td>
						<td>'.$rowstd['computer_browser'].'</td>
						<td>'.$newDate.' ('.$timeago.')</td>
						<td>'.$rowstd['remarks'].'</td>
					</tr>';
					//------------------------------------------------
					} // end while loop
					//------------------------------------------------
					echo '
					</tbody>
					</table>
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
			<h5 class="card-title">Skills</h5>
			<div class="row">
				<div class="col-md-10 col-lg-6">
					<p>These Are The Skills That A User Possess </p>
					<ul>
						<li>
							<label>English Writing Skills</label>
							<div class="progress">
								<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="'.$eng_writing_skill.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$eng_writing_skill.'%">'.$eg_wt_sk.'%</div>
							</div>
						</li>
						<li>
							<label>Urdu Writing Skills</label>
							<div class="progress">
								<div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="'.$ur_writing_skill.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$ur_writing_skill.'%">'.$ur_wt_sk.'%</div>
							</div>
						</li>
						<li>
							<label>Video Editing Skills</label>
							<div class="progress">
								<div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" aria-valuenow="'.$video_editing_skill.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$video_editing_skill.'%">'.$vd_ed_sk.'%</div>
							</div>
						</li>
						<li>
							<label>Graphic Editing Skills</label>
							<div class="progress">
								<div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" aria-valuenow="'.$graphic_designing_skill.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$graphic_designing_skill.'%">'.$gh_dg_sk.'%</div>
							</div>
						</li>
						<li>
							<label>Management Skills</label>
							<div class="progress">
								<div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" aria-valuenow="'.$manage_skills.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$manage_skills.'%">'.$manage_sk.'%</div>
							</div>
						</li>
					</ul>
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