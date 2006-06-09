<?php

/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/

//include("Serializer.php");
//require_once("config.php");
//require_once('modules/Contacts/Contact.php');
//require_once('modules/Products/Product.php');
//equire_once('modules/HelpDesk/HelpDesk.php');
//require_once('include/logging.php');
//require_once('/include/database/PearDatabase.php');
//require_once('/SOAP/lib/nusoap.php');

require_once('/home/anil/vtiger_crm/include/nusoap/nusoap.php');

require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Contact/BAO/Contact.php';
require_once('api/Contact.php');
require_once 'api/utils.php';
session_start();
$config =& CRM_Core_Config::singleton();
$config->userFramework          = 'Soap';
$config->userFrameworkClass     = 'CRM_Utils_System_Soap';




global $ufClass;
// create object
//$serializer = new XML_Serializer();
//$NAMESPACE = 'http://www.vtigercrm.com/vtigercrm';
$server = new soap_server;

$server->configureWSDL('vtigersoap');


$server->wsdl->addComplexType(
    'task_detail',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'start_date' => array('name'=>'start_date','type'=>'xsd:datetime'),
        'date_modified' => array('name'=>'date_modified','type'=>'xsd:datetime'),
        'name' => array('name'=>'name','type'=>'xsd:string'),
        'status' => array('name'=>'status','type'=>'xsd:string'),
        'date_due' => array('name'=>'date_due','type'=>'xsd:string'),
        'time_due' => array('name'=>'time_due','type'=>'xsd:datetime'),
        'priority' => array('name'=>'priority','type'=>'xsd:string'),
        'description' => array('name'=>'description','type'=>'xsd:string'),
	    'contact_name' => array('name'=>'contact_name','type'=>'xsd:string'),
        'id' => array('name'=>'id','type'=>'xsd:string'),

    )
);
    
$server->wsdl->addComplexType(
    'task_detail_array',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:task_detail[]')
    ),
    'tns:task_detail'
);

//calendar
$server->wsdl->addComplexType(
    'calendar_detail',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'start_date' => array('name'=>'start_date','type'=>'xsd:string'),
        'date_modified' => array('name'=>'date_modified','type'=>'xsd:string'),
        'name' => array('name'=>'name','type'=>'xsd:string'),
        'location' => array('name'=>'location','type'=>'xsd:string'),
        'date_due' => array('name'=>'date_due','type'=>'xsd:string'),
        'time_due' => array('name'=>'time_due','type'=>'xsd:string'),
        //'priority' => array('name'=>'priority','type'=>'xsd:string'),
        'description' => array('name'=>'description','type'=>'xsd:string'),
		'contact_name' => array('name'=>'contact_name','type'=>'xsd:string'),
        'id' => array('name'=>'id','type'=>'xsd:string'),

        )
);
    
$server->wsdl->addComplexType(
    'calendar_detail_array',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:calendar_detail[]')
    ),
    'tns:calendar_detail'
);
//calendar

$server->wsdl->addComplexType(
    'contact_detail',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'email_address' => array('name'=>'email_address','type'=>'xsd:string'),
        'first_name' => array('name'=>'first_name','type'=>'xsd:string'),
        'last_name' => array('name'=>'last_name','type'=>'xsd:string'),
        'primary_address_city' => array('name'=>'primary_address_city','type'=>'xsd:string'),
        'account_name' => array('name'=>'account_name','type'=>'xsd:string'),
				'account_id' => array('name'=>'account_id','type'=>'xsd:string'),
        'id' => array('name'=>'id','type'=>'xsd:string'),
        'salutation' => array('name'=>'salutation','type'=>'xsd:string'),
        'title'=> array('name'=>'title','type'=>'xsd:string'),
        'phone_mobile'=> array('name'=>'phone_mobile','type'=>'xsd:string'),
        'reports_to'=> array('name'=>'reports_to','type'=>'xsd:string'),
        'primary_address_city'=> array('name'=>'primary_address_city','type'=>'xsd:string'),
        'primary_address_street'=> array('name'=>'primary_address_street','type'=>'xsd:string'),
        'primary_address_state'=> array('name'=>'primary_address_state','type'=>'xsd:string'),
        'primary_address_postalcode'=> array('name'=>'primary_address_postalcode','type'=>'xsd:string'),
        'primary_address_country'=> array('name'=>'primary_address_country','type'=>'xsd:string'),
        'alt_address_city'=> array('name'=>'alt_address_city','type'=>'xsd:string'),
        'alt_address_street'=> array('name'=>'alt_address_street','type'=>'xsd:string'),
        'alt_address_state'=> array('name'=>'alt_address_state','type'=>'xsd:string'),
        'alt_address_postalcode'=> array('name'=>'alt_address_postalcode','type'=>'xsd:string'),
        'alt_address_country'=> array('name'=>'alt_address_country','type'=>'xsd:string'),

        'office_phone'=> array('name'=>'office_phone','type'=>'xsd:string'),
        'home_phone'=> array('name'=>'home_phone','type'=>'xsd:string'),
        'other_phone'=> array('name'=>'other_phone','type'=>'xsd:string'),
        'fax'=> array('name'=>'fax','type'=>'xsd:string'),
        'department'=> array('name'=>'fax','type'=>'xsd:string'),
        'birthdate'=> array('name'=>'birthdate','type'=>'xsd:string'),
        'assistant_name'=> array('name'=>'assistant_name','type'=>'xsd:string'),
        'assistant_phone'=> array('name'=>'assistant_phone','type'=>'xsd:string')

    )
);

$server->wsdl->addComplexType(
    'contact_detail_array',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:contact_detail[]')
    ),
    'tns:contact_detail'
);

  


$server->wsdl->addComplexType(
    'contact_column_detail',
    'complexType',
    'array',
    '',
    array(
        'email_address' => array('name'=>'email_address','type'=>'xsd:string'),
        'first_name' => array('name'=>'first_name','type'=>'xsd:string'),
        'last_name' => array('name'=>'last_name','type'=>'xsd:string'),
        'primary_address_city' => array('name'=>'primary_address_city','type'=>'xsd:string'),
        'account_name' => array('name'=>'account_name','type'=>'xsd:string'),
        'id' => array('name'=>'id','type'=>'xsd:string'),
        'salutation' => array('name'=>'salutation','type'=>'xsd:string'),
        'title'=> array('name'=>'title','type'=>'xsd:string'),
        'phone_mobile'=> array('name'=>'phone_mobile','type'=>'xsd:string'),
        'reports_to'=> array('name'=>'reports_to','type'=>'xsd:string'),
        'primary_address_city'=> array('name'=>'primary_address_city','type'=>'xsd:string'),
        'primary_address_street'=> array('name'=>'primary_address_street','type'=>'xsd:string'),
        'primary_address_state'=> array('name'=>'primary_address_state','type'=>'xsd:string'),
        'primary_address_postalcode'=> array('name'=>'primary_address_postalcode','type'=>'xsd:string'),
        'primary_address_country'=> array('name'=>'primary_address_country','type'=>'xsd:string'),
        'alt_address_city'=> array('name'=>'alt_address_city','type'=>'xsd:string'),
        'alt_address_street'=> array('name'=>'alt_address_street','type'=>'xsd:string'),
        'alt_address_state'=> array('name'=>'alt_address_state','type'=>'xsd:string'),
        'alt_address_postalcode'=> array('name'=>'alt_address_postalcode','type'=>'xsd:string'),
        'alt_address_country'=> array('name'=>'alt_address_country','type'=>'xsd:string'),
    )
);


/*$server->wsdl->addComplexType(
    'contact_column_array',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:contact_column_detail[]')
    ),
    'tns:contact_column_detail'
);

 $server->wsdl->addComplexType(
    'account_column_array',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:account_column_detail[]')
    ),
    'tns:account_column_detail'
);

$server->wsdl->addComplexType(
    'lead_column_array',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:lead_column_detail[]')
    ),
    'tns:lead_column_detail'
);*/
 



$server->wsdl->addComplexType(
    'account_column_detail',
    'complexType',
    'array',
    '',
    array(
        'accountid' => array('name'=>'accountid','type'=>'xsd:string'),
        'accountname' => array('name'=>'accountname','type'=>'xsd:string'),
        'parentid' => array('name'=>'parentid','type'=>'xsd:string'),
        'account_type' => array('name'=>'account_type','type'=>'xsd:string'),
        'industry' => array('name'=>'industry','type'=>'xsd:string'), 
        'annualrevenue' => array('name'=>'annualrevenue','type'=>'xsd:string'),
        'rating'=> array('name'=>'rating','type'=>'xsd:string'), 
        'ownership' => array('name'=>'ownership','type'=>'xsd:string'),
        'siccode' => array('name'=>'siccode','type'=>'xsd:string'),
        'tickersymbol' => array('name'=>'tickersymbol','type'=>'xsd:string'),
        'phone' => array('name'=>'phone','type'=>'xsd:string'),
        'otherphone' => array('name'=>'otherphone','type'=>'xsd:string'),
        'email1' => array('name'=>'email1','type'=>'xsd:string'),
        'email2' => array('name'=>'email2','type'=>'xsd:string'),
        'website' => array('name'=>'website','type'=>'xsd:string'),
        'fax' => array('name'=>'fax','type'=>'xsd:string'),
        //'employees' => array('name'=>'employees','type'=>'xsd:string'),
			)
);

