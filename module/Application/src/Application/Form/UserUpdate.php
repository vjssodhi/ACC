<?php

namespace Application\Form;

use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Validator\Identical;
use Zend\I18n\Validator\Alnum;

class UserUpdate extends Form {
	private $displayAllFields;
	private $userList;
	public function __construct($userList = array(), $name = null, $options = array(), $displayAllFields = false) {
		$this->displayAllFields = $displayAllFields;
		$this->userList = $userList;
		parent::__construct ( $name, $options );
		$this->addElements ();
		$this->setInputFilter ( $this->createInputFilter () );
	}
	public function addElements() {
		$this->add ( array (
				'name' => 'fullName',
				'type' => 'Zend\Form\Element\Text',
				'attributes' => array (
						'placeholder' => 'Name',
						'class' => 'form-control',
						'id' => 'fullNameI',
						'required' => 'required' 
				),
				'options' => array (
						'label' => 'Name' 
				) 
		) );
		$this->add ( array (
				'name' => 'employeeCode',
				'attributes' => array (
						'placeholder' => 'Employee Code',
						'class' => 'form-control',
						'id' => 'empCodeI',
						'required' => 'required' 
				),
				'options' => array (
						'label' => 'Employee Code' 
				) 
		) );
		$this->add ( array (
				'name' => 'loginId',
				'attributes' => array (
						'placeholder' => 'Login Id for User',
						'class' => 'form-control',
						'id' => 'loginIdI',
						'required' => 'required' 
				),
				'options' => array (
						'label' => 'Login ID' 
				) 
		) );
		$dates = array ();
		for($i = 1; $i <= 31; $i = $i + 1) {
			$dates [$i] = $i;
		}
		$this->add ( array (
				'type' => 'Zend\Form\Element\Select',
				'name' => 'birthDay',
				'options' => array (
						'empty_option' => '<Date>',
						'label' => '',
						'value_options' => $dates 
				),
				'attributes' => array (
						'class' => 'form-control',
						'id' => 'birthDayS',
						'required' => 'required' 
				) 
		) );
		
		$this->add ( array (
				'type' => 'Zend\Form\Element\Select',
				'name' => 'userIdParent',
				'options' => array (
						'empty_option' => '<Select Supervisor/Manager>',
						'label' => '',
						'value_options' => $this->userList 
				),
				'attributes' => array (
						'required' => 'required',
						'class' => 'form-control',
						'id' => 'userIdParentS' 
				) 
		) );
		$this->add ( array (
				'type' => 'Zend\Form\Element\Select',
				'name' => 'birthMonth',
				'options' => array (
						'empty_option' => '<Month>',
						'label' => '',
						'value_options' => array (
								'01' => 'Jan',
								'02' => 'February',
								'03' => 'March',
								'04' => 'April',
								'05' => 'May',
								'06' => 'June',
								'07' => 'July',
								'08' => 'August',
								'09' => 'September',
								'10' => 'October',
								'11' => 'November',
								'12' => 'December' 
						) 
				),
				'attributes' => array (
						'required' => 'required',
						'class' => 'form-control',
						'id' => 'birthMonthS' 
				) 
		) );
		$this->add ( array (
				'name' => 'birthYear',
				'attributes' => array (
						'required' => 'required',
						'class' => 'form-control',
						'id' => 'birthYearI',
						'placeholder' => 'Year' 
				),
				'options' => array (
						'label' => '' 
				) 
		) );
		$this->add ( 

		array (
				'type' => 'Zend\Form\Element\Select',
				'name' => 'gender',
				'options' => array (
						'empty_option' => '<Select Gender>',
						'label' => 'Gender',
						'value_options' => array (
								'Male' => 'Male',
								'Female' => 'Female',
								'other' => 'Other' 
						) 
				),
				'attributes' => array (
						'required' => 'required',
						'class' => 'form-control',
						'id' => 'bioGenderS' 
				) 
		) );
		$this->add ( array (
				'name' => 'mobile',
				'attributes' => array (
						'required' => 'required',
						'class' => 'form-control',
						'id' => 'mobileI',
						'placeholder' => 'Mobile' 
				),
				'options' => array (
						'label' => '' 
				) 
		) );
		if ($this->displayAllFields) {
			$this->add ( array (
					'type' => 'Zend\Form\Element\Select',
					'name' => 'marritalStatus',
					'options' => array (
							'empty_option' => '<Your Marrital Status>',
							'label' => 'Marrital Status',
							'value_options' => array (
									'Single' => 'Single',
									'Married' => 'Married' 
							) 
					),
					'attributes' => array (
							'required' => 'required',
							'class' => 'form-control',
							'id' => 'marritalStatusS' 
					) 
			) );
			
			$this->add ( array (
					'name' => 'nationality',
					'attributes' => array (
							'class' => 'form-control',
							'required' => 'required' 
					),
					'type' => 'Zend\Form\Element\Select',
					'options' => array (
							'empty_option' => '<Country>',
							'label' => 'Country',
							'value_options' => array (
									"AF" => "Afghanistan",
									"AX" => "Åland Islands",
									"AL" => "Albania",
									"DZ" => "Algeria",
									"AS" => "American Samoa",
									"AD" => "Andorra",
									"AO" => "Angola",
									"AI" => "Anguilla",
									"AQ" => "Antarctica",
									"AG" => "Antigua and Barbuda",
									"AR" => "Argentina",
									"AM" => "Armenia",
									"AW" => "Aruba",
									"AU" => "Australia",
									"AT" => "Austria",
									"AZ" => "Azerbaijan",
									"BS" => "Bahamas",
									"BH" => "Bahrain",
									"BD" => "Bangladesh",
									"BB" => "Barbados",
									"BY" => "Belarus",
									"BE" => "Belgium",
									"BZ" => "Belize",
									"BJ" => "Benin",
									"BM" => "Bermuda",
									"BT" => "Bhutan",
									"BO" => "Bolivia, Plurinational State of",
									"BQ" => "Bonaire, Sint Eustatius and Saba",
									"BA" => "Bosnia and Herzegovina",
									"BW" => "Botswana",
									"BV" => "Bouvet Island",
									"BR" => "Brazil",
									"IO" => "British Indian Ocean Territory",
									"BN" => "Brunei Darussalam",
									"BG" => "Bulgaria",
									"BF" => "Burkina Faso",
									"BI" => "Burundi",
									"KH" => "Cambodia",
									"CM" => "Cameroon",
									"CA" => "Canada",
									"CV" => "Cape Verde",
									"KY" => "Cayman Islands",
									"CF" => "Central African Republic",
									"TD" => "Chad",
									"CL" => "Chile",
									"CN" => "China",
									"CX" => "Christmas Island",
									"CC" => "Cocos (Keeling) Islands",
									"CO" => "Colombia",
									"KM" => "Comoros",
									"CG" => "Congo",
									"CD" => "Congo, the Democratic Republic of the",
									"CK" => "Cook Islands",
									"CR" => "Costa Rica",
									"CI" => "Côte d'Ivoire",
									"HR" => "Croatia",
									"CU" => "Cuba",
									"CW" => "Curaçao",
									"CY" => "Cyprus",
									"CZ" => "Czech Republic",
									"DK" => "Denmark",
									"DJ" => "Djibouti",
									"DM" => "Dominica",
									"DO" => "Dominican Republic",
									"EC" => "Ecuador",
									"EG" => "Egypt",
									"SV" => "El Salvador",
									"GQ" => "Equatorial Guinea",
									"ER" => "Eritrea",
									"EE" => "Estonia",
									"ET" => "Ethiopia",
									"FK" => "Falkland Islands (Malvinas)",
									"FO" => "Faroe Islands",
									"FJ" => "Fiji",
									"FI" => "Finland",
									"FR" => "France",
									"GF" => "French Guiana",
									"PF" => "French Polynesia",
									"TF" => "French Southern Territories",
									"GA" => "Gabon",
									"GM" => "Gambia",
									"GE" => "Georgia",
									"DE" => "Germany",
									"GH" => "Ghana",
									"GI" => "Gibraltar",
									"GR" => "Greece",
									"GL" => "Greenland",
									"GD" => "Grenada",
									"GP" => "Guadeloupe",
									"GU" => "Guam",
									"GT" => "Guatemala",
									"GG" => "Guernsey",
									"GN" => "Guinea",
									"GW" => "Guinea-Bissau",
									"GY" => "Guyana",
									"HT" => "Haiti",
									"HM" => "Heard Island and McDonald Islands",
									"VA" => "Holy See (Vatican City State)",
									"HN" => "Honduras",
									"HK" => "Hong Kong",
									"HU" => "Hungary",
									"IS" => "Iceland",
									"IN" => "India",
									"ID" => "Indonesia",
									"IR" => "Iran, Islamic Republic of",
									"IQ" => "Iraq",
									"IE" => "Ireland",
									"IM" => "Isle of Man",
									"IL" => "Israel",
									"IT" => "Italy",
									"JM" => "Jamaica",
									"JP" => "Japan",
									"JE" => "Jersey",
									"JO" => "Jordan",
									"KZ" => "Kazakhstan",
									"KE" => "Kenya",
									"KI" => "Kiribati",
									"KP" => "Korea, Democratic People's Republic of",
									"KR" => "Korea, Republic of",
									"KW" => "Kuwait",
									"KG" => "Kyrgyzstan",
									"LA" => "Lao People's Democratic Republic",
									"LV" => "Latvia",
									"LB" => "Lebanon",
									"LS" => "Lesotho",
									"LR" => "Liberia",
									"LY" => "Libya",
									"LI" => "Liechtenstein",
									"LT" => "Lithuania",
									"LU" => "Luxembourg",
									"MO" => "Macao",
									"MK" => "Macedonia, the former Yugoslav Republic of",
									"MG" => "Madagascar",
									"MW" => "Malawi",
									"MY" => "Malaysia",
									"MV" => "Maldives",
									"ML" => "Mali",
									"MT" => "Malta",
									"MH" => "Marshall Islands",
									"MQ" => "Martinique",
									"MR" => "Mauritania",
									"MU" => "Mauritius",
									"YT" => "Mayotte",
									"MX" => "Mexico",
									"FM" => "Micronesia, Federated States of",
									"MD" => "Moldova, Republic of",
									"MC" => "Monaco",
									"MN" => "Mongolia",
									"ME" => "Montenegro",
									"MS" => "Montserrat",
									"MA" => "Morocco",
									"MZ" => "Mozambique",
									"MM" => "Myanmar",
									"NA" => "Namibia",
									"NR" => "Nauru",
									"NP" => "Nepal",
									"NL" => "Netherlands",
									"NC" => "New Caledonia",
									"NZ" => "New Zealand",
									"NI" => "Nicaragua",
									"NE" => "Niger",
									"NG" => "Nigeria",
									"NU" => "Niue",
									"NF" => "Norfolk Island",
									"MP" => "Northern Mariana Islands",
									"NO" => "Norway",
									"OM" => "Oman",
									"PK" => "Pakistan",
									"PW" => "Palau",
									"PS" => "Palestinian Territory, Occupied",
									"PA" => "Panama",
									"PG" => "Papua New Guinea",
									"PY" => "Paraguay",
									"PE" => "Peru",
									"PH" => "Philippines",
									"PN" => "Pitcairn",
									"PL" => "Poland",
									"PT" => "Portugal",
									"PR" => "Puerto Rico",
									"QA" => "Qatar",
									"RE" => "Réunion",
									"RO" => "Romania",
									"RU" => "Russian Federation",
									"RW" => "Rwanda",
									"BL" => "Saint Barthélemy",
									"SH" => "Saint Helena, Ascension and Tristan da Cunha",
									"KN" => "Saint Kitts and Nevis",
									"LC" => "Saint Lucia",
									"MF" => "Saint Martin (French part)",
									"PM" => "Saint Pierre and Miquelon",
									"VC" => "Saint Vincent and the Grenadines",
									"WS" => "Samoa",
									"SM" => "San Marino",
									"ST" => "Sao Tome and Principe",
									"SA" => "Saudi Arabia",
									"SN" => "Senegal",
									"RS" => "Serbia",
									"SC" => "Seychelles",
									"SL" => "Sierra Leone",
									"SG" => "Singapore",
									"SX" => "Sint Maarten (Dutch part)",
									"SK" => "Slovakia",
									"SI" => "Slovenia",
									"SB" => "Solomon Islands",
									"SO" => "Somalia",
									"ZA" => "South Africa",
									"GS" => "South Georgia and the South Sandwich Islands",
									"SS" => "South Sudan",
									"ES" => "Spain",
									"LK" => "Sri Lanka",
									"SD" => "Sudan",
									"SR" => "Suriname",
									"SJ" => "Svalbard and Jan Mayen",
									"SZ" => "Swaziland",
									"SE" => "Sweden",
									"CH" => "Switzerland",
									"SY" => "Syrian Arab Republic",
									"TW" => "Taiwan, Province of China",
									"TJ" => "Tajikistan",
									"TZ" => "Tanzania, United Republic of",
									"TH" => "Thailand",
									"TL" => "Timor-Leste",
									"TG" => "Togo",
									"TK" => "Tokelau",
									"TO" => "Tonga",
									"TT" => "Trinidad and Tobago",
									"TN" => "Tunisia",
									"TR" => "Turkey",
									"TM" => "Turkmenistan",
									"TC" => "Turks and Caicos Islands",
									"TV" => "Tuvalu",
									"UG" => "Uganda",
									"UA" => "Ukraine",
									"AE" => "United Arab Emirates",
									"GB" => "United Kingdom",
									"US" => "United States",
									"UM" => "United States Minor Outlying Islands",
									"UY" => "Uruguay",
									"UZ" => "Uzbekistan",
									"VU" => "Vanuatu",
									"VE" => "Venezuela, Bolivarian Republic of",
									"VN" => "Viet Nam",
									"VG" => "Virgin Islands, British",
									"VI" => "Virgin Islands, U.S.",
									"WF" => "Wallis and Futuna",
									"EH" => "Western Sahara",
									"YE" => "Yemen",
									"ZM" => "Zambia",
									"ZW" => "Zimbabwe" 
							) 
					) 
			) );
			$this->add ( array (
					'attributes' => array (
							'class' => 'form-control',
							'required' => 'required' 
					),
					'type' => 'Zend\Form\Element\Select',
					'name' => 'state',
					'options' => array (
							'empty_option' => '<State>',
							'label' => 'State',
							'value_options' => array (
									"Andaman and Nicobar Islands" => 'Andaman and Nicobar Islands',
									"Andhra Pradesh" => 'Andhra Pradesh',
									"Arunachal Pradesh" => 'Arunachal Pradesh',
									"Assam" => 'Assam',
									"Bihar" => 'Bihar',
									"Chandigarh" => 'Chandigarh',
									"Chhattisgarh" => 'Chhattisgarh',
									"Dadra and Nagar Haveli" => 'Dadra and Nagar Haveli',
									"Daman and Diu" => 'Daman and Diu',
									"Delhi" => 'Delhi',
									"Goa" => 'Goa',
									"Gujarat" => 'Gujarat',
									"Haryana" => 'Haryana',
									"Himachal Pradesh" => 'Himachal Pradesh',
									"Jammu and Kashmir" => 'Jammu and Kashmir',
									"Jharkhand" => 'Jharkhand',
									"Karnataka" => 'Karnataka',
									"Kerala" => 'Kerala',
									"Lakshadweep" => 'Lakshadweep',
									"Madhya Pradesh" => 'Madhya Pradesh',
									"Maharashtra" => 'Maharashtra',
									"Manipur" => 'Manipur',
									"Meghalaya" => 'Meghalaya',
									"Mizoram" => 'Mizoram',
									"Nagaland" => 'Nagaland',
									"Orissa" => 'Orissa',
									"Pondicherry" => 'Pondicherry',
									"Punjab" => 'Punjab',
									"Rajasthan" => 'Rajasthan',
									"Sikkim" => 'Sikkim',
									"Tamil Nadu" => 'Tamil Nadu',
									"Tripura" => 'Tripura',
									"Uttaranchal" => 'Uttaranchal',
									"Uttar Pradesh" => 'Uttar Pradesh',
									"West Bengal" => 'West Bengal' 
							) 
					) 
			) );
			$this->add ( array (
					
					'name' => 'city',
					'type' => 'Zend\Form\Element\Text',
					'attributes' => array (
							'placeholder' => 'City',
							'class' => 'form-control',
							'id' => 'cityI',
							'required' => 'required' 
					),
					'options' => array (
							'label' => 'City ' 
					) 
			) );
			$this->add ( array (
					'name' => 'district',
					'type' => 'Zend\Form\Element\Text',
					'attributes' => array (
							'placeholder' => 'District',
							'class' => 'form-control',
							'id' => 'districtI',
							'required' => 'required' 
					),
					'options' => array (
							'label' => 'District' 
					) 
			) );
			$this->add ( array (
					'name' => 'pincode',
					'attributes' => array (
							'class' => 'form-control',
							'id' => 'pincodeI',
							'placeholder' => 'Pincode',
							'required' => 'required' 
					),
					'options' => array (
							'label' => 'Pin Code' 
					) 
			) );
		}
		
		$this->add ( array (
				'name' => 'personalEmailId',
				'type' => 'Zend\Form\Element\Email',
				'attributes' => array (
						'placeholder' => 'Your Email-Id',
						'class' => 'form-control',
						'id' => 'personalEmailIdI',
						'required' => 'required' 
				),
				'options' => array (
						'label' => 'Your Email-Id' 
				) 
		) );
		
		$this->add ( array (
				'name' => 'password',
				'type' => 'Zend\Form\Element\Password',
				'attributes' => array (
						'class' => 'form-control',
						'id' => 'passwordI',
						'placeholder' => 'Set Password',
						'required' => 'required' 
				),
				'options' => array (
						'label' => 'Set Password' 
				) 
		) );
		$this->add ( array (
				'name' => 'passwordConfirm',
				'type' => 'Zend\Form\Element\Password',
				'attributes' => array (
						'class' => 'form-control',
						'id' => 'passwordConfirmI',
						'placeholder' => 'Confirm Password or Passphrase',
						'required' => 'required' 
				),
				'options' => array (
						'label' => 'Confirm Password' 
				) 
		) );
		$this->add ( array (
				'name' => 'mcsrf',
				'type' => 'Zend\Form\Element\Csrf',
				'options' => array (
						'csrf_options' => array (
								'timeout' => CSRF_TIMEOUT_SECONDS 
						) 
				) 
		) );
	}
	public function createInputFilter() {
		$inputFilter = new InputFilter\InputFilter ();
		if ($this->displayAllFields) {
			$reqIps = array (
					'gender',
					'birthDay',
					'birthMonth',
					'marritalStatus',
					'state',
					'personalEmailId',
					'userIdParent' 
			);
			$names = array (
					'fullName' => array (
							'type' => 'Alpha',
							'allowWhiteSpace' => true,
							'required' => true 
					),
					'city' => array (
							'type' => 'Alpha',
							'allowWhiteSpace' => true,
							'required' => true 
					),
					'district' => array (
							'type' => 'Alpha',
							'allowWhiteSpace' => true,
							'required' => true 
					) 
			);
		} else {
			$reqIps = array (
					'gender',
					'birthDay',
					'birthMonth',
					'personalEmailId',
					'userIdParent' 
			);
			$names = array (
					'fullName' => array (
							'type' => 'Alpha',
							'allowWhiteSpace' => true,
							'required' => true 
					) 
			);
		}
		foreach ( $reqIps as $inputName ) {
			$inputFilter->add ( array (
					'name' => $inputName,
					'required' => true 
			) );
		}
		
		foreach ( $names as $name => $reqs ) {
			$inputFilter->add ( array (
					'name' => $name,
					'required' => $reqs ['required'],
					'validators' => array (
							new \Zend\I18n\Validator\Alpha ( array (
									'allowWhiteSpace' => $reqs ['allowWhiteSpace'] 
							) ),
							array (
									'name' => 'StringLength',
									'options' => array (
											'min' => 4,
											'max' => 180 
									) 
							) 
					),
					'filters' => array (
							array (
									'name' => 'StripTags' 
							),
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) );
		}
		$inputFilter->add ( array (
				'name' => 'loginId',
				'required' => true,
				'validators' => array (
						array (
								'name' => 'StringLength',
								'options' => array (
										'encoding' => 'UTF-8',
										'min' => 1,
										'max' => 150 
								) 
						) 
				),
				'filters' => array (
						array (
								'name' => 'StripTags' 
						),
						array (
								'name' => 'StringTrim' 
						) 
				) 
		) );
		$inputFilter->add ( array (
				'name' => 'employeeCode',
				'required' => true,
				'validators' => array (
						new Alnum (),
						array (
								'name' => 'StringLength',
								'options' => array (
										'min' => 3,
										'max' => 100 
								) 
						) 
				),
				'filters' => array (
						array (
								'name' => 'StripTags' 
						),
						array (
								'name' => 'StringTrim' 
						) 
				) 
		) );
		$inputFilter->add ( array (
				'name' => 'mobile',
				'required' => true,
				'validators' => array (
						new \Zend\Validator\Digits (),
						array (
								'name' => 'StringLength',
								'options' => array (
										'min' => 10,
										'max' => 10 
								) 
						) 
				),
				'filters' => array (
						array (
								'name' => 'StripTags' 
						),
						array (
								'name' => 'StringTrim' 
						) 
				) 
		) );
		$inputFilter->add ( array (
				'name' => 'password',
				'required' => true,
				'filters' => array (
						array (
								'name' => 'StringTrim' 
						) 
				),
				'validators' => array (
						array (
								'name' => 'StringLength',
								'options' => array (
										'encoding' => 'UTF-8',
										'min' => 6,
										'max' => 200 
								) 
						) 
				) 
		) );
		$inputFilter->add ( array (
				'name' => 'passwordConfirm',
				'required' => true,
				'filters' => array (
						array (
								'name' => 'StringTrim' 
						) 
				),
				'validators' => array (
						array (
								'name' => 'StringLength',
								'options' => array (
										'encoding' => 'UTF-8',
										'min' => 6,
										'max' => 200 
								) 
						),
						array (
								'name' => 'Identical',
								'options' => array (
										'token' => 'password',
										'messages' => array (
												Identical::NOT_SAME => '"Hey bud, your passwords do not match, something ain\'t right!"' 
										) 
								) 
						) 
				) 
		) );
		
		$years = array (
				'birthYear' => true 
		);
		foreach ( $years as $year => $req ) {
			$inputFilter->add ( array (
					'name' => $year,
					'required' => $req,
					'validators' => array (
							new \Zend\Validator\Digits (),
							array (
									'name' => 'StringLength',
									'options' => array (
											'min' => 4,
											'max' => 4 
									) 
							) 
					),
					'filters' => array (
							array (
									'name' => 'StripTags' 
							),
							array (
									'name' => 'StringTrim' 
							) 
					) 
			) );
		}
		
		return $inputFilter;
	}
}

