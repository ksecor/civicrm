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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

/** 
 * this file contains functions to manage / process the recommendations
 * for a given student
 */


require_once 'CRM/Quest/DAO/Student.php';

class CRM_Quest_BAO_Recommendation {
    const
        TEACHER   = 1,
        COUNSELOR = 2;

    /**
     * class constructor
     */
    function __construct( ) {
    }

    static function cleanup( $contactID, $recommenderID, $schoolID, $rsTypeID, $rcTypeID,
                             $firstName, $lastName, $email ) {
        // remove the relationships 
        self::deactivateRelationship( $rsTypeID, $contactID, $recommenderID ); 
        self::deactivateRelationship( $rcTypeID, $recommenderID, $schoolID  );

        self::cancelTaskStatus( 10, $recommenderID, $contactID );

        $displayName = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                    $contactID,
                                                    'display_name' );

        // send email to the recommender
        $params = array( 'recommenderFirstName' => $firstName,
                         'recommenderLastName'  => $lastName,
                         'recommenderEmail'     => $email,
                         'studentName'          => $displayName,
                         'schoolName'           => CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                                                $schoolID,
                                                                                'display_name' ) );
        
        $template =& CRM_Core_Smarty::singleton( );
        $template->assign( $params );
        $message = $template->fetch( 'CRM/Quest/Page/MatchApp/Recommendation/RecommenderRemove.tpl' );

        // send the mail
        require_once 'CRM/Utils/Mail.php';
        CRM_Utils_Mail::send( '"QuestBridge" <questbridge@questbridge.org>',
                              "$firstName $lastName",
                              $email,
                              "Online Recommendation for {$displayName} Cancelled",
                              $message,
                              'recommendations@questbridge.org' );
        return true;
    }

    static function process( $contactID, $firstName, $lastName, $email, $schoolID, $type ) {
        list( $recommenderID, $hash, $drupalID, $alreadyExists ) = self::createContact( $firstName, $lastName, $email );

        $ids = array( $recommenderID );

        // add person to recommender group(id = 4)
        CRM_Contact_BAO_GroupContact::addContactsToGroup( $ids, 4 );

        // add person to either teacher or counselor group(id = 4)
        $groupID = ( $type == self::TEACHER ) ? 5 : 6;
        CRM_Contact_BAO_GroupContact::addContactsToGroup( $ids, $groupID );

        // add relationships
        $rtypeID = ( $type == self::TEACHER ) ? 9 : 10;
        self::createRelationship( $rtypeID, $contactID, $recommenderID );

        $rtypeID = ( $type == self::TEACHER ) ? 11 : 12;
        self::createRelationship( $rtypeID, $recommenderID, $schoolID );

        self::createTaskStatus( 10, $recommenderID, $contactID, 326 );

        $verify = quest_drupal_is_user_verified( $drupalID );

        if ( ! $verify ) {
            $md5Email = md5( $email );
            $url = CRM_Utils_System::url( 'civicrm/quest/verify',
                                          "reset=1&h={$hash}&m={$md5Email}",
                                          true, null, false );
        } else {
            $url = CRM_Utils_System::url( 'user/login' );
        }

        $displayName = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                    $contactID,
                                                    'display_name' );

        // send email to the recommender
        $params = array( 'recommenderFirstName' => $firstName,
                         'recommenderLastName'  => $lastName,
                         'recommenderEmail'     => $email,
                         'recommenderURL'       => $url,
                         'studentName'          => $displayName,
                         'schoolName'           => CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                                                $schoolID,
                                                                                'display_name' ) );
        
        $template =& CRM_Core_Smarty::singleton( );
        $template->assign( $params );
        if ( ! $verify ) {
            $message = $template->fetch( 'CRM/Quest/Page/MatchApp/Recommendation/RecommenderFirst.tpl' );
        } else {
            $message = $template->fetch( 'CRM/Quest/Page/MatchApp/Recommendation/RecommenderRepeat.tpl' );
        }

        // send the mail
        require_once 'CRM/Utils/Mail.php';
        CRM_Utils_Mail::send( '"QuestBridge" <questbridge@questbridge.org>',
                              "$firstName $lastName",
                              $email,
                              "Online Recommendation for {$displayName}",
                              $message,
                              'recommendations@questbridge.org' );
        return true;
    }

    static function createContact( $firstName, $lastName, $email ) {
        // check if contact exists, if so return that contact IDo
        require_once 'CRM/Contact/BAO/Contact.php';
        require_once 'CRM/Core/BAO/UFMatch.php';
        
        $dao =& CRM_Contact_BAO_Contact::matchContactOnEmail( $email );
        if ( $dao ) {
            return array( $dao->contact_id,
                          $dao->hash, 
                          CRM_Core_BAO_UFMatch::getUFId( $dao->contact_id ),
                          true
                          );
        }

        // create the contact
        require_once 'CRM/Core/BAO/LocationType.php';
        $locationType   =& CRM_Core_BAO_LocationType::getDefault( );  
        $hash           =  md5( uniqid( rand( ), true ) );

        $params= array( 'first_name'       => $firstName,
                        'last_name'        => $lastName,
                        'email'            => $email,
                        'hash'             => $hash,
                        'contact_sub_type' => 'Recommender',
                        'location_type'    => $locationType->name );
        $contact =& crm_create_contact( $params, 'Individual' );
        if ( is_a( $contact, 'CRM_Core_Error' ) ) {
            CRM_Core_Error::fatal( ts( 'Could not create contact' ) );
        }

        // also create a drupal user
        $drupalID = quest_drupal_create_user( $email );
        if ( ! $drupalID ) {
            CRM_Core_Error::fatal( ts( 'Could not create drupal user' ) );
        }

        self::createUFMatch( $contact->id, $drupalID, $email );

        return array( $contact->id, $hash, $drupalID, false );
    }

    static function createUFMatch( $contactID, $drupalID, $mail ) {
        $dao =& new CRM_Core_DAO_UFMatch( );
        $dao->uf_id      = $drupalID;
        $dao->domain_id  = CRM_Core_Config::domainID( );
        $dao->contact_id = $contactID;
        if ( ! $dao->find( true ) ) {
            $dao->email = $mail;
            $dao->save( );
        }
    }

    static function createRelationship( $rtypeID, $aID, $bID ) {
        require_once 'CRM/Contact/DAO/Relationship.php';

        $dao =& new CRM_Contact_DAO_Relationship( );

        $dao->relationship_type_id = $rtypeID;
        $dao->contact_id_a         = $aID;
        $dao->contact_id_b         = $bID;
        $dao->find( true );

        // make sure we set the active field
        $dao->is_active = 1;
        $dao->save( );
    }

    static function deactivateRelationship( $rtypeID, $aID, $bID ) {
        require_once 'CRM/Contact/DAO/Relationship.php';

        $dao =& new CRM_Contact_DAO_Relationship( );

        $dao->relationship_type_id = $rtypeID;
        $dao->contact_id_a         = $aID;
        $dao->contact_id_b         = $bID;
        if ( $dao->find( true ) ) {
            // make sure we set the active field
            $dao->is_active = 0;
        } else {
            CRM_Core_Error::fatal( "Could not find relationship for: $rtypeID, $aID, $bID" );
        }

        $dao->save( );
    }

    static function createTaskStatus( $taskID, $responsible, $target, $statusID ) {
        require_once 'CRM/Project/DAO/TaskStatus.php';
        
        $dao =& new CRM_Project_DAO_TaskStatus( );

        $dao->task_id = $taskID;
        $dao->responsible_entity_table = 'civicrm_contact';
        $dao->responsible_entity_id    = $responsible;
        $dao->target_entity_table      = 'civicrm_contact';
        $dao->target_entity_id         = $target;
        $dao->status_id                = $statusID;
        $now                           = date( 'YmdHis' );
        if ( ! $dao->find( true ) ) {
            $dao->create_date   = $now;
        }
        $dao->modified_date     = $now;
        $dao->save( );
    }

    static function cancelTaskStatus( $taskID, $responsible, $target ) {
        require_once 'CRM/Project/DAO/TaskStatus.php';
        
        $dao =& new CRM_Project_DAO_TaskStatus( );

        $dao->task_id = $taskID;
        $dao->responsible_entity_table = 'civicrm_contact';
        $dao->responsible_entity_id    = $responsible;
        $dao->target_entity_table      = 'civicrm_contact';
        $dao->target_entity_id         = $target;
        if ( $dao->find( true ) ) {
            $dao->status_id = 330;
            $dao->save( );
        } else {
            CRM_Core_Error::fatal( "Could not find task status for: $taskID, $responsible, $target" );
        }
    }

}
    
?>
