<?php 

//--------------- Status ------------------

$admstatus = array (

						array('status_id'=>1, 'status_name'=>'Active')		, array('status_id'=>2, 'status_name'=>'Inactive')

				   );

function get_admstatus($id) {

	$listadmstatus= array (

							'1' => '<span class="label label-success" id="bns-status-badge">Active</span>', 

							'2' => '<span class="label label-danger" id="bns-status-badge">Inactive</span>');

	return $listadmstatus[$id];

}

//--------------- BillStatus ------------------

$BillStatus = array (

						array('id'=>1, 'name'=>'Paid')		, 

						array('id'=>2, 'name'=>'Due')	

				   );



function get_BillStatus($id) {

	$listBillStatus = array (

							'1' => '<span class="label label-success" id="bns-status-badge">Paid</span>'					, 

							'2' => '<span class="label label-danger" id="bns-status-badge">Due</span>'	

						  );

	return $listBillStatus[$id];

}

//--------------- Export -------------------------------------------------

function get_BillStatusExport($id) {

	$listBillStatus = array (

							'1' => 'Paid'					, 

							'2' => 'Due'	

						  );

	return $listBillStatus[$id];

}

//--------------- Bill Updated -------------------------------------------------

$BillUpdating = array (

						array('id'=>1, 'name'=>'Auto Updated')				, 

						array('id'=>2, 'name'=>'Manully Updated')	

				   );



function get_BillUpdating($id) {

	$listBillUpdating = array (

							'1'		=> 'Auto Updated'						,

							'2'		=> 'Manully Updated'

						  );

	return $listBillUpdating[$id];

}

//--------------- Blocks -------------------------------------------------

$Blocks = array (

						array('block_id'=>1, 'block_name'=>'BLOCK - A')		, 

						array('block_id'=>2, 'block_name'=>'BLOCK - B')		, 

						array('block_id'=>3, 'block_name'=>'BLOCK - C')		, 

						array('block_id'=>4, 'block_name'=>'BLOCK - D')		, 

						array('block_id'=>5, 'block_name'=>'BLOCK - E')		, 

						array('block_id'=>6, 'block_name'=>'BLOCK - F')		, 

						array('block_id'=>7, 'block_name'=>'BLOCK - G')		, 

						array('block_id'=>8, 'block_name'=>'BLOCK - H')		, 

						array('block_id'=>9, 'block_name'=>'BLOCK - J')		, 

						array('block_id'=>10, 'block_name'=>'BLOCK - K')	, 

						array('block_id'=>11, 'block_name'=>'BLOCK - MISC')	

				   );



function get_Blocks($id) {

	$listBlocks = array (

							'1'		=> 'BLOCK - A'					,

							'2'		=> 'BLOCK - B'					,

							'3'		=> 'BLOCK - C'					,

							'4'		=> 'BLOCK - D'					,

							'5'		=> 'BLOCK - E'					,

							'6'		=> 'BLOCK - F'					,

							'7'		=> 'BLOCK - G'					,

							'8'		=> 'BLOCK - H'					,

							'9'		=> 'BLOCK - J'					,

							'10'	=> 'BLOCK - K'					,

							'11'	=> 'BLOCK - MISC'

						  );

	return $listBlocks[$id];

}

//--------------- Bank Alfalah ------------------

$BankAlfalah = array (

						array('id'=>1, 'name'=>'Alfa Wallet')				, 

						array('id'=>2, 'name'=>'Alfalah Bank Account')		, 

						array('id'=>3, 'name'=>'Credit/Debit Card')	

				   );



function get_BankAlfalah($id) {

	$listBankAlfalah = array (

							'1'		=> 'Alfa Wallet'						,

							'2'		=> 'Alfalah Bank Account'				,

							'3'		=> 'Credit/Debit Card'

						  );

	return $listBankAlfalah[$id];

}

//--------------- Post Status ------------------

$poststatus = array (

						array('id'=>1, 'name'=>'Unlisted')			, 

						array('id'=>2, 'name'=>'Active')			, 

						array('id'=>3, 'name'=>'All')

				   );



function get_poststatus($id) {

	$listpoststatus= array (

						'1'  => '<span class="label label-success" id="bns-status-badge"> Unlisted </span>'				, 

						'2'  => '<span class="label label-danger" id="bns-status-badge"> Active </span>'				,

						'3' =>'<span class="label label-success" id="bns-status-badge"> All </span>');

	return $listpoststatus[$id];

}

//--------------- BillStatus ------------------

