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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

/** 
 *  this file contains functions for Student
 */


require_once 'CRM/Quest/DAO/Student.php';

class CRM_Quest_BAO_Student extends CRM_Quest_DAO_Student {

    
    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * function to add/update student Information
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function &create(&$params, &$ids) {
        
        $dao = & new CRM_Quest_DAO_Student();
        $dao->copyValues($params);
        if( $ids['id'] ) {
            $dao->id = $ids['id'];
        }
        $dao->save();
        return $dao;
    }

    static function retrieve( &$params, &$defaults, &$ids ) {
        $dao = & new CRM_Quest_DAO_Student();
        $dao->contact_id = $params['contact_id'];
        if ( $dao->find( true ) ) {
            CRM_Core_DAO::storeValues( $dao, $defaults );
            $ids['student_id'] = $dao->id;
            $names = array( 'citizenship_status_id' => array('newName' => 'citizenship_status','groupName' => 'citizenship_status'),
                            'gpa_id'                => array('newName' => 'gpa', 'groupName' => 'gpa'),
                            'ethnicity_id_1'        => array('newName' => 'ethnicity_1', 'groupName' => 'ethnicity'),
                            'ethnicity_id_2'        => array('newName' => 'ethnicity_2', 'groupName' => 'ethnicity'),
                            'parent_grad_college_id'=> array('newName' => 'parent_grad_college', 'groupName' => 'parent_grad_college'),
                            'educational_interest'  => array('newName' => 'educational_interest_display', 'groupName' => 'educational_interest'),
                            'college_interest'      => array('newName' => 'college_interest_display', 'groupName' => 'college_interest'),
                            'fed_lunch_id'          => array('newName' => 'fed_lunch', 'groupName' => 'fed_lunch')
                            );
            require_once 'CRM/Core/OptionGroup.php';
            CRM_Core_OptionGroup::lookupValues( $defaults, $names, false );
        }
    }

    static function exportableFields( ) {
        $fields = CRM_Quest_DAO_Student::export( );
        return $fields;
    }

   

    static function getStudentDetails( $contactId ,&$defaults ) {
        
        $indvidualDetails = array();
        $studentDetails   = array();  
        // get student ID
        $dao             = & new CRM_Quest_DAO_Student();
        $dao->contact_id = $contactId;
        if ( $dao->find(true) ) {
            $studentID = $dao->contact_id;
        } else {
            return null;
        }
        
        $params = array( 'contact_id' => $contactId,
                         'id'         => $contactId);

        $ids = array();
        
        CRM_Contact_BAO_Contact::retrieve( &$params, &$indvidualDetails, &$ids );

        $defaults['Individual'] = $indvidualDetails;
        CRM_Core_DAO::storeValues( $dao, $studentDetails);

        $names = array( 'citizenship_status_id'  => array('newName' => 'citizenship_status','groupName' => 'citizenship_status'),
                        'home_area_id'           => array('newName' => 'home_area', 'groupName' => 'home_area'),
                        'parent_grad_college_id' => array('newName' => 'parent_grad_college', 'groupName' => 'parent_grad_college'),
                        'internet_access_id'     => array('newName' => 'internet_access', 'groupName' => 'internet_access'),
                        'study_method_id'        => array('newName' => 'study_method', 'groupName' => 'study_method'),
                        'educational_interest'   => array('newName' => 'educational_interest_display', 'groupName' => 'educational_interest'),
                        'college_type'           => array('newName' => 'college_type_display', 'groupName' => 'college_type'),
                        'college_interest'       => array('newName' => 'college_interest_display', 'groupName' => 'college_interest'),

                        'gpa_id'                => array('newName' => 'gpa', 'groupName' => 'gpa'),
                        'ethnicity_id_1'        => array('newName' => 'ethnicity_1', 'groupName' => 'ethnicity'),
                        'ethnicity_id_2'        => array('newName' => 'ethnicity_2', 'groupName' => 'ethnicity'),
                        'fed_lunch_id'          => array('newName' => 'fed_lunch', 'groupName' => 'fed_lunch'),
                        'class_rank_percent_id' => array('newName' => 'class_rank_percent', 'groupName' => 'class_rank_percent'),
                        'class_rank_percent_id' => array('newName' => 'class_rank_percent', 'groupName' => 'class_rank_percent'),

                        
                        );
        
        CRM_Core_OptionGroup::lookupValues( $studentDetails, $names, false);
       
        //guardian details
        $defaults['Student']    = $studentDetails;

        $guardianDetails = array();
        $guardian             = new CRM_Quest_DAO_Person();
        $guardian->contact_id = $contactId;
        $guardian->is_sibling = 0;
        $guardian->find();
        while( $guardian->fetch() ) {
            CRM_Core_DAO::storeValues( $guardian, $guardianDetails[$guardian->id]);
            $names = array('relationship_id'                => array('newName' => 'relationship','groupName' => 'relationship'),
                           'marital_status_id'              => array('newName' => 'marital_status','groupName' => 'marital_status'),               
                           'industry_id'                    => array('newName' => 'industry','groupName' => 'industry_id'),
                           'highest_school_level_id'        => array('newName' => 'highest_school_level','groupName' => 'highest_school_level'),
                           
                           );
            CRM_Core_OptionGroup::lookupValues( $guardianDetails[$guardian->id], $names, false);
        }
        $defaults['Guardian'] = $guardianDetails;

        //siblings details

        $siblingDetails = array();;
        $sibling        = new CRM_Quest_DAO_Person();
        $sibling->contact_id = $contactId;
        $sibling->is_sibling = 1;
        $sibling->find();
        while( $sibling->fetch() ) {
            CRM_Core_DAO::storeValues( $sibling, $siblingDetails[$sibling->id]);
            $names = array('relationship_id'                => array('newName' => 'relationship','groupName' => 'relationship'),
                           'marital_status_id'              => array('newName' => 'marital_status','groupName' => 'marital_status'),               
                           'industry_id'                    => array('newName' => 'industry','groupName' => 'industry_id'),
                           'highest_school_level_id'        => array('newName' => 'highest_school_level','groupName' => 'highest_school_level'),
                           
                           );
            CRM_Core_OptionGroup::lookupValues( $siblingDetails[$sibling->id], $names, false);
        }
        $defaults['Sibling'] = $siblingDetails;

        
    }




}
    
?>