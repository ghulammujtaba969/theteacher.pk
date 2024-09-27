<?php

function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}

//----------------------- Country LIST ----------------------------
$countrylist = array (
					array('id'=>4,  'name'=>'Afghanistan')									,
					array('id'=>5,  'name'=>'Bangladesh')									,
					array('id'=>16, 'name'=>'Albania')										,
					array('id'=>17, 'name'=>'Algeria')										,
					array('id'=>18, 'name'=>'Andorra')										,
					array('id'=>19, 'name'=>'Angola')										,
					array('id'=>20, 'name'=>'Anguilla')										,
					array('id'=>21, 'name'=>'Antarctica')									,
					array('id'=>22, 'name'=>'Argentina')									,
					array('id'=>23, 'name'=>'Armenia')										,
					array('id'=>24, 'name'=>'Aruba')										,
					array('id'=>25, 'name'=>'Australia')									,
					array('id'=>26, 'name'=>'Austria')										,
					array('id'=>27, 'name'=>'Azerbaijan')									,
					array('id'=>28, 'name'=>'Bahamas')										,
					array('id'=>29, 'name'=>'Bahrain')										,
					array('id'=>30, 'name'=>'Barbados')										,
					array('id'=>31, 'name'=>'Belarus')										,
					array('id'=>32, 'name'=>'Belgium')										,
					array('id'=>33, 'name'=>'Belize')										,
					array('id'=>34, 'name'=>'Benin')										,
					array('id'=>35, 'name'=>'Bermuda')										,
					array('id'=>36, 'name'=>' Bhutan')										,
					array('id'=>37, 'name'=>'Bolivia')										,
					array('id'=>38, 'name'=>'Bosnia')										,
					array('id'=>39, 'name'=>'Botswana')										,
					array('id'=>40, 'name'=>'Bouvet Island')								,
					array('id'=>41, 'name'=>'Brazil')										,
					array('id'=>42, 'name'=>'Brunei')										,
					array('id'=>43, 'name'=>'Bulgaria')										,
					array('id'=>44, 'name'=>'Burkina Faso')									,
					array('id'=>45, 'name'=>'Burundi')										,
					array('id'=>46, 'name'=>'Cambodia')										,
					array('id'=>47, 'name'=>'Cameroon')										,
					array('id'=>3,  'name'=>'Canada')										,
					array('id'=>48, 'name'=>'Cape Verde')									,
					array('id'=>49, 'name'=>'Cayman Islands')								,
					array('id'=>50, 'name'=>'Central African Republic')						,
					array('id'=>51, 'name'=>'Chad52Chile')									,
					array('id'=>53, 'name'=>'China')										,	
					array('id'=>54, 'name'=>'Christmas Island')								,
					array('id'=>55, 'name'=>'Cocos (Keeling) Islands')						,
					array('id'=>56, 'name'=>'Colombia')										,
					array('id'=>57, 'name'=>'Comoros')										,
					array('id'=>58, 'name'=>'Congo')										,
					array('id'=>65, 'name'=>'Congo (DRC)')									,
					array('id'=>59, 'name'=>'Cook Islands')									,
					array('id'=>60, 'name'=>'Costa Rica')									,
					array('id'=>61, 'name'=>'Croatia (Hrvatska)')							,
					array('id'=>62, 'name'=>'Cuba')											,
					array('id'=>63, 'name'=>'Cyprus')										,
					array('id'=>64, 'name'=>'Czech Republic')								,
					array('id'=>6,  'name'=>'Denmark')										,
					array('id'=>66, 'name'=>'Djibouti')										,
					array('id'=>67, 'name'=>'Dominica')										,
					array('id'=>68, 'name'=>'Dominican Republic')							,
					array('id'=>69, 'name'=>'East Timor')									,
					array('id'=>70, 'name'=>'Ecuador')										,
					array('id'=>71, 'name'=>'Egypt')										,
					array('id'=>72, 'name'=>'El Salvador')									,
					array('id'=>9,  'name'=>'England')										,
					array('id'=>73, 'name'=>'Equatorial Guinea')							,
					array('id'=>74, 'name'=>'Eritrea')										,
					array('id'=>75, 'name'=>'Estonia')										,
					array('id'=>76, 'name'=>'Ethiopia')										,
					array('id'=>77, 'name'=>'Falkland Islands')								,
					array('id'=>78, 'name'=>'Faroe Islands')								,
					array('id'=>79, 'name'=>'Fiji Islands')									,
					array('id'=>80, 'name'=>'Finland10France')								,
					array('id'=>81, 'name'=>'French Guiana')								,
					array('id'=>82, 'name'=>'French Polynesia')								,
					array('id'=>83, 'name'=>'Gabon')										,
					array('id'=>84, 'name'=>'Gambia')										,
					array('id'=>85, 'name'=>'Georgia')										,
					array('id'=>86, 'name'=>'Germany')										,
					array('id'=>87, 'name'=>'Ghana')										,
					array('id'=>88, 'name'=>'Gibraltar')									,
					array('id'=>89, 'name'=>'Greece')										,
					array('id'=>90, 'name'=>'Greenland')									,
					array('id'=>91, 'name'=>'Grenada')										,
					array('id'=>92, 'name'=>'Guadeloupe')									,
					array('id'=>93, 'name'=>'Guam')											,
					array('id'=>94, 'name'=>'Guatemala')									,
					array('id'=>95, 'name'=>'Guinea')										,	
					array('id'=>96, 'name'=>'Guinea-Bissau')								,
					array('id'=>97, 'name'=>'Guyana')										,
					array('id'=>98, 'name'=>'Haiti')										,
					array('id'=>99, 'name'=>'Honduras')										,
					array('id'=>100, 'name'=>'Hong Kong SAR')								,
					array('id'=>101, 'name'=>'Hungary')										,
					array('id'=>102, 'name'=>'Iceland')										,
					array('id'=>2, 'name'=>'India')											,
					array('id'=>103, 'name'=>'Indonesia')									,
					array('id'=>14, 'name'=>'Iran')											,
					array('id'=>104, 'name'=>'Iraq')										,
					array('id'=>105, 'name'=>'Ireland')										,
					array('id'=>13, 'name'=>'Italy')										,
					array('id'=>106, 'name'=>'Jamaica')										,
					array('id'=>107, 'name'=>'Japan')										,
					array('id'=>108, 'name'=>'Jordan')										,
					array('id'=>109, 'name'=>'Kazakhstan')									,
					array('id'=>110, 'name'=>'Kenya')										,
					array('id'=>111, 'name'=>'Kiribati')									,
					array('id'=>112, 'name'=>'Korea')										,
					array('id'=>113, 'name'=>'Kuwait')										,
					array('id'=>114, 'name'=>'Kyrgyzstan')									,
					array('id'=>115, 'name'=>'Laos')										,
					array('id'=>116, 'name'=>'Latvia')										,
					array('id'=>117, 'name'=>'Lebanon')										,
					array('id'=>118, 'name'=>'Lesotho')										,
					array('id'=>119, 'name'=>'Liberia')										,
					array('id'=>120, 'name'=>'Libya')										,
					array('id'=>121, 'name'=>'Liechtenstein')								,
					array('id'=>122, 'name'=>'Lithuania')									,
					array('id'=>123, 'name'=>'Luxembourg')									,
					array('id'=>124, 'name'=>'Macao SAR')									,
					array('id'=>125, 'name'=>'Macedonia')									,
					array('id'=>126, 'name'=>'Madagascar')									,
					array('id'=>127, 'name'=>'Malawi')										,
					array('id'=>128, 'name'=>'Malaysia')									,
					array('id'=>129, 'name'=>'Maldives')									,
					array('id'=>130, 'name'=>'Mali')										,
					array('id'=>131, 'name'=>'Malta')										,
					array('id'=>132, 'name'=>'Marshall Islands')							,
					array('id'=>133, 'name'=>'Martinique')									,
					array('id'=>134, 'name'=>'Mauritania')									,
					array('id'=>135, 'name'=>'Mauritius')									,
					array('id'=>136, 'name'=>'Mayotte')										,
					array('id'=>137, 'name'=>'Mexico')										,
					array('id'=>138, 'name'=>'Micronesia')									,
					array('id'=>139, 'name'=>'Moldova')										,
					array('id'=>140, 'name'=>'Monaco')										,
					array('id'=>141, 'name'=>'Mongolia')									,
					array('id'=>142, 'name'=>'Montserrat')									,
					array('id'=>143, 'name'=>'Morocco')										,
					array('id'=>144, 'name'=>'Mozambique')									,
					array('id'=>145, 'name'=>'Myanmar')										,
					array('id'=>146, 'name'=>'Namibia')										,
					array('id'=>147, 'name'=>'Nauru')										,
					array('id'=>12,  'name'=>'Nepal')										,
					array('id'=>148, 'name'=>'Netherlands')									,
					array('id'=>149, 'name'=>'Netherlands Antilles')						,
					array('id'=>150, 'name'=>'New Caledonia')								,
					array('id'=>15,  'name'=>'New Zeeland')									,
					array('id'=>151, 'name'=>'Nicaragua')									,
					array('id'=>152, 'name'=>'Niger')										,
					array('id'=>153, 'name'=>'Nigeria')										,
					array('id'=>154, 'name'=>'Niue')										,
					array('id'=>155, 'name'=>'Norfolk Island')								,
					array('id'=>156, 'name'=>'North Korea')									,
					array('id'=>157, 'name'=>'Northern Mariana Islands')					,
					array('id'=>158, 'name'=>'Norway')										,
					array('id'=>159, 'name'=>'Not Listed')									,
					array('id'=>11, 'name'=>'Oman')											,
					array('id'=>1,   'name'=>'Pakistan')									,
					array('id'=>160, 'name'=>'Palau')										,
					array('id'=>161, 'name'=>'Palestine')									,
					array('id'=>162, 'name'=>'Panama')										,
					array('id'=>163, 'name'=>'Papua New Guinea')							,
					array('id'=>164, 'name'=>'Paraguay')									,
					array('id'=>165, 'name'=>'Peru')										,
					array('id'=>166, 'name'=>'Philippines')									,
					array('id'=>167, 'name'=>'Pitcairn Islands')							,
					array('id'=>168, 'name'=>'Poland')										,
					array('id'=>169, 'name'=>'Portugal')									,
					array('id'=>170, 'name'=>'Puerto Rico')									,
					array('id'=>171, 'name'=>'Qatar')										,
					array('id'=>172, 'name'=>'Reunion')										,
					array('id'=>173, 'name'=>'Romania')										,
					array('id'=>174, 'name'=>'Russia')										,
					array('id'=>175, 'name'=>'Rwanda')										,
					array('id'=>176, 'name'=>'Samoa')										,
					array('id'=>177, 'name'=>'San Marino')									,
					array('id'=>7,   'name'=>'Saudi Arabia')								,
					array('id'=>231, 'name'=>'Scotland')									,
					array('id'=>178, 'name'=>'Senegal')										,
					array('id'=>179, 'name'=>'Serbia and Montenegro')						,
					array('id'=>180, 'name'=>'Seychelles')									,
					array('id'=>181, 'name'=>'Sierra Leone')								,
					array('id'=>182, 'name'=>'Singapore')									,
					array('id'=>183, 'name'=>'Slovakia')									,	
					array('id'=>184, 'name'=>'Slovenia')									,
					array('id'=>185, 'name'=>'Solomon Islands')								,
					array('id'=>186, 'name'=>'Somalia')										,
					array('id'=>187, 'name'=>'South Africa')								,
					array('id'=>188, 'name'=>'Spain')										,
					array('id'=>189, 'name'=>'Sri Lanka')									,
					array('id'=>190, 'name'=>'St. Helena')									,
					array('id'=>191, 'name'=>'St. Kitts and Nevis')							,
					array('id'=>192, 'name'=>'St. Lucia')									,
					array('id'=>193, 'name'=>'St. Pierre and Miquelon')						,
					array('id'=>194, 'name'=>'St. Vincent and the Grenadines')				,
					array('id'=>195, 'name'=>'Sudan196Suriname')							,
					array('id'=>197, 'name'=>'Svalbard and Jan Mayen')						,
					array('id'=>198, 'name'=>'Swaziland')									,
					array('id'=>199, 'name'=>'Sweden')										,
					array('id'=>200, 'name'=>'Switzerland')									,
					array('id'=>201, 'name'=>'Syria')										,
					array('id'=>202, 'name'=>'Taiwan')										,
					array('id'=>203, 'name'=>'Tajikistan')									,
					array('id'=>204, 'name'=>'Tanzania')									,
					array('id'=>205, 'name'=>'Thailand')									,
					array('id'=>206, 'name'=>'Togo')										,
					array('id'=>207, 'name'=>'Tokelau')										,
					array('id'=>208, 'name'=>'Tonga')										,
					array('id'=>209, 'name'=>'Trinidad and Tobago')							,
					array('id'=>210, 'name'=>'Tunisia')										,
					array('id'=>211, 'name'=>'Turkey')										,
					array('id'=>212, 'name'=>'Turkmenistan')								,
					array('id'=>213, 'name'=>'Turks and Caicos Islands')					,
					array('id'=>214, 'name'=>'Tuvalu')										,
					array('id'=>215, 'name'=>'Uganda')										,
					array('id'=>216, 'name'=>'Ukraine')										,
					array('id'=>217, 'name'=>'United Arab Emirates')						,
					array('id'=>8,   'name'=>'United Kingdom')								,
					array('id'=>218, 'name'=>'United States')								,
					array('id'=>219, 'name'=>'Uruguay')										,
					array('id'=>220, 'name'=>'Uzbekistan')									,
					array('id'=>221, 'name'=>'Vanuatu')										,
					array('id'=>222, 'name'=>'Vatican City')								,
					array('id'=>223, 'name'=>'Venezuela')									,
					array('id'=>224, 'name'=>'Viet Nam')									,
					array('id'=>226, 'name'=>'Virgin Islands')								,
					array('id'=>225, 'name'=>'Virgin Islands (British)')					,
					array('id'=>227, 'name'=>'Wallis and Futuna')							,
					array('id'=>228, 'name'=>'Yemen')										,
					array('id'=>229, 'name'=>'Zambia')										,
					array('id'=>230, 'name'=>'Zimbabwe')
 );

//-----------------------------------------------------------------

function searchArrayKeyVal($sKey, $id, $array) {
	foreach ($array as $key => $val) {
		if ($val[$sKey] == $id) {
			return $key;
		}
	}
	return null;
}

//-----------------------------------------------------------------

//echo ip_info("116.58.42.131", "Country"); // United States

$countyKeyID = searchArrayKeyVal("name", ip_info($_SERVER['REMOTE_ADDR'], "country"), $countrylist);
$cuntryid	 = $countrylist[$countyKeyID]['id'];


?>