$ComplaintStatus = array (

						array('id'=>1, 'name'=>'Pending')				, 

						array('id'=>2, 'name'=>'Processing')			, 

						array('id'=>3, 'name'=>'Resolved')	

				   );



function get_ComplaintStatus($id) {

	$listComplaintStatus = array (

							'1' => '<span class="label label-danger" id="bns-status-badge">Pending</span>', 

							'2' => '<span class="label label-info" id="bns-status-badge">Processing</span>', 

							'3' => '<span class="label label-success" id="bns-status-badge">Resolved</span>'	

						  );

	return $listComplaintStatus[$id];

}

//--------------- Login Types ------------------

$logintypes = array (

					array('id'=>1, 'name'=>'Staffs')		,

					array('id'=>2, 'name'=>'Teachers')		,

					array('id'=>3, 'name'=>'Students')		,

					array('id'=>4, 'name'=>'Parents')

				   );



function get_logintypes($id) {

	$listlogintypes = array (

							'1'	=> 'Staffs'				,

							'2'	=> 'Teachers'			,

							'3'	=> 'Students'			,

							'4'	=> 'Parents'

							);

	return $listlogintypes[$id];

}

//------------Complaints---------

$ComplaintTypes = array (

						array ('status_id' => '1'		,'status_name' => 'Electricity')	,

						array ('status_id' => '2'		,'status_name' => 'Security')		,

						array ('status_id' => '3'		,'status_name' => 'Sanitation')		,

						array ('status_id' => '4'		,'status_name' => 'Miscellaneous')		

					);

function get_ComplaintTypes($id) {

	$listComplaintTypes = array (

							'1' => '<span class="label label-success" id="bns-status-badge">Electricity</span>'	, 

							'2' => '<span class="label label-info" id="bns-status-badge">Security</span>'			, 

							'3' => '<span class="label label-warning" id="bns-status-badge">Sanitation</span>'		,

							'4' => '<span class="label label-warning" id="bns-status-badge">Miscellaneous</span>'					

						  );

	return $listComplaintTypes[$id];

}

//------------Tarrif---------

$Tarrif = array (

						array ('status_id' => '1'		,'status_name' => 'Member')			,

						array ('status_id' => '2'		,'status_name' => 'Non Member')		,

						array ('status_id' => '3'		,'status_name' => 'Quarters')		,

						array ('status_id' => '4'		,'status_name' => 'Commercial')		,

						array ('status_id' => '5'		,'status_name' => 'Temporary')		,

						array ('status_id' => '6'		,'status_name' => 'Religion')		,

						array ('status_id' => '7'		,'status_name' => 'illegal')		

					);

function get_Tarriftype($id) {

	$lisTarriftype = array (

							'1' => '<span class="label label-success" id="bns-status-badge">Member</span>'			, 

							'2' => '<span class="label label-info" 	  id="bns-status-badge">Non Member</span>'		, 

							'3' => '<span class="label label-primary" id="bns-status-badge">Quarters</span>'		,

							'4' => '<span class="label label-primary" id="bns-status-badge">Commercial</span>'		,

							'5' => '<span class="label label-primary" id="bns-status-badge">Temporary</span>'		,

							'6' => '<span class="label label-primary" id="bns-status-badge">Religion</span>'		,

							'7' => '<span class="label label-warning" id="bns-status-badge">illegal</span>'					

						  );

	return $lisTarriftype[$id];

}

//--------------- Month By ----------

$monthby = array (

					array('id'=>'01', 'name'=>'Janaury')				,

					array('id'=>'02', 'name'=>'February')				,

					array('id'=>'03', 'name'=>'March')					,	

					array('id'=>'04', 'name'=>'April')					,

					array('id'=>'05', 'name'=>'May')					,

					array('id'=>'06', 'name'=>'June')					,

					array('id'=>'07', 'name'=>'July')					,

					array('id'=>'08', 'name'=>'August')					,

					array('id'=>'09', 'name'=>'September')				,

					array('id'=>'10', 'name'=>'October')				,

					array('id'=>'11', 'name'=>'November')				,

					array('id'=>'12', 'name'=>'December')	

				   );



function get_monthby($id) {

	$listmonthby = array (

							'01'		=> 'Janaury'						,

							'02'		=> 'February'						,

							'03'		=> 'March'							,

							'04'		=> 'April'							,

							'05'		=> 'May'							,

							'06'		=> 'June'							,

							'07'		=> 'July'							,

							'08'		=> 'August'							,

							'09'		=> 'September'						,

							'10'		=> 'October'						,

							'11'		=> 'November'						,

							'12'		=> 'December'		

							);

	return $listmonthby[$id];

}

//--------------- Months By ID ----------

