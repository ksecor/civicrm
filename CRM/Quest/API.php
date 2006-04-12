<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * The API that exposes quest data to other modules
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

class CRM_Quest_API {

    static protected $_initialized = false;

    static function initialize( ) {
        if ( ! self::$_initialized ) {
            require_once 'CRM/Core/Config.php';
            $config =& CRM_Core_Config::singleton();
        }
        return;
    }

    static function &getTaskStatus( $id ) {
        require_once 'CRM/Project/DAO/TaskStatus.php';
        $dao =& new CRM_Project_DAO_TaskStatus( );
        $dao->responsible_entity_table = 'civicrm_contact';
        $dao->responsible_entity_id    = $cid;
        $dao->task_id                  = 2;
        
        if ( $dao->find( true ) ) {
            return $dao;
        }
        return null;
    }

    static function getApplicationStatus( $id ) {
        self::initialize( );

        $task =& self::getTaskStatus( $id );
        if ( ! $task ) {
            return ts( 'Not Started' );
        }

        require_once 'CRM/Core/OptionGroup.php';
        $status =& CRM_Core_OptionGroup::values( 'task_status' );
        return $status[$task->status_id];
    }

    static function &getApplicationInfo( $id ) {
        self::initialize( );

        $task =& self::getTaskStatus( $id );
        if ( ! $task ) {
            $result = array( );
            $result['status'] = ts( 'Not Started' );
            return $result;
        }

        require_once 'CRM/Core/OptionGroup.php';
        $status =& CRM_Core_OptionGroup::values( 'task_status' );

        $result = array( );
        $result['status'       ] = $status[$task->status_id];
        $result['create_date'  ] = $task->create_date;
        $result['modified_date'] = $task->modified_date;
        $result['link'         ] = CRM_Utils_System::url( 'civicrm/quest/preapp', 'reset=1&redirect=1' );
        return $result;
    }

    static function getContactInfo( $id ) {
        self::initialize( );

        $params = array( 'contact_id' => $id );
        $returnProperties = array( 'first_name' => 1,
                                   'last_name'  => 1,
                                   'location_type_id' => 1,
                                   'street_address' => 1,
                                   'supplemental_address_1' => 1,
                                   'city' => 1,
                                   'postal_code' => 1,
                                   'state_province' => 1,
                                   'country' => 1,
                                   'phone' => 1,
                                   'email' => 1 );

        require_once 'api/Search.php';
        list( $result, $options ) = crm_contact_search( $params, $returnProperties );

        if ( ! empty( $result ) ) {
            return array_pop( $result );
        }
        return null;
    }

}

?>
