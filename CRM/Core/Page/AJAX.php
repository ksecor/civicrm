<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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

        case 'caseSubject':
             return $this->caseSubject( $config );

        case 'template':
            return $this->template( $config );

        case 'custom':
            return $this->customField( $config );

        case 'help':
            return $this->help( $config );

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
        $name      = str_replace( '*', '%', $name );
        
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
       

        if ( $shared ) {
            
            $query = "
SELECT CONCAT_WS( ':::', household_name, LEFT( street_address, 25 ) , city ) 'sort_name', 
civicrm_contact.id 'id'
FROM civicrm_contact
LEFT JOIN civicrm_address ON ( civicrm_contact.id = civicrm_address.contact_id
                                AND civicrm_address.is_primary=1
                                 )
WHERE civicrm_contact.contact_type='Household' AND household_name LIKE '$name%'
ORDER BY household_name ";

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
WHERE sort_name LIKE '$name'
AND domain_id = $domainID
ORDER BY sort_name ";
            
        }
        
        $nullArray = array( );
        $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );

        $count = 0;
        $elements = array( );
        while ( $dao->fetch( ) && $count < 5 ) {
        //while ( $dao->fetch( ) ) {
            $elements[] = array( 'name' => $dao->sort_name,
                                 'id'   => $dao->id );
            $count++;
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
        $name     = strtolower( CRM_Utils_Type::escape( $_GET['name'], 'String'  ) );
        $name     = str_replace( '*', '%', $name );

        $query = "
SELECT title
  FROM civicrm_event
 WHERE domain_id = $domainID
   AND title LIKE '$name%'
ORDER BY title
";

        $nullArray = array( );
        $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );

        $elements = array( );
        while ( $dao->fetch( ) ) {
            $elements[] = array( 'name' => $dao->title,
                                 'title'=> $dao->title );
        }
        
        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements, 'title');
    }

    /**
     * Function for building Event Type combo box
     */
    function eventType( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $domainID = CRM_Utils_Type::escape( $_GET['d'], 'Integer' );
        $name     = strtolower( CRM_Utils_Type::escape( $_GET['name'], 'String'  ) );
        $name     = str_replace( '*', '%', $name );


        $query ="
SELECT v.label 
FROM   civicrm_option_value v,
       civicrm_option_group g
WHERE  v.option_group_id = g.id 
AND g.name = 'event_type'
AND v.is_active = 1
AND v.label  LIKE '$name%' 
ORDER by v.weight";

        $nullArray = array( );
        $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );
       
        $elements = array( );
        while ( $dao->fetch( ) ) {
            $elements[] = array( 'name'  => $dao->label, 
                                 'label' => $dao->label );
        }

        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements, 'label');
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
            $elements[] = array( 'name'  => trim($name, "%"),
                                 'value' => trim($name, "%") 
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
        //$name      = CRM_Utils_Type::escape( $_GET['s'], 'String'  );

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

?>