$Months = array (

					array('id'=>'1', 'name'=>'Janaury')				,

					array('id'=>'2', 'name'=>'February')				,

					array('id'=>'3', 'name'=>'March')					,	

					array('id'=>'4', 'name'=>'April')					,

					array('id'=>'5', 'name'=>'May')					,

					array('id'=>'6', 'name'=>'June')					,

					array('id'=>'7', 'name'=>'July')					,

					array('id'=>'8', 'name'=>'August')					,

					array('id'=>'9', 'name'=>'September')				,

					array('id'=>'10', 'name'=>'October')				,

					array('id'=>'11', 'name'=>'November')				,

					array('id'=>'12', 'name'=>'December')	

				   );



function get_Months($id) {

	$listMonths = array (

							'1'		=> 'Janaury'						,

							'2'		=> 'February'						,

							'3'		=> 'March'							,

							'4'		=> 'April'							,

							'5'		=> 'May'							,

							'6'		=> 'June'							,

							'7'		=> 'July'							,

							'8'		=> 'August'							,

							'9'		=> 'September'						,

							'10'	=> 'October'						,

							'11'	=> 'November'						,

							'12'	=> 'December'		

							);

	return $listMonths[$id];

}

//--------------- Admins Rights ----------

$admtypes = array (

					array('id'=>1, 'name'=>'SuperAdmin')			,
					array('id'=>2, 'name'=>'Administrator')			,
					array('id'=>3, 'name'=>'Teacher')				,
					array('id'=>4, 'name'=>'Accountant')			,
					array('id'=>5, 'name'=>'Receptionist')			,
					array('id'=>6, 'name'=>'Librarian')				,
					array('id'=>7, 'name'=>'Hostel')				,
					array('id'=>8, 'name'=>'Student')				

				   );



function get_admtypes($id) {

	$listadmrights = array (

							'1'	=> 'SuperAdmin'					,
							'2'	=> 'Administrator'				,
							'3'	=> 'Teacher'					,
							'4'	=> 'Accountant'					,
							'5'	=> 'Receptionist'				,
							'6'	=> 'Librarian'					,
							'7'	=> 'Hostel'						,
							'8'	=> 'Student'		

							);

	return $listadmrights[$id];

}

//--------------- Course Level ----------

$curslevel = array('Basic', 'Middle' ,'Advance');

//--------------- Pay Method ------------------

$paymethod = array (

						array('id'=>1, 'name'=>'Paypal')			, 

						array('id'=>2, 'name'=>'Bank')

				   );



function get_paymethod($id) {

	$listpaymethod = array (

							'1' => 'Paypal'						, 

							'2' => 'Bank'

						  );

	return $listpaymethod[$id];

}



//--------------- Payments Status ------------------

$payments = array (

						array('id'=>1, 'name'=>'Paid')		, 

						array('id'=>2, 'name'=>'Unpaid')	

				   );



function get_payments($id) {

	$listpayments = array (

							'1' => 'Paid'					, 

							'2' => 'Unpaid'	

						  );

	return $listpayments[$id];

}

//--------------- Main Heads ------------------

$feedbackpoint = array (

					array('id'=>1, 'name'=>'Good'),

					array('id'=>2, 'name'=>'Very Good'),

					array('id'=>3, 'name'=>'Excellent'),

					array('id'=>4, 'name'=>'Neutral'),

					array('id'=>5, 'name'=>'Bad')

				   );



function get_feedbackpoint($id) {

	$listfeedbackpoint = array (

							'1'	=> 'Good',

							'2'	=> 'Very Good',

							'3'	=> 'Excellent',

							'4'	=> 'Neutral',

							'5'	=> 'Bad'

							);

	return $listfeedbackpoint[$id];

}

//--------------- Status Yes No ----------

$statusyesno = array (

						array('id'=>1, 'name'=>'Yes'), array('id'=>2, 'name'=>'No')

				   );



function get_statusyesno($id) {

	

	$liststatusyesno = array (

								'1'	=> 'Yes',	'2'	=> 'No'

							 );

	return $liststatusyesno[$id];

}
//--------------- Gender ----------
$GenderType = array (
						array('id'=>1, 'name'=>'Male')			, 
						array('id'=>2, 'name'=>'Female')			,	 
						array('id'=>3, 'name'=>'Others')
				   );

function get_GenderType($id) {
	$listGenderType = array ( '1' => 'Male'						, 
							  '2' => 'Female'						, 
							  '3' => 'Others');
	return $listGenderType[$id];
}
//--------------- Payment Types ------------------