$server->wsdl->addComplexType(
    'lead_column_detail',
    'complexType',
    'array',
    '',
    array(
        'id' => array('name'=>'id','type'=>'xsd:string'), 
        'date_entered' => array('name'=>'date_entered','type'=>'xsd:string'),
        'date_modified' => array('name'=>'date_modified','type'=>'xsd:string'),
        'modified_user_id' => array('name'=>'modified_user_id','type'=>'xsd:string'),
        'assigned_user_id' => array('name'=>'assigned_user_id','type'=>'xsd:string'),
        'salutation' => array('name'=>'salutation','type'=>'xsd:string'),
        'first_name' => array('name'=>'first_name','type'=>'xsd:string'),
        'last_name' => array('name'=>'last_name','type'=>'xsd:string'),
        'company' => array('name'=>'company','type'=>'xsd:string'),
        'designation' => array('name'=>'designation','type'=>'xsd:string'),
        'lead_source' => array('name'=>'lead_source','type'=>'xsd:string'),
        'industry' => array('name'=>'industry','type'=>'xsd:string'),
        'annual_revenue' => array('name'=>'annual_revenue','type'=>'xsd:string'),
        'license_key' => array('name'=>'license_key','type'=>'xsd:string'),
        'phone' => array('name'=>'phone','type'=>'xsd:string'),
        'mobile' => array('name'=>'mobile','type'=>'xsd:string'),
        'fax' => array('name'=>'fax','type'=>'xsd:string'),
        'email' => array('name'=>'email','type'=>'xsd:string'),
        'yahoo_id' => array('name'=>'yahoo_id','type'=>'xsd:string'),
        'website' => array('name'=>'website','type'=>'xsd:string'),
        'lead_status' => array('name'=>'lead_status','type'=>'xsd:string'),
        'rating' => array('name'=>'rating','type'=>'xsd:string'),
        'employees' => array('name'=>'employees','type'=>'xsd:string'),
        'address_street' => array('name'=>'address_street','type'=>'xsd:string'),
        'address_city' => array('name'=>'address_city','type'=>'xsd:string'),
        'address_state' => array('name'=>'address_state','type'=>'xsd:string'),
        'address_postalcode' => array('name'=>'address_postalcode','type'=>'xsd:string'),
        'address_country' => array('name'=>'address_country','type'=>'xsd:string'),
        'description' => array('name'=>'description','type'=>'xsd:string'),
        'deleted' => array('name'=>'deleted','type'=>'xsd:string'),
        'converted' => array('name'=>'converted','type'=>'xsd:string'),
    )
);

//end code for mail merge

//Field array for troubletickets

$server->wsdl->addComplexType(
	'tickets_list_array',
	'complexType',
	'array',
	'',
	array(
	        'ticketid' => array('name'=>'ticketid','type'=>'xsd:string'),
	        'title' => array('name'=>'title','type'=>'xsd:string'),
        	'groupname' => array('name'=>'groupname','type'=>'xsd:string'),
        	'firstname' => array('name'=>'firstname','type'=>'xsd:string'),
        	'lastname' => array('name'=>'lastname','type'=>'xsd:string'),
	        'parent_id' => array('name'=>'parent_id','type'=>'xsd:string'),
	        'productid' => array('name'=>'productid','type'=>'xsd:string'),
	        'productname' => array('name'=>'productname','type'=>'xsd:string'),
	        'priority' => array('name'=>'priority','type'=>'xsd:string'),
	        'severity' => array('name'=>'severity','type'=>'xsd:string'),
	        'status' => array('name'=>'status','type'=>'xsd:string'),
	        'category' => array('name'=>'category','type'=>'xsd:string'),
	        'description' => array('name'=>'description','type'=>'xsd:string'),
	        'solution' => array('name'=>'solution','type'=>'xsd:string'),
	        'createdtime' => array('name'=>'createdtime','type'=>'xsd:string'),
	        'modifiedtime' => array('name'=>'modifiedtime','type'=>'xsd:string'),
	     )
);	
$server->wsdl->addComplexType(
        'ticket_comments_array',
        'complexType',
        'array',
        '',
        array(
                'comments' => array('name'=>'comments','type'=>'tns:xsd:string'),
             )
);	
$server->wsdl->addComplexType(
        'combo_values_array',
        'complexType',
        'array',
        '',
        array(
                'productid' => array('name'=>'productid','type'=>'tns:xsd:string'),
                'productname' => array('name'=>'productname','type'=>'tns:xsd:string'),
                'ticketpriorities' => array('name'=>'ticketpriorities','type'=>'tns:xsd:string'),
                'ticketseverities' => array('name'=>'ticketseverities','type'=>'tns:xsd:string'),
                'ticketcategories' => array('name'=>'ticketcategories','type'=>'tns:xsd:string'),
             )
);	
$server->wsdl->addComplexType(
        'KBase_array',
        'complexType',
        'array',
        '',
	'SOAP-ENC:Array',
	array(),
        array(
                array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:kbase_detail[]')
	     ),
	'tns:kbase_detail'
);
$server->wsdl->addComplexType(
	'kbase_detail',
	'complexType',
        'array',
        '',
	array(
              'faqcategory' => array('name'=>'faqcategory','type'=>'tns:xsd:string'),
              'faq' => array(
				'id' => array('name'=>'id','type'=>'tns:xsd:string'),
		                'question' => array('name'=>'question','type'=>'tns:xsd:string'),
		                'answer' => array('name'=>'answer','type'=>'tns:xsd:string'),
        		        'category' => array('name'=>'category','type'=>'tns:xsd:string'),
        		        'faqcreatedtime' => array('name'=>'createdtime','type'=>'tns:xsd:string'),
        		        'faqmodifiedtime' => array('name'=>'createdtime','type'=>'tns:xsd:string'),
        		        'faqcomments' => array('name'=>'faqcomments','type'=>'tns:xsd:string'),
		    	    )
             )
);
$server->wsdl->addComplexType(
        'ticket_update_comment_array',
        'complexType',
        'array',
        '',
        array(
                'ticketid' => array('name'=>'ticketid','type'=>'tns:xsd:string'),
                'parent_id' => array('name'=>'parent_id','type'=>'tns:xsd:string'),
                'createdtime' => array('name'=>'createdtime','type'=>'tns:xsd:string'),
                'comments' => array('name'=>'comments','type'=>'tns:xsd:string'),
             )
);	
//Added for User Details
$server->wsdl->addComplexType(
	'user_array',
	'complexType',
	'array',
        '',
        array(
		'id' => array('name'=>'id','type'=>'xsd:string'),
		'user_name' => array('name'=>'user_name','type'=>'xsd:string'),
		'user_password' => array('name'=>'user_password','type'=>'xsd:string'),
		'last_login' => array('name'=>'last_login_time','type'=>'xsd:string'),
		'support_start_date' => array('name'=>'support_start_date','type'=>'xsd:string'),
		'support_end_date' => array('name'=>'support_end_date','type'=>'xsd:string'),
	     )
);

