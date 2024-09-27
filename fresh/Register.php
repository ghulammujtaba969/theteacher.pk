<?php
//-----------------------------------------------------------------------------------------------------------
    include "dbsetting/lms_vars_config.php";
	include "dbsetting/classdbconection.php";
	$dblms = new dblms();
//-----------------------------------------------------------------------------------------------------------
function cleanvars($str){ 
	return is_array($str) ? array_map('cleanvars', $str) :  str_replace( "\\",  "\\\\",   htmlspecialchars( stripslashes($str)  , ENT_QUOTES)   )  ; 

}
//--------------- Courses ------------------
$Courses = array (

					array('id'=>1, 'name'=>'Quran Translation 6th Class'),
					array('id'=>2, 'name'=>'Quran Translation 7th Class'),
					array('id'=>3, 'name'=>'Quran Translation 8th Class'),
					array('id'=>4, 'name'=>'Quran Translation 9th Class')
				   );

function get_Courses($id) {
	$listCourses = array (
							'1'	=> 'Quran Translation 6th Class',
							'2'	=> 'Quran Translation 7th Class',
							'3'	=> 'Quran Translation 8th Class',
							'4'	=> 'Quran Translation 9th Class'
							);
	return $listCourses[$id];

}
//--------------- PlanForBuy ------------------
$PlanForBuy = array (

					array('id'=>1, 'name'=>'Individual'),
					array('id'=>2, 'name'=>'Teacher'),
					array('id'=>3, 'name'=>'Institute'),
					array('id'=>4, 'name'=>'Campus')
				   );

function get_PlanForBuy($id) {
	$listPlanForBuy = array (
							'1'	=> 'Individual',
							'2'	=> 'Teacher',
							'3'	=> 'Institute',
							'4'	=> 'Campus'
							);
	return $listPlanForBuy[$id];

}
//---------------------------------------------------
$gender = array('Male', 'Female');
//--------------- TeacherEducation ------------------
$TeacherEducation = array (

					array('id'=>1, 'name'=>'Matric'),
					array('id'=>2, 'name'=>'FA'),
					array('id'=>3, 'name'=>'BA/BS'),
					array('id'=>4, 'name'=>'MA'),
					array('id'=>5, 'name'=>'M.phill/ MS'),
					array('id'=>6, 'name'=>'Phd')
				   );
