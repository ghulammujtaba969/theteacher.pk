<?php
define('TITLE', 'Register');
//--------------------------------------------------
include "dbsetting/lms_vars_config.php";
include "dbsetting/classdbconection.php";
$dblms = new dblms();
include("include/header.php");
//--------------------------------------------------
//-----------------------------------------------------------------------------------------------------------
function cleanvars($str)
{
   return is_array($str) ? array_map('cleanvars', $str) :  str_replace("\\",  "\\\\",   htmlspecialchars(stripslashes($str), ENT_QUOTES));
}
//--------------- Courses ------------------
$Courses = array(

   array('id' => 1, 'name' => 'Quran Translation 6th Class'),
   array('id' => 2, 'name' => 'Quran Translation 7th Class'),
   array('id' => 3, 'name' => 'Quran Translation 8th Class'),
   array('id' => 4, 'name' => 'Quran Translation 9th Class')
);

function get_Courses($id)
{
   $listCourses = array(
      '1'   => 'Quran Translation 6th Class',
      '2'   => 'Quran Translation 7th Class',
      '3'   => 'Quran Translation 8th Class',
      '4'   => 'Quran Translation 9th Class'
   );
   return $listCourses[$id];
}
//--------------- PlanForBuy ------------------
$PlanForBuy = array(

   array('id' => 1, 'name' => 'Individual'),
   array('id' => 2, 'name' => 'Teacher'),
   array('id' => 3, 'name' => 'Institute'),
   array('id' => 4, 'name' => 'Campus')
);

function get_PlanForBuy($id)
{
   $listPlanForBuy = array(
      '1'   => 'Individual',
      '2'   => 'Teacher',
      '3'   => 'Institute',
      '4'   => 'Campus'
   );
   return $listPlanForBuy[$id];
}
//---------------------------------------------------
$gender = array('Male', 'Female');
//--------------- TeacherEducation ------------------
$TeacherEducation = array(

   array('id' => 1, 'name' => 'Under Matric'),
   array('id' => 2, 'name' => 'Matric'),
   array('id' => 3, 'name' => 'FA'),
   array('id' => 4, 'name' => 'BA/BS'),
   array('id' => 5, 'name' => 'MA'),
   array('id' => 6, 'name' => 'M.phill/ MS'),
   array('id' => 7, 'name' => 'Phd')
);
function get_PTeacherEducation($id)
{
   $listTeacherEducation = array(
      '1'   => 'Under Matric',
      '2'   => 'Matric',
      '3'   => 'FA',
      '4'   => 'BA/BS',
      '5'   => 'MA',
      '6'   => 'M.phill/ MS',
      '7'   => 'Phd'
   );
   return $listTeacherEducation[$id];
}
//-----------------------------------------------------------------------------------------------------------
function to_seo_url($str)
{
   // if($str !== mb_convert_encoding( mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') )
   //  $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
   $str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
   $str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $str);
   $str = html_entity_decode($str, ENT_NOQUOTES, 'UTF-8');
   $str = preg_replace(array('`[^a-z0-9]`i', '`[-]+`'), '-', $str);
   $str = trim($str, '-');
   return $str;
}
//------------------------------------------------------
function curPageURL()
{
   if (isset($_SERVER["HTTPS"]) && !empty($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] != "on")) {
      $url = "https://" . $_SERVER["SERVER_NAME"]; //https url
   } else {
      $url =  "https://" . $_SERVER["SERVER_NAME"]; //http url
   }
   if (($_SERVER["SERVER_PORT"] != 80)) {
      $url .= $_SERVER["SERVER_PORT"];
   }
   $url .= $_SERVER["REQUEST_URI"];
   return $url;
}


echo '


<!-- inner- slider -->
<div class="ibadat-inner-banner-main-wrapper">
   <div class="container custom-container">
      <div class="inner-page-title">
         <h2>Register</h2>
         <ul>
            <li><a href="javascript:;">Home</a></li>
            <li><a href="javascript:;">| &nbsp; Register</a></li>
         </ul>
      </div>
   </div>
