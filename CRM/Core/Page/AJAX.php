<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

class CRM_Core_Page_AJAX extends CRM_Core_Page {

    function run( &$args ) {
        $this->invoke( $args );
        exit( );
    }

    // build the query
    function invoke( &$args ) {
        // intialize the system
        $config =& CRM_Core_Config::singleton( );

        if ( $args[0] != 'civicrm' && $args[1] != 'ajax' ) {
            exit( );
        }

        switch ( $args[2] ) {

        case 'help':
            return $this->help( $config );

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

        case 'message':
            return $this->message( $config );

        default:
            return;

        }

    }

    function help( &$config ) {
        $id   = urldecode( $_GET['id'] );
        $file = urldecode( $_GET['file'] );

        $template =& CRM_Core_Smarty::singleton( );
        $file = str_replace( '.tpl', '.hlp', $file );

        $template->assign( 'id', $id );
        echo $template->fetch( $file );
    }

    function search( &$config ) {
        require_once 'CRM/Utils/Type.php';
        $domainID  = CRM_Utils_Type::escape( $_GET['d'], 'Integer' );
        $name      = strtolower( CRM_Utils_Type::escape( $_GET['s'], 'String'  ) );
        
        if ( $_GET['sh'] ) {
            $shared    = CRM_Utils_Type::escape( $_GET['sh'], 'Integer');
        }

        if ( $shared ) {
            $query = "
SELECT CONCAT_WS( ', ', household_name, LEFT( street_address, 25 ) , city ) 'sort_name', 
civicrm_household.contact_id 'id'
FROM civicrm_household
LEFT JOIN civicrm_location ON civicrm_location.entity_id=civicrm_household.contact_id 
AND civicrm_location.is_primary=1 
AND civicrm_location.entity_table='civicrm_contact'
LEFT JOIN civicrm_address ON civicrm_address.location_id=civicrm_location.id
where household_name LIKE '$name%'
ORDER BY household_name LIMIT 6";
        } else {
            $query = "
SELECT sort_name, id
FROM civicrm_contact
WHERE sort_name LIKE '$name%'
AND domain_id = $domainID
ORDER BY sort_name LIMIT 6";
        }

        $nullArray = array( );
        $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );

        $count = 0;
        $elements = array( );
        while ( $dao->fetch( ) && $count < 5 ) {
            $elements[] = array( $dao->sort_name, $dao->id );
            $count++;
        }
        
        require_once 'Services/JSON.php';
        $json =& new Services_JSON( );
        echo $json->encode( $elements );
    }

    function event( &$config ) {
        require_once 'CRM/Utils/Type.php';
        $domainID = CRM_Utils_Type::escape( $_GET['d'], 'Integer' );
        $name     = strtolower( CRM_Utils_Type::escape( $_GET['s'], 'String'  ) );

        $query = "
SELECT title
  FROM civicrm_event
 WHERE domain_id = $domainID
   AND title LIKE '$name%'
ORDER BY title
LIMIT 6";

        $nullArray = array( );
        $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );

        $count = 0;
        $elements = array( );
        while ( $dao->fetch( ) && $count < 5 ) {
            $elements[] = array( $dao->title, $dao->title );
            $count++;
        }

        require_once 'Services/JSON.php';
        $json =& new Services_JSON( );
        echo $json->encode( $elements );
    }

    function eventType( &$config ) {
        require_once 'CRM/Utils/Type.php';
        $domainID = CRM_Utils_Type::escape( $_GET['d'], 'Integer' );
        $name     = strtolower( CRM_Utils_Type::escape( $_GET['s'], 'String'  ) );

        $query ="
SELECT label 
FROM   civicrm_option_value v,
       civicrm_option_group g
WHERE  v.option_group_id = g.id
AND  g.name = 'event_type'";

        $nullArray = array( );
        $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );

        $count = 0;
        $elements = array( );
        while ( $dao->fetch( ) && $count < 5 ) {
            $elements[] = array( $dao->label, $dao->label );
            $count++;
        }

        require_once 'Services/JSON.php';
        $json =& new Services_JSON( );
        echo $json->encode( $elements );
    }

    function status( &$config ) {
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

    function message( &$config ) {
        require_once 'CRM/Utils/Type.php';
        $domainID = CRM_Utils_Type::escape( $_GET['d'], 'Integer' );

        $query = "
SELECT id, msg_title,msg_text,msg_subject
  FROM civicrm_msg_template
 WHERE domain_id = $domainID
 AND is_active =1 
ORDER BY msg_title
LIMIT 6";

        $nullArray = array( );
        $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );

        $count = 0;
        $elements = array( );
        while ( $dao->fetch( ) && $count < 5 ) {
            $elements[] = array( $dao->msg_title,
                                 $dao->msg_text . "^A" . $dao->msg_subject,
                                 $dao->id );
            $count++;
        }
        require_once 'Services/JSON.php';
        $json =& new Services_JSON( );
        echo $json->encode( $elements );
    }

    function state( &$config ) {
        require_once 'CRM/Utils/Type.php';
        $countryName  = strtolower( CRM_Utils_Type::escape( $_GET['node'], 'String'  ) );
        $stateName    = strtolower( CRM_Utils_Type::escape( $_GET['s'], 'String'  ) );
        $includeState = strtolower( CRM_Utils_Type::escape( $_GET['sc'], 'String'  ) );

        $query = "
SELECT civicrm_state_province.name name
  FROM civicrm_state_province, civicrm_country
 WHERE civicrm_state_province.country_id = civicrm_country.id
  AND  civicrm_country.name LIKE '$countryName%'";

        if ( $includeState ) {
            $query .= " AND  civicrm_state_province.name LIKE '$stateName%' ";
        }

        $query .= " ORDER BY name";

        $nullArray = array( );
        $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );

        $count = 0;
        $elements = array( );
        while ( $dao->fetch( ) && $count < 5 ) {
            $elements[] = array( $dao->name, $dao->name );
            $count++;
        }

        require_once 'Services/JSON.php';
        $json =& new Services_JSON( );
        echo $json->encode( $elements );
    }

    function country( &$config ) {
        require_once 'CRM/Utils/Type.php';
        $name     = strtolower( CRM_Utils_Type::escape( $_GET['s'], 'String'  ) );

        $query = "
SELECT civicrm_country.name name
  FROM civicrm_country
 WHERE civicrm_country.name LIKE '$name%'
ORDER BY name";

        $nullArray = array( );
        $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );

        $count = 0;
        $elements = array( );
        while ( $dao->fetch( ) && $count < 5 ) {
            $elements[] = array( $dao->name, $dao->name );
            $count++;
        }

        require_once 'Services/JSON.php';
        $json =& new Services_JSON( );
        echo $json->encode( $elements );
    }

}

?>