$pay_types = array (

						array('id'=>1, 'name'=>'Bank'), array('id'=>2, 'name'=>'Cash'), array('id'=>3, 'name'=>'Cheque')

				   );



function get_paytypes($id) {

	$listpaytypes = array ('1' => 'Bank', '2' => 'Cash', '3' => 'Cheque');

	return $listpaytypes[$id];

}

//--------------- Gender ----------

$gender = array('Male', 'Female');

//--------------- Curruncy ----------

$curruncy = array('AUD','USD', 'CAD', 'EUR', 'GBP');

//--------------- Marital Status ----------

$marital = array('Married', 'Single');

//----------------Blood Groups------------------------------

$bloodgroup = array('A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-');

//----------------Blood Groups------------------------------



function cleanvars($str){ 
	return is_array($str) ? array_map('cleanvars', $str) :  str_replace( "\\",  "\\\\",   htmlspecialchars( stripslashes($str)  , ENT_QUOTES)   )  ; 

}

//----------------Time Format------------------------------

function get_hours_range( $start = 0, $end = 86400, $step = 1800, $format = 'g:i a' ) {

        $times = array();

        foreach ( range( $start, $end, $step ) as $timestamp ) {

                $hour_mins = gmdate( 'H:i', $timestamp );

                if ( ! empty( $format ) )

                        $times[$hour_mins] = gmdate( $format, $timestamp );

                else $times[$hour_mins] = $hour_mins;

        }

        return $times;

}



//------------------------------------------- Key Value

function searchArrayKeyVal($sKey, $id, $array) {

	foreach ($array as $key => $val) {

		if ($val[$sKey] == $id) {

			return $key;

		}

	}

	return null;

}

//--------------- Arrary Search ------------------

function arrayKeyValueSearch($array, $key, $value)

{

    $results = array();

    if (is_array($array)) {

        if (isset($array[$key]) && $array[$key] == $value) {

            $results[] = $array;

        }

        foreach ($array as $subArray) {

            $results = array_merge($results, arrayKeyValueSearch($subArray, $key, $value));

        }

    }

    return $results;

}

//-----------------------------------------------------------------

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

//--------------- Log File Action----------

function get_logfile($id) {



	$listlogfile = array (

							'1' => 'Add'		, 

							'2' => 'Update'		, 

							'3' => 'Delete'		,

							'4' => 'Login'	

						  );

	return $listlogfile[$id];



}

//----------------------- Country LIST ----------------------------