$server->register(
    'create_session',
    array('user_name'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
    'end_session',
    array('user_name'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
	'create_contact',
    array('user_name'=>'xsd:string', 'first_name'=>'xsd:string', 'last_name'=>'xsd:string', 'email_address'=>'xsd:string','account_name'=>'xsd:string', 'salutation'=>'xsd:string', 'title'=>'xsd:string', 'phone_mobile'=>'xsd:string' , 'reports_to'=>'xsd:string', 'primary_address_street'=>'xsd:string', 'primary_address_city'=>'xsd:string', 'primary_address_state'=>'xsd:string' , 'primary_address_postalcode'=>'xsd:string', 'primary_address_country'=>'xsd:string', 'alt_address_city'=>'xsd:string', 'alt_address_street'=>'xsd:string','alt_address_state'=>'xsd:string', 'alt_address_postalcode'=>'xsd:string', 'alt_address_country'=>'xsd:string','office_phone'=>'xsd:string','home_phone'=>'xsd:string','fax'=>'xsd:string','department'=>'xsd:string','description'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
    'get_version',
    array('user_name'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);


$server->register(
    'contact_by_email',
    array('user_name'=>'xsd:string','email_address'=>'xsd:string'),
    array('return'=>'tns:contact_detail_array'),
    $NAMESPACE);

$server->register(
	'authenticate_user',
	array('user_name'=>'xsd:string','password'=>'xsd:string'),
	array('return'=>'tns:user_array'),
	$NAMESPACE);

$server->register(
	'change_password',
	array('id'=>'xsd:string','user_name'=>'xsd:string','password'=>'xsd:string'),
	array('return'=>'tns:user_array'),
	$NAMESPACE);
  
$server->register(
	'create_ticket',
	array('title'=>'xsd:string','description'=>'xsd:string','priority'=>'xsd:string','severity'=>'xsd:string','category'=>'xsd:string','user_name'=>'xsd:string','parent_id'=>'xsd:string','product_id'=>'xsd:string'),
	array('return'=>'tns:tickets_list_array'),
	$NAMESPACE);
 
$server->register(
	'get_tickets_list',
	array('user_name'=>'xsd:string','id'=>'xsd:string'),
	array('return'=>'tns:tickets_list_array'),
	$NAMESPACE);

$server->register(
	'get_ticket_comments',
	array('id'=>'xsd:string'),
	array('return'=>'tns:ticket_comments_array'),
	$NAMESPACE);

$server->register(
	'get_combo_values',
	array('id'=>'xsd:string'),
	array('return'=>'tns:combo_values_array'),
	$NAMESPACE);

$server->register(
	'get_KBase_details',
	array(''=>''),
	array('return'=>'tns:KBase_array'),
	$NAMESPACE);

$server->register(
	'create_lead_from_webform',
	array('lastname'=>'xsd:string',
		'email'=>'xsd:string', 
		'phone'=>'xsd:string', 
		'company'=>'xsd:string', 
		'country'=>'xsd:string', 
		'description'=>'xsd:string'),
	array('return'=>'xsd:string'),
	$NAMESPACE);

$server->register(
	'save_faq_comment',
	array('faqid'=>'xsd:string','comments'=>'xsd:string'),
	array('return'=>'tns:KBase_array'),
	$NAMESPACE);

$server->register(
	'update_ticket_comment',
	array('ticketid'=>'xsd:string'),
	array('ownerid'=>'xsd:string'),
	array('createdtime'=>'xsd:string'),
	array('comments'=>'xsd:string'),
	array('return'=>'tns:ticket_update_comment_array'),
	$NAMESPACE);
$server->register(
        'close_current_ticket',
        array('ticketid'=>'xsd:string'),
	array('return'=>'xsd:string'),
        $NAMESPACE);

$server->register(
	'update_login_details',
	array('id'=>'xsd:string','flag'=>'xsd:string'),
	array('return'=>'tns:user_array'),
	$NAMESPACE);

$server->register(
	'send_mail_for_password',
	array('email'=>'xsd:string'),
	array('return'=>'xsd:string'),
	$NAMESPACE);

$server->register(
    'contact_by_search',
    array('name'=>'xsd:string'),
    array('return'=>'tns:contact_detail_array'),
    $NAMESPACE);

$server->register(
	'update_contact',
        array('user_name'=>'xsd:string', 'id'=>'xsd:string','first_name'=>'xsd:string', 'last_name'=>'xsd:string', 'email_address'=>'xsd:string' , 'account_name'=>'xsd:string', 'salutation'=>'xsd:string', 'title'=>'xsd:string', 'phone_mobile'=>'xsd:string' , 'reports_to'=>'xsd:string', 'primary_address_street'=>'xsd:string', 'primary_address_city'=>'xsd:string', 'primary_address_state'=>'xsd:string' , 'primary_address_postalcode'=>'xsd:string', 'primary_address_country'=>'xsd:string', 'alt_address_city'=>'xsd:string', 'alt_address_street'=>'xsd:string','alt_address_city'=>'xsd:string', 'alt_address_state'=>'xsd:string', 'alt_address_postalcode'=>'xsd:string', 'alt_address_country'=>'xsd:string','office_phone'=>'xsd:string','home_phone'=>'xsd:string','other_phone'=>'xsd:string','fax'=>'xsd:string','department'=>'xsd:string','birthdate'=>'xsd:datetime','assistant_name'=>'xsd:string','assistant_phone'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);
	    
$server->register(
	'delete_contact',
    array('user_name'=>'xsd:string', 'id'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

/*
$server->register(
	'sync_contact',
    array('user_name'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);
*/

$server->register(
	'create_task',
        array('user_name'=>'xsd:string', 'start_date'=>'xsd:datetime', 'date_modified'=>'xsd:datetime','name'=>'xsd:string','status'=>'xsd:string','priority'=>'xsd:string','description'=>'xsd:string','date_due'=>'xsd:string','contact_name'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

//security
$server->register(
	'authorize_module',
        array('user_name'=>'xsd:string','module_name'=>'xsd:string', 'action'=>'xsd:string'),
        array('return'=>'xsd:string'),
        $NAMESPACE);
//security    

$server->register(
	'update_task',
        array('user_name'=>'xsd:string', 'id'=>'xsd:string', 'start_date'=>'xsd:datetime','name'=>'xsd:string','status'=>'xsd:string','priority'=>'xsd:string','description'=>'xsd:string','date_due'=>'xsd:date','contact_name'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

        //'date_due_flag'=>'xsd:string', 'date_due'=>'xsd:string', 'time_due'=>'xsd:datetime','parent_type'=>xsd:string,'parent_id'=>'xsd:string', 'contact_id'=>'xsd:string', 'priority'=>'xsd:string', 'description'=>'xsd:string','deleted'=>xsd:string),

$server->register(
    'retrieve_task',
    array('name'=>'xsd:string'),
    array('return'=>'tns:task_detail_array'),
    $NAMESPACE);

$server->register(
	'delete_task',
    array('user_name'=>'xsd:string', 'id'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);


/*
$server->register(
	'sync_task',
    array('user_name'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);
*/

$server->register(
	'track_email',
    array('user_name'=>'xsd:string', 'contact_ids'=>'xsd:string', 'date_sent'=>'xsd:date', 'email_subject'=>'xsd:string', 'email_body'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

 $server->register(
	'upload_emailattachment',
    array('email_id'=>'xsd:string', 'filename'=>'xsd:string','binFile'=>'xsd:string','fileSize'=>'xsd:long'),
   array('return'=>'xsd:string'),
    $NAMESPACE);

    $server->register(
   'create_contacts',
    array('user_name'=>'xsd:string','contacts'=>'tns:contact_detail_array'),
    array('return'=>'tns:contact_detail_array'),
    $NAMESPACE);

  $server->register(
   'create_tasks',
    array('user_name'=>'xsd:string','tasks'=>'tns:task_detail_array'),
    array('return'=>'tns:task_detail_array'),
    $NAMESPACE);


$server->register(
    'contact_by_range',
    array('user_name'=>'xsd:string','from_index'=>'xsd:int','offset'=>'xsd:int'),
    array('return'=>'tns:contact_detail_array'),
    $NAMESPACE);

$server->register(
    'get_contacts_count',
    array('user_name'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'xsd:int'),
    $NAMESPACE);

$server->register(
    'task_by_range',
    array('user_name'=>'xsd:string','from_index'=>'xsd:int','offset'=>'xsd:int'),
    array('return'=>'tns:task_detail_array'),
    $NAMESPACE);

 $server->register(
    'get_tasks_count',
    array('user_name'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'xsd:int'),
    $NAMESPACE);

$server->register(
    'get_tickets_columns',
    array('user_name'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'tns:tickets_list_array'),
    $NAMESPACE);

$server->register(
    'get_contacts_columns',
    array('user_name'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'tns:contact_column_detail'),
    $NAMESPACE);

$server->register(
    'get_accounts_columns',
    array('user_name'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'tns:account_column_detail'),
    $NAMESPACE);

$server->register(
    'get_leads_columns',
    array('user_name'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'tns:lead_column_detail'),
    $NAMESPACE);
	 
//calendar
$server->register(
    'get_calendar_count',
    array('user_name'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'xsd:int'),
    $NAMESPACE);
    
$server->register(
    'calendar_by_range',
    array('user_name'=>'xsd:string','from_index'=>'xsd:int','offset'=>'xsd:int'),
    array('return'=>'tns:calendar_detail_array'),
    $NAMESPACE);

$server->register(
   'create_calendars',
    array('user_name'=>'xsd:string','tasks'=>'tns:calendar_detail_array'),
    array('return'=>'tns:calendar_detail_array'),
    $NAMESPACE);

$server->register(
	'create_calendar',
    array('user_name'=>'xsd:string', 'start_date'=>'xsd:string','name'=>'xsd:string','description'=>'xsd:string','date_due'=>'xsd:string','contact_name'=>'xsd:string','location'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
	'update_calendar',
    array('user_name'=>'xsd:string', 'id'=>'xsd:string', 'start_date'=>'xsd:string','name'=>'xsd:string','description'=>'xsd:string','date_due'=>'xsd:string','contact_name'=>'xsd:string','location'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
    'retrieve_calendar',
    array('name'=>'xsd:string'),
    array('return'=>'tns:calendar_detail_array'),
    $NAMESPACE);

$server->register(
	'delete_calendar',
    array('user_name'=>'xsd:string', 'id'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);
        
//calendar   

function get_tickets_columns($user_name, $password)
{
    require_once('modules/HelpDesk/HelpDesk.php');
    $helpdesk = new HelpDesk();
    return $helpdesk->getColumnNames_Hd();
}

function get_contacts_columns($user_name, $password)
{
    require_once('modules/Contacts/Contact.php');
    $contact = new Contact();
    return $contact->getColumnNames();
}

/*function authorize_module($user_name,$module_name,$action)
{
	require_once('modules/Users/UserInfoUtil.php');
	if($module_name == "Tasks")
	{
		$module_name = "Activities";
	}
	$user_id = getUserId_Ol($user_name);
	if($user_id != 0)
	{
		$auth_val = isAllowed_Outlook($module_name,$action,$user_id,"");
	}else
	{
	    $auth_val = "no";
	}
	return $auth_val;
}*/
/*require_once('modules/Accounts/Account.php');
$account = new Account();
foreach($account->getColumnNames_Acnt() as $flddetails)
{
	echo $flddetails;
}
*/

function get_accounts_columns($user_name, $password)
{
    require_once('modules/Accounts/Account.php');
    $account = new Account();
    return $account->getColumnNames_Acnt();
}


function get_leads_columns($user_name, $password)
{
    require_once('modules/Leads/Lead.php');
    $lead = new Lead();
    return $lead->getColumnNames_Lead();
}
//end code for mail merge

function get_contacts_count($user_name, $password)
{
    /* global $current_user;
	require_once('modules/Users/User.php');
	$seed_user = new User();
	$user_id = $seed_user->retrieve_user_id($user_name);
	$current_user = $seed_user;
	$current_user->retrieve($user_id);
   
    require_once('modules/Contacts/Contact.php');
    $contact = new Contact();
   
    return $contact->getCount($user_name);*/
    $count =2;
    return $count; 
}

function create_contacts($user_name,$output_list)
{
	$counter=0;
	foreach($output_list as $contact)
	{
   
        if($contact[birthdate]=="4501-01-01")
        {
	        $contact[birthdate] = "0000-00-00";
        }
		$id = create_contact1($user_name, $contact[first_name], $contact[last_name], $contact[email_address ],$contact[account_name ], $contact[salutation ], $contact[title], $contact[phone_mobile], $contact[reports_to],$contact[primary_address_street],$contact[primary_address_city],$contact[primary_address_state],$contact[primary_address_postalcode],$contact[primary_address_country],$contact[alt_address_city],$contact[alt_address_street],$contact[alt_address_state],$contact[alt_address_postalcode],$contact[alt_address_country],$contact[office_phone],$contact[home_phone],$contact[other_phone],$contact[fax],$contact[department],$contact[birthdate],$contact[assistant_name],$contact[assistant_phone]);
      
	  $output_list[$counter] ['id']=$id;
	   $counter++;
	}
	return array_reverse($output_list);
}

function create_tasks($user_name,$output_list)
{
	$counter=0;
	foreach($output_list as $task)
	{
  
		if($task[date_due] == "4501-01-01")
		{
			$task[date_due] = "";
		}
		$id= create_task($user_name, $task[start_date], $task[date_modified],$task[name],$task[status],$task[priority],$task[description],$task[date_due],$task[contact_name]);
   
      
	  $output_list[$counter] ['id']=$id;
	   $counter++;
	}
	return array_reverse($output_list);
}


function get_version($user_name, $password)
{
	return "4.2";
}

function contact_by_email($user_name,$email_address)
{
        $seed_contact = new Contact();
        $output_list = Array();
   
         {  
            $response = $seed_contact->get_contacts1($user_name,$email_address);
            $contactList = $response['list'];

    
       // create a return array of names and email addresses.
    foreach($contactList as $contact)
    {
   
        $output_list[] = Array("first_name"    => $contact[first_name],
            "last_name" => $contact[last_name],
            "primary_address_city" => $contact[primary_address_city],
            "account_name" => $contact[account_name],
	    "account_id"=> $contact[account_id],
            "id" => $contact[id],
            "email_address" => $contact[email1],
            "salutation"=>$contact[salutation],
            "title"=>$contact[title],
            "phone_mobile"=>$contact[phone_mobile],
            "reports_to"=>$contact[reports_to_name],
            "primary_address_street"=>$contact[primary_address_street],
            "primary_address_city"=>$contact[primary_address_city],
            "primary_address_state"=>$contact[primary_address_state] ,
            "primary_address_postalcode"=>$contact[primary_address_postalcode],
            "primary_address_country"=>$contact[primary_address_country],
            "alt_address_city"=>$contact[alt_address_city],
            "alt_address_street"=>$contact[alt_address_street],
            "alt_address_city"=>$contact[alt_address_city],
            "alt_address_state"=>$contact[alt_address_state],
            "alt_address_postalcode"=>$contact[alt_address_postalcode],
            "alt_address_country"=>$contact[alt_address_country],
 );
    }
}

           


    //to remove an erroneous compiler warning
    $seed_contact = $seed_contact;


    return $output_list;
}

function get_ticket_comments($ticketid)
{
	$seed_ticket = new HelpDesk();
        $output_list = Array();

	$response = $seed_ticket->get_ticket_comments_list($ticketid);

	return $response;
}
function get_combo_values($id)
{
	global $adb;
	$output = Array();
	$sql = "select * from products inner join crmentity on crmentity.crmid=products.productid where crmentity.deleted=0";
	$result = $adb->query($sql);
	$noofrows = $adb->num_rows($result);
	for($i=0;$i<$noofrows;$i++)
        {
        	$output['productid']['productid'][$i] = $adb->query_result($result,$i,"productid");
                $output['productname']['productname'][$i] = $adb->query_result($result,$i,"productname");
        }

	$result1 = $adb->query("select * from ticketpriorities");
	for($i=0;$i<$adb->num_rows($result1);$i++)
	{
		$output['ticketpriorities']['ticketpriorities'][$i] = $adb->query_result($result1,$i,"ticketpriorities");
	}

        $result2 = $adb->query("select * from ticketseverities");
        for($i=0;$i<$adb->num_rows($result2);$i++)
        {
                $output['ticketseverities']['ticketseverities'][$i] = $adb->query_result($result2,$i,"ticketseverities");
        }

        $result3 = $adb->query("select * from ticketcategories");
        for($i=0;$i<$adb->num_rows($result3);$i++)
        {
                $output['ticketcategories']['ticketcategories'][$i] = $adb->query_result($result3,$i,"ticketcategories");
        }
	return $output;
}
function get_KBase_details($id='')
{
	global $adb;

	$category_query = "select * from faqcategories";
	$category_result = $adb->query($category_query);
	$category_noofrows = $adb->num_rows($category_result);
	for($j=0;$j<$category_noofrows;$j++)
	{
		$faqcategory = $adb->query_result($category_result,$j,'faqcategories');
		$result['faqcategory'][$j] = $faqcategory;
	}

	$product_query = "select * from products inner join crmentity on crmentity.crmid=products.productid where crmentity.deleted=0";
        $product_result = $adb->query($product_query);
        $product_noofrows = $adb->num_rows($product_result);
        for($i=0;$i<$product_noofrows;$i++)
        {
		$productid = $adb->query_result($product_result,$i,'productid');
                $productname = $adb->query_result($product_result,$i,'productname');
                $result['product'][$i]['productid'] = $productid;
                $result['product'][$i]['productname'] = $productname;
	}

	$faq_query = "select faq.*, crmentity.createdtime, crmentity.modifiedtime from faq inner join crmentity on crmentity.crmid=faq.id where crmentity.deleted=0 and faq.status='Published' order by crmentity.modifiedtime DESC";
	$faq_result = $adb->query($faq_query);
	$faq_noofrows = $adb->num_rows($faq_result);
	for($k=0;$k<$faq_noofrows;$k++)
	{
		$faqid = $adb->query_result($faq_result,$k,'id');
		$result['faq'][$k]['id'] = $faqid;
		$result['faq'][$k]['product_id']  = $adb->query_result($faq_result,$k,'product_id');
		$result['faq'][$k]['question'] =  nl2br($adb->query_result($faq_result,$k,'question'));
		$result['faq'][$k]['answer'] = nl2br($adb->query_result($faq_result,$k,'answer'));
		$result['faq'][$k]['category'] = $adb->query_result($faq_result,$k,'category');
		$result['faq'][$k]['faqcreatedtime'] = $adb->query_result($faq_result,$k,'createdtime');
		$result['faq'][$k]['faqmodifiedtime'] = $adb->query_result($faq_result,$k,'modifiedtime');

		$faq_comment_query = "select * from faqcomments where faqid=".$faqid;
		$faq_comment_result = $adb->query($faq_comment_query);
		$faq_comment_noofrows = $adb->num_rows($faq_comment_result);
		for($l=0;$l<$faq_comment_noofrows;$l++)
		{
			$faqcomments = nl2br($adb->query_result($faq_comment_result,$l,'comments'));
			$faqcreatedtime = $adb->query_result($faq_comment_result,$l,'createdtime');
			if($faqcomments != '')
			{
				$result['faq'][$k]['comments'][$l] = $faqcomments;
				$result['faq'][$k]['createdtime'][$l] = $faqcreatedtime;
			}
		}
	}
	$adb->println($result);	
	return $result;
}

function create_lead_from_webform($lastname,$email,$phone,$company,$country,$description)
{
	global $adb;
	$adb->println("Create New Lead from Web Form - Starts");
	require_once("modules/Leads/Lead.php");

	$focus = new Lead();
	$focus->column_fields['lastname'] = $lastname;
	$focus->column_fields['email'] = $email;
	$focus->column_fields['phone'] = $phone;
	$focus->column_fields['company'] = $company;
	$focus->column_fields['country'] = $country;
	$focus->column_fields['description'] = $description;

	$focus->save("Leads");
	
	$focus->retrieve_entity_info($focus->id,"Leads");

	$adb->println("Create New Lead from Web Form - Ends");

	if($focus->id != '')
		return 'Thank you for your interest. Information has been successfully added as Lead.';
	else
		return "Lead creation failed. Try again";
}

function save_faq_comment($faqid,$comment)
{
	global $adb;
	$createdtime = date('Y-m-d H:i:s');
	$faq_query = "insert into faqcomments values('',".$faqid.",'".$comment."','".$createdtime."')";
	$adb->query($faq_query);
	$result = get_KBase_details('');
	return $result;
}
function get_tickets_list($user_name,$id)
{
//	require_once('modules/Users/User.php');
//        $seed_user = new User();
//        $user_id = $seed_user->retrieve_user_id($user_name);

        $seed_ticket = new HelpDesk();
        $output_list = Array();
   
	$response = $seed_ticket->get_user_tickets_list($user_name,$id);
        $ticketsList = $response['list'];
    
       	// create a return array of ticket details.
	foreach($ticketsList as $ticket)
	{
   		$output_list[] = Array(
			"ticketid" => $ticket[ticketid],
			"title"    => $ticket[title],
			"firstname" => $ticket[firstname],
			"lastname" => $ticket[lastname],
			"parent_id"=> $ticket[parent_id],
			"productid"=> $ticket[productid],
			"productname"=> $ticket[productname],
			"priority" => $ticket[priority],
			"severity"=>$ticket[severity],
			"status"=>$ticket[status],
			"category"=>$ticket[category],
			"description"=>$ticket[description],
			"solution"=>$ticket[solution],
                        "createdtime"=>$ticket[createdtime],
                        "modifiedtime"=>$ticket[modifiedtime],
 			);
    	}

    //to remove an erroneous compiler warning
    $seed_ticket = $seed_ticket;

    return $output_list;
}


function contact_by_range($user_name,$from_index,$offset)
{
    
    // $seed_contact = new Contact();
     $output_list = Array();
     $contactList = Array();
   
         {  
         
             $response = crm_get_contacts();
  
       // create a return array of names and email addresses.
             foreach($response as $contactId)
                 {
                     $param = array('contact_id'=>$contactId);
                     $contact = crm_get_contact($param);
                     
                     //
                     //$account_name= $key;
                     $birthdate = $contact->contact_type_object->birth_date;
                     /*if($account_name=="Vtiger_Crm")
        {
            $account_name="";
        }*/
                     if($birthdate == "0000-00-00" or $birthdate=="")
                         {
                             $birthdate = "4501-01-01";
                         }
                     $fname = "";
                     $lname = "";
                     //$fname = ucfirst($contact->sort_name);
                     //$lname = ucfirst($contact->sort_name);
                     if($contact->contact_type =='Individual') {
                         $fname = ucfirst($contact->contact_type_object->first_name);
                         $lname = ucfirst($contact->contact_type_object->last_name);
                     } else {
                         $fname = ucfirst($contact->sort_name);
                     }
                     
                     $output_list[] = Array("first_name"    => $fname,
                                            "last_name"     => $lname,
                                            // "primary_address_city" => $contact[primary_address_city],
                                            //"account_name" => $account_name,
                                            //"account_id"=> $contact[account_id],
                                            "id" => $contact->id,
                                            "email_address" => $contact->location[1]->email[1]->email,
                                            "salutation"=>$contact->contact_type_object->prefix,//$contact[salutation],
                                            "title"=>$contact->job_title,
                                            //"phone_mobile"=>$contact[phone_mobile],
                                            //"reports_to"=>$contact[reports_to_name],
                                            "primary_address_street"=>$contact->location[1]->address->street_address,
                                            "primary_address_city"=>$contact->location[1]->address->city,
                                            //"primary_address_state"=> $contact->location[1]->address->state_province_id,
                                            "primary_address_postalcode"=>$contact->location[1]->address->postal_code,
                                            //"primary_address_country"=>$contact->location[1]->address->country_id,
                                            "alt_address_city"=>$contact->location[2]->address->city,
                                            "alt_address_street"=>$contact->location[2]->address->street_address,
                                            //"alt_address_city"=>$contact[alt_address_city],
                                            //"alt_address_state"=> $contact->location[2]->address->state_province_id,
                                            "alt_address_postalcode"=>$contact->location[2]->address->postal_code,
                                            //"alt_address_country"=>$contact->location[2]->address->country_id,
                                            "office_phone"=>$contact->location[1]->phone[1]->phone,
                                            "home_phone"=>$contact->location[1]->phone[1]->phone,
                                            "other_phone"=>$contact->location[1]->phone[2]->phone,
                                            //"fax"=>$contact[fax],
                                            //"department"=>$contact[department],
                                            "birthdate"=>$birthdate,
                                            //"assistant_name"=>$contact[assistant_name],
                                            //"assistant_phone"=>$contact[assistant_phone],

                                            );
                     
                 }
         }

         

         
         //to remove an erroneous compiler warning
         $seed_contact = $seed_contact;
    
    return $output_list;
}

function get_tasks_count($user_name, $password)
{   
    
    global $current_user;
	require_once('modules/Users/User.php');
	$seed_user = new User();
	$user_id = $seed_user->retrieve_user_id($user_name);
	$current_user = $seed_user;
	$current_user->retrieve($user_id);
   
    require_once('modules/Activities/Activity.php');
    $task = new Activity();

   
    return $task->getCount($user_name);

}

function task_by_range($user_name,$from_index,$offset)
{
	require_once('modules/Activities/Activity.php');
        $seed_task = new Activity();
        $output_list = Array();
   
         {  
            $response = $seed_task->get_tasks($user_name,$from_index,$offset);
            $taskList = $response['list'];
            foreach($taskList as $temptask)
           {
		 if($temptask[date_due]=="0000-00-00")
		 {
			 $temptask[date_due]="4501-01-01";
		 }
		 if($temptask[time_due]=="")
		 {
			 $temptask[time_due]=NULL;
		 }



        		$output_list[] = Array(
                 "name"	=> $temptask[name],
		    	"date_modified" => $temptask[date_modified],
			    "start_date" => $temptask[start_date],
                "id" => $temptask[id],
    			
    			"status" => $temptask[status],
		        "date_due" => $temptask[date_due],	
	    		"description" => $temptask[description],		
                "contact_name" => $temptask[contact_name],		
		    	"priority" => $temptask[priority]);
                 
                 }
        }   


    //to remove an erroneous compiler warning
    $seed_task = $seed_task;
   
    return $output_list;
}



function create_session($user_name, $password)
{
	return "TempSessionID";	
}

function end_session($user_name)
{
	return "Success";	
}
 
function add_contacts_matching_email_address(&$output_list, $email_address, &$seed_contact)	
{
  //global $log;
	$safe_email_address = addslashes($email_address);
	
	$where = "email1 like '$safe_email_address' OR email2 like '$safe_email_address'";
	$response = $seed_contact->get_list("first_name,last_name,primary_address_city", $where, 0);
	$contactList = $response['list'];
	
	//$log->fatal("Retrieved the list");
	
	// create a return array of names and email addresses.
	foreach($contactList as $contact)
	{
		//$log->fatal("Adding another contact to the list: $contact-first_name");
		$output_list[] = Array("first_name"	=> $contact->first_name,
			"last_name" => $contact->last_name,
                        "primary_address_city" => $contact->primary_address_city,
                        "account_name" => $contact->account_name,
			"id" => $contact->id,
                        "email_address" => $contact->email1,
                       "salutation"=>$contact->salutation,
                       "title"=>$contact->title,
                       "phone_mobile"=>$contact->phone_mobile,
                      "reports_to_id"=>$contact->reports_to_id,
                      "primary_address_street"=>$contact->primary_address_street,
                     "primary_address_city"=>$contact->primary_address_city,
                     "primary_address_state"=>$contact->primary_address_state ,
                     "primary_address_postalcode"=>$contact->primary_address_postalcode,
                     "primary_address_country"=>$contact->primary_address_country,
                      "alt_address_city"=>$contact->alt_address_city,
                    "alt_address_street"=>$contact->alt_address_street,
                    "alt_address_city"=>$contact->alt_address_city,
                   "alt_address_state"=>$contact->alt_address_state,
                   "alt_address_postalcode"=>$contact->alt_address_postalcode,
                   "alt_address_country"=>$contact->alt_address_country,
);
        }
          }






function delete_contact($user_name,$id)
{
    /* global $current_user;
	require_once('modules/Users/User.php');
	$seed_user = new User();
	$user_id = $seed_user->retrieve_user_id($user_name);
	$current_user = $seed_user;
	$current_user->retrieve($user_id);

        require_once('modules/Contacts/Contact.php');
        $contact = new Contact();
        $contact->id = $id;
        //$contact->delete($contact->id);
	$contact->mark_deleted($contact->id);
    //	$contact->delete();        */
    
    $contactId = $id;
    $param = array('contact_id'=>$contactId);
    $contact = crm_get_contact($param);
    
    crm_delete_contact(&$contact);
        
    
return "Suceeded in deleting contact";
}


function sync_contact($user_name)
{
  return "synchronized contact successfully";
}


/*function contact_by_email($email_address) 
{ 
  //global $log;
  //$this->log->debug("Contact by email called with: $email_address" .$email_address);
	
	$seed_contact = new Contact();
	$output_list = Array();

	$email_address_list = explode("; ", $email_address);

	// remove duplicate email addresses
	$non_duplicate_email_address_list = Array();
	foreach( $email_address_list as $single_address)
	{
		// Check to see if the current address is a match of an existing address
		$found_match = false;
		foreach( $non_duplicate_email_address_list as $non_dupe_single)
		{
			if(strtolower($single_address) == $non_dupe_single)
			{
				$found_match = true;
				break;
			}
		}	
		
		if($found_match == false)
		{
			$non_duplicate_email_address_list[] = strtolower($single_address);
		}	
	}
	
	// now copy over the non-duplicated list as the original list.
	$email_address_list = &$non_duplicate_email_address_list;
	
	foreach( $email_address_list as $single_address)
	{
          add_contacts_matching_email_address($output_list, $single_address, $seed_contact);	
	}

	//to remove an erroneous compiler warning
	$seed_contact = $seed_contact;
	
	//$log->debug("Contact by email returning");
	return $output_list;
} */ 

function contact_by_search($name) 
{ 
//	global $log;
	$seed_contact = new Contact();
	$where = "first_name like '$name%' OR last_name like '$name%' OR email1 like '$name%' OR email2 like '$name%'";
	$response = $seed_contact->get_list("first_name, last_name", $where, 0);
	$contactList = $response['list'];
	//$row_count = $response['row_count'];
	
	$output_list = Array();
	
	//$log->fatal("Retrieved the list");
	
	// create a return array of names and email addresses.
	foreach($contactList as $contact)
	{
		//$log->fatal("Adding another contact to the list");
		$output_list[] = Array("first_name"	=> $contact->first_name,
			"last_name" => $contact->last_name,
			"account_name" => $contact->account_name,
			"id" => $contact->id,
			"email_address" => $contact->email1);
	}
	
	return $output_list;
}  

function upload_emailattachment($email_id, $filename,$binFile,$filesize)
{
	global $adb;
  $filetype= $_FILES['binFile']['type'];
  $filedata = "./cache/mails/".$email_id.$filename;

      $user_id=1;

  $account  = new Account();
  
  $account->insertIntoAttachment1($email_id,"Emails",$filedata,$filename,$filesize,$filetype,$user_id);

    unlink($filedata);

  return "Suceeded in upload_attachment";

    
}
function track_email($user_name, $contact_ids, $date_sent, $email_subject, $email_body)
{

//	$date_sent = ereg_replace("([0-9]*)/([0-9]*)/([0-9]*)( .*$)", "\\3-\\1-\\2\\4", $date_sent);
	
	
	
	global $current_user;
	require_once('modules/Users/User.php');
	$seed_user = new User();
	$user_id = $seed_user->retrieve_user_id($user_name);
	$current_user = $seed_user;
	$current_user->retrieve($user_id);
	
	$date_sent = getDisplayDate($date_sent);

	require_once('modules/Emails/Email.php');
	
	$email = new Email();

	$email_body = str_replace("'", "''", $email_body);
	$email_subject = str_replace("'", "''", $email_subject);
	
	//fixed subject issue 9/6/05
	$email->column_fields[subject]=$email_subject;
	$email->column_fields[assigned_user_id] = $user_id;
	$email->column_fields[date_start] = $date_sent;
	$email->column_fields[description]  = $email_body;

	
	// Save one copy of the email message
	//$email->saveentity("Emails");
	$email->save("Emails");


	
	// for each contact, add a link between the contact and the email message
	$contact_id_list = explode(";", $contact_ids);

	foreach( $contact_id_list as $contact_id)
	{
		$email->set_emails_contact_invitee_relationship($email->id, $contact_id);
		$email->set_emails_se_invitee_relationship($email->id,$contact_id);
	}
	$email->set_emails_user_invitee_relationship($email->id, $user_id);
	
	//return "Suceeded";
	return $email->id;
}

/*
function sync_task($user_name)
{
  return "synchronized task successfully";
}

*/

/*

    array('user_name'=>'xsd:string', 'first_name'=>'xsd:string', 'last_name'=>'xsd:string', 'email_address'=>'xsd:string','account_name'=>'xsd:string', 'salutation'=>'xsd:string', 'title'=>'xsd:string', 'phone_mobile'=>'xsd:string' , 'reports_to_id'=>'xsd:string', 'primary_address_street'=>'xsd:string', 'primary_address_city'=>'xsd:string', 'primary_address_state'=>'xsd:string' , 'primary_address_postalcode'=>'xsd:string', 'primary_address_country'=>'xsd:string', 'alt_address_city'=>'xsd:string', 'alt_address_street'=>'xsd:string','alt_address_city'=>'xsd:string', 'alt_address_state'=>'xsd:string', 'alt_address_postalcode'=>'xsd:string', 'alt_address_country'=>'xsd:string'),


*/

function create_contact($user_name, $first_name, $last_name, $email_address ,$account_name , $salutation , $title, $phone_mobile, $reports_to,$primary_address_street,$primary_address_city,$primary_address_state,$primary_address_postalcode,$primary_address_country,$alt_address_city,$alt_address_street,$alt_address_state,$alt_address_postalcode,$alt_address_country,$office_phone="",$home_phone="",$fax="",$department="",$description="")
{
    if($birthdate == "4501-01-01")
    {
	    $birthdate = "0000-00-00";
    }
	return create_contact1($user_name, $first_name, $last_name, $email_address ,$account_name , $salutation , $title, $phone_mobile, $reports_to,$primary_address_street,$primary_address_city,$primary_address_state,$primary_address_postalcode,$primary_address_country,$alt_address_city,$alt_address_street,$alt_address_state,$alt_address_postalcode,$alt_address_country,$office_phone,$home_phone,"",$fax,$department,"","","",$description);
}


function create_contact1($user_name, $first_name, $last_name, $email_address ,$account_name , $salutation , $title, $phone_mobile, $reports_to,$primary_address_street,$primary_address_city,$primary_address_state,$primary_address_postalcode,$primary_address_country,$alt_address_city,$alt_address_street,$alt_address_state,$alt_address_postalcode,$alt_address_country,$office_phone,$home_phone,$other_phone,$fax,$department,$birthdate,$assistant_name,$assistant_phone,$description)
{

    $contact = array();
	$contact['prefix']=$salutation; 
    $contact['first_name']=$first_name;
	$contact['last_name']=$last_name;
	$contact['birth_date']=$birthdate;
    $contact['job_title']=$title;
    $contact['contact_type']="Individual";
	$contact['location']=array(
                               "1"=>array("location_type_id"=>1,
                                          "is_primary" => 1,
                                          "address"=>array("street_address"    =>$primary_address_street,
                                                           "city"              =>$primary_address_city,
                                                           "postal_code"       =>$primary_address_postalcode,
                                                           "state_province_id" =>"",
                                                           "country_id"        =>"",
                                                           ),
                                          "phone"  =>array(
                                                           "1"=>array(
                                                                      "phone_type"=>"Phone",
                                                                      "phone"     =>$home_phone
                                                                      ),
                                                           "2"=>array(
                                                                      "phone_type"=>"Mobile",
                                                                      "phone"     =>$phone_mobile
                                                                      )
                                                           
                                                           ),
                                          "email" =>array(
                                                          "1"=>array(
                                                                     "email"=>$email_address
                                                                     )
                                                          )
                                          ),

                               "2"=>array("location_type_id"=>2,
                                          
                                          "address"=>array("street_address"    =>$alt_address_street,
                                                           "city"              =>$alt_address_city,
                                                           "postal_code"       =>$alt_address_postalcode,
                                                           "state_province_id" =>"",
                                                           "country_id"        =>"",
                                                           ),
                                          "phone"  =>array(
                                                           "1"=>array(
                                                                      "phone_type"=>"Phone",
                                                                      "phone"     =>$office_phone
                                                                      ),
                                                           "2"=>array(
                                                                      "phone_type"=>"Fax",
                                                                      "phone"     =>$fax
                                                                      )
                                                           
                                                           ),
                                         
                                          )

                               );

    //$new=crm_create_contact( &$contact, $contact_type = 'Individual' );
    $ids=array();
    $con = CRM_Contact_BAO_Contact::create($contact, $ids,"2");

	return $con->id;
}

function create_ticket($title,$description,$priority,$severity,$category,$user_name,$parent_id,$product_id)
{
/*	require_once('modules/Users/User.php');
        $seed_user = new User();
        $user_id = $seed_user->retrieve_user_id($user_name);
*/
        $seed_ticket = new HelpDesk();
        $output_list = Array();
   
	//$response = $seed_ticket->create_tickets_list($user_name,$smcreatorid);
	require_once('modules/HelpDesk/HelpDesk.php');
	$ticket = new HelpDesk();
	
    	$ticket->column_fields[ticket_title] = $title;
	$ticket->column_fields[description]=$description;
	$ticket->column_fields[ticketpriorities]=$priority;
	$ticket->column_fields[ticketseverities]=$severity;
	$ticket->column_fields[ticketcategories]=$category;
	$ticket->column_fields[ticketstatus]='Open';

	$ticket->column_fields[parent_id]=$parent_id;
	$ticket->column_fields[product_id]=$product_id;
//	$ticket->column_fields[assigned_user_id]=$user_id;
    	//$ticket->saveentity("HelpDesk");
    	$ticket->save("HelpDesk");

	$_REQUEST['name'] = '[ Ticket ID : '.$ticket->id.' ] '.$title;
	$body = ' Ticket ID : '.$ticket->id.'<br> Ticket Title : '.$title.'<br><br>';
	$_REQUEST['description'] = $body.$description;
	$_REQUEST['return_module'] = 'HelpDesk';
	$_REQUEST['parent_id'] = $parent_id; 
	$_REQUEST['assigned_user_id'] = $parnet_id; 
	require_once('modules/Emails/send_mail.php');

	return get_tickets_list($user_name,$parent_id); 
	//return $ticket->id;
}
function update_ticket_comment($ticketid,$ownerid,$createdtime,$comments)
{
	global $adb;
	$servercreatedtime = date("Y-m-d H:i:s");
	$sql = "insert into ticketcomments values('',".$ticketid.",'".$comments."','".$ownerid."','customer','".$servercreatedtime."')";
	$adb->query($sql);

	$updatequery = "update crmentity set modifiedtime = '".$servercreatedtime."' where crmid=".$ticketid;
	$adb->query($updatequery);
}
function close_current_ticket($ticketid)
{
	global $adb;
	$sql = "update troubletickets set status='Closed' where ticketid=".$ticketid;
	$result = $adb->query($sql);
	if($result)
		return "<br><b>Ticket status is updated as 'Closed'.</b>";
	else
		return "<br><b>Ticket could not be closed.</br>";
}
function authenticate_user($username,$password)
{
	global $adb;
	$current_date = date("Y-m-d");
	$sql = "select id, user_name, user_password,last_login_time, support_start_date, support_end_date from PortalInfo inner join CustomerDetails on PortalInfo.id=CustomerDetails.customerid where user_name='".$username."' and user_password = '".$password."' and isactive=1 and CustomerDetails.support_end_date >= ".$current_date;
	$result = $adb->query($sql);	
	$list['id'] = $adb->query_result($result,0,'id');
	$list['user_name'] = $adb->query_result($result,0,'user_name');
	$list['user_password'] = $adb->query_result($result,0,'user_password');
	$list['last_login_time'] = $adb->query_result($result,0,'last_login_time');
	$list['support_start_date'] = $adb->query_result($result,0,'support_start_date');
	$list['support_end_date'] = $adb->query_result($result,0,'support_end_date');

	return $list;
}
function change_password($id,$username,$password)
{
	global $adb;
	$sql = "update PortalInfo set user_password='".$password."' where id=".$id." and user_name='".$username."'";
	$result = $adb->query($sql);

	$list = authenticate_user($username,$password);

        return $list;
}
function update_login_details($id,$flag)
{
        global $adb;
	$current_time = date("Y-m-d H:i:s");

	if($flag == 'login')
	{
	        $sql = "update PortalInfo set login_time='".$current_time."' where id=".$id;
	        $result = $adb->query($sql);
	}
	elseif($flag == 'logout')
	{
		$sql = "select * from PortalInfo where id=".$id;
                $result = $adb->query($sql);
                if($adb->num_rows($result) != 0)
                        $last_login = $adb->query_result($result,0,'login_time');

		$sql = "update PortalInfo set logout_time = '".$current_time."', last_login_time='".$last_login."' where id=".$id;
		$result = $adb->query($sql);
	}

        return $list;
}
function send_mail_for_password($mailid)
{
	global $adb;
        include("modules/Emails/class.phpmailer.php");

	$sql = "select * from PortalInfo  where user_name='".$mailid."'";
	$user_name = $adb->query_result($adb->query($sql),0,'user_name');
	$password = $adb->query_result($adb->query($sql),0,'user_password');
	$isactive = $adb->query_result($adb->query($sql),0,'isactive');

	$fromquery = "select users.user_name, users.email1 from users inner join crmentity on users.id = crmentity.smownerid inner join contactdetails on contactdetails.contactid=crmentity.crmid where contactdetails.email ='".$mailid."'";
	$initialfrom = $adb->query_result($adb->query($fromquery),0,'user_name');
	$from = $adb->query_result($adb->query($fromquery),0,'email1');

	$contents = "<br>Following are your Customer Portal login details :";
	$contents .= "<br><br>User Name : ".$user_name;
	$contents .= "<br>Password : ".$password;

        $mail = new PHPMailer();

        $mail->Subject = "Regarding your Customer Portal login details";
        $mail->Body    = $contents;
        $mail->IsSMTP();

        $mailserverresult=$adb->query("select * from systems where server_type='email'");
        $mail_server=$adb->query_result($mailserverresult,0,'server');

        $mail->Host = $mail_server;
        $mail->SMTPAuth = true;
        $mail->Username = $mail_server_username;
        $mail->Password = $mail_server_password;
        $mail->From = $from;
        $mail->FromName = $initialfrom;

        $mail->AddAddress($user_name);
        $mail->AddReplyTo($current_user->name);
        $mail->WordWrap = 50;

        $mail->IsHTML(true);

        $mail->AltBody = "This is the body in plain text for non-HTML mail clients";
	if($mailid == '')
	{
		return "false@@@<b>Please give your email id</b>";
	}
	elseif($user_name == '' && $password == '')
	{
		return "false@@@<b>Please check your email id for Customer Portal</b>";
	}
	elseif($isactive == 0)
        {
                return "false@@@<b>Your login is revoked. Please contact your admin.</b>";
        }
	elseif(!$mail->Send())
	{
		return "false@@@<b>Mail could not be sent</b>";
	}
	else
		return "true@@@<b>Mail has been sent to your mail id with the customer portal login details</b>";

}

/*function retrievereportsto($reports_to,$user_id,$account_id)
{
  if($reports_to=="")
    {
        return null;
    }
     if($reports_to==null)
     {
         return null;
     }


$first_name;
$last_name;
$tok = strtok($reports_to," \n\t");
if($tok) {
    $first_name=$tok;
    $tok = strtok(" \n\t");
}
if($tok) {
    $last_name=$tok;
    $tok = strtok(" \n\t");
}

  if($first_name=="") 
    {
        return null;
    }
    if($last_name=="") 
    {
        return null;    
    }



// to do handle smartly handle the manager name
     $query = "select contactdetails.contactid as contactid from contactdetails inner join crmentity on crmentity.crmid=contactdetails.contactid where crmentity.deleted=0 and contactdetails.firstname like '".$first_name ."' and contactdetails.lastname like '" .$last_name ."'";



    	require_once('modules/Contacts/Contact.php');
	    $contact = new Contact();


    $db = new PearDatabase();
    $result= $db->query($query) or  die ("Not able to execute retyrievereports query");



    $rows_count =  $db->getRowCount($result);
    if($rows_count==0)
    {
    	$contact->column_fields[firstname] = $first_name;
        $contact->column_fields[lastname] = $last_name;
    	$contact->column_fields[assigned_user_id]=$user_id;
        $contact->column_fields[account_id]=$account_id;
    	//$contact->saveentity("Contacts");
    	$contact->save("Contacts");
        //mysql_close();
    	return $contact->id;
    }
    else if ($rows_count==1)
    {
        $row = $db->fetchByAssoc($result, 0);
        //mysql_close();
        return $row["contactid"];	    
    }
    else
    {
        $row = $db->fetchByAssoc($result, 0);
        //mysql_close();
        return $row["contactid"];	    
    }

}
*/

/*function retrieve_account_id($account_name,$user_id)
{
  if($account_name=="")
    {
        return null;
    }

    $query = "select account.accountname accountname,account.accountid accountid from account inner join crmentity on crmentity.crmid=account.accountid where crmentity.deleted=0 and account.accountname='" .$account_name."'";


	$db = new PearDatabase();
    $result=  $db->query($query) or die ("Not able to execute insert");
    
    $rows_count =  $db->getRowCount($result);
    if($rows_count==0)
    {
    	require_once('modules/Accounts/Account.php');
	    $account = new Account();
    	$account->column_fields[accountname] = $account_name;
    	$account->column_fields[assigned_user_id]=$user_id;
    	//$account->saveentity("Accounts");
    	$account->save("Accounts");
        //mysql_close();
    	return $account->id;
    }
    else if ($rows_count==1)
    {
        $row = $db->fetchByAssoc($result, 0);
        //mysql_close();
        return $row["accountid"];	    
    }
    else
    {
        $row = $db->fetchByAssoc($result, 0);
        //mysql_close();
        return $row["accountid"];	    
    }
    
}*/


function create_task($user_name, $start_date, $date_modified,$name,$status,$priority,$description,$date_due,$contact_name)
{
	//global $log;
	
	//todo make the activity body not be html encoded
	//$log->fatal("In Create contact: username: $user_name first/last/email ($first_name, $last_name, $email_address)");
	
	global $current_user;
	require_once('modules/Users/User.php');
	$seed_user = new User();
	$user_id = $seed_user->retrieve_user_id($user_name);
	$current_user = $seed_user;
	$current_user->retrieve($user_id);
	
	require_once('modules/Activities/Activity.php');
	$task = new Activity();
	
	//$task->date_entered = $date_entered;
	//$task->date_modified = $date_modified;
	//$task[assigned_user_id] = $assigned_user_id;
    $task->column_fields[subject] = $name;
	$task->column_fields[taskstatus]=$status;
    $task->column_fields[date_start]=getDisplayDate($start_date);
	$task->column_fields[taskpriority]=$priority;
	$task->column_fields[description]=$description;
   	$task->column_fields[activitytype]="Task";
    // NOT EXIST IN DATA MODEL
    $task->column_fields[due_date]=getDisplayDate($date_due);
   	$task->column_fields[contact_id]= retrievereportsto($contact_name,$user_id,null); 
	$task->column_fields[assigned_user_id]=$user_id;
    //$task->saveentity("Activities");
    $task->save("Activities");


	return $task->id;
}


function update_contact($user_name,$id, $first_name, $last_name, $email_address ,$account_name , $salutation , $title, $phone_mobile, $reports_to,$primary_address_street,$primary_address_city,$primary_address_state,$primary_address_postalcode,$primary_address_country,$alt_address_city,$alt_address_street,$alt_address_state,$alt_address_postalcode,$alt_address_country,$office_phone,$home_phone,$other_phone,$fax,$department,$birthdate,$assistant_name,$assistant_phone)
{
    /*	global $current_user;
	require_once('modules/Users/User.php');
	$seed_user = new User();
	$user_id = $seed_user->retrieve_user_id($user_name);
	$current_user = $seed_user;
	$current_user->retrieve($user_id);
	
	require_once('modules/Contacts/Contact.php');
	$contact = new Contact();
	/*
        $contact->first_name = $first_name;
	$contact->last_name = $last_name;
	$contact->email1 = $email_address;
        //$contact->account_name = $account_name;
	$contact->account_id=retrieve_account_id($account_name,$user_id);
        $contact->salutation = $salutation;
        $contact->title = $title;
        $contact->phone_mobile = $phone_mobile;
        $contact->reports_to_id = retrievereportsto($reports_to,$user_id,$contact->account_id);  
	$contact->primary_address_city = $primary_address_city;
        $contact->primary_address_postalcode = $primary_address_postalcode;
        $contact->primary_address_state = $primary_address_state;
        $contact->primary_address_street = $primary_address_street;
   $contact->primary_address_country = $primary_address_country; 
        $contact->alt_address_country = $alt_address_country;
        $contact->alt_address_postalcode = $alt_address_postalcode;
        $contact->alt_address_state = $alt_address_state;
        $contact->alt_address_street = $alt_address_street;

  $contact->alt_address_city = $alt_address_city;

	$contact->id=$id;

	

	$contact->save();
	*/
	
	/*$contact->column_fields[firstname]=$first_name;
	$contact->column_fields['lastname']=$last_name;*/
	
	/*if($account_name=='')
	{
		$account_name="Vtiger_Crm";
	}
	else if($account_name==null)
	{
		$account_name="Vtiger_Crm";
	}*/
	
	/*$contact->column_fields[account_id]=retrieve_account_id($account_name,$user_id);// NULL value is not supported NEED TO FIX
	
	$contact->column_fields[salutation]=$salutation;
	// EMAIL IS NOT ADDED
	$contact->column_fields[title]=$title;
	$contact->column_fields[email]=$email_address;
	
	
	$contact->column_fields[mobile]=$phone_mobile;
	$contact->column_fields[reports_to_id] =retrievereportsto($reports_to,$user_id,$account_id);// NOT FIXED IN SAVEENTITY.PHP
	$contact->column_fields[mailingstreet]=$primary_address_street;
	$contact->column_fields[mailingcity]=$primary_address_city;
	$contact->column_fields[mailingcountry]=$primary_address_country;
	$contact->column_fields[mailingstate]=$primary_address_state;
	$contact->column_fields[mailingzip]=$primary_address_postalcode;

	$contact->column_fields[otherstreet]=$alt_address_street;
	$contact->column_fields[othercity]=$alt_address_city;
	$contact->column_fields[othercountry]=$alt_address_country;
	$contact->column_fields[otherstate]=$alt_address_state;
	$contact->column_fields[otherzip]=$alt_address_postalcode;

	$contact->column_fields[assigned_user_id]=$user_id;
	$contact->id=$id;
    $contact->column_fields[phone]= $office_phone;
    $contact->column_fields[homephone]= $home_phone;
    $contact->column_fields[otherphone]= $other_phone;
    $contact->column_fields[fax]= $fax;
    $contact->column_fields[department]=$department;
    if($birthdate == "4501-01-01")
    {
	    $birthdate = "0000-00-00";
    }
    $contact->column_fields[birthday]= getDisplayDate($birthdate);
    $contact->column_fields[assistant]= $assistant_name;
    $contact->column_fields[assistantphone]= $assistant_phone;

	$contact->mode="edit";
	//$contact->saveentity("Contacts");
	$contact->save("Contacts");
	
	return "Suceeded";.*/
    
    
   
    /*$contact = array();
    $contactId = $id ;
    
    $contact['id']= $id;
	$contact['first_name']=$first_name;
	$contact['last_name']=$last_name;
    $contact['prefix']=$salutation;
	$contact['city'] = $primary_address_city;
	$contact['email']=$email_address;*/

    $contactId = $id ;
    $contact = array();
	$contact['prefix']=$salutation; 
    $contact['first_name']=$first_name;
	$contact['last_name']=$last_name;
	$contact['birth_date']=$birthdate;
    $contact['job_title']=$title;
    $contact['contact_type']="Individual";
	$contact['location']=array(
                               "1"=>array("location_type_id"=>1,
                                          "is_primary" => 1,
                                          "address"=>array("street_address"    =>$primary_address_street,
                                                           "city"              =>$primary_address_city,
                                                           "postal_code"       =>$primary_address_postalcode,
                                                           "state_province_id" =>"",
                                                           "country_id"        =>"",
                                                           ),
                                          "phone"  =>array(
                                                           "1"=>array(
                                                                      "phone_type"=>"Phone",
                                                                      "phone"     =>$home_phone
                                                                      ),
                                                           "2"=>array(
                                                                      "phone_type"=>"Mobile",
                                                                      "phone"     =>$phone_mobile
                                                                      )
                                                           
                                                           ),
                                          "email" =>array(
                                                          "1"=>array(
                                                                     "email"=>$email_address
                                                                     )
                                                          )
                                          ),

                               "2"=>array("location_type_id"=>2,
                                          
                                          "address"=>array("street_address"    =>$alt_address_street,
                                                           "city"              =>$alt_address_city,
                                                           "postal_code"       =>$alt_address_postalcode,
                                                           "state_province_id" =>"",
                                                           "country_id"        =>"",
                                                           ),
                                          "phone"  =>array(
                                                           "1"=>array(
                                                                      "phone_type"=>"Phone",
                                                                      "phone"     =>$office_phone
                                                                      ),
                                                           "2"=>array(
                                                                      "phone_type"=>"Fax",
                                                                      "phone"     =>$fax
                                                                      )
                                                           
                                                           ),
                                         
                                          )

                               );

    crm_update_contact($contactId, &$contact);
        //$new=crm_create_contact( &$contact, $contact_type = 'Individual' );

    //	return $new->id;

    




    return "Suceeded";


}


function update_task($user_name, $id,$start_date, $name, $status,$priority,$description,$date_due,$contact_name)
{
	global $current_user;
	require_once('modules/Users/User.php');
	$seed_user = new User();
	$user_id = $seed_user->retrieve_user_id($user_name);
	$current_user = $seed_user;
	$current_user->retrieve($user_id);
	
    require_once('modules/Activities/Activity.php');
	$task = new Activity();

	if($date_due == "4501-01-01")
	{
	    $date_due = "0000-00-00";
	}
		
    $task->column_fields[subject] = $name;
	$task->column_fields[taskstatus]=$status;
	$task->column_fields[taskpriority]=$priority;
	$task->column_fields[description]=$description;
    $task->column_fields[activitytype]="Task";
    $task->column_fields[due_date]= getDisplayDate($date_due);
    $task->column_fields[date_start]= getDisplayDate($start_date);
    $task->column_fields[contact_id]= retrievereportsto($contact_name,$user_id,null); 
	$task->column_fields[assigned_user_id]=$user_id;

    
	$task->id = $id;
    $task->mode="edit";
    //$task->saveentity("Activities");
    $task->save("Activities");
    return "Suceeded in updating task";
}




function delete_task($user_name,$id)
{
        global $current_user;
	require_once('modules/Users/User.php');
	$seed_user = new User();
	$user_id = $seed_user->retrieve_user_id($user_name);
	$current_user = $seed_user;
	$current_user->retrieve($user_id);

     require_once('modules/Activities/Activity.php');
        $task = new Activity();
        $task->id = $id;
        $task->mark_deleted($id);
        return "Suceeded in deleting task";
}




function retrieve_task($name) 
{ 
//	global $log;
	$task = new Task();
	$where = "name like '$name%'";
	$response = $task->get_list("name", $where, 0);
	$taskList = $response['list'];
	$output_list = Array();
	
	foreach($taskList as $temptask)
	{
		$output_list[] = Array("name"	=> $temptask->name,
			"date_modified" => $temptask->date_modified,
			"start_date" => $temptask->start_date,
			"id" => $temptask->id,

			"status" => $temptask->status,
		        "date_due" => $temptask->date_due,	
			"description" => $temptask->description,		
			"priority" => $temptask->priority);
	}
	
	return $output_list;
}  

//calendar
function get_calendar_count($user_name, $password)
{   
    global $current_user;
	require_once('modules/Users/User.php');
	$seed_user = new User();
	$user_id = $seed_user->retrieve_user_id($user_name);
	$current_user = $seed_user;
	$current_user->retrieve($user_id);
   
    require_once('modules/Activities/Activity.php');
    $calObj = new Activity();

    return $calObj->getCount_Meeting($user_name);
}

function calendar_by_range($user_name,$from_index,$offset)
{
	require_once('modules/Activities/Activity.php');
	$seed_task = new Activity();
	$output_list = Array(); 
 
	$response = $seed_task->get_calendars($user_name,$from_index,$offset);
	$taskList = $response['list'];
	foreach($taskList as $temptask)
	{
		$starthour = explode(":",$temptask[time_start]);
		
		if($temptask[date_due]== "0000-00-00" || $temptask[date_due] == "")
		{
			$temptask[date_due] = $temptask[start_date];
		}
		
		if($temptask[duehours] == "0")
		{
			$temptask[duehours] = $starthour[0];
			if(strlen($temptask[duehours]) == 1)
			{
				$temptask[duehours] = "0".$temptask[duehours];
			}
		}else if($temptask[duehours] == "00")
		{
		   $temptask[duehours] = $starthour[0];
		   if(strlen($temptask[duehours]) == 1)
		   {
				$temptask[duehours] = "0".$temptask[duehours];
		   }
		}else
		{
			$temptask[duehours] = intval($starthour[0]) + intval($temptask[duehours]);
			if(intval($temptask[duehours]) == 24)
			{
				$temptask[duehours] = "00";
			}
		    if(strlen($temptask[duehours]) == 1)
		    {
				$temptask[duehours] = "0".$temptask[duehours];
		    }
		}
		
		$startdate = $temptask[start_date]." ".$temptask[time_start];
	    $duedate = $temptask[date_due]." ".$temptask[duehours].":".$temptask[dueminutes];
		$output_list[] = Array(
		"name"	=> $temptask[name],
		"date_modified" => $temptask[date_modified],
		"start_date" => $startdate,
		"id" => $temptask[id],	
		"date_due" => $duedate,	
		"description" => $temptask[description],		
		"contact_name" => $temptask[contact_name],
		"location" => $temptask[location],);		
	}   
	//to remove an erroneous compiler warning
	$seed_task = $seed_task;
	return $output_list;
}

function create_calendars($user_name,$output_list)
{
	$counter=0;
	foreach($output_list as $task)
	{
       $id= create_calendar($user_name, $task[start_date], $task[date_modified],$task[name],$task[description],$task[date_due],$task[contact_name],$task[location]);
      
	   $output_list[$counter] ['id']=$id;
	   $counter++;
	}
	return array_reverse($output_list);
}

function create_calendar($user_name, $start_date, $date_modified,$name,$description,$date_due,$contact_name,$location)
{
	
	global $current_user;
	require_once('modules/Users/User.php');
	$seed_user = new User();
	$user_id = $seed_user->retrieve_user_id($user_name);
	$current_user = $seed_user;
	$current_user->retrieve($user_id);
	
	require_once('modules/Activities/Activity.php');
	$task = new Activity();
	
	$task->column_fields[subject] = $name;
	
	//<<<<<<<<<<<<<<<<<Date Time>>>>>>>>>>>>>>>
	$startdate = explode(" ",$start_date);
    $task->column_fields[date_start]=getDisplayDate($startdate[0]);
    $task->column_fields[time_start]=$startdate[1];
    
    $starthourmin = explode(":",$startdate[1]);
   	$task->column_fields[activitytype]="Meeting";

    $duedate = explode(" ",$date_due);
    $task->column_fields[due_date]=getDisplayDate($duedate[0]);
    
    $duetime = explode(":",$duedate[1]);

    if(intval($starthourmin[0]) < 23)
	{
	  $due_hour = intval($duetime[0]) - intval($starthourmin[0]);
	}else
	{
	  if($duetime[0] == "00")
	  {
	     $due_hour = 24 - intval($starthourmin[0]);
  	  }else
  	  {
	  	 $due_hour = intval($duetime[0]) - intval($starthourmin[0]);
  	  }
	}
    $task->column_fields[duration_hours] = $due_hour;
    
    $task->column_fields[duration_minutes] =$duetime[1];
    //<<<<<<<<<<<<<<<<<Date Time>>>>>>>>>>>>>>>
    
    $task->column_fields[description] = $description;
    $task->column_fields[location] = $location;
        // NOT EXIST IN DATA MODEL    
   	$task->column_fields[contact_id]= retrievereportsto($contact_name,$user_id,null); 
	$task->column_fields[assigned_user_id]=$user_id;
    $task->saveentity("Activities");
	return $task->id;
}

function update_calendar($user_name, $id,$start_date,$name,$description,$date_due,$contact_name,$location)
{
	global $current_user;
	require_once('modules/Users/User.php');
	$seed_user = new User();
	$user_id = $seed_user->retrieve_user_id($user_name);
	$current_user = $seed_user;
	$current_user->retrieve($user_id);
	
    require_once('modules/Activities/Activity.php');
	$task = new Activity();

    $task->column_fields[subject] = $name;
	$task->column_fields[activitytype]="Meeting";
    //$task->column_fields[date_due]=$date_due;
    //$task->column_fields[date_start]=$start_date;
    
    //<<<<<<<<<<<<<<<<<Date Time>>>>>>>>>>>>>>>
	$startdate = explode(" ",$start_date);
    $task->column_fields[date_start] = getDisplayDate($startdate[0]);
    $task->column_fields[time_start]=$startdate[1];
    
    $starthourmin = explode(":",$startdate[1]);
   	$task->column_fields[activitytype]="Meeting";

    $duedate = explode(" ",$date_due);
    $task->column_fields[due_date]= getDisplayDate($duedate[0]);
    
    $duetime = explode(":",$duedate[1]);

    if(intval($starthourmin[0]) < 23)
	{
	  $due_hour = intval($duetime[0]) - intval($starthourmin[0]);
	}else
	{
	  if($duetime[0] == "00")
	  {
	     $due_hour = 24 - intval($starthourmin[0]);
  	  }else
  	  {
	  	 $due_hour = intval($duetime[0]) - intval($starthourmin[0]);
  	  }
	}
    $task->column_fields[duration_hours] = $due_hour;
    
    $task->column_fields[duration_minutes] =$duetime[1];
    //<<<<<<<<<<<<<<<<<Date Time>>>>>>>>>>>>>>>
    
    $task->column_fields[description] = $description;
    $task->column_fields[location] = $location;
    
    $task->column_fields[contact_id]= retrievereportsto($contact_name,$user_id,null); 
	$task->column_fields[assigned_user_id]=$user_id;

    
	$task->id = $id;
    $task->mode="edit";
    $task->saveentity("Activities");
    return "Suceeded in updating Calendar";
}

function delete_calendar($user_name,$id)
{
        global $current_user;
	require_once('modules/Users/User.php');
	$seed_user = new User();
	$user_id = $seed_user->retrieve_user_id($user_name);
	$current_user = $seed_user;
	$current_user->retrieve($user_id);

        require_once('modules/Activities/Activity.php');
        $task = new Activity();
        //$task->id = $id;
        $task->mark_deleted($id);
        return "Suceeded in deleting Calendar";
}

function retrieve_calendar($name) 
{ 
	$task = new Task();
	$where = "name like '$name%'";
	$response = $task->get_list("name", $where, 0);
	$taskList = $response['list'];
	$output_list = Array();
	
	foreach($taskList as $temptask)
	{
		$output_list[] = Array("name"	=> $temptask->name,
                               "date_modified" => $temptask->date_modified,
                               "start_date" => $temptask->start_date,
                               "id" => $temptask->id,
                               "status" => $temptask->status,
                               "date_due" => $temptask->date_due,	
                               "description" => $temptask->description,		
                               "priority" => $temptask->priority);
	}
	
	return $output_list;
}
//calendar
//$a = contact_by_range($user_name,$from_index,$offset);
//print_r($a);
//$log->fatal("In soap.php");

/* Begin the HTTP listener service and exit. */ 
$server->service($HTTP_RAW_POST_DATA); 


exit(); 


?>
