<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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

    static function &getTaskStatus( $sourceID, $targetID, $taskID ) {
        require_once 'CRM/Project/DAO/TaskStatus.php';
        $dao =& new CRM_Project_DAO_TaskStatus( );
        $dao->responsible_entity_table = 'civicrm_contact';
        $dao->responsible_entity_id    = $sourceID;
        $dao->target_entity_table      = 'civicrm_contact';
        $dao->target_entity_id         = $targetID;
        $dao->task_id                  = $taskID;
        
        if ( $dao->find( true ) ) {
            require_once 'CRM/Core/OptionGroup.php';
            $status =& CRM_Core_OptionGroup::values( 'task_status' );
            return $status[$dao->status_id];
        }

        return ts( 'Not Started' );
    }

    static function getRecommendationStatus( $sourceID ) {

        $query = "
SELECT cr.id           as contact_id,
       cr.display_name as display_name,
       ts.status_id    as status_id
  FROM civicrm_contact      cs,
       civicrm_contact      cr,
       civicrm_relationship rs,
       civicrm_task_status  ts
 WHERE rs.relationship_type_id IN ( 9, 10 )
   AND rs.contact_id_a = cs.id
   AND rs.contact_id_b = cr.id
   AND rs.is_active    = 1
   AND cs.id           = $sourceID
   AND ts.task_id      = 10
   AND ts.responsible_entity_table = 'civicrm_contact'
   AND ts.responsible_entity_id    = cr.id
   AND ts.target_entity_table      = 'civicrm_contact'
   AND ts.target_entity_id         = $sourceID
";

        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        $params = array( );
        $count  = 0;
        while ( $dao->fetch( ) ) {
            $params[$count] = $dao->contact_id;
            $params[$count]['contact_id'     ] = $dao->contact_id;
            $params[$count]['display_name'   ] = $dao->display_name;
            $params[$count]['email'          ] = $dao->email;
            $params[$count]['status'         ] = $dao->status_id ? $status[$dao->status_id] : 'Not Started';
            $count++;
        }
    }

    static function getMatchAppURL( $sourceID, $action ) {
        require_once 'CRM/Utils/System.php';
        return CRM_Utils_System::url( 'civicrm/quest/matchapp', "reset=1&id=$sourceID&action=$action" );
    }

    static function getRecommendationURL( $sourceID, $targetID, $type, $action ) {
        require_once 'CRM/Utils/System.php';
        return CRM_Utils_System::url( "civicrm/quest/$type/recommendation", "reset=1&id=$sourceID&scid=$targetID&action=$action" );
    }

    static function &getTaskStatusInfo( $sourceID, $targetID, $taskID ) {
        self::initialize( );

        require_once 'CRM/Project/DAO/TaskStatus.php';
        $dao =& new CRM_Project_DAO_TaskStatus( );
        $dao->responsible_entity_table = 'civicrm_contact';
        $dao->responsible_entity_id    = $sourceID;
        $dao->target_entity_table      = 'civicrm_contact';
        $dao->target_entity_id         = $targetID;
        $dao->task_id                  = $taskID;
        
        if ( $dao->find( true ) ) {
            $result = array( );
            $result['status'] = ts( 'Not Started' );
            return $result;
        }

        require_once 'CRM/Core/OptionGroup.php';
        $status =& CRM_Core_OptionGroup::values( 'task_status' );
        
        $result = array( );
        $result['status'       ] = $status[$dao->status_id];
        $result['create_date'  ] = $dao->create_date;
        $result['modified_date'] = $dao->modified_date;
        $result['link'         ] = CRM_Utils_System::url( 'civicrm/quest/matchapp', "reset=1&id=$sourceID" );
        return $result;
    }

    static function getRecommenderStudentInfo( $sourceID ) {

        $query = "
SELECT cr.id           as contact_id,
       cr.display_name as display_name,
       ts.status_id    as status_id
  FROM civicrm_contact      cs,
       civicrm_contact      cr,
       civicrm_relationship rs,
       civicrm_task_status  ts
 WHERE rs.relationship_type_id IN ( 9, 10 )
   AND rs.contact_id_a = cs.id
   AND rs.contact_id_b = cr.id
   AND rs.is_active    = 1
   AND cr.id           = $sourceID
   AND ts.task_id      = 10
   AND ts.responsible_entity_table = 'civicrm_contact'
   AND ts.responsible_entity_id    = $sourceID
   AND ts.target_entity_table      = 'civicrm_contact'
   AND ts.target_entity_id         = cs.id
";

        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        $params = array( );
        $count  = 0;
        while ( $dao->fetch( ) ) {
            $params[$count] = $dao->contact_id;
            $params[$count]['contact_id'     ] = $dao->contact_id;
            $params[$count]['display_name'   ] = $dao->display_name;
            $params[$count]['email'          ] = $dao->email;
            $params[$count]['status'         ] = $dao->status_id ? $status[$dao->status_id] : 'Not Started';
            $count++;
        }
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
                                   'email' => 1,
                                   'contact_sub_type' => 1
                                   );

        require_once 'api/Search.php';
        list( $result, $options ) = crm_contact_search( $params, $returnProperties );

        if ( ! empty( $result ) ) {
            return array_pop( $result );
        }
        return null;
    }

    static function getContactByHash( $h, $m, $email ) {
        require_once 'CRM/Contact/BAO/Contact.php';

        $email = trim( $email );

        // make sure email and the md5 are the same
        if ( $m != md5( $email ) ) {
            return false;
        }

        $dao =& CRM_Contact_BAO_Contact::matchContactOnEmail( $email );
        if ( ! $dao ) {
            return false;
        }

        if ( $dao->hash != $h ) {
            return false;
        }

        require_once 'CRM/Core/BAO/UFMatch.php';
        return CRM_Core_BAO_UFMatch::getUFId( $dao->contact_id );
    }

}

?>