$countrylist = array (

					array('id'=>1, 'name'=>'Afghanistan')							,

					array('id'=>2, 'name'=>'Albania')								,

					array('id'=>3, 'name'=>'Algeria')								,

					array('id'=>4, 'name'=>'Andorra')								,

					array('id'=>5, 'name'=>'Angola')								,

					array('id'=>6, 'name'=>'Antigua and Barbuda')					,

					array('id'=>7, 'name'=>'Argentina')								,

					array('id'=>8, 'name'=>'Armenia')								,

					array('id'=>9, 'name'=>'Aruba')									,

					array('id'=>10, 'name'=>'Australia')							,

					array('id'=>11, 'name'=>'Austria')								,

					array('id'=>12, 'name'=>'Azerbaijan')							,

					array('id'=>13, 'name'=>'Bahamas, The')							,

					array('id'=>14, 'name'=>'Bahrain')								,

					array('id'=>15, 'name'=>'Bangladesh')							,

					array('id'=>16, 'name'=>'Barbados')								,

					array('id'=>17, 'name'=>'Belarus')								,

					array('id'=>18, 'name'=>'Belgium')								,

					array('id'=>19, 'name'=>'Belize')								,

					array('id'=>20, 'name'=>'Benin')								,

					array('id'=>21, 'name'=>'Bhutan')								,

					array('id'=>22, 'name'=>'Bolivia')								,

					array('id'=>23, 'name'=>'Bosnia and Herzegovina')				,

					array('id'=>24, 'name'=>'Botswana')								,

					array('id'=>25, 'name'=>'Brazil')								,

					array('id'=>26, 'name'=>'Brunei')								,

					array('id'=>27, 'name'=>'Bulgaria')								,

					array('id'=>28, 'name'=>'Burkina Faso')							,

					array('id'=>29, 'name'=>'Burma')								,

					array('id'=>30, 'name'=>'Burundi')								,

					array('id'=>31, 'name'=>'Cambodia')								,

					array('id'=>32, 'name'=>'Cameroon')								,

					array('id'=>33, 'name'=>'Canada')								,

					array('id'=>34, 'name'=>'Cabo Verde')							,

					array('id'=>35, 'name'=>'Central African Republic')				,

					array('id'=>36, 'name'=>'Chad')									,

					array('id'=>37, 'name'=>'Chile')								,

					array('id'=>38, 'name'=>'China')								,

					array('id'=>39, 'name'=>'Colombia')								,

					array('id'=>40, 'name'=>'Comoros')								,

					array('id'=>41, 'name'=>'Congo, Democratic Republic of the')	,

					array('id'=>42, 'name'=>'Congo, Republic of the')				,

					array('id'=>43, 'name'=>'Costa Rica')							,

					array('id'=>44, 'name'=>'Cote dIvoire')							,

					array('id'=>45, 'name'=>'Croatia')								,

					array('id'=>46, 'name'=>'Cuba')									,

					array('id'=>47, 'name'=>'Curacao')								,

					array('id'=>48, 'name'=>'Cyprus')								,

					array('id'=>49, 'name'=>'Czechia')								,

					array('id'=>50, 'name'=>'Denmark')								,

					array('id'=>51, 'name'=>'Djibouti')								,

					array('id'=>52, 'name'=>'Dominica')								,

					array('id'=>53, 'name'=>'Dominican Republic')					,

					array('id'=>54, 'name'=>'East Timor (see Timor-Leste)')			,

					array('id'=>55, 'name'=>'Ecuador')								,

					array('id'=>56, 'name'=>'Egypt')								,

					array('id'=>57, 'name'=>'El Salvador')							,

					array('id'=>58, 'name'=>'Equatorial Guinea')					,

					array('id'=>59, 'name'=>'Eritrea')								,

					array('id'=>60, 'name'=>'Estonia')								,

					array('id'=>61, 'name'=>'Ethiopia')								,

					array('id'=>62, 'name'=>'Fiji')									,

					array('id'=>63, 'name'=>'Finland')								,

					array('id'=>64, 'name'=>'France')								,

					array('id'=>65, 'name'=>'Gabon')								,

					array('id'=>66, 'name'=>'Gambia, The')							,

					array('id'=>67, 'name'=>'Georgia')								,

					array('id'=>68, 'name'=>'Germany')								,

					array('id'=>69, 'name'=>'Ghana')								,

					array('id'=>70, 'name'=>'Greece')								,

					array('id'=>71, 'name'=>'Grenada')								,

					array('id'=>72, 'name'=>'Guatemala')							,

					array('id'=>73, 'name'=>'Guinea')								,

					array('id'=>74, 'name'=>'Guinea-Bissau')						,

					array('id'=>75, 'name'=>'Guyana')								,

					array('id'=>76, 'name'=>'Haiti')								,

					array('id'=>77, 'name'=>'Holy See')								,

					array('id'=>78, 'name'=>'Honduras')								,

					array('id'=>79, 'name'=>'Hong Kong')							,

					array('id'=>80, 'name'=>'Hungary')								,

					array('id'=>81, 'name'=>'Iceland')								,

					array('id'=>82, 'name'=>'India')								,

					array('id'=>83, 'name'=>'Indonesia')							,

					array('id'=>84, 'name'=>'Iran')									,

					array('id'=>85, 'name'=>'Iraq')									,

					array('id'=>86, 'name'=>'Ireland')								,

					array('id'=>87, 'name'=>'Israel')								,

					array('id'=>88, 'name'=>'Italy')								,

					array('id'=>89, 'name'=>'Jamaica')								,

					array('id'=>90, 'name'=>'Japan')								,

					array('id'=>91, 'name'=>'Jordan')								,

					array('id'=>92, 'name'=>'Kazakhstan')							,

					array('id'=>93, 'name'=>'Kenya')								,

					array('id'=>94, 'name'=>'Kiribati')								,

					array('id'=>95, 'name'=>'Korea, North')							,

					array('id'=>96, 'name'=>'Korea, South')							,

					array('id'=>97, 'name'=>'Kosovo')								,

					array('id'=>98, 'name'=>'Kuwait')								,

					array('id'=>99, 'name'=>'Kyrgyzstan')							,

					array('id'=>100, 'name'=>'Laos')								,

					array('id'=>101, 'name'=>'Latvia')								,

					array('id'=>102, 'name'=>'Lebanon')								,

					array('id'=>103, 'name'=>'Lesotho')								,

					array('id'=>104, 'name'=>'Liberia')								,

					array('id'=>105, 'name'=>'Libya')								,

					array('id'=>106, 'name'=>'Liechtenstein')						,

					array('id'=>107, 'name'=>'Lithuania')							,

					array('id'=>108, 'name'=>'Luxembourg')							,

					array('id'=>109, 'name'=>'Macau')								,

					array('id'=>110, 'name'=>'Macedonia')							,

					array('id'=>111, 'name'=>'Madagascar')							,

					array('id'=>112, 'name'=>'Malawi')								,

					array('id'=>113, 'name'=>'Malaysia')							,

					array('id'=>114, 'name'=>'Maldives')							,

					array('id'=>115, 'name'=>'Mali')								,

					array('id'=>116, 'name'=>'Malta')								,

					array('id'=>117, 'name'=>'Marshall Islands')					,

					array('id'=>118, 'name'=>'Mauritania')							,

					array('id'=>119, 'name'=>'Mauritius')							,

					array('id'=>120, 'name'=>'Mexico')								,

					array('id'=>121, 'name'=>'Micronesia')							,

					array('id'=>122, 'name'=>'Moldova')								,

					array('id'=>123, 'name'=>'Monaco')								,

					array('id'=>124, 'name'=>'Mongolia')							,

					array('id'=>125, 'name'=>'Montenegro')							,

					array('id'=>126, 'name'=>'Morocco')								,

					array('id'=>127, 'name'=>'Mozambique')							,

					array('id'=>128, 'name'=>'Namibia')								,

					array('id'=>129, 'name'=>'Nauru')								,

					array('id'=>130, 'name'=>'Nepal')								,

					array('id'=>131, 'name'=>'Netherlands')							,

					array('id'=>132, 'name'=>'New Zealand')							,

					array('id'=>133, 'name'=>'Nicaragua')							,

					array('id'=>134, 'name'=>'Niger')								,

					array('id'=>135, 'name'=>'Nigeria')								,

					array('id'=>136, 'name'=>'North Korea')							,

					array('id'=>137, 'name'=>'Norway')								,

					array('id'=>138, 'name'=>'Oman')								,

					array('id'=>139, 'name'=>'Pakistan')							,

					array('id'=>140, 'name'=>'Palau')								,

					array('id'=>141, 'name'=>'Palestinian Territories')				,

					array('id'=>142, 'name'=>'Panama')								,

					array('id'=>143, 'name'=>'Papua New Guinea')					,

					array('id'=>144, 'name'=>'Paraguay')							,

					array('id'=>145, 'name'=>'Peru')								,

					array('id'=>146, 'name'=>'Philippines')							,

					array('id'=>147, 'name'=>'Poland')								,

					array('id'=>148, 'name'=>'Portugal')							,

					array('id'=>149, 'name'=>'Qatar')								,

					array('id'=>150, 'name'=>'Romania')								,

					array('id'=>151, 'name'=>'Russia')								,

					array('id'=>152, 'name'=>'Rwanda')								,

					array('id'=>153, 'name'=>'Saint Kitts and Nevis')				,

					array('id'=>154, 'name'=>'Saint Lucia')							,

					array('id'=>155, 'name'=>'Saint Vincent and the Grenadines')	,

					array('id'=>156, 'name'=>'Samoa')								,

					array('id'=>157, 'name'=>'San Marino')							,

					array('id'=>158, 'name'=>'Sao Tome and Principe')				,

					array('id'=>159, 'name'=>'Saudi Arabia')						,

					array('id'=>160, 'name'=>'Senegal')								,

					array('id'=>161, 'name'=>'Serbia')								,

					array('id'=>162, 'name'=>'Seychelles')							,

					array('id'=>163, 'name'=>'Sierra Leone')						,

					array('id'=>164, 'name'=>'Singapore')							,

					array('id'=>165, 'name'=>'Sint Maarten')						,

					array('id'=>166, 'name'=>'Slovakia')							,

					array('id'=>167, 'name'=>'Slovenia')							,

					array('id'=>168, 'name'=>'Solomon Islands')						,	

					array('id'=>169, 'name'=>'Somalia')								,

					array('id'=>170, 'name'=>'South Africa')						,

					array('id'=>171, 'name'=>'South Korea')							,

					array('id'=>172, 'name'=>'South Sudan')							,

					array('id'=>173, 'name'=>'Spain')								,

					array('id'=>174, 'name'=>'Sri Lanka')							,

					array('id'=>175, 'name'=>'Sudan')								,

					array('id'=>176, 'name'=>'Suriname')							,

					array('id'=>177, 'name'=>'Swaziland')							,

					array('id'=>178, 'name'=>'Sweden')								,

					array('id'=>179, 'name'=>'Switzerland')							,

					array('id'=>180, 'name'=>'Syria')								,

					array('id'=>181, 'name'=>'Taiwan')								,

					array('id'=>182, 'name'=>'Tajikistan')							,

					array('id'=>183, 'name'=>'Tanzania')							,

					array('id'=>184, 'name'=>'Thailand')							,

					array('id'=>185, 'name'=>'Timor-Leste')							,

					array('id'=>186, 'name'=>'Togo')								,

					array('id'=>187, 'name'=>'Tonga')								,

					array('id'=>188, 'name'=>'Trinidad and Tobago')					,

					array('id'=>189, 'name'=>'Tunisia')								,

					array('id'=>190, 'name'=>'Turkey')								,

					array('id'=>191, 'name'=>'Turkmenistan')						,

					array('id'=>192, 'name'=>'Tuvalu')								,

					array('id'=>193, 'name'=>'Uganda')								,

					array('id'=>194, 'name'=>'Ukraine')								,

					array('id'=>195, 'name'=>'United Arab Emirates')				,

					array('id'=>196, 'name'=>'United Kingdom')						,

					array('id'=>197, 'name'=>'Uruguay')								,

					array('id'=>198, 'name'=>'Uzbekistan')							,

					array('id'=>199, 'name'=>'Vanuatu')								,

					array('id'=>200, 'name'=>'Venezuela')							,

					array('id'=>201, 'name'=>'Vietnam')								,

					array('id'=>202, 'name'=>'Yemen')								,

					array('id'=>203, 'name'=>'Zambia')								,

					array('id'=>204, 'name'=>'Zimbabwe')						

 );