</div>
';

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
                                             inq_name										   ,
                                             inq_fathername									,
                                             inq_email										,
                                             inq_cellno										,
                                             inq_gender										,
                                             inq_address										,
                                             inq_city										   ,
                                             id_country										,
                                             id_curs											,
                                             id_plan											,
                                             inq_insti_name									,
                                             inq_class										,
                                             total_studs										,
                                             total_campus									,
                                             inq_url											,
                                             inq_web											,
                                             inq_planfor										,
                                             inq_teachereducation							,
                                             id_campus										,	
                                             date_added								
                                            )
                                          VALUES(
                                             '1'												      , 
                                             '".$formNo."'									      ,	
                                             '".$name."'										      ,	
                                             '".cleanvars($_POST['inq_fathername'])."'		,
                                             '".$email."'									      ,
                                             '".cleanvars($_POST['inq_cellno'])."'			,
                                             '".$gender."'									      ,
                                             '".cleanvars($_POST['inq_address'])."'			,
                                             '".cleanvars($_POST['inq_city'])."'				,
                                             '".cleanvars($_POST['id_country'])."'			,
                                             '1'												      ,
                                             '".cleanvars($_POST['id_plan'])."'				,
                                             '".cleanvars($_POST['inq_insti_name'])."'		,
                                             '".cleanvars($_POST['inq_class'])."'			,
                                             '".cleanvars($_POST['total_studs'])."'			,
                                             '".cleanvars($_POST['total_campus'])."'		,
                                             '".curPageURL()."'								   ,
                                             'theteacher'									      ,
                                             '".$PlanFor."'									      ,
                                             '".$TeacherEducation."'							   ,
                                             '".$campus."'									      ,
                                             NOW()										
                                            )"
                        );							
   //-----------------------------------------------------------------------------------------------------------
   echo '
   <div class="main-elearn">
   <form>
   <div align="center">
      <h1 style="color:white;">REGISTRATION SUCCESSFULL<br>Your Username Password Will be Sent To You Via Email soon...</h1>
   </div>
   </form>
   </div>';
   //-----------------------------------------------------------------------------------------------------------
   }
   


echo ' 
<!--  -->
<div class="ibadat-contact-main-wrapper">
   <div class="container custom-container">
      <h2 class="m-3">Select Your Plan</h2>
      <div class="contact-top-block">
         <div class="block  wow fadeInUp" data-wow-delay="100ms" id="plan1" onclick="individual_plan()">
            <span>
               <i class="fa fa-user-circle-o fa-5x text-success"></i>
            </span>
            <h4>Individual Plan</h4>
         </div>
         <div class="block  wow fadeInUp" data-wow-delay="100ms" id="plan2" onclick="teacher_plan()">
            <span>
               <i class="fa fa-graduation-cap fa-5x text-success"></i>
            </span>
            <h4>For Teacher</h4>
         </div>
         <div class="block  wow fadeInUp" data-wow-delay="200ms" id="plan3" onclick="insititude_plan()">
            <span>               
               <i class="fa fa-building fa-5x text-success"></i>
            </span>
            <h4>For Institute</h4>
         </div>
         <div class="block  wow fadeInUp" data-wow-delay="300ms" id="plan4" onclick="campus_plan()">
            <span>               
               <i class="fa fa-university fa-5x text-success"></i>
            </span>
            <h4>For Campus</h4>
         </div>
      </div>
      

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
            margin-left: 10px;
            margin-top: 6px;
            color: #FFF;
         }

         /* Floating column for inputs: 50% width */
         .col-el-12 {
            float: left;
            width: 97%;
            margin-left: 10px;
            margin-top: 6px;
            color: #FFF;
         }

         .req:after {
            content: "*";
            font-size: 18px;
            color: #fc0113;
            padding-left: 4px
         }

         /* Style inputs, select elements and textareas */
         input[type=text],
         input[type=email],
         input[type=number],
         input[type=date],
         select,
         textarea {
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
            margin-top: 50px;
         }



         /* Responsive layout - when the screen is less than 600px wide, make the two columns stack on top of each other instead of next to each other */
         @media screen and (max-width: 600px) {

            .col-el-6,
            .col-el-12,
            input[type=submit] {
               width: 100%;
               margin-top: 10px;
               ;
            }
         }
      </style>


      <div class="contact-form-wrapper justify-content-center">
         <div class="contact-form w-75">
            <h4>Register With Us</h4>
            <input type="hidden" id="plan_type" name="plan_type">

            <div class="main-elearn">
               <form id="addNewUsr" class="form-elearn" method="post" action="new_Register.php" enctype="multipart/form-data">

                  <div class="col-el-6">
                     <label class="req">Name</label>
                     <input type="text" id="txt_name" name="txt_name" placeholder="Enter Name Here.. " required>
                  </div>

                  <div class="col-el-6">
                     <label> Gender </label>
                     <select id="txt_gender" name="txt_gender" autocomplete="off" required>
                        <option value="">Select Gender</option>';
