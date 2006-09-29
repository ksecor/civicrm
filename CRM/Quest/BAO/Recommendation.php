<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
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
            $url = CRM_Utils_System::url( 'civicrm/quest/verify',
                                          "reset=1",
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
                         'hash'                 => $hash,
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




    static function getRecommendationDetails( $cid ,&$details) {
        require_once 'CRM/Quest/BAO/Student.php';
        $schoolSelect = CRM_Quest_BAO_Student::getSchoolSelect( $cid );
        unset( $schoolSelect[''] );
        $schoolIDs = implode( ',', array_keys( $schoolSelect ) );
        if ( $schoolIDs ) {
            $query = "
SELECT cr.id as contact_id,
       rs.relationship_type_id as rs_type_id,
       rc.relationship_type_id as rc_type_id
       FROM civicrm_contact      cs,
       civicrm_contact      cr,
       civicrm_individual   i,
       civicrm_email        e,
       civicrm_location     l,
       civicrm_relationship rs,
       civicrm_relationship rc,
       civicrm_task_status  t
 WHERE rs.relationship_type_id IN ( 9, 10 )
   AND rc.relationship_type_id IN ( 11, 12 )
   AND rs.contact_id_a = cs.id
   AND rs.contact_id_b = cr.id
   AND rc.contact_id_a = cr.id
   AND rc.contact_id_b IN ( $schoolIDs )
   AND rs.is_active    = 1
   AND rc.is_active    = 1
   AND rc.contact_id_a = cr.id
   AND cs.id           = {$cid}
   AND i.contact_id    = cr.id
   AND l.entity_table  = 'civicrm_contact'
   AND l.entity_id     = cr.id
   AND e.location_id   = l.id
   AND t.responsible_entity_table = 'civicrm_contact'
   AND t.responsible_entity_id    = cr.id
   AND t.target_entity_table      = 'civicrm_contact'
   AND t.target_entity_id         = cs.id
 ORDER BY rs.relationship_type_id
";
            
            $recommenders = array();
            $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            while ( $dao->fetch( ) ) {
                $recommenders[$dao->contact_id] = $dao->rc_type_id;
            }
            
            $count = 1;
            if (!empty( $recommenders ) ) {
                foreach ( $recommenders as $key => $value ) {
                    if ( $value == 11) {
                        self::getTeachersDetails( $cid, $key , $details, $count );
                        $count++;
                    }else if ( $value == 12 ) {
                        self::getCounselorDetails( $cid, $key, $details);
                    }
                }
            }
            
            
            
        }
        return true;
    }

    static function getTeachersDetails( $cid, $recommenderId, &$details, $count ) {
        //Persoanal Information
        $teacherDetails = array();
        $query = 
            "SELECT civicrm_contact.id AS contact_id, civicrm_contact.display_name, civicrm_individual.first_name, civicrm_individual.middle_name, civicrm_individual.last_name, civicrm_phone.phone
FROM `civicrm_contact`
LEFT JOIN civicrm_individual ON ( civicrm_contact.id = civicrm_individual.contact_id )
LEFT JOIN civicrm_location ON ( (
civicrm_contact.id = civicrm_location.entity_id
)
AND (
civicrm_location.entity_table = 'civicrm_contact'
) )
LEFT JOIN civicrm_phone ON ( civicrm_location.id = civicrm_phone.location_id )
WHERE civicrm_location.is_primary =1 AND civicrm_contact.id = " . $recommenderId;

        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch( ) ) {
            $teacherDetails["PersonalInfo"]["contact_id"]  = $dao->contact_id;
                $teacherDetails["PersonalInfo"]["first_name"]  = $dao->first_name;
                $teacherDetails["PersonalInfo"]["middle_name"] = $dao->middle_name;
                $teacherDetails["PersonalInfo"]["last_name"]   = $dao->last_name;
                $teacherDetails["PersonalInfo"]["phone"]   = $dao->phone;
        }

        require_once 'CRM/Quest/DAO/StudentRanking.php';
        $dao =&new CRM_Quest_DAO_StudentRanking();
        $dao->target_contact_id = $cid;
        $dao->source_contact_id = $recommenderId;
        $ids = array();
        if ( $dao->find(true) ) {
            CRM_Core_DAO::storeValues( $dao, $teacherDetails["StudentRanking"]);
        }
        $names = array('leadership_id'                  => array( 'newName' => 'leadership_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'intellectual_id'                => array( 'newName' => 'intellectual_display',
                                                                  'groupName' => 'recommender_ranking' ),               
                       'challenge_id'                   => array( 'newName' => 'challenge_dispaly',
                                                                  'groupName' => 'recommender_ranking' ),
                       'maturity_id'                    => array( 'newName' => 'maturity_dispaly',
                                                                  'groupName' => 'recommender_ranking' ),
                       'work_ethic_id'                  => array( 'newName' => 'work_ethic_dispaly',
                                                                  'groupName' => 'recommender_ranking' ),
                       'originality_id'                 => array( 'newName'   => 'originality_dispaly',
                                                                  'groupName' => 'recommender_ranking' ),
                       'humor_id'                       => array( 'newName'   => 'humor_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'energy_id'                      => array( 'newName'   => 'energy_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'respect_differences_id'         => array( 'newName'   => 'respect_differences_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'respect_faculty_id'             => array( 'newName'   => 'respect_faculty_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'respect_peers_id'               => array( 'newName'   => 'respect_peers_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'compare_academic_id'            => array( 'newName'   => 'compare_academic_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'compare_extracurricular_id'     => array( 'newName'   => 'compare_extracurricular_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'compare_personal_id'            => array( 'newName'   => 'compare_personal_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'compare_overall_id'             => array( 'newName'   => 'compare_overall_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'recommend_student_id'           => array( 'newName'   => 'recommend_student_id',
                                                                  'groupName' => 'recommender_ranking' ),

                       );

        
        require_once 'CRM/Core/OptionGroup.php';
        CRM_Core_OptionGroup::lookupValues( $teacherDetails["StudentRanking"], $names, false );

        //student Evaluation
        require_once "CRM/Quest/DAO/TeacherEvaluation.php";
        $dao =& new CRM_Quest_DAO_TeacherEvaluation();
        $dao->target_contact_id = $cid;
        $dao->source_contact_id = $recommenderId;
        if ( $dao->find(true) ) {
            CRM_Core_DAO::storeValues( $dao , $teacherDetails["Evaluation"]  );
        }

        $teacherDetails["Evaluation"]["success_factor"] = str_replace( "\001", ",", $teacherDetails["Evaluation"]["success_factor"] );
        require_once "CRM/Quest/BAO/Essay.php";
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_teacher_eval', $recommenderId, $cid );
        require_once "CRM/Quest/BAO/Essay.php";
        CRM_Quest_BAO_Essay::setDefaults( $essays, $teacherDetails["Evaluation"] );
        
        //additional Info 
        $essays = CRM_Quest_BAO_Essay::getFields( "cm_teacher_additional" ,$recommenderId,$cid);
        CRM_Quest_BAO_Essay::setDefaults( $essays, $teacherDetails["AdditionalInfo"] );
        $details["teacher_{$count}"] = $teacherDetails;
       

    }

    static function getCounselorDetails( $cid, $recommenderId, &$details ) {
        $counselorDetails = array();
        $query = 
            "SELECT civicrm_contact.id AS contact_id, civicrm_contact.display_name, civicrm_individual.first_name, civicrm_individual.middle_name, civicrm_individual.last_name, civicrm_phone.phone
FROM `civicrm_contact`
LEFT JOIN civicrm_individual ON ( civicrm_contact.id = civicrm_individual.contact_id )
LEFT JOIN civicrm_location ON ( (
civicrm_contact.id = civicrm_location.entity_id
)
AND (
civicrm_location.entity_table = 'civicrm_contact'
) )
LEFT JOIN civicrm_phone ON ( civicrm_location.id = civicrm_phone.location_id )
WHERE civicrm_location.is_primary =1 AND civicrm_contact.id = " . $recommenderId;

        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch( ) ) {
                $counselorDetails["PersonalInfo"]["contact_id"]  = $dao->contact_id;
                $counselorDetails["PersonalInfo"]["first_name"]  = $dao->first_name;
                $counselorDetails["PersonalInfo"]["middle_name"] = $dao->middle_name;
                $counselorDetails["PersonalInfo"]["last_name"]   = $dao->last_name;
                $counselorDetails["PersonalInfo"]["phone"]   = $dao->phone;
        }


        require_once 'CRM/Quest/DAO/StudentRanking.php';
        $dao =&new CRM_Quest_DAO_StudentRanking();
        $dao->target_contact_id = $cid;
        $dao->source_contact_id = $recommenderId;
        $ids = array();
        if ( $dao->find(true) ) {
            CRM_Core_DAO::storeValues( $dao, $counselorDetails["StudentRanking"]);
        }
        $names = array('leadership_id'                  => array( 'newName' => 'leadership_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'intellectual_id'                => array( 'newName' => 'intellectual_display',
                                                                  'groupName' => 'recommender_ranking' ),               
                       'challenge_id'                   => array( 'newName' => 'challenge_dispaly',
                                                                  'groupName' => 'recommender_ranking' ),
                       'maturity_id'                    => array( 'newName' => 'maturity_dispaly',
                                                                  'groupName' => 'recommender_ranking' ),
                       'work_ethic_id'                  => array( 'newName' => 'work_ethic_dispaly',
                                                                  'groupName' => 'recommender_ranking' ),
                       'originality_id'                 => array( 'newName'   => 'originality_dispaly',
                                                                  'groupName' => 'recommender_ranking' ),
                       'humor_id'                       => array( 'newName'   => 'humor_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'energy_id'                      => array( 'newName'   => 'energy_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'respect_differences_id'         => array( 'newName'   => 'respect_differences_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'respect_faculty_id'             => array( 'newName'   => 'respect_faculty_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'respect_peers_id'               => array( 'newName'   => 'respect_peers_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'compare_academic_id'            => array( 'newName'   => 'compare_academic_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'compare_extracurricular_id'     => array( 'newName'   => 'compare_extracurricular_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'compare_personal_id'            => array( 'newName'   => 'compare_personal_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'compare_overall_id'             => array( 'newName'   => 'compare_overall_display',
                                                                  'groupName' => 'recommender_ranking' ),
                       'recommend_student_id'           => array( 'newName'   => 'recommend_student_id',
                                                                  'groupName' => 'recommender_ranking' ),
                       'counselor_basis'                => array( 'newName'   => 'counselor_basis_display',
                                                                  'groupName' => 'counselor_basis' ),                  
                       
                       );



        require_once 'CRM/Core/OptionGroup.php';
        CRM_Core_OptionGroup::lookupValues( $counselorDetails["StudentRanking"], $names, false );

        foreach ($names as $key => $value) {
             $counselorDetails["StudentRanking"]["{$key}"] = str_replace( "\001", ",", $counselorDetails["StudentRanking"]["{$key}"] );
        }
        
        //student academics
        require_once 'CRM/Quest/DAO/Academic.php';
        $dao =&new CRM_Quest_DAO_Academic();
        $dao->target_contact_id = $cid;
        $dao->source_contact_id = $recommenderId;
        $ids = array();
        if ( $dao->find(true) ) {
            CRM_Core_DAO::storeValues( $dao, $counselorDetails["AcademicRecord"]);
        }


        //student Evaluation
        require_once "CRM/Quest/BAO/Essay.php";
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_counselor_eval', $recommenderId, $cid );
        CRM_Quest_BAO_Essay::setDefaults( $essays, $counselorDetails["Evaluation"] );
        
        require_once "CRM/Quest/DAO/CounselorEvaluation.php";
        $dao = & new CRM_Quest_DAO_CounselorEvaluation();
        $dao->source_contact_id = $recommenderId;
        $dao->target_contact_id = $cid;
        if ($dao->find(true)) {
            CRM_Core_DAO::storeValues($dao,$counselorDetails["Evaluation"]);
        }
        
        $details["counselor"] = $counselorDetails;
    }
    
    
    static function &xml( $id ) {
        $details = array( );

        $xml = array( );
        if ( self::getRecommendationDetails( $id, $details ) ) {
             foreach ( $details as $name => $value ) {
                 if ( $value ) {
                     $xml[$name] = "<{$name}>\n" . CRM_Utils_Array::xml( $value ) . "</{$name}>\n";
                 }
             }
        }

        return $xml;
    }



}
    
?>