//------------------------------------------------------------------

function generateString132($len128 = 128) {
	$token = "poiuztrewqasdfghjklmnbvcxy1234567890abcdefghijklmnopqrstuvwxyz0123456789";
	$token = str_shuffle($token);
	$token = substr($token, 0, $len128);
	return $token;
}
//------------------------------------------------------------------
function generateString36($len36 = 36) {
	$token36 = "poiuztrewqasdfghjklmnbvcxy1234567890";
	$token36 = str_shuffle($token36);
	$token36 = substr($token36, 0, $len36);
	return $token36;
}
//------------------------------------------------------------------

function redirectToLoginPage() {

	header('Location: login.php');

	exit();

}

//------------------------------------------------------------------

gethostname();

gethostbyname(gethostname());

php_uname();



class OS_BR{



    private $agent = "";

    private $info = array();



    function __construct(){

        $this->agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;

        $this->getBrowser();

        $this->getOS();

    }



    function getBrowser(){

        $browser = array("Navigator"            => "/Navigator(.*)/i",

                         "Firefox"              => "/Firefox(.*)/i",

                         "Internet Explorer"    => "/MSIE(.*)/i",

                         "Google Chrome"        => "/chrome(.*)/i",

                         "MAXTHON"              => "/MAXTHON(.*)/i",

                         "Opera"                => "/Opera(.*)/i",

                         );

        foreach($browser as $key => $value){

            if(preg_match($value, $this->agent)){

                $this->info = array_merge($this->info,array("Browser" => $key));

                $this->info = array_merge($this->info,array(

                  "Version" => $this->getVersion($key, $value, $this->agent)));

                break;

            }else{

                $this->info = array_merge($this->info,array("Browser" => "UnKnown"));

                $this->info = array_merge($this->info,array("Version" => "UnKnown"));

            }

        }

        return $this->info['Browser'];

    }



