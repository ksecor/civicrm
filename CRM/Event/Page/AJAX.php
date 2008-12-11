
<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

/**
 * This class contains all the function that are called using AJAX (dojo)
 */
class CRM_Event_Page_AJAX
{
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
WHERE cg.name LIKE 'civicrm_event.amount%'
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

}