function get_PTeacherEducation($id) {
	$listTeacherEducation = array (
							'1'	=> 'Matric',
							'2'	=> 'FA',
							'3'	=> 'BA/BS',
							'4'	=> 'MA',
							'5'	=> 'M.phill/ MS',
							'6'	=> 'Phd'
							);
	return $listTeacherEducation[$id];

}
//-----------------------------------------------------------------------------------------------------------
function to_seo_url($str){
   // if($str !== mb_convert_encoding( mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') )
      //  $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
    $str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
    $str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $str);
    $str = html_entity_decode($str, ENT_NOQUOTES, 'UTF-8');
    $str = preg_replace(array('`[^a-z0-9]`i','`[-]+`'), '-', $str);
    $str = trim($str, '-');
    return $str;
}
//------------------------------------------------------
function curPageURL() {
  if(isset($_SERVER["HTTPS"]) && !empty($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] != "on" )) {
        $url = "https://".$_SERVER["SERVER_NAME"];//https url
  }  else {
    $url =  "https://".$_SERVER["SERVER_NAME"];//http url
  }
  if(( $_SERVER["SERVER_PORT"] != 80 )) {
     $url .= $_SERVER["SERVER_PORT"];
  }
  $url .= $_SERVER["REQUEST_URI"];
  return $url;
}
//-----------------------------------------------------------------------------------------------------------
if(isset($_POST['join_now'])){	
//-----------------------------------------------------------------------------------------------------------
	$name		 		= cleanvars($_POST['txt_name']);
	$email		 		= cleanvars($_POST['txt_email']);
	$phone 		 		= cleanvars($_POST['txt_phone']);
	$gender		 		= cleanvars($_POST['txt_gender']);
	$PlanFor	 		= cleanvars($_POST['txt_plan']);
	$TeacherEducation	= cleanvars($_POST['txt_TeacherEducation']);
//-----------------------------------------------------------------------------------------------------------
	$sqllmsformno  = $dblms->querylms("SELECT inq_id FROM elearn_inquiry ORDER BY inq_id DESC");
	$valueformno	= mysqli_fetch_array($sqllmsformno);
//-----------------------------------------------------------------------------------------------------------
$dateofbirth  = new DateTime($_POST['inq_dob']);
$todaydate    = new DateTime('today');
$currentage = $dateofbirth->diff($todaydate)->y;
//-----------------------------------------------------------------------------------------------------------
	$formNo = ('TECH-'.($valueformno['inq_id'] + 1));
//-----------------------------------------------------------------------------------------------------------
$sqllms  = $dblms->querylms("INSERT INTO ".INQUIRY."(
														inq_status										, 
														inq_form_no										,
														inq_name										,
														inq_fathername									,
														inq_email										,
														inq_cellno										,
														inq_gender										,
														inq_address										,
														inq_city										,
														id_country										,
														id_curs											,
														id_plan											,
														inq_url											,
														inq_web											,
														inq_planfor										,
														inq_teachereducation							,
														id_campus										,	
														date_added								
													  )
	   											VALUES(
														'3'												, 
														'".$formNo."'									,	
														'".$name."'										,	
														'".cleanvars($_POST['inq_fathername'])."'		,
														'".$email."'									,
														'".cleanvars($_POST['inq_cellno'])."'			,
														'".$gender."'									,
														'".cleanvars($_POST['inq_address'])."'			,
														'".cleanvars($_POST['inq_city'])."'				,
														'".cleanvars($_POST['id_country'])."'			,
														'1'												,
														'".cleanvars($_POST['id_plan'])."'				,
														'".curPageURL()."'								,
														'theteacher'									,
														'".$PlanFor."'									,
														'".$TeacherEducation."'							,
														'".$campus."'									,
														NOW()										
													  )"
							);							
//-----------------------------------------------------------------------------------------------------------
echo '
<div class="main-elearn">
<form>
<div align="center">
	<h1 style="color:white;">REGISTRATION SUCCESSFULL<br>Our representative will contact you soon...</h1>
</div>
</form>
</div>';
//-----------------------------------------------------------------------------------------------------------
}

//-----------------------------------------------------------------------------------------------------------
echo'
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title> Register | The Teacher.pk </title>
<link rel="icon" sizes="16x16" type="image/png" href="favicon.png">

<style>