    function getOS(){

        $OS = array("Windows"   =>   "/Windows/i",

                    "Linux"     =>   "/Linux/i",

                    "Unix"      =>   "/Unix/i",

                    "Mac"       =>   "/Mac/i"

                    );



        foreach($OS as $key => $value){

            if(preg_match($value, $this->agent)){

                $this->info = array_merge($this->info,array("Operating System" => $key));

                break;

            }

        }

        return $this->info['Operating System'];

    }



    function getVersion($browser, $search, $string){

        $browser = $this->info['Browser'];

        $version = "";

        $browser = strtolower($browser);

        preg_match_all($search,$string,$match);

        switch($browser){

            case "firefox": $version = str_replace("/","",$match[1][0]);

            break;



            case "internet explorer": $version = substr($match[1][0],0,4);

            break;



            case "opera": $version = str_replace("/","",substr($match[1][0],0,5));

            break;



            case "navigator": $version = substr($match[1][0],1,7);

            break;



            case "maxthon": $version = str_replace(")","",$match[1][0]);

            break;



            case "google chrome": $version = substr($match[1][0],1,10);

        }

        return $version;

    }



    function showInfo($switch){

        $switch = strtolower($switch);

        switch($switch){

            case "browser": return $this->info['Browser'];

            break;



            case "os": return $this->info['Operating System'];

            break;



            case "version": return $this->info['Version'];

            break;



            case "all" : return array($this->info["Version"], 

              $this->info['Operating System'], $this->info['Browser']);

            break;



            default: return "Unkonw";

            break;



        }

    }

}



