<?php 
error_reporting(0);
session_start();
	include "../../dbsetting/lms_vars_config.php";
	include "../../dbsetting/classdbconection.php";
	$dblms = new dblms();
	include "../../functions/login_func.php";
	include "../../functions/functions.php";
//--------------------------------------------
if(isset($_GET['id_country'])) {
	$id_country = $_GET['id_country']; 
//--------------------------------------------
//echo $id_prg.'abd ahahda dahda had a';
echo ' 
	<div class="col-sm-41">
		<div class="form_sep" style="margin-top:5px;">
			<label>City</label>
			<select id="id_city" name="id_city" style="width:100%" autocomplete="off">
				<option value="">Select City</option>';
			 $sqllmscity	= $dblms->querylms("SELECT city_id, city_name FROM ".CITIES." 
			 											WHERE city_status = '1' AND id_country = '".cleanvars($id_country)."' ORDER BY city_name ASC");
			while($valuecity 	= mysqli_fetch_array($sqllmscity)) {
				echo '<option value="'.$valuecity['city_id'].'">'.$valuecity['city_name'].'</option>';
			}
	echo'
			</select>
		</div> 
	</div>';
}
//--------------------------------------------
if(isset($_GET['id_country_edit'])) {
	$id_country_edit = $_GET['id_country_edit']; 
//--------------------------------------------
//echo $id_prg.'abd ahahda dahda had a';
echo ' 
	<div class="col-sm-41">
		<div class="form_sep" style="margin-top:5px;">
			<label>City</label>
			<select id="id_city_edit" name="id_city_edit" style="width:100%" autocomplete="off">
				<option value="">Select City</option>';
			 $sqllmscity	= $dblms->querylms("SELECT city_id, city_name FROM ".CITIES." 
			 											WHERE city_status = '1' AND id_country = '".cleanvars($id_country_edit)."' ORDER BY city_name ASC");
			while($valuecity 	= mysqli_fetch_array($sqllmscity)) {
				echo '<option value="'.$valuecity['city_id'].'">'.$valuecity['city_name'].'</option>';
			}
	echo'
			</select>
		</div> 
	</div>';
}

?>
<!--JS_SELECT_LISTS-->
<script>
	$("#id_city").select2({
		allowClear: true
	});

	$("#id_city_edit").select2({
		allowClear: true
	});
</script>