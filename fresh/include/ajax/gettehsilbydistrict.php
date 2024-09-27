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
if($id_country == '158') {
echo '
	<div class="col-sm-41">
		<div class="form_sep" style="margin-top:5px;">
			<label class="req">District</label>
			<select id="id_district" name="id_district" onchange="gettehsilbydistrict(this.value)" style="width:100%" autocomplete="off" required>
				<option value="">Select District</option>';
				 $sqllmsdists	= $dblms->querylms("SELECT district_id, district_name  
				 											FROM ".DISTRICTS." WHERE district_status = '1' ORDER BY district_name ASC");
			while($valuedists 	= mysqli_fetch_array($sqllmsdists)) { 
				echo '<option value="'.$valuedists['district_id'].'">'.$valuedists['district_name'].'</option>';
			}
	echo'
			</select>
		</div> 
	</div>
<div id="gettehsilbydistrict">
	<div class="col-sm-41">
		<div class="form_sep" style="margin-top:5px;">
			<label class="req">Tehsil</label>
			<select id="id_tehsil" name="id_tehsil" style="width:100%" autocomplete="off" required>
				<option value="">Select Tehsil</option>
			</select>
		</div> 
	</div>
	
	<div class="col-sm-41">
		<div class="form_sep" style="margin-top:5px;">
			<label>Tanzeemi Tehsil</label>
			<select id="id_tanzeemitehsil" name="id_tanzeemitehsil" style="width:100%" autocomplete="off">
				<option value="">Select Tehsil</option>
			</select>
		</div> 
	</div>
</div>	';
} else { 
echo '
	<div class="col-sm-62">
		<div class="form_sep" style="margin-top:5px;">
			<label>City Name</label>
			<input type="text" class="form-control" id="city" name="city" autocomplete="off">
		</div> 
	</div>';
}
}

