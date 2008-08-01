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

        case 'eventFee':
            return $this->eventFee( $config );
            
        case 'pledgeName':
            return $this->pledgeName( $config );
        
        case 'caseSubject':
             return $this->caseSubject( $config );

        case 'template':
            return $this->template( $config );

        case 'custom':
            return $this->customField( $config );

        case 'help':
            return $this->help( $config );

        case 'contact':
            return $this->contact( $config );

        case 'employer':
            return $this->getPermissionedEmployer( $config );

        case 'mapper':
            require_once 'CRM/Core/Page/AJAX/Mapper.php';
            $method = array( 'CRM_Core_Page_AJAX_Mapper',
                             $args[3] );

            if ( is_callable( $method ) ) {
                return eval( "return CRM_Core_Page_AJAX_Mapper::{$args[3]}( " . ' $config ); ' );
            }
            exit( );

        case 'groupTree':
            return $this->groupTree( $config );

        case 'permlocation':
            return $this->getPermissionedLocation( $config );

        case 'memType':
            return $this->getMemberTypeDefaults( $config );
            
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
        $name      = CRM_Utils_Array::value( 'name', $_GET, '' );
        $name      = CRM_Utils_Type::escape( $name, 'String' ); 
        $whereIdClause = '';
        if ( CRM_Utils_Array::value( 'id', $_GET ) ) {
            if ( is_numeric( $_GET['id'] ) ) {
                $id  = CRM_Utils_Type::escape( $_GET['id'], 'Integer' ) ; 
                $whereIdClause = " AND civicrm_contact.id = {$id}";
            } else {
                $name = $_GET['id'];
            }
        }

        $elements = array( );
        if ( $name || isset( $id ) ) {
            $name  = str_replace( '*', '%', $name );

            //contact's based of relationhip type
            $relType = null;
            if ( isset($_GET['rel']) ) {
                $relation = explode( '_', $_GET['rel'] );
                $relType  = CRM_Utils_Type::escape( $relation[0], 'Integer');
                $rel      = CRM_Utils_Type::escape( $relation[2], 'String');
            }

            //shared household info
            $shared = null;
            if ( isset($_GET['sh']) ) {
                $shared = CRM_Utils_Type::escape( $_GET['sh'], 'Integer');
                 if ( $shared == 1 ) {
                     $contactType = 'Household';
                     $cName = 'household_name';
                 } else {
                     $contactType = 'Organization';
                     $cName = 'organization_name';
                 }
            }

            // contacts of type household
            $hh = null;
            if ( isset($_GET['hh']) ) {
                $hh = CRM_Utils_Type::escape( $_GET['hh'], 'Integer');
            }
            
            //organization info
            $organization = null;
            if ( isset($_GET['org']) ) {
                $organization = CRM_Utils_Type::escape( $_GET['org'], 'Integer');
            }
            
            if ( isset($_GET['org']) || isset($_GET['hh']) ) {
                list( $contactName, $street, $city) = explode( ' :: ', $name );
                
                if ( $street ) {
                    $addStreet = "AND civicrm_address.street_address LIKE '$street%'";
                }
                if ( $city ) {
                    $addCity = "AND civicrm_address.city LIKE '$city%'";
                }
            }

            if ( $organization ) {
                
                $query = "
SELECT CONCAT_WS(' :: ',sort_name,LEFT(street_address,25),city) 'sort_name', 
civicrm_contact.id 'id'
FROM civicrm_contact
LEFT JOIN civicrm_address ON ( civicrm_contact.id = civicrm_address.contact_id
                                AND civicrm_address.is_primary=1
                             )
WHERE civicrm_contact.contact_type='Organization' AND organization_name LIKE '%$contactName%'
{$addStreet} {$addCity} {$whereIdClause}
ORDER BY organization_name ";

            } else if ( $shared ) {
                
                $query = "
SELECT CONCAT_WS(':::' , sort_name, supplemental_address_1, sp.abbreviation, postal_code, cc.name )'sort_name' , civicrm_contact.id 'id' , civicrm_contact.display_name 'disp' FROM civicrm_contact LEFT JOIN civicrm_address ON (civicrm_contact.id =civicrm_address.contact_id AND civicrm_address.is_primary =1 )LEFT JOIN civicrm_state_province sp ON (civicrm_address.state_province_id =sp.id )LEFT JOIN civicrm_country cc ON (civicrm_address.country_id =cc.id )WHERE civicrm_contact.contact_type ='{$contactType}' AND {$cName} LIKE '%$name%' {$whereIdClause} ORDER BY {$cName} ";

            } else if ( $hh ) {
                
                $query = "
SELECT CONCAT_WS(' :: ' , sort_name, LEFT(street_address, 25), city ) 'sort_name' , civicrm_contact.id 'id' FROM civicrm_contact LEFT JOIN civicrm_address ON (civicrm_contact.id =civicrm_address.contact_id AND civicrm_address.is_primary =1 )
WHERE civicrm_contact.contact_type ='Household' AND household_name LIKE '%$contactName%' {$addStreet} {$addCity} {$whereIdClause} ORDER BY household_name ";
                
            } else if ( $relType ) {
                
                $query = "
SELECT c.sort_name, c.id
FROM civicrm_contact c, civicrm_relationship_type r
WHERE c.sort_name LIKE '%$name%'
AND r.id = $relType
AND c.contact_type = r.contact_type_{$rel} {$whereIdClause} 
ORDER BY sort_name" ;
            
            } else {
                
                $query = "
SELECT sort_name, id
FROM civicrm_contact
WHERE sort_name LIKE '%$name'
{$whereIdClause}
ORDER BY sort_name ";            
        }

            $start = 0;
            $end   = 10;
            
            if ( isset( $_GET['start'] ) ) {
                $start = CRM_Utils_Type::escape( $_GET['start'], 'Integer' );
            }
            
            if ( isset( $_GET['count'] ) ) {
                $end   = CRM_Utils_Type::escape( $_GET['count'], 'Integer' );
            }
            
            $query .= " LIMIT {$start},{$end}";
            
            $dao = CRM_Core_DAO::executeQuery( $query );
            
            if ( $shared ) {
                while ( $dao->fetch( ) ) {
                    echo $dao->sort_name;
                    exit();
                }
            } else {  
                while ( $dao->fetch( ) ) {
                    $elements[] = array( 'name' => $dao->sort_name,
                                         'id'   => $dao->id );
                }
            }
        }

        if ( isset($_GET['sh']) ) {
            echo "";
            exit();
        }

        if ( empty( $elements ) ) {
            $name = str_replace( '%', '', $name );
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
        
        $getRecords = false;
        if ( isset( $_GET['name'] ) && $_GET['name'] ) {
            $name     = CRM_Utils_Type::escape( $_GET['name'], 'String' );
            $name     = str_replace( '*', '%', $name );
            $whereClause = " title LIKE '$name%' ";
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
WHERE {$whereClause}
ORDER BY title
";
            $dao = CRM_Core_DAO::executeQuery( $query );
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

        $getRecords = false;
        if ( isset( $_GET['name'] ) && $_GET['name'] ) {
            $name = CRM_Utils_Type::escape( $_GET['name'], 'String' );
            $name = str_replace( '*', '%', $name );
            $whereClause = " v.label LIKE '$name%'  ";
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

            $dao = CRM_Core_DAO::executeQuery( $query );
            
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
     * Function for building EventFee combo box
     */
    function eventFee( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        
        $getRecords = false;
        if ( isset( $_GET['name'] ) && $_GET['name'] ) {
            $name     = CRM_Utils_Type::escape( $_GET['name'], 'String' );
            $name     = str_replace( '*', '%', $name );
            $whereClause = "cv.label LIKE '$name%' ";
            $getRecords = true;
        }
        
        if ( isset( $_GET['id'] ) && is_numeric($_GET['id']) ) {
            $levelId     = CRM_Utils_Type::escape( $_GET['id'], 'Integer'  );
            $whereClause = "cv.id = {$levelId} ";
            $getRecords = true;
        }
        
        if ( $getRecords ) {
            $query = "
SELECT distinct(cv.label), cv.id
FROM civicrm_option_value cv, civicrm_option_group cg
WHERE cg.name LIKE 'civicrm_event_page.amount%'
   AND cg.id = cv.option_group_id AND {$whereClause}
   GROUP BY cv.label
";
            $dao = CRM_Core_DAO::executeQuery( $query );
            $elements = array( );
            while ( $dao->fetch( ) ) {
                $elements[] = array( 'name' => $dao->label,
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
     * Function for building Pledge Name combo box
     */
    function pledgeName( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        
        $getRecords = false;
        if ( isset( $_GET['name'] ) && $_GET['name'] ) {
            $name     = CRM_Utils_Type::escape( $_GET['name'], 'String' );
            $name     = str_replace( '*', '%', $name );
            $whereClause = "p.creator_pledge_desc LIKE '%$name%' ";
            $getRecords = true;
        }
        
        if ( isset( $_GET['id'] ) && is_numeric($_GET['id']) ) {
            $pledgeId    = CRM_Utils_Type::escape( $_GET['id'], 'Integer'  );
            $whereClause = "p.id = {$pledgeId} ";
            $getRecords = true;
        }
        
        if ( $getRecords ) {
            $query = "
SELECT p.creator_pledge_desc, p.id
FROM civicrm_pb_pledge p
WHERE {$whereClause}
";
            $dao = CRM_Core_DAO::executeQuery( $query );
            $elements = array( );
            while ( $dao->fetch( ) ) {
                $elements[] = array( 'name' => $dao->creator_pledge_desc,
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

        $stateId = null;
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
                $stateClause = " civicrm_state_province.name LIKE '$stateName%' ";
            } else {
                $stateClause = " civicrm_state_province.id = {$stateId} ";
            }
            
            $query = "
SELECT civicrm_state_province.name name, civicrm_state_province.id id
  FROM civicrm_state_province
WHERE {$countryClause}
    AND {$stateClause}
ORDER BY name";

            $dao = CRM_Core_DAO::executeQuery( $query );
            
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
        $name      = CRM_Utils_Array::value( 'name', $_GET, '' );
        $name      = CRM_Utils_Type::escape( $name, 'String'  );

        $countryId = null;
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
                $countryClause = " civicrm_country.name LIKE '$name%' ";
            } else {
                $countryClause = " civicrm_country.id = {$countryId} ";
            }
            
            $query = "
SELECT id, name
  FROM civicrm_country
 WHERE {$countryClause}
   AND {$whereClause} 
ORDER BY name";

            $dao = CRM_Core_DAO::executeQuery( $query );
            
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
        $whereclause = $caseIdClause = null; 
        if ( isset( $_GET['name'] ) ) {
            $name        = CRM_Utils_Type::escape( $_GET['name'], 'String'  ) ;
            $name        = str_replace( '*', '%', $name );
            $whereclause = "AND civicrm_case.subject LIKE '%$name'";
        }
        
        if ( isset( $_GET['id'] ) ) {
            $caseId = CRM_Utils_Type::escape( $_GET['id'], 'Integer' );
            $caseIdClause = " AND civicrm_case.id = {$caseId}";
        }
        
        $elements = array( );
        if ( $name || $caseIdClause ) {
            $contactID = CRM_Utils_Type::escape( $_GET['c'], 'Integer' );
            
            $query = "
SELECT civicrm_case.subject as subject, civicrm_case.id as id
FROM civicrm_case
LEFT JOIN civicrm_case_contact ON civicrm_case_contact.case_id = civicrm_case.id
WHERE civicrm_case_contact.contact_id = $contactID  {$whereclause} {$caseIdClause}
ORDER BY subject";
            
            $dao = CRM_Core_DAO::executeQuery( $query );
            
            while ( $dao->fetch( ) ) {
                $elements[] = array( 'name' => $dao->subject,
                                     'id'   => $dao->id
                                     );
            }
        }

        if ( empty( $elements ) ) {
            $name = str_replace( '%', '', $name );
            $elements[] = array( 'name' => $name,
                                 'id'=> $name);
        }


        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements );
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

    /**
     * Function to check how many contact exits in db for given criteria, if one then return contact id else null
     */
    function contact( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $name      = CRM_Utils_Type::escape( $_GET['name'], 'String' ); 

        $query = "
SELECT id
FROM civicrm_contact
WHERE sort_name LIKE '%$name%'";
        
        $dao = CRM_Core_DAO::executeQuery( $query );
        $dao->fetch( );
        
        if ( $dao->N == 1) {
            echo $dao->id;
        }
    }

    /**
     * Function to obtain list of permissioned employer for the given contact-id.
     */
    function getPermissionedEmployer( &$config ) 
    {
        $cid       = CRM_Utils_Type::escape( $_GET['cid'], 'Integer' );
        $name      = trim(CRM_Utils_Type::escape( $_GET['name'], 'String')); 
        $name      = str_replace( '*', '%', $name );

        require_once 'CRM/Contact/BAO/Relationship.php';
        $elements = CRM_Contact_BAO_Relationship::getPermissionedEmployer( $cid, $name );

        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements, 'value');
    }

    /**
     * Function to obtain the location of given contact-id. 
     * This method is used by on-behalf-of form to dynamically generate poulate the 
     * location field values for selected permissioned contact. 
     */
    function getPermissionedLocation( &$config ) 
    {
        $cid = CRM_Utils_Type::escape( $_GET['cid'], 'Integer' );
        
        require_once 'CRM/Core/BAO/Location.php';
        $entityBlock = array( 'contact_id' => $cid );
        $loc =& CRM_Core_BAO_Location::getValues( $entityBlock, $location );

        $str  = "location_1_phone_1_phone::" . $location['location'][1]['phone'][1]['phone'] . ';;';
        $str .= "location_1_email_1_email::". $location['location'][1]['email'][1]['email'] . ';;';

        $addressSequence = array_flip($config->addressSequence());

        if ( array_key_exists( 'street_address', $addressSequence) ) {
            $str .= "location_1_address_street_address::" . $location['location'][1]['address']['street_address'] . ';;';
        }
        if ( array_key_exists( 'supplemental_address_1', $addressSequence) ) {
            $str .= "location_1_address_supplemental_address_1::" . $location['location'][1]['address']['supplemental_address_1'] . ';;';
        }
        if ( array_key_exists( 'supplemental_address_2', $addressSequence) ) {
            $str .= "location_1_address_supplemental_address_2::" . $location['location'][1]['address']['supplemental_address_2'] . ';;';
        }
        if ( array_key_exists( 'city', $addressSequence) ) {
            $str .= "location_1_address_city::" . $location['location'][1]['address']['city'] . ';;';
        }
        if ( array_key_exists( 'postal_code', $addressSequence) ) {
            $str .= "location_1_address_postal_code::" . $location['location'][1]['address']['postal_code'] . ';;';
            $str .= "location_1_address_postal_code_suffix::" . $location['location'][1]['address']['postal_code_suffix'] . ';;';
        }
        if ( array_key_exists( 'country', $addressSequence) || array_key_exists( 'state_province', $addressSequence) ) {
            $str .= "id_location[1][address][country_state]_0::" . $location['location'][1]['address']['country_id'] . '-' . $location['location'][1]['address']['state_province_id'] . ';;';

        }
        echo $str;
    }

    function groupTree( $config ) 
    {
        require_once 'CRM/Contact/BAO/GroupNestingCache.php';
        echo CRM_Contact_BAO_GroupNestingCache::json( );
    }

    /**
     * Function to setDefaults according to membership type
     */
    function getMemberTypeDefaults( $config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $memType  = CRM_Utils_Type::escape( $_GET['mtype'], 'Integer') ; 
        
        $contributionType = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType', 
                                                         $memType, 
                                                         'contribution_type_id' );
        
        $totalAmount = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipType', 
                                                    $memType, 
                                                    'minimum_fee' );

        echo $contributionType . "^A" . $totalAmount;
    }
}
