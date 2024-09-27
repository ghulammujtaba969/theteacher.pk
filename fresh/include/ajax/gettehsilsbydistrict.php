<?php 
error_reporting(0);
session_start();
	include "../../dbsetting/lms_vars_config.php";
	include "../../dbsetting/classdbconection.php";
	$dblms = new dblms();
	include "../../functions/login_func.php";
	include "../../functions/functions.php";
//--------------------------------------------
if(isset($_GET['id_district'])) {
	$id_district = $_GET['id_district']; 
//--------------------------------------------
//echo $id_prg.'abd ahahda dahda had a';
echo ' 
			<select id="id_tehsil" name="id_tehsil" style="width:100%" autocomplete="off">
				<option value="">Select Tehsil</option>';
				 $sqllmstehsil	= $dblms->querylms("SELECT tehsil_id, tehsil_name  
				 											FROM ".TEHSILS." 
															WHERE tehsil_status = '1' AND id_district = '".cleanvars($id_district)."' 
															ORDER BY tehsil_name ASC");
			while($valuetehsil 	= mysqli_fetch_array($sqllmstehsil)) { 
				echo '<option value="'.$valuetehsil['tehsil_id'].'">'.$valuetehsil['tehsil_name'].'</option>';
			}
	echo'
			</select>';
}
//--------------------------------------------
if(isset($_GET['id_district_edit'])) {
	$id_district_edit = $_GET['id_district_edit']; 
//--------------------------------------------
//echo $id_prg.'abd ahahda dahda had a';
echo ' 
			<select id="id_tehsil_edit" name="id_tehsil_edit" style="width:100%" autocomplete="off">
				<option value="">Select Tehsil</option>';
				 $sqllmstehsil	= $dblms->querylms("SELECT tehsil_id, tehsil_name  
				 											FROM ".TEHSILS." 
															WHERE tehsil_status = '1' AND id_district = '".cleanvars($id_district_edit)."' 
															ORDER BY tehsil_name ASC");
			while($valuetehsil 	= mysqli_fetch_array($sqllmstehsil)) { 
				echo '<option value="'.$valuetehsil['tehsil_id'].'">'.$valuetehsil['tehsil_name'].'</option>';
			}
	echo'
			</select>';
}

?>
<!--JS_SELECT_LISTS-->
<script>
	$("#id_tehsil").select2({
		allowClear: true
	});

	$("#id_tehsil_edit").select2({
		allowClear: true
	});
</script>