// using

// create an new instant of OS_BR class

$obj = new OS_BR();

// // if you want to show one by one information then try showInfo() function



//------------------------------------------------------------------

function get_timeago( $ptime )

{

    $estimate_time = time() - $ptime;



    if( $estimate_time < 1 )

    {

        return 'less than 1 second ago';

    }



    $condition = array( 

                12 * 30 * 24 * 60 * 60  =>  'year',

                30 * 24 * 60 * 60       =>  'month',

                24 * 60 * 60            =>  'day',

                60 * 60                 =>  'hour',

                60                      =>  'minute',

                1                       =>  'second'

    );



    foreach( $condition as $secs => $str )

    {

        $d = $estimate_time / $secs;



        if( $d >= 1 )

        {

            $r = round( $d );

            return 'about ' . $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';

        }

    }

}

//-------Rupees in Word-------------------------------

function convert_number_to_words($number) {



    $hyphen      = '-';

    $conjunction = ' and ';

    $separator   = ', ';

    $negative    = 'negative ';

    $decimal     = ' point ';

    $dictionary  = array(

        0                   => 'Zero',

        1                   => 'One',

        2                   => 'Two',

        3                   => 'Three',

        4                   => 'Four',

        5                   => 'Five',

        6                   => 'Six',

        7                   => 'Seven',

        8                   => 'Eight',

        9                   => 'Nine',

        10                  => 'Ten',

        11                  => 'Eleven',

        12                  => 'Twelve',

        13                  => 'Thirteen',

        14                  => 'Fourteen',

        15                  => 'Fifteen',

        16                  => 'Sixteen',

        17                  => 'Seventeen',

        18                  => 'Eighteen',

        19                  => 'Nineteen',

        20                  => 'Twenty',

        30                  => 'Thirty',

        40                  => 'Fourty',

        50                  => 'Fifty',

        60                  => 'Sixty',

        70                  => 'Seventy',

        80                  => 'Eighty',

        90                  => 'Ninety',

        100                 => 'Hundred',

        1000                => 'Thousand',

        1000000             => 'Million',

        1000000000          => 'Billion',

        1000000000000       => 'Trillion',

        1000000000000000    => 'Quadrillion',

        1000000000000000000 => 'Quintillion'

    );



    if (!is_numeric($number)) {

        return false;

    }



    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {

        // overflow

        trigger_error(

            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,

            E_USER_WARNING

        );

        return false;

    }



    if ($number < 0) {

        return $negative . convert_number_to_words(abs($number));

    }



    $string = $fraction = null;



    if (strpos($number, '.') !== false) {

        list($number, $fraction) = explode('.', $number);

    }



    switch (true) {

        case $number < 21:

            $string = $dictionary[$number];

            break;

        case $number < 100:

            $tens   = ((int) ($number / 10)) * 10;

            $units  = $number % 10;

            $string = $dictionary[$tens];

            if ($units) {

                $string .= $hyphen . $dictionary[$units];

            }

            break;

        case $number < 1000:

            $hundreds  = $number / 100;

            $remainder = $number % 100;

            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];

            if ($remainder) {

                $string .= $conjunction . convert_number_to_words($remainder);

            }

            break;

        default:

            $baseUnit = pow(1000, floor(log($number, 1000)));

            $numBaseUnits = (int) ($number / $baseUnit);

            $remainder = $number % $baseUnit;

            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];

            if ($remainder) {

                $string .= $remainder < 100 ? $conjunction : $separator;

                $string .= convert_number_to_words($remainder);

            }

            break;

    }



    if (null !== $fraction && is_numeric($fraction)) {

        $string .= $decimal;

        $words = array();

        foreach (str_split((string) $fraction) as $number) {

            $words[] = $dictionary[$number];

        }

        $string .= implode(' ', $words);

    }



    return $string;

}



function ordinal($number) {

    $ends = array('th','st','nd','rd','th','th','th','th','th','th');

    if ((($number % 100) >= 11) && (($number%100) <= 13))

        return $number. 'th';

    else

        return $number. $ends[$number % 10];

}



//------------------------------------------------------------------

function getskills($input){
	switch ($input) {
        case 0:
            return 0;
        case 1:
            return 25;
        case 2:
            return 50;
        case 3:
            return 75;
        case 4:
            return 100;
        default:
            return 0;
    }
}

	

?>