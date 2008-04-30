<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * This class contains all the function that are called using AJAX (dojo)
 */
class CRM_Core_Page_AJAX extends CRM_Core_Page 
{
    /**
     * Run the page
     */
    function run( &$args ) 
    {
        $this->invoke( $args );
        exit( );
    }

    /**
     * Invoke function that redirects to respective functions
     */
    function invoke( &$args ) 
    {
        // intialize the system
        $config =& CRM_Core_Config::singleton( );

        if ( $args[0] != 'civicrm' && $args[1] != 'ajax' ) {
            exit( );
        }

        switch ( $args[2] ) {

        case 'search':
            return $this->search( $config );

        case 'state':
            return $this->state( $config );

        case 'country':
            return $this->country( $config );

        case 'status':
            return $this->status( $config );

        case 'event':
            return $this->event( $config );
        
        case 'eventType':
            return $this->eventType( $config );

        case 'customdatatype':
            return $this->customDataType( $config );

        case 'custominputtype':
            return $this->customInputType( $config );

        case 'caseSubject':
             return $this->caseSubject( $config );

        case 'template':
            return $this->template( $config );

        case 'custom':
            return $this->customField( $config );

        case 'help':
            return $this->help( $config );

        case 'mapper':
            require_once 'CRM/Core/Page/AJAX/Mapper.php';
            $method = array( 'CRM_Core_Page_AJAX_Mapper',
                             $args[3] );

            if ( is_callable( $method ) ) {
                return eval( "return CRM_Core_Page_AJAX_Mapper::{$args[3]}( " . ' $config ); ' );
            }
            exit( );

        default:
            return;
        }
    }