foreach ($gender as $itemgender) {
   echo "<option value='$itemgender'>$itemgender</option>";
}
echo '
                     </select>
                  </div>
                  <div style="clear:both;"></div>

                  <div class="col-el-6">
                     <label class="req"> Email </label>
                     <input type="email" id="txt_email" name="txt_email" placeholder="Enter Email Here.." required>
                  </div>


                  <div class="col-el-6">
                     <label class="req"> Course </label>
                     <input type="number" id="txt_course" name="txt_course" placeholder="Quran Translation 6th Class" required readonly>
                  </div>

                  
                  <div style="clear:both;"></div>

                  <div class="col-el-6">
                     <label class="req"> Cell Phone # </label>
                     <input type="number" id="inq_cellno" name="inq_cellno" placeholder="Enter your Cell Phone number with country code # Here.." / required>
                  </div>



                  <div class="col-el-6" id="txt_TeacherEducation">
                     <label> Education Level </label>
                     <select name="txt_TeacherEducation" style="width:100%" autocomplete="off" >
                        <option value="">Select Education Level</option>';
foreach ($TeacherEducation as $itemTeacherEducation) {
   echo "<option value='$itemTeacherEducation[id]'>$itemTeacherEducation[name]</option>";
}
echo '
                     </select>
                  </div>


                  <div style="clear:both;"></div>

                  <div class="col-el-6">
                     <label> City </label>
                     <input type="text" id="inq_city" name="inq_city" placeholder="Enter City Name Here.." />
                  </div>

                  <div class="col-el-6">
                     <label>Country</label>
                     <select id="id_country" name="id_country" autocomplete="off">
                        <option value="">Select Country</option>';