if(isset($_GET['id_country_edit'])) {
	$id_country_edit = $_GET['id_country_edit']; 
if($id_country_edit == '158') {
echo '
	<div class="col-sm-41">
		<div class="form_sep" style="margin-top:5px;">
			<label class="req">District</label>
			<select id="id_district_edit" name="id_district_edit" onchange="gettehsilbydistrictedit(this.value)" style="width:100%" autocomplete="off" required>
				<option value="">Select District</option>';
				 $sqllmsdists	= $dblms->querylms("SELECT district_id, district_name  
				 											FROM ".DISTRICTS." WHERE district_status = '1' ORDER BY district_name ASC");
			while($valuedists 	= mysqli_fetch_array($sqllmsdists)) { 
				if($rowstd['id_district'] == $valuedists['district_id']) {
					echo '<option value="'.$valuedists['district_id'].'" selected>'.$valuedists['district_name'].'</option>';
				} else {
					echo '<option value="'.$valuedists['district_id'].'">'.$valuedists['district_name'].'</option>';
				}
			}
	echo'
			</select>
		</div> 
	</div>
<div id="gettehsilbydistrictedit">
	<div class="col-sm-41">
		<div class="form_sep" style="margin-top:5px;">
			<label class="req">Tehsil</label>
			<select id="id_tehsil_edit" name="id_tehsil_edit" style="width:100%" autocomplete="off" required>
				<option value="">Select Tehsil</option>';
				 $sqllmstehsil	= $dblms->querylms("SELECT tehsil_id, tehsil_name  
				 											FROM ".TEHSILS." WHERE tehsil_status = '1' ORDER BY tehsil_name ASC");
			while($valuetehsil 	= mysqli_fetch_array($sqllmstehsil)) { 
				if($rowstd['id_tehsil'] == $valuetehsil['tehsil_id']) {
					echo '<option value="'.$valuetehsil['tehsil_id'].'" selected>'.$valuetehsil['tehsil_name'].'</option>';
				} else {
					echo '<option value="'.$valuetehsil['tehsil_id'].'">'.$valuetehsil['tehsil_name'].'</option>';
				}
			}
	echo'
			</select>
		</div> 
	</div>
	
	<div class="col-sm-41">
		<div class="form_sep" style="margin-top:5px;">
			<label>Tanzeemi Tehsil</label>
			<select id="id_tanzeemitehsil_edit" name="id_tanzeemitehsil_edit" style="width:100%" autocomplete="off">
				<option value="">Select Tehsil</option>';
				 $sqllmstehsilt	= $dblms->querylms("SELECT tehsil_id, tehsil_name  
				 											FROM ".TEHSILS_TANZEEMI." WHERE tehsil_status = '1' ORDER BY tehsil_name ASC");
			while($valuetehsilt 	= mysqli_fetch_array($sqllmstehsilt)) { 
				if($rowstd['id_tanzeemitehsil'] == $valuetehsilt['tehsil_id']) { 
					echo '<option value="'.$valuetehsilt['tehsil_id'].'" selected>'.$valuetehsilt['tehsil_name'].'</option>';
				} else {
					echo '<option value="'.$valuetehsilt['tehsil_id'].'">'.$valuetehsilt['tehsil_name'].'</option>';
				}

			}
	echo'
			</select>
		</div> 
	</div>
</div>	';
} else { 
echo '
	<div class="col-sm-62">
		<div class="form_sep" style="margin-top:5px;">
			<label>City Name</label>
			<input type="text" class="form-control" id="city_edit" name="city_edit" autocomplete="off">
		</div> 
	</div>';
}
}


if(isset($_GET['id_district'])) {
	$id_district = $_GET['id_district']; 
//--------------------------------------------
//echo $id_prg.'abd ahahda dahda had a';
echo ' 
	<div class="col-sm-41">
		<div class="form_sep" style="margin-top:5px;">
			<label class="req">Tehsil</label>
			<select id="id_tehsil" name="id_tehsil" style="width:100%" autocomplete="off" required>
				<option value="">Select Tehsil</option>';
				 $sqllmstehsil	= $dblms->querylms("SELECT tehsil_id, tehsil_name  
				 											FROM ".TEHSILS." 
															WHERE tehsil_status = '1' AND id_district = '".cleanvars($id_district)."' 
															ORDER BY tehsil_name ASC");
			while($valuetehsil 	= mysqli_fetch_array($sqllmstehsil)) { 
				echo '<option value="'.$valuetehsil['tehsil_id'].'">'.$valuetehsil['tehsil_name'].'</option>';
			}
	echo'
			</select>
		</div> 
	</div>
	
	<div class="col-sm-41">
		<div class="form_sep" style="margin-top:5px;">
			<label>Tanzeemi Tehsil</label>
			<select id="id_tanzeemitehsil" name="id_tanzeemitehsil" style="width:100%" autocomplete="off">
				<option value="">Select Tehsil</option>';
				 $sqllmstehsil	= $dblms->querylms("SELECT tehsil_id, tehsil_name  
				 											FROM ".TEHSILS_TANZEEMI." 
															WHERE tehsil_status = '1' AND id_district = '".cleanvars($id_district)."' 
															ORDER BY tehsil_name ASC");
			while($valuetehsil 	= mysqli_fetch_array($sqllmstehsil)) { 
				echo '<option value="'.$valuetehsil['tehsil_id'].'">'.$valuetehsil['tehsil_name'].'</option>';
			}
	echo'
			</select>
		</div> 
	</div>';
}

if(isset($_GET['iddistrict'])) {
	$iddistrict = $_GET['iddistrict']; 
//--------------------------------------------
//echo $id_prg.'abd ahahda dahda had a';
echo ' 
	<div class="col-sm-41">
		<div class="form_sep" style="margin-top:5px;">
			<label class="req">Tehsil</label>
			<select id="idtehsil" name="idtehsil" style="width:100%" autocomplete="off" required>
				<option value="">Select Tehsil</option>';
				 $sqllmstehsil	= $dblms->querylms("SELECT tehsil_id, tehsil_name  
				 											FROM ".TEHSILS." 
															WHERE tehsil_status = '1' AND id_district = '".cleanvars($iddistrict)."' 
															ORDER BY tehsil_name ASC");
			while($valuetehsil 	= mysqli_fetch_array($sqllmstehsil)) { 
				echo '<option value="'.$valuetehsil['tehsil_id'].'">'.$valuetehsil['tehsil_name'].'</option>';
			}
	echo'
			</select>
		</div> 
	</div>
	
	<div class="col-sm-41">
		<div class="form_sep" style="margin-top:5px;">
			<label>Tanzeemi Tehsil</label>
			<select id="idtanzeemitehsil" name="idtanzeemitehsil" style="width:100%" autocomplete="off">
				<option value="">Select Tehsil</option>';
				 $sqllmstehsil	= $dblms->querylms("SELECT tehsil_id, tehsil_name  
				 											FROM ".TEHSILS_TANZEEMI." 
															WHERE tehsil_status = '1' AND id_district = '".cleanvars($iddistrict)."' 
															ORDER BY tehsil_name ASC");
			while($valuetehsil 	= mysqli_fetch_array($sqllmstehsil)) { 
				echo '<option value="'.$valuetehsil['tehsil_id'].'">'.$valuetehsil['tehsil_name'].'</option>';
			}
	echo'
			</select>
		</div> 
	</div>';
}
//--------------------------------------------
if(isset($_GET['id_district_edit'])) {
	$id_district_edit = $_GET['id_district_edit']; 
//--------------------------------------------
//echo $id_prg.'abd ahahda dahda had a';
echo ' 
	<div class="col-sm-41">
		<div class="form_sep" style="margin-top:5px;">
			<label class="req">Tehsil</label>
			<select id="id_tehsil_edit" name="id_tehsil_edit" style="width:100%" autocomplete="off" required>
				<option value="">Select Tehsil</option>';
				 $sqllmstehsil	= $dblms->querylms("SELECT tehsil_id, tehsil_name  
				 											FROM ".TEHSILS." 
															WHERE tehsil_status = '1' AND id_district = '".cleanvars($id_district_edit)."' 
															ORDER BY tehsil_name ASC");
			while($valuetehsil 	= mysqli_fetch_array($sqllmstehsil)) { 
				echo '<option value="'.$valuetehsil['tehsil_id'].'">'.$valuetehsil['tehsil_name'].'</option>';
			}
	echo'
			</select>
		</div> 
	</div>
	
	<div class="col-sm-41">
		<div class="form_sep" style="margin-top:5px;">
			<label>Tanzeemi Tehsil</label>
			<select id="id_tanzeemitehsil_edit" name="id_tanzeemitehsil_edit" style="width:100%" autocomplete="off">
				<option value="">Select Tehsil</option>';
				 $sqllmstehsil	= $dblms->querylms("SELECT tehsil_id, tehsil_name  
				 											FROM ".TEHSILS_TANZEEMI." 
															WHERE tehsil_status = '1' 
															AND id_district = '".cleanvars($id_district_edit)."' 
															ORDER BY tehsil_name ASC");
			while($valuetehsil 	= mysqli_fetch_array($sqllmstehsil)) { 
				echo '<option value="'.$valuetehsil['tehsil_id'].'">'.$valuetehsil['tehsil_name'].'</option>';
			}
	echo'
			</select>
		</div> 
	</div>';
}

?>
<!--JS_SELECT_LISTS-->
<script>
	$("#id_district").select2({
		allowClear: true
	});
	$("#id_district_edit").select2({
		allowClear: true
	});
	$("#id_tehsil").select2({
		allowClear: true
	});
	$("#idtehsil").select2({
		allowClear: true
	});

	$("#id_tehsil_edit").select2({
		allowClear: true
	});
	$("#id_tanzeemitehsil").select2({
		allowClear: true
	});
	$("#idtanzeemitehsil").select2({
		allowClear: true
	});

	$("#id_tanzeemitehsil_edit").select2({
		allowClear: true
	});
</script>
<script type="text/javascript" src="../../js/gethalqabyblock.js"></script>