    /**
     * Function for building contact combo box
     */
    function search( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $domainID  = CRM_Utils_Type::escape( $_GET['d'], 'Integer' );
        $name      = strtolower( CRM_Utils_Type::escape( $_GET['name'], 'String'  ) ); 
        
        $elements = array( );
        if ( $name ) {
            $name      = str_replace( '*', '%', $name );
        
            list($contactName,$street,$city) = explode(':::',$name);
        
            if ($street) {
                $addStreet = "AND civicrm_address.street_address LIKE '$street%'";
            }
            if ($city) {
                $addCity = "AND civicrm_address.city LIKE '$city%'";
            }
            
            $shared = null;
            if ( isset($_GET['sh']) ) {
                $shared = CRM_Utils_Type::escape( $_GET['sh'], 'Integer');
            }
            
            $relType = null;
            if ( isset($_GET['reID']) ) {
                $relType = CRM_Utils_Type::escape( $_GET['reID'], 'Integer');
                $rel = CRM_Utils_Type::escape( $_GET['retyp'], 'String');
                
                if ( !$_GET['retyp'] ) {
                    return;
                }
                
            }
            
            $organization = null;
            if ( isset($_GET['org']) ) {
                $organization = CRM_Utils_Type::escape( $_GET['org'], 'Integer');
            }
            
            if ( $organization ) {
                
                $query = "
SELECT CONCAT_WS(':::',TRIM(organization_name),LEFT(street_address,25),city) 'sort_name', 
civicrm_contact.id id
FROM civicrm_contact
LEFT JOIN civicrm_address ON ( civicrm_contact.id = civicrm_address.contact_id
                                AND civicrm_address.is_primary=1
                             )
WHERE civicrm_contact.contact_type='Organization' AND organization_name LIKE '$contactName%'
{$addStreet} {$addCity}
ORDER BY organization_name ";

            } else if ( $shared ) {
                
                $query = "
SELECT CONCAT_WS(':::' , household_name , street_address , supplemental_address_1 , city , sp.abbreviation ,postal_code, cc.name , cw.name )'sort_name' , civicrm_contact.id 'id' , civicrm_contact.display_name 'disp' FROM civicrm_contact LEFT JOIN civicrm_address ON (civicrm_contact.id =civicrm_address.contact_id AND civicrm_address.is_primary =1 )LEFT JOIN civicrm_state_province sp ON (civicrm_address.state_province_id =sp.id )LEFT JOIN civicrm_country cc ON (civicrm_address.country_id =cc.id )LEFT JOIN civicrm_worldregion cw ON (cw.id =cc.region_id )WHERE civicrm_contact.contact_type ='Household' AND household_name LIKE '$name%' ORDER BY household_name ";

            } else if($relType) {
                
                $query = "
SELECT c.sort_name, c.id
FROM civicrm_contact c, civicrm_relationship_type r
WHERE c.sort_name LIKE '$name%'
AND c.domain_id = $domainID
AND r.id = $relType
AND c.contact_type = r.contact_type_{$rel}
ORDER BY sort_name" ;
            
            } else {
                
                $query = "
SELECT sort_name, id
FROM civicrm_contact
WHERE sort_name LIKE '%$name'
AND domain_id = $domainID
ORDER BY sort_name ";            
        }
 
            $start = CRM_Utils_Type::escape( $_GET['start'], 'Integer' );
            $end = 10;
            
            if ( isset( $_GET['count'] ) ) {
                $end   = CRM_Utils_Type::escape( $_GET['count'], 'Integer' );
            }
            
            $query .= " LIMIT {$start},{$end}";
            
            $nullArray = array( );
            $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );
            
            if ( $shared ) {
                while ( $dao->fetch( ) ) {
                    $elements[] = array( 'name' => $dao->disp,
                                         'id'   => $dao->sort_name,
                                         );
                }
            } else {  
                while ( $dao->fetch( ) ) {
                    $elements[] = array( 'name' => $dao->sort_name,
                                         'id'   => $dao->id );
                }
            }
        } else {
            $elements[] = array( 'name' => $name,
                                 'id'   => $name );
        
        }

        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements );
    }
    
    /**
     * Function for building Event combo box
     */
    function event( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $domainID = CRM_Utils_Type::escape( $_GET['d'], 'Integer' );
        
        $getRecords = false;
        if ( isset( $_GET['name'] ) && $_GET['name'] ) {
            $name     = strtolower( CRM_Utils_Type::escape( $_GET['name'], 'String'  ) );
            $name     = str_replace( '*', '%', $name );
            $whereClause = " LOWER(title) LIKE '$name%' ";
            $getRecords = true;
        }
        
        if ( isset( $_GET['id'] ) && is_numeric($_GET['id']) ) {
            $eventId     = CRM_Utils_Type::escape( $_GET['id'], 'Integer'  );
            $whereClause = " id = {$eventId} ";
            $getRecords = true;
        }

        if ( $getRecords ) {
            $query = "
SELECT title, id
FROM civicrm_event
WHERE domain_id = $domainID
   AND {$whereClause}
ORDER BY title
";
            $nullArray = array( );
            $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );
            $elements = array( );
            while ( $dao->fetch( ) ) {
                $elements[] = array( 'name' => $dao->title,
                                     'value'=> $dao->id );
            }
        }
        
        if ( empty( $elements) ) { 
            $name = $_GET['name'];
            if ( !$name && isset( $_GET['id'] ) ) {
                $name = $_GET['id'];
            } 
            $elements[] = array( 'name' => trim( $name, '*'),
                                 'value'=> trim( $name, '*') );
        }
        
        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements, 'value');
    }

    /**
     * Function for building Event Type combo box
     */
    function eventType( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $domainID = CRM_Utils_Type::escape( $_GET['d'], 'Integer' );

        $getRecords = false;
        if ( isset( $_GET['name'] ) && $_GET['name'] ) {
            $name = strtolower( CRM_Utils_Type::escape( $_GET['name'], 'String'  ) );
            $name = str_replace( '*', '%', $name );
            $whereClause = " LOWER(v.label)  LIKE '$name%'  ";
            $getRecords = true;
        }
        
        if ( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ) {
            $eventTypeId     = CRM_Utils_Type::escape( $_GET['id'], 'Integer'  );
            $whereClause = " v.value = {$eventTypeId} ";
            $getRecords = true;
        }

        if ( $getRecords ) {
            
            $query ="
SELECT v.label ,v.value
FROM   civicrm_option_value v,
       civicrm_option_group g
WHERE  v.option_group_id = g.id 
AND g.name = 'event_type'
AND v.is_active = 1
AND {$whereClause}
ORDER by v.weight";

            $nullArray = array( );
            $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );
            
            $elements = array( );
            while ( $dao->fetch( ) ) {
                $elements[] = array( 'name'  => $dao->label, 
                                     'value' => $dao->value );
            }
        }
        
        if ( empty( $elements) ) { 
            $name = $_GET['name'];
            if ( !$name && isset( $_GET['id'] ) ) {
                $name = $_GET['id'];
            } 
            $elements[] = array( 'name' => trim( $name, '*'),
                                 'value'=> trim( $name, '*') );
        }
        
        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements,'value' );
    }

    /**
     * Function for building Custom Data Type
     */
    function customDataType( &$config ) 
    {
        static $dataType = null;

        if (! $dataType) { 
            require_once 'CRM/Core/BAO/CustomField.php';
            $dataType = array_values(CRM_Core_BAO_CustomField::dataType());
        }

        $dataTypeName = trim(CRM_Utils_Type::escape($_GET['name'], 'String'));      
        $dataTypeName = str_replace(array('*', '/'), array('', '\/'), $dataTypeName);        
        $pattern = '/^' . $dataTypeName .'/i';

        $elements = array( );
        if ( is_array($dataType) ) {
            foreach ( $dataType as $key => $val ) {
                if ( preg_match($pattern, $val) ) {
                    $elements[]= array( 'name'  => $val, 
                                        'value' => $key );
                }
            }
        }

        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements, 'value' );
    }

    /**
     * Function for building Custom Input Type
     */
    function customInputType( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $inputTypeId    = CRM_Utils_Type::escape($_GET['node1'], 'String');

        $name = array();

        // simulating - dynamic building of array.
        switch ( $inputTypeId ) {
        case '0': 
            $name['Text']         = 'Text';
            $name['Select']       = 'Select';
            $name['Radio']        = 'Radio';
            $name['CheckBox']     = 'CheckBox';
            $name['Multi-Select'] = 'Multi-Select';
            break;
        case '1': 
        case '2': 
        case '3': 
            $name['Text']         = 'Text';
            $name['Select']       = 'Select';
            $name['Radio']        = 'Radio';
            break;
        case '4':
            $name['TextArea']     = 'TextArea';
            $name['RichTextEditor']  = 'Rich Text Editor';
            break;
        case '5':
            $name['Date']         = 'Select Date';
            break;
        case '6':
            $name['Radio']        = 'Radio';
            break;
        case '7':
            $name['StateProvince'] = 'Select State/Province';
            break;
        case '8':
            $name['Country']      = 'Select Country';
            $name['Multi-Select'] = 'Multi-Select Country';
            break;
        case '9':
            $name['File']         = 'Select File';
            break;
        case '10':
            $name['Link']         = 'Link';
            break;
        }

        $inputTypeName = trim(CRM_Utils_Type::escape($_GET['name'], 'String'));        
        $inputTypeName = str_replace( '*', '', $inputTypeName );        
        $pattern = '/^' . $inputTypeName .'/i';

        $elements = array( );
        if ( is_array($name) ) {
            foreach ( $name as $key => $val ) {
                if ( preg_match($pattern, $val) ) {
                    $elements[]= array( 'name'  => $val, 
                                        'value' => $key );
                }
            }
        }
        if (empty($elements)) {
            $elements[] = array( 'value' => '',
                                 'name'  => '- input field type n/a -' );
        }

        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements, 'value' );
    }

    /**
     * Function to show import status
     */
    function status( &$config ) 
    {
        // make sure we get an id
        if ( ! isset( $_GET['id'] ) ) {
            return;
        }

        $file = "{$config->uploadDir}status_{$_GET['id']}.txt";
        if ( file_exists( $file ) ) {
            $str = file_get_contents( $file );
            echo $str;
        } else {
            require_once 'Services/JSON.php';
            $json =& new Services_JSON( );
            $status = "<div class='description'>&nbsp; " . ts('No processing status reported yet.') . "</div>";
            echo $json->encode( array( 0, $status ) );
        }
    }

    /**
     * Function to build state province combo box
     */
    function state( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $countryName  = $stateName = null;
        $elements = array( );
        $countryClause = " 1 ";
        if ( isset( $_GET['node'] ) ) {
            $countryId     = CRM_Utils_Type::escape( $_GET['node'], 'String');
            $countryClause = " civicrm_state_province.country_id = {$countryId}";
        } 

        if ( isset( $_GET['name'] ) ) {
            $stateName    = trim (CRM_Utils_Type::escape( $_GET['name']   , 'String') );
        }

        if ( isset( $_GET['id'] ) ) {
            $stateId = CRM_Utils_Type::escape( $_GET['id'], 'Positive', false  );
        }
        
        $validValue = true;
        if ( !$stateName && !$stateId ) {
            $validValue = false;
        }

        if ( $validValue ) {
            $stateClause = " 1 ";
            if ( !$stateId ) {
                $stateName = str_replace( '*', '%', $stateName );        
                $stateClause = " civicrm_state_province.name LIKE LOWER('$stateName%') ";
            } else {
                $stateClause = " civicrm_state_province.id = {$stateId} ";
            }
            
            $query = "
SELECT civicrm_state_province.name name, civicrm_state_province.id id
  FROM civicrm_state_province
WHERE {$countryClause}
    AND {$stateClause}
ORDER BY name";

            $nullArray = array( );
            $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );
            
            $count = 0;
            
            while ( $dao->fetch( ) && $count < 5 ) {
                $elements[] = array( 'name'  => ts($dao->name),
                                     'value' => $dao->id );
                $count++;
            }
        }

        if ( empty( $elements) ) {
            if ( !$stateName && isset( $_GET['id'] )) {
                if ( $stateId ) {
                    $stateProvinces  = CRM_Core_PseudoConstant::stateProvince( false, false );
                    $stateName =  $stateProvinces[$stateId];
                    $stateValue = $stateId;
                } else {
                    $stateName = $stateValue = $_GET['id'];
                }
            } else if ( !is_numeric( $stateName ) )  {
                $stateValue = $stateName;
            }
            
            $elements[] = array( 'name'  => trim($stateName, "%"),
                                 'value' => trim($stateValue, "%") 
                                 );
        }

        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements, 'value');
    }

    /**
     * Function to build country combo box
     */
    function country( &$config ) 
    {
        //get the country limit and restrict the combo select options
        $limitCodes = $config->countryLimit( );
        if ( ! is_array( $limitCodes ) ) {
            $limitCodes = array( $config->countryLimit => 1);
        }
        
        $limitCodes = array_intersect( CRM_Core_PseudoConstant::countryIsoCode(), $limitCodes);
        if ( count($limitCodes) ) {
            $whereClause = " iso_code IN ('" . implode("', '", $limitCodes) . "')";
        } else {
            $whereClause = " 1";
        }

        $elements = array( );
        require_once 'CRM/Utils/Type.php';
        $name      = CRM_Utils_Type::escape( $_GET['name'], 'String'  );

        if ( isset( $_GET['id'] ) ) {
            $countryId = CRM_Utils_Type::escape( $_GET['id'], 'Positive', false );
        }

        //temporary fix to handle locales other than default US,
        // CRM-2653
        if ( !$countryId && $name && $config->lcMessages != 'en_US') {
            $countries = CRM_Core_PseudoConstant::country();
            
            // get the country name in en_US, since db has this locale
            $countryName = array_search( $name, $countries );
            
            if ( $countryName ) {
                $countryId = $countryName;
            }
        }

        $validValue = true;
        if ( !$name && !$countryId ) {
            $validValue = false;
        }

        if ( $validValue ) {
            if ( !$countryId ) {
                $name = str_replace( '*', '%', $name );
                $countryClause = " civicrm_country.name LIKE LOWER('$name%') ";
            } else {
                $countryClause = " civicrm_country.id = {$countryId} ";
            }
            
            $query = "
SELECT id, name
  FROM civicrm_country
 WHERE {$countryClause}
   AND {$whereClause} 
ORDER BY name";

            $nullArray = array( );
            $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );
            
            $count = 0;
            while ( $dao->fetch( ) && $count < 5 ) {
                $elements[] = array( 'name'  => ts($dao->name),
                                     'value' => $dao->id );
                $count++;
            }
        }
        
        if ( empty( $elements) ) {
            if ( isset( $_GET['id'] ) ) {
                $name = $_GET['id'];
            }

            $elements[] = array( 'name'  => trim($name, "%"),
                                 'value' => trim($name, "%") 
                                 );
        }

        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements, 'value');
    }

    /**
     * Function for Case Subject combo box
     */
    function caseSubject( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $contactID = CRM_Utils_Type::escape( $_GET['c'], 'Integer' );

        $query = "
SELECT subject
FROM civicrm_case
WHERE contact_id = $contactID 
ORDER BY subject";
        $nullArray = array( );
        $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );
        $elements = array( );
       
        while ( $dao->fetch( ) ) {
            $elements[] = array('name' => $dao->subject);
        }

        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements, 'name');
    }

    /**
     * Function to fetch the template text/html messages
     */
    function template( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $templateId = CRM_Utils_Type::escape( $_GET['tid'], 'Integer' );

        require_once "CRM/Core/DAO/MessageTemplates.php";
        $messageTemplate =& new CRM_Core_DAO_MessageTemplates( );
        $messageTemplate->id = $templateId;
        $messageTemplate->selectAdd( );
        $messageTemplate->selectAdd( 'msg_text, msg_html, msg_subject' );
        $messageTemplate->find( true );
        
        echo $messageTemplate->msg_text . "^A" . $messageTemplate->msg_html . "^A" . $messageTemplate->msg_subject;
    }

    /**
     * Function to fetch the custom field help 
     */
    function customField( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $fieldId = CRM_Utils_Type::escape( $_GET['id'], 'Integer' );

        $helpPost = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField',
                                                 $fieldId,
                                                 'help_post' );
        echo $helpPost;
    }
}