$sqllmszone = $dblms->querylms("SELECT country_id, country_name FROM " . COUNTRIES . "
                        WHERE country_status = '1' ORDER BY country_name ASC");
while ($valuezone = mysqli_fetch_array($sqllmszone)) {
   echo '<option value="' . $valuezone['country_id'] . '">' . $valuezone['country_name'] . '</option>';
}
echo '
                     </select>
                  </div>

                  <div style="clear:both;"></div>


                  <div class="col-el-6" id="inq_insti_name">
                     <label> Institute Name </label>
                     <input type="text" name="inq_insti_name" placeholder="Enter City Name Here.." />
                  </div>

                  <div class="col-el-6" id="inq_class">
                     <label> For Class </label>
                     <input type="text" name="inq_class" placeholder="Enter Class Name Here.." />
                  </div>

                  
                  <div style="clear:both;"></div>

                  <div class="col-el-6" id="total_studs">
                     <label> Total Students </label>
                     <input type="text" name="total_studs" placeholder="Enter Total Students Here.." />
                  </div>

                  <div class="col-el-6" id="total_campus">
                     <label> Total Campuses </label>
                     <input type="text" name="total_campus" placeholder="Enter Total Campus Here.." />
                  </div>

                  
                  <div style="clear:both;"></div>

                  <div class="col-el-12">
                     <label> Address </label>
                     <input type="text" id="inq_address" name="inq_address" placeholder="Enter Address Here.." />
                  </div>

                  <div style="clear:both;"></div>

                  <div class="col-el-6">
                     <input type="submit" id="join_now" name="join_now" value="Join Now">
                  </div>

                  <div style="clear:both;"></div>

               </form>
            </div>

         </div>
      </div>
   </div>
</div>

<script>
function resetBorders() {
    // Get all plan elements and remove their borders
    var plans = document.getElementsByClassName("block");
    for (var i = 0; i < plans.length; i++) {
        plans[i].style.border = "none";
    }
}

function updatePlanType(value) {
    // Update the value of the hidden input field
    document.getElementById("plan_type").value = value;
}

function hideAllFields() {
    // Hide all input fields initially
    document.getElementById("inq_insti_name").style.display = "none";
    document.getElementById("txt_TeacherEducation").style.display = "none";
    document.getElementById("inq_class").style.display = "none";
    document.getElementById("total_studs").style.display = "none";
    document.getElementById("total_campus").style.display = "none";
}

function individual_plan(){
    resetBorders();
    hideAllFields(); // Hide all fields
    var indi_plan = document.getElementById("plan1");
    indi_plan.style.border = "2px solid green";
    updatePlanType(1); // Set the value of the hidden input field to 1
    console.log(1);
}

function teacher_plan(){
    resetBorders();
    hideAllFields(); // Hide all fields first
    // Show only specific fields for teacher plan
    document.getElementById("inq_insti_name").style.display = "block";
    document.getElementById("inq_class").style.display = "block";
    document.getElementById("txt_TeacherEducation").style.display = "block";
    var teach_plan = document.getElementById("plan2");
    teach_plan.style.border = "2px solid green";
    updatePlanType(2); // Set the value of the hidden input field to 2
    console.log(2);
}

function insititude_plan(){
    resetBorders();
    hideAllFields(); // Hide all fields first
    // Show specific fields for institution plan
    document.getElementById("inq_insti_name").style.display = "block";
    document.getElementById("inq_class").style.display = "block";
    document.getElementById("total_studs").style.display = "block";
    document.getElementById("txt_TeacherEducation").style.display = "block";
    var insiti_plan = document.getElementById("plan3");
    insiti_plan.style.border = "2px solid green";
    updatePlanType(3); // Set the value of the hidden input field to 3
    console.log(3);
}

function campus_plan(){
    resetBorders();
    hideAllFields(); // Hide all fields first
    // Show all fields for campus plan
    document.getElementById("inq_insti_name").style.display = "block";
    document.getElementById("inq_class").style.display = "block";
    document.getElementById("total_studs").style.display = "block";
    document.getElementById("total_campus").style.display = "block";
    document.getElementById("txt_TeacherEducation").style.display = "block";
    var camp_plan = document.getElementById("plan4");
    camp_plan.style.border = "2px solid green";
    updatePlanType(4); // Set the value of the hidden input field to 4
    console.log(4);
}


</script>


<!--  -->
<!-- subscribe -->
<div class="ibadat-subcribe-main-wrapper mt-0">
   <div class="container custom-container">
      <div class="subcribe-wrapper">
         <div class="subcribe-text">
            <span>
               <svg width="39" height="32" viewBox="0 0 39 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M3.89971 6.8252L0 10.1207L3.89971 11.9733V6.8252Z" fill="white" />
                  <path
                     d="M33.1493 12.909V0.975145C33.1519 0.715618 33.05 0.46622 32.8666 0.28275C32.6834 0.0992884 32.4337 -0.00256658 32.1745 4.91648e-05H6.82436C6.56516 -0.0025624 6.31543 0.0992884 6.1323 0.28275C5.94884 0.466211 5.84698 0.715618 5.8496 0.975145V12.909L19.4991 19.4023L33.1493 12.909ZM12.6746 5.84987H26.3249C26.8632 5.84987 27.2997 6.28666 27.2997 6.82497C27.2997 7.36327 26.8632 7.80006 26.3249 7.80006H12.6746C12.1363 7.80006 11.6998 7.36327 11.6998 6.82497C11.6998 6.28666 12.1363 5.84987 12.6746 5.84987ZM11.6998 10.725C11.6972 10.4655 11.7991 10.2161 11.9825 10.0326C12.1657 9.84916 12.4154 9.7473 12.6746 9.74992H26.3249C26.8632 9.74992 27.2997 10.1864 27.2997 10.725C27.2997 11.2633 26.8632 11.6998 26.3249 11.6998H12.6746C12.4154 11.7024 12.166 11.6005 11.9825 11.4171C11.7991 11.2336 11.6972 10.9842 11.6998 10.725Z"
                     fill="white" />
                  <path
                     d="M19.4997 21.4498C19.3577 21.4475 19.218 21.4142 19.0904 21.3522L0.000476258 12.2656V30.2248C-0.00213531 30.484 0.0997155 30.7338 0.283177 30.9172C0.466638 31.1004 0.716045 31.2023 0.975238 31.1999H38.0252C38.2844 31.2023 38.5338 31.1004 38.7173 30.9172C38.9007 30.7338 39.0026 30.484 39 30.2248V12.2656L19.91 21.3522C19.7824 21.4142 19.6427 21.4475 19.5007 21.4498H19.4997Z"
                     fill="white" />
                  <path d="M35.0998 6.8252V11.9733L38.9995 10.1207L35.0998 6.8252Z" fill="white" />
               </svg>
            </span>
            <h4>Subscribe Our Newsletter</h4>
         </div>
         <div class="subcribe-search">
            <input type="text" class="form-control" placeholder="Email Address">
            <button class="redButton"><span>Subscribe Now</span></button>
         </div>
      </div>
   </div>
</div>
';
include("include/footer.php");