/* Style the main-elearn */
.main-elearn {
	max-width: 850px;
    background: linear-gradient(135deg, #06F, #4CAF50);
    padding: 30px;
    margin: 50px auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    border-radius: 10px;
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    font-size: 18px;
    color: #333;
	
}

/* Floating column for inputs: 50% width */
.col-el-6 {
  float: left;
  width: 48%;
  margin-left:10px;
  margin-top: 6px;
  color:#FFF;
}
/* Floating column for inputs: 50% width */
.col-el-12 {
  float: left;
  width: 97%;
  margin-left:10px;
  margin-top: 6px;
  color:#FFF;
}

.req:after {content:"*";font-size:18px;color:#fc0113;padding-left:4px}

/* Style inputs, select elements and textareas */
input[type=text],input[type=email],input[type=number],input[type=date], select, textarea{
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  resize: vertical;
}

/* Style the label to display next to the inputs */
label {
  padding: 12px 12px 12px 0;
  display: inline-block;
}

/* Style the submit button */
input[type=submit] {
  background-color: #4CAF50;
  color: white;
  padding: 18px 28px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-top:50px;
}



/* Responsive layout - when the screen is less than 600px wide, make the two columns stack on top of each other instead of next to each other */
@media screen and (max-width: 600px) {
  .col-el-6,.col-el-12, input[type=submit] {
    width: 100%;
    margin-top: 10px;;
  }
}

</style>

</head>

<body>';
//--------------------------------------------------------
if(!isset($_POST['join_now']) ){	
//--------------------------------------------------------
echo'

<div class="main-elearn">
<form id="addNewUsr" class="form-elearn" method="post" action="#" enctype="multipart/form-data">

      <div class="col-el-6">
       	<label class="req">Student Name</label>
      	<input type="text" id="txt_name" name="txt_name" placeholder="Enter Student Name Here.. " required>
      </div>
      
      <div class="col-el-6">
       	<label class="req">Father Name</label>
        <input type="text" id="inq_fathername" name="inq_fathername" placeholder="Father Name Here.." required>
      </div>
   
<div style="clear:both;"></div>
	  
   	<div class="col-el-6">
       	<label> Gender </label>
        <select id="txt_gender" name="txt_gender" style="width:100%" autocomplete="off" required>
				<option value="">Select Gender</option>';
			foreach($gender as $itemgender) {
				echo "<option value='$itemgender'>$itemgender</option>";
			}
	echo'
			</select>
	</div>
	  
      <div class="col-el-6">
       	<label class="req"> Course </label>
        <input type="number"  id="txt_course" name="txt_course" placeholder="Quran Translation 6th Class"  required readonly>
      </div>
      
<div style="clear:both;"></div>
      
      <div class="col-el-6">
       	<label class="req"> Email </label>
        <input type="email" id="txt_email" name="txt_email" placeholder="Enter Email Here.." required>
      </div>

      <div class="col-el-6">
       	<label class="req"> Cell Phone # </label>
        <input type="number"  id="inq_cellno" name="inq_cellno" placeholder="Enter your Cell Phone number with country code # Here.." / required>
      </div>

    
<div style="clear:both;"></div>
 
   <div class="col-el-6">
       	<label> Plan For </label>
        <select id="txt_plan" name="txt_plan" style="width:100%" autocomplete="off" required>
				<option value="">Select Plan For</option>';
			foreach($PlanForBuy as $itemPlanForBuy) {
				echo "<option value='$itemPlanForBuy[id]'>$itemPlanForBuy[name]</option>";
			}
	echo'
			</select>
</div>

   <div class="col-el-6">
       	<label> Teacher Education </label>
        <select id="txt_TeacherEducation" name="txt_TeacherEducation" style="width:100%" autocomplete="off" required>
				<option value="">Select Teacher Education</option>';
			foreach($TeacherEducation as $itemTeacherEducation) {
				echo "<option value='$itemTeacherEducation[id]'>$itemTeacherEducation[name]</option>";
			}
	echo'
			</select>
</div>

		
<div style="clear:both;"></div>

	<div class="col-el-6">
       	 <label> City </label>
         <input type="text" id="inq_city" name="inq_city" placeholder="Enter City Name Here.."  />
      </div>
      
   <div class="col-el-6">
       	<label>Country</label>
        <select id="id_country" name="id_country" autocomplete="off">
				<option value="">Select Country</option>';
				  $sqllmszone	= $dblms->querylms("SELECT country_id, country_name FROM ".COUNTRIES." 
				  												WHERE country_status = '1' ORDER BY country_name ASC");
			while($valuezone 	= mysqli_fetch_array($sqllmszone)) { 
					echo '<option value="'.$valuezone['country_id'].'">'.$valuezone['country_name'].'</option>';

				}
	echo'
		</select>
</div>

<div style="clear:both;"></div>

	<div class="col-el-12">
       	 <label> Address </label>
         <input type="text" id="inq_address" name="inq_address" placeholder="Enter Address Here.."  />
      </div>

<div style="clear:both;"></div>

    <div class="col-el-6">
      <input type="submit" id="join_now" name="join_now" value="Join Now">
    </div>
    
<div style="clear:both;"></div>
    
</form>
</div>';
//----------------------------------------------
} 
//----------------------------------------------
echo'
</body>
</html>';
?>