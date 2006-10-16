<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                |
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
 *  this file contains functions for Student
 */


require_once 'CRM/Quest/DAO/Student.php';

class CRM_Quest_BAO_Student extends CRM_Quest_DAO_Student {

    /**
     * array that contains student fields that takes multiple options.
     *
     */
    static $multipleSelectFields = array ('educational_interest' => 1,  
                                          'college_type'         => 1,
                                          'college_interest'     => 1, 
                                          'test_tutoring'        => 1,
                                          
                                          'cmr_first_generation_id' => 1, // readers fields
                                          'cmr_income_increase_id'  => 1,
                                          'cmr_need_id'             => 1, 
                                          'cmr_grade_id'            => 1, 
                                          'cmr_class_id'            => 1, 
                                          'cmr_score_id'            => 1, 
                                          'cmr_academic_id'         => 1, 
                                          'cmr_disposition_id'      => 1
                                          );
    
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
            $names = array( 'citizenship_status_id' => array( 'newName' => 'citizenship_status',
                                                              'groupName' => 'citizenship_status' ),
                            'gpa_weighted_id'      => array( 'newName' => 'gpa_weighted', 
                                                              'groupName' => 'gpa_weighted' ),
                            'gpa_unweighted_id'    => array( 'newName' => 'gpa_unweighted', 
                                                              'groupName' => 'gpa_unweighted' ),
                            'ethnicity_id_1'        => array( 'newName' => 'ethnicity_1', 
                                                              'groupName' => 'ethnicity' ),
                            'ethnicity_id_2'        => array( 'newName' => 'ethnicity_2', 
                                                              'groupName' => 'ethnicity' ),
                            'parent_grad_college_id'=> array( 'newName' => 'parent_grad_college', 
                                                              'groupName' => 'parent_grad_college' ),
                            'educational_interest'  => array( 'newName' => 'educational_interest_display', 
                                                              'groupName' => 'educational_interest' ),
                            'college_interest'      => array( 'newName' => 'college_interest_display', 
                                                              'groupName' => 'college_interest' ),
                            'college_type'          => array( 'newName' => 'college_type_display', 
                                                              'groupName' => 'college_type' ),
                            'fed_lunch_id'          => array( 'newName' => 'fed_lunch', 
                                                              'groupName' => 'fed_lunch' )
                            );
            require_once 'CRM/Core/OptionGroup.php';
            CRM_Core_OptionGroup::lookupValues( $defaults, $names, false );
        }
     
    }

    static function exportableFields( ) {
        require_once "CRM/Quest/DAO/StudentSummary.php";
        $student = CRM_Quest_DAO_Student::export( );
        $studentSummary = CRM_Quest_DAO_StudentSummary::export( );
        $fields = array_merge($student, $studentSummary);
        return $fields;
    }

   
     /**
     * function retrieve student Information
     *
     * @param Int $contactId conatct id
     * @param array $defaults   reference array contains all student Information
     * 
     * @access public
     * @static 
     * @return void
     */
    static function studentDetails( $id ,&$details, $summary = false ) {
        
        require_once 'CRM/Quest/DAO/Student.php';

        self::individual( $id, $details );
        
        // make sure student exists, else early abort
        if ( ! self::student( $id, $details ) ) {
            return false;
        }

        self::studentSummary( $id, $details );
        
        if ( ! $summary ) {
            self::household( $id, $details );
        }

        self::guardian( $id, $details, true );
            
        if ( ! $summary ) {
            self::guardian( $id, $details, false );
            
            self::income( $id, $details );
            
            self::school( $id, $details );
        }
            
        self::test( $id, $details );
            
        if ( ! $summary ) {
            self::essay( $id, $details );
            
            self::referral( $id, $details );
            
            self::partnerRelative( $id, $details );
            
            self::extracurricular ( $id, $details );
            
            self::workexperience( $id, $details );
            
            self::transcript ( $id, $details );
            
            self::honor( $id, $details );
        }

        self::partnerRanking($id, $details);
        

        /**
        CRM_Core_Error::debug( $id, $details );
        exit( );
        **/

        return true;
    }

    static function individual( $id, &$details ) {
        $params = array( 'contact_id' => $id,
                         'id'         => $id );
        $ids = array();
        $individual = array();
        
        require_once 'CRM/Contact/BAO/Contact.php';
        CRM_Contact_BAO_Contact::retrieve( $params, $individual, $ids );
        CRM_Contact_BAO_Contact::resolveDefaults( $individual );

        $details['Individual'] = array( );
        $details['Individual']['contact_id'] = $id;

        $properties = array( 'sort_name', 'display_name', 'nick_name',
                             'prefix', 'suffix',
                             'first_name', 'middle_name', 'last_name',
                             'gender', 'birth_date', );
        foreach ( $properties as $key ) {
            $details['Individual'][$key] = $individual[$key];
        }

        if ( $individual['gender'] ) {
            $details['Individual']['gender_' . strtolower( $individual['gender'] )] = 'x';
        }

        // get address information
        $properties = array( 'street_address', 'city', 'state_province', 'postal_code', 'postal_code_suffix', 'country' );
        for ( $i = 1; $i <= 2; $i++ ) {
            $details["Address_$i"] = array( );
            foreach ( $properties as $key ) {
                $details["Address_$i"][$key] = $individual['location'][$i]['address'][$key];
            }
            $details["Email_$i"] = $individual['location'][$i]['email'][1]['email'];
            $details["Phone_{$i}_Main"] = $individual['location'][$i]['phone'][1]['phone'];
            $details["Phone_{$i}_Alt" ] = $individual['location'][$i]['phone'][2]['phone'];
        }
    }

    static function student( $id, &$details ) {
        $dao             = & new CRM_Quest_DAO_Student();
        $dao->contact_id = $id;
        if ( ! $dao->find(true) ) {
            return false;
        }

        $studentDetails    = array();
        CRM_Core_DAO::storeValues( $dao, $studentDetails);

        $names = array( 'citizenship_status_id'  => array( 'newName' => 'citizenship_status',
                                                           'groupName' => 'citizenship_status' ),
                        'home_area_id'           => array( 'newName' => 'home_area', 
                                                           'groupName' => 'home_area' ),
                        'parent_grad_college_id' => array( 'newName' => 'parent_grad_college', 
                                                           'groupName' => 'parent_grad_college' ),
                        'internet_access_id'     => array( 'newName' => 'internet_access', 
                                                           'groupName' => 'internet_access' ),
                        'study_method_id'        => array( 'newName' => 'study_method', 
                                                           'groupName' => 'study_method' ),
                        'educational_interest'   => array( 'newName' => 'educational_interest_display', 
                                                           'groupName' => 'educational_interest' ),
                        'college_type'           => array( 'newName' => 'college_type_display', 
                                                           'groupName' => 'college_type' ),
                        'college_interest'       => array( 'newName' => 'college_interest_display', 
                                                           'groupName' => 'college_interest' ),
                        'gpa_weighted_id'        => array( 'newName' => 'gpa_weighted', 
                                                           'groupName' => 'gpa_weighted' ),
                        'gpa_unweighted_id'      => array( 'newName' => 'gpa_unweighted', 
                                                           'groupName' => 'gpa_unweighted' ),
                        'ethnicity_id_1'        => array( 'newName' => 'ethnicity_1', 
                                                          'groupName' => 'ethnicity' ),
                        'ethnicity_id_2'        => array( 'newName' => 'ethnicity_2', 
                                                          'groupName' => 'ethnicity' ),
                        'fed_lunch_id'          => array( 'newName' => 'fed_lunch', 
                                                          'groupName' => 'fed_lunch' ),
                        'class_rank_percent_id' => array( 'newName' => 'class_rank_percent', 
                                                          'groupName' => 'class_rank_percent' ),
                        'class_rank_percent_id' => array( 'newName' => 'class_rank_percent', 
                                                          'groupName' => 'class_rank_percent' ),
                        'award_ranking_1_id'    => array( 'newName' => 'award_ranking_1', 
                                                          'groupName' => 'award_ranking' ),
                        'award_ranking_2_id'    => array( 'newName' => 'award_ranking_2', 
                                                          'groupName' => 'award_ranking' ),
                        'award_ranking_3_id'    => array( 'newName' => 'award_ranking_3', 
                                                          'groupName' => 'award_ranking' ),
                        'test_tutoring'         => array( 'newName' => 'test_tutoring_display', 
                                                          'groupName' => 'test' ), 
                        );

        require_once 'CRM/Core/OptionGroup.php';
        CRM_Core_OptionGroup::lookupValues( $studentDetails, $names, false);

        if ( ! array_key_exists( 'Student', $details ) ) {
            $details['Student']    = array( );
        }
        foreach ( $names as $key => $value ) {
            $details['Student'][$key]              = $studentDetails[$key];
            $details['Student'][$value['newName']] = $studentDetails[$value['newName']];
        }

        if ( $studentDetails['home_area'] ) {
            $details['Student']['home_area_' . strtolower( $studentDetails['home_area'] )] = 'x';
        }

        //fix for country
        $countryIds = array( 'citizenship_country' => 'citizenship_country_id',
                             'growup_country'      => 'growup_country_id',
                             'nationality_country' => 'nationality_country_id' );

        require_once 'CRM/Core/PseudoConstant.php'; 
        foreach( $countryIds as $key => $val ) {
            if( $studentDetails[$val] ) {
                $details['Student'][$key] = CRM_Core_PseudoConstant::country($studentDetails[$val]);
            } else {
                $details['Student'][$key] = null;
            }
        }

        // add other student details
        $properties = array( 'years_in_us', 'first_language', 'primary_language', 
                             'internet_access_other',
                             'is_home_computer', 'is_home_internet', 'is_take_SAT_ACT',
                             'educational_interest_other', 'college_interest_other',
                             'is_class_ranking', 'class_rank', 'class_num_students',
                             'gpa_explanation', 'test_tutoring',
                             'high_school_grad_year',
                             'number_siblings', 'financial_aid_applicant',
                             'register_standarized_tests' );
        foreach ( $properties as $key ) {
            $details['Student'][$key] = $studentDetails[$key];
        }

        // fix parent_grad_college which is a boolean
        $details['Student']['parent_grad_college'] = $studentDetails['parent_grad_college_id'];
        if ( $studentDetails['parent_grad_college_id'] == '1' ) {
            $details['Student']['parent_grad_college_yes'] = 'x';
        } else if ( $studentDetails['parent_grad_college_id'] == '0' ) {
            $details['Student']['parent_grad_college_no'] = 'x'; 
        } else {
            $details['Student']['parent_grad_college_dont_know'] = 'x';
        }


        $multiSelectElements = array( 'educational_interest', 'college_type', 'college_interest' );
        foreach ( $multiSelectElements as $key ) {
            $details['Student']["{$key}_ids"] = str_replace( "\001", ",", $studentDetails[$key] );
            unset($details['Student']["{$key}"]);
            $elements = explode( ',', $details['Student']["{$key}_display"] );
            foreach ( $elements as $el ) {
                $el = trim( $el );
                if ( empty( $el ) ) {
                    continue;
                }
                $el = strtolower( $el );
                $el = str_replace( ' ', '_', $el );
                $el = str_replace( '/', '_OR_', $el );
                $el = str_replace( '(', '', $el );
                $el = str_replace( ')', '', $el );
                $details['Student']["{$key}_{$el}"] = "x";
            }
        }

        return true;
    }

    static function studentSummary( $id, &$details ) {
        require_once 'CRM/Quest/DAO/StudentSummary.php';
        $dao             = & new CRM_Quest_DAO_StudentSummary();
        $dao->contact_id = $id;
        if ( ! $dao->find(true) ) {
            return false;
        }

        $studentDetails    = array();
        CRM_Core_DAO::storeValues( $dao, $studentDetails);

        if ( ! array_key_exists( 'Student', $details ) ) {
            $details['Student'] = array( );
        }
        $properties = array( 'gpa_unweighted_calc', 'gpa_weighted_calc', 'SAT_composite',
                             'SAT_composite_alt', 'SAT_reading', 'SAT_math', 'SAT_writing',
                             'ACT_composite', 'ACT_english', 'ACT_reading', 'ACT_math', 'ACT_science',
                             'PSAT_composite', 'PLAN_composite', 'household_income_total',
                             'household_member_count','academic_index','financial_need_index','cmr_comment' );
        foreach ( $properties as $key ) {
            $details['Student'][$key] = $studentDetails[$key];
        }
        
        $names = array( 'cmr_disposition_id'        => array( 'newName'     => 'cmr_disposition',
                                                              'groupName'   => 'cmr_disposition' ),
                        'cmr_first_generation_id'   => array( 'newName'     => 'cmr_first_generation',
                                                              'groupName'   => 'cmr_first_generation' ),
                        'cmr_income_increase_id'    => array( 'newName'     => 'cmr_income_increase', 
                                                              'groupName'   => 'cmr_income_increase' ),
                        'cmr_need_id'               => array( 'newName'     => 'cmr_need', 
                                                              'groupName'   => 'cmr_need' ),
                        'cmr_grade_id'              => array( 'newName'     => 'cmr_grade', 
                                                              'groupName'   => 'cmr_grade' ),
                        'cmr_class_id'              => array( 'newName'     => 'cmr_class', 
                                                              'groupName'   => 'cmr_class' ),
                        'cmr_score_id'              => array( 'newName'     => 'cmr_score', 
                                                              'groupName'   => 'cmr_score' ),
                        'cmr_academic_id'           => array( 'newName'     => 'cmr_academic', 
                                                              'groupName'   => 'cmr_academic' ),
                        );

        require_once 'CRM/Core/OptionGroup.php';
        CRM_Core_OptionGroup::lookupValues( $studentDetails, $names, false);
        
        foreach ( $names as $key => $value ) {
            $details['Student'][$key]              = $studentDetails[$key];
            $details['Student'][$value['newName']] = $studentDetails[$value['newName']];
        }
    }

    static function household( $id, &$details ) {
        require_once 'CRM/Quest/DAO/Household.php';
        $dao = & new CRM_Quest_DAO_Household();
        $dao->contact_id = $id;
        $dao->find( );

        $names = array( 'years_lived_id' => array( 'newName'   => 'years_lived',
                                                   'groupName' => 'years_lived' ) );

        $properties = array( 'member_count', 'description', 'years_lived' );
        $people     = array( 'id', 'first_name', 'last_name' );

        require_once 'CRM/Quest/DAO/Person.php';
        while ( $dao->fetch( ) ) {
            $prefix = "Household_{$dao->household_type}";

            $defaults = array( );
            CRM_Core_DAO::storeValues( $dao, $defaults );

            CRM_Core_OptionGroup::lookupValues( $defaults, $names );

            foreach ( $properties as $prop ) {
                $details[$prefix][$prop] = $defaults[$prop];
            }

            foreach ( $names as $name => $dontCare ) {
                $details["{$prefix}_$count"][$name] = $defaults[$name];
            }

            for ( $j = 1; $j <= 2; $j++ ) {
                $personDAO = & new CRM_Quest_DAO_Person();
                $string = "person_{$j}_id";
                $personDAO->id = $dao->$string;
                if ( $personDAO->id && $personDAO->find(true) ) {
                    foreach ( $people as $prop ) {
                        $details[$prefix]["Person_$j"][$prop] = $personDAO->$prop;
                    }
                }
            }
        }
    }

    static function guardian( $id, &$details, $isGuardian ) {

        require_once 'CRM/Quest/DAO/Person.php';
        $person             =& new CRM_Quest_DAO_Person();
        $person->contact_id =  $id;
        $person->is_sibling =  $isGuardian ? 0 : 1;
        $person->orderby( 'relationship_id asc' );
        $person->find();

        $prefix = $isGuardian ? 'Guardian' : 'Sibling';

        $properties = array( 'id', 'first_name', 'last_name', 'is_deceased',
                             'job_organization', 'job_occupation', 'job_current_years',
                             'is_contact_with_student',
                             'college_name', 'college_grad_year', 'college_major',
                             'prof_school_name', 'lived_with_from_age', 'lived_with_to_age',
                             'description' );
        
        $dates = array( 'birth_date', 'deceased_year', 'college_grad_year', 'prof_grad_year' );

        $names = array('relationship_id'                => array( 'newName' => 'relationship',
                                                                  'groupName' => 'relationship' ),
                       'marital_status_id'              => array( 'newName' => 'marital_status',
                                                                  'groupName' => 'marital_status' ),               
                       'industry_id'                    => array( 'newName' => 'industry',
                                                                  'groupName' => 'industry' ),
                       'highest_school_level_id'        => array( 'newName' => 'highest_school_level',
                                                                  'groupName' => 'highest_school_level' ),
                       'current_school_level_id'        => array( 'newName' => 'current_school_level',
                                                                  'groupName' => 'current_school_level' ),
                       'prof_school_degree_id'          => array( 'newName'   => 'prof_school_degree',
                                                                  'groupName' => 'prof_school_degree' ),
                       );
        if ( ! $isGuardian ) {
            $names['relationship_id']['groupName'] = 'sibling_relationship';
        }

        $count = 1;
        while( $person->fetch() ) {
            $personDetails = array( );
            CRM_Core_DAO::storeValues( $person, $personDetails );

            CRM_Core_OptionGroup::lookupValues( $personDetails, $names, false);
            $details["{$prefix}_$count"] = array( );

            foreach ( $properties as $key ) {
                $details["{$prefix}_$count"][$key] = $personDetails[$key];
            }
            
            foreach ( $names as $name => $dontCare ) {
                $details["{$prefix}_$count"][$name] = $personDetails[$name];
            }

            if ( $details["{$prefix}_$count"]['is_deceased'] ) {
                $details["{$prefix}_$count"]['deceased_yes'] = 'x';
            } else {
                $details["{$prefix}_$count"]['deceased_no'] = 'x';
            }

            foreach ( $dates as $date ) {
                $details["{$prefix}_$count"][$date] = $personDetails[$date];
            }

            if ( $personDetails['college_country_id'] ) {
                $details["{$prefix}_$count"]['college_country'] = CRM_Core_PseudoConstant::country($personDetails['college_country_id']);
            } else {
                $details["{$prefix}_$count"]['college_country'] = null;
            }

            foreach ( $names as $key => $value ) {
                $details["{$prefix}_$count"][$value['newName']] = $personDetails[$value['newName']];
            }

            $count++;
        }
    }

    static function extracurricular ( $id, &$details ) {
        require_once 'CRM/Quest/DAO/Extracurricular.php';

        $dao =& new CRM_Quest_DAO_Extracurricular( );
        $dao->contact_id = $id;
        $dao->owner      = "Extracurricular";    
        $dao->find() ;
        $count = 0;
        while ( $dao->fetch( ) ) {
            $count++;
            $prefix = "Extracurricular_".$count;
            $details[$prefix]['activity']      = $dao->description;   
            $details[$prefix]['grade_level_9'] = $dao->is_grade_9;
            $details[$prefix]['grade_level_10'] = $dao->is_grade_10;
            $details[$prefix]['grade_level_11'] = $dao->is_grade_11;
            $details[$prefix]['grade_level_12'] = $dao->is_grade_12;
            $details[$prefix]['grade_level_PS'] = $dao->is_post_secondary;
            $details[$prefix]['positions']     = $dao->position_honor;
            $details[$prefix]['hours_per_week']  = $dao->weekly_hours;
            $details[$prefix]['Weeks_per_year']  = $dao->annual_weeks;
            
            foreach ( $details[$prefix] as $key => $value ) {
                if (substr( $key ,0 ,11 ) == "grade_level") {
                    if ( $details[$prefix][$key] ) {
                        $details[$prefix][$key] = "x";
                    }
                }
            }
        

        }
       
    }
    
    static function workexperience( $id, &$details ) {
        require_once 'CRM/Quest/DAO/WorkExperience.php';
        $dao = &new CRM_Quest_DAO_WorkExperience();
        $dao->contact_id = $id;
        $dao->find() ;
        $count = 0;
        while ( $dao->fetch() ){
            $count++;
            $prefix = "WorkExperience_".$count;
            $details[$prefix]['nature_of_work'] = $dao->description;
            $details[$prefix]['employer'] = $dao->employer;
            $details[$prefix]['start_date'] =  $dao->start_date;
            $details[$prefix]['end_date']   =  $dao->end_date;
            $details[$prefix]['hrs'] = $dao->weekly_hours;
        } 
        
    }
    

    static function income( $id, &$details ) {

        require_once 'CRM/Quest/DAO/Income.php';

        $dao =& new CRM_Quest_DAO_Person( );
        $dao->contact_id = $id;
        $dao->is_income_source   = true;
        $dao->find( );
        
        $names = array('source_1_id' => array( 'newName'   => 'source_1',
                                               'groupName' => 'type_of_income' ),
                       'source_2_id' => array( 'newName'   => 'source_2',
                                               'groupName' => 'type_of_income' ),
                       'source_3_id' => array( 'newName'   => 'source_3',
                                               'groupName' => 'type_of_income' ),
                       );

        $properties = array( 'id', 'job_1', 'job_2', 'job_3', 'amount_1', 'amount_2', 'amount_3' );

        $count = 1;
        while( $dao->fetch() ) {
            $incomeDAO =& new CRM_Quest_DAO_Income( );
            $incomeDAO->person_id = $dao->id;
            $incomeDAO->find(true);
            
            $incomeDetails = array( );
            CRM_Core_DAO::storeValues( $incomeDAO, $incomeDetails );
            CRM_Core_OptionGroup::lookupValues( $incomeDetails, $names, false);

            for ( $i = 1; $i <= 3; $i++ ) {
                if ( ! empty( $incomeDetails["amount_$i"] ) ) {
                    $details["Income_$count"]['first_name']      = $dao->first_name;
                    $details["Income_$count"]['last_name']       = $dao->last_name;
                    $details["Income_$count"]['job']             = $incomeDetails["job_$i"];
                    $details["Income_$count"]['amount']          = $incomeDetails["amount_$i"];
                    $details["Income_$count"]['source']          = $incomeDetails["source_$i"];
                    $details["Income_$count"]["source_{$i}_$id"] = $incomeDetails["source_$i"];
                    $count++;
                }
            }
        }
    }

    static function school( $id, &$details ) {

        $highSchoolDetails  = array();
        $otherSchoolDetails = array();
        $organization      = array(); 

        require_once 'CRM/Contact/BAO/Relationship.php';
        require_once  'CRM/Core/BAO/CustomGroup.php';
        $relationship  = CRM_Contact_BAO_Relationship::getRelationship( $id );
        
        foreach( $relationship as $key => $value ) {
            if ($value['relation'] == 'Attends School' ) {
                $params = array( 'contact_id' => $value['cid'],
                                 'id'         => $value['cid']);
                $ids = array();
                $orgDetails = array();
                CRM_Contact_BAO_Contact::retrieve( $params, $orgDetails, $ids );
                CRM_Contact_BAO_Contact::resolveDefaults( $orgDetails );
                $groupTree = & CRM_Core_BAO_CustomGroup::getTree( 'Organization', $value['cid'], 0 );
                CRM_Core_BAO_CustomGroup::setDefaults( $groupTree, $orgDetails, true, false );

                $orgDetails['start_date'] = $value['start_date'];
                $orgDetails['end_date'  ] = $value['end_date'  ];

                $organization[$key] = $orgDetails;
            }
        }

        $address = array( 'street_address', 'city', 'state_province', 'postal_code', 'postal_code_suffix', 'country' );
        $highCount = $otherCount = 1;

        $map = array( 310 => 'Public', 311 => 'Private', 312 => 'Parochial' );

        foreach( $organization as $key => $value ) {
            if ( $value['custom_4'] == 'Highschool' ) {
                $prefix = "HighSchool_" . $highCount;
                $highCount++;
            } else {
                $prefix = "OtherSchool_" . $otherCount;
                $otherCount++;
            }

            $details[$prefix] = array( );

            $details[$prefix]['organization_name'] = $value['organization_name'];

            for ( $i =1; $i <= 4; $i++ ) {
                if ( $i == 2 ) {
                    $details[$prefix]["custom_{$i}"] = $map[$value["custom_{$i}"]];
                } else {
                    $details[$prefix]["custom_{$i}"] = $value["custom_{$i}"];
                }
            }

            $details[$prefix]['start_date'] = $value['start_date'];
            $details[$prefix]['end_date'  ] = $value['end_date'  ];

            if ( $value['note'] ) {
                foreach( $value['note'] as $k1 => $v1) {
                    $details[$prefix]['note'] = $v1['note'];
                }
            } else {
                $details[$prefix]['note'] = null;
            }

            foreach ( $address as $key ) {
                $details[$prefix][$key] = $value['location'][1]['address'][$key];
            }
            $details[$prefix]['phone'] = $value['location'][1]['phone'][1]['phone']; 
        }
    }

    static function test( $id, &$details ) {
        require_once 'CRM/Quest/DAO/Test.php';
  
        $testDAO = & new CRM_Quest_DAO_Test();
        $testDAO->contact_id = $id;
        $testDAO->find( );
        
        $count = 1;
        $names = array('test_id'         => array( 'newName' => 'test',
                                                   'groupName' => 'test' ),
                       'score_composite' => array( 'newName' => 'score',
                                                   'groupName' => 'ap_score' ),
                       'subject_id'      => array( 'newName' => 'subject',
                                                   'groupName' => 'ap_subject' ),
                       );

        $satCount = $apCount = $satIICount = 0;
        while( $testDAO->fetch() ) {
            //test details
            if ( $testDAO->test_id != 350) {
                $testDetails = array();
                
                CRM_Core_DAO::storeValues( $testDAO, $testDetails );
                
                if ( $testDetails['test_id'] == 291 ) {
                    $names['subject_id']['groupName'] = 'satII_subject';
                } else if ( $testDetails['test_id'] == 292 ) {
                    $names['subject_id']['groupName'] = 'ap_subject';
                }
                
                $testDetails['subject_id'] = $testDetails['subject'];
                
                CRM_Core_OptionGroup::lookupValues( $testDetails , $names, false);

                $prefix = 'test_' . str_replace(" " , "_" , $testDetails['test']);
                switch ( $testDetails['test_id'] ) {
                case 290:
                    $satCount++;
                    $prefix .= "_{$satCount}";
                    break;

                case 291:
                    $satIICount++;
                    $prefix .= "_{$satIICount}";
                    break;
                    
                case 292:
                    $apCount++;
                    $prefix .= "_{$apCount}";
                    break;

                default:
                    break;
                }

                $details[$prefix] = $testDetails;
            }
        }
    }

    static function transcript( $id, &$details ) {
        
        require_once 'CRM/Quest/DAO/Transcript.php';
        $daoTrn = &new CRM_Quest_DAO_Transcript();
        $daoTrn->contact_id  = $id;
        $daoTrn->find();
        $honors = CRM_Core_OptionGroup::values( 'academic_honor_status' );
        while ( $daoTrn->fetch() ) {
            $prefix = "transcript_".$daoTrn->school_year;
            $transcriptId = $daoTrn->id;
            require_once 'CRM/Quest/DAO/TranscriptCourse.php';
            $dao = &new CRM_Quest_DAO_TranscriptCourse();
            $dao->transcript_id = $transcriptId;
            $dao->find();
            $count = 0;
            while( $dao->fetch() ) {
                $count++;
                $details[$prefix]['academic_subject_id_'.$count] = $dao->academic_subject_id;
                $details[$prefix]['course_title_'.$count]        = $dao->course_title; 
                $details[$prefix]['academic_credit_'.$count]     = $dao->academic_credit;
                if ( $dao->academic_honor_status_id ) {
                    $details[$prefix]['academic_honor_status_id_'.$count] = $dao->academic_honor_status_id;
                    $details[$prefix]['academic_honor_status_'.$count] = $honors[$dao->academic_honor_status_id];
                }
                $details[$prefix]['summer_year_'.$count] =  $dao->summer_year ;
                for ($j = 1; $j<=4; $j++ ) {
                    $details[$prefix]['grade_'.$count."_".$j] = $dao->{"term_".$j};
                }
                $names = array('academic_subject_id_'.$count => array( 'newName' => 'academic_subject_'.$count,
                                                                       'groupName' => 'academic_subject' )
                               );
                CRM_Core_OptionGroup::lookupValues( $details[$prefix] , $names, false);
            }
        }



    }

    static function partnerRanking($id, &$details) {
        require_once "CRM/Quest/BAO/Partner.php";
        $partners = CRM_Quest_BAO_Partner::getPartners( 'All' );
        
        require_once 'CRM/Quest/DAO/PartnerRanking.php';
        $dao = & new CRM_Quest_DAO_PartnerRanking();
        $dao->contact_id = $id;
        $dao->find();
        while( $dao->fetch() ){
            if ( array_key_exists( $dao->partner_id, $partners ) ) {
                if ( $dao->ranking ) {
                    $details["PartnerRanking"][str_replace(" " , "_" ,$partners[$dao->partner_id])."_"."Ranking"] = $dao->ranking;
                }
                if ( $dao->is_forward ) {
                    $details["PartnerRanking"][str_replace(" " , "_" ,$partners[$dao->partner_id])."_"."Forward"] = 1;
                }
            }
        }
    }

    static function essay( $id, &$details ) {
        require_once 'CRM/Quest/DAO/Essay.php';
        require_once 'CRM/Quest/DAO/EssayType.php';
        $essay = array();
        $essayDAO = & new CRM_Quest_DAO_Essay();
        $essayDAO->target_contact_id = $id;
        $essayDAO->source_contact_id = $id;
        $essayDAO->find();
        while ( $essayDAO->fetch() ) {
            $essayType = & new CRM_Quest_DAO_EssayType();
            $essayType->id = $essayDAO->essay_type_id;
            $essayType->find(true);
            $details['Essay'][$essayType->grouping][$essayType->name] = $essayDAO->essay;
        }
    }

    static function honor( $id, &$details ) {
        require_once 'CRM/Quest/DAO/Honor.php';

        $honor = array();
        $honorDAO = & new CRM_Quest_DAO_Honor();
        $honorDAO->contact_id = $id;
        $honorDAO->find( );

        $count = 1;
        while ( $honorDAO->fetch( ) ) {
            $name = "Honor_{$count}";
            $details[$name]['description'] = $honorDAO->description;
            $details[$name]['award_date' ] = $honorDAO->award_date;
            $count++;
        }
    }

    static function referral( $id, &$details ) {
        require_once 'CRM/Quest/DAO/Referral.php';

        $referral = array();
        $referralDAO = & new CRM_Quest_DAO_Referral();
        $referralDAO->contact_id = $id;
        $referralDAO->find( );

        $count = 1;
        while ( $referralDAO->fetch( ) ) {
            $name = "Referral_$count";
            $details[$name]['first_name' ] = $referralDAO->first_name; 
            $details[$name]['last_name' ] = $referralDAO->last_name; 
            $details[$name]['school' ] = $referralDAO->school; 
            $details[$name]['email'] = $referralDAO->email;
            $count++;
        }
    }
        
    static function partnerRelative( $id, &$details ) {
        require_once 'CRM/Quest/DAO/PartnerRelative.php';
        
        $dao = & new CRM_Quest_DAO_PartnerRelative();
        $dao->contact_id      = $id;
        $dao->find();
        $count = 0;
        while ( $dao->fetch() ) {
             $count++;
             $prefix = $dao->connection_type."_".$count;
             $details[$prefix]["alumni_last_name"]              = $dao->last_name;
             $details[$prefix]["alumni_first_name"]             = $dao->first_name;
             $details[$prefix]["alumni_relationship"]           = $dao->relationship;
             $details[$prefix]["alumni_class_year"]        = $dao->college_grad_year;  
        }
        
    }
    
    static function deleteStudent( $id ) {

        //delete civicrm_student record
        $dao = & new CRM_Quest_DAO_Student();
        $dao->contact_id = $id;
        $dao->delete();

        //delete civicrm_essay record
        require_once 'CRM/Quest/DAO/Essay.php';
        $essayDAO = & new CRM_Quest_DAO_Essay();
        $essayDAO->contact_id = $id;
        $essayDAO->delete();

        //delete civicrm_test record
        require_once 'CRM/Quest/DAO/Test.php';
        $testDAO = & new CRM_Quest_DAO_Test();
        $testDAO->contact_id = $id;
        $testDAO->delete();

        //delete civicrm_referral record
        require_once 'CRM/Quest/DAO/Referral.php';
        $refDAO = & new CRM_Quest_DAO_Referral();
        $refDAO->contact_id = $id;
        $refDAO->delete();

        //delete civicrm_household record
        require_once 'CRM/Quest/DAO/Household.php';
        $houseDAO = & new CRM_Quest_DAO_Household();
        $houseDAO->contact_id = $id;
        $houseDAO->delete();

        //delete civicrm_income record
        require_once 'CRM/Quest/DAO/Person.php';
        require_once 'CRM/Quest/DAO/Income.php';
        $personDAO =& new CRM_Quest_DAO_Person( );
        $personDAO->contact_id = $id;
        $personDAO->is_income_source   = true;
        $personDAO->find();
        while ($personDAO->fetch()) {
            $incomeDAO =& new CRM_Quest_DAO_Income( );
            $incomeDAO->person_id = $personDAO->id;
            $incomeDAO->delete();
        }

        //delete civicrm_person record
        $personDAO =& new CRM_Quest_DAO_Person( );
        $personDAO->contact_id = $id;
        $personDAO->delete();
        
        //delete civicrm_honor record
        require_once 'CRM/Quest/DAO/Honor.php';
        $honorDAO = & new CRM_Quest_DAO_Honor();
        $honorDAO->contact_id = $id;
        $honorDAO->delete();
    }

    static function &xml( $id, $summary = false ) {
        $details = array( );

        if ( self::studentDetails( $id, $details, $summary ) ) {
            $xml = "<StudentDetail>\n" . CRM_Utils_Array::xml( $details ) . "</StudentDetail>\n";
            return $xml;
        }

        return null;
    }

    static function &xmlFlatValues( $id ) {
        $details = array( );

        if ( self::studentDetails( $id, $details ) ) {
            $flat = array( );
            CRM_Utils_Array::flatten( $details, $flat );
            return $flat;
        }

        return null;
    }

    static function pdf( $id ) {
        $config =& CRM_Core_Config::singleton( );

        require_once 'CRM/Utils/PDFlib.php';
        $values = CRM_Quest_BAO_Student::xmlFlatValues( $id );

        return CRM_Utils_PDFlib::compose( 'readerPDF.pdf',
                                          $config->templateDir . '/Quest/pdf/',
                                          $values, 6, false );
    }

    /**
     * Function to build student form for profile
     *
     * @params $form       object form object
     * @params $fieldName  string name of the field
     * @params $contactId  int    contact id
     *
     * @return null
     * @static
     * @access public

     */    
    static function buildStudentForm( &$form, $fieldName, $title = null, $contactId = null ) {
        if ($contactId) {
            $name = "field[$contactId][$fieldName]";
        } else {
            $name = $fieldName;
        }


        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Student' );

        $names = array('citizenship_status_id'  =>  'citizenship_status',
                       'home_area_id'           =>  'home_area', 
                       'parent_grad_college_id' =>  'parent_grad_college', 
                       'internet_access_id'     =>  'internet_access', 
                       'study_method_id'        =>  'study_method', 
                       'educational_interest'   =>  'educational_interest', 
                       'college_type'           =>  'college_type', 
                       'college_interest'       =>  'college_interest', 
                       'gpa_id'                 =>  'gpa', 
                       'ethnicity_id_1'         =>  'ethnicity', 
                       'ethnicity_id_2'         =>  'ethnicity', 
                       'fed_lunch_id'           =>  'fed_lunch', 
                       'class_rank_percent_id'  =>  'class_rank_percent', 
                       'class_rank_percent_id'  =>  'class_rank_percent', 
                       'award_ranking_1_id'     =>  'award_ranking', 
                       'award_ranking_2_id'     =>  'award_ranking', 
                       'award_ranking_3_id'     =>  'award_ranking', 
                       'test_tutoring'          =>  'test', 
                       );
        
        $scoreAttribs = array('SAT_composite', 
                              'SAT_composite_alt', 
                              'SAT_reading', 
                              'SAT_math' ,
                              'SAT_writing', 
                              'ACT_composite', 
                              'ACT_english', 
                              'ACT_reading', 
                              'ACT_math', 
                              'ACT_science', 
                              'PSAT_composite', 
                              'PLAN_composite', 
                              'household_income_total', 
                              'household_member_count');
        
        $readers = array('cmr_first_generation_id', 'cmr_income_increase_id', 'cmr_need_id', 
                         'cmr_grade_id', 'cmr_class_id', 'cmr_score_id', 'cmr_academic_id', 'cmr_disposition_id');
        
        if ( in_array($fieldName, array('gpa_id', 
                                        'ethnicity_id_1',
                                        'award_ranking_1_id',
                                        'award_ranking_2_id',
                                        'award_ranking_3_id',
                                        'citizenship_status_id',
                                        'internet_access_id',
                                        'study_method_id',
                                        'fed_lunch_id')) ) {
            
            require_once 'CRM/Core/OptionGroup.php';
            $form->add('select', $name, $title, 
                       array(''=>ts( '-select-' )) + CRM_Core_OptionGroup::values($names[$fieldName]), $required );
            return true;
            
        } else if ( ($fieldName == 'gpa_unweighted_calc') || ($fieldName == 'gpa_weighted_calc')) {
            
            require_once 'CRM/Core/OptionGroup.php';
            $form->add('select', $name, $title,
                       array(''=>ts( '-select-' )) + CRM_Core_OptionGroup::values($fieldName), $required);
            return true;
        } else if (substr( $fieldName, 0, 4) == 'cmr_' && $fieldName != 'cmr_comment') { //for  readers group
            $readerGroup = substr($fieldName, 0, -3);
            require_once 'CRM/Core/OptionGroup.php';
            $form->add('select', $name, $title,
                       array(''=>ts( '-select-' )) + CRM_Core_OptionGroup::values($readerGroup), $required);
            return true;
            
        } else if ($fieldName == 'high_school_grad_year' ) {
            $form->add('date', $name, $title, CRM_Core_SelectValues::date( 'custom', 0, 2, "Y" ) );
            return true;
            
        } else if ( in_array($fieldName, array('educational_interest', 
                                               'college_type', 
                                               'college_interest',
                                               'test_tutoring')) ) {
            
            $form->addCheckBox($name, $title, 
                               CRM_Core_OptionGroup::values( $names[$fieldName], true ), false, null,false );
            return true;
            
        } else if ( in_array($name, array('is_class_ranking', 
                                          'is_partner_share', 
                                          'is_home_computer', 
                                          'is_home_internet', 
                                          'is_take_SAT_ACT', 
                                          'financial_aid_applicant',
                                          'register_standarized_tests' )) ) {
            
            $form->addYesNo($name, $title);
            return true;
            
        } else if ( in_array($fieldName, array('class_rank', 
                                               'class_num_students', 
                                               'score_SAT', 
                                               'score_PSAT', 
                                               'score_ACT',
                                               'score_PLAN', 
                                               'household_income_total', 
                                               'number_siblings', 
                                               'years_in_us',
                                               'first_language', 
                                               'primary_language',
                                               'internet_access_other')) || in_array($fieldName, $scoreAttribs)) {
            
            if (in_array($fieldName, $scoreAttribs) && (! $attributes[$fieldName])) {
                $field['attributes'] = array('maxlength' => 8, 'size' => 4);
            }
            $form->addElement('text', $name, $title, $attributes[$fieldName]);
            return true;
            
        } else if ($fieldName == 'gpa_explanation' || $fieldName == 'cmr_comment') {
            
            $form->addElement('textarea', $name, $title, $attributes[$fieldName]);
            return true;
            
        } else if ( in_array( $fieldName, array('citizenship_country_id', 
                                                'growup_country_id', 
                                                'nationality_country_id') ) ) {
            
            $form->addElement('select', $name, $title,
                              array('' => ts('- select -')) + CRM_Core_PseudoConstant::country( ) );
            return true;
            
        } else if ( $fieldName == 'home_area_id' ) {
            $form->addRadio( $name, $title,
                             CRM_Core_OptionGroup::values($fieldName) );
            return true;
            
        } else {
            return false;
        }
    }

    /**
     * function to set values for the radio fields to be displayed in profile-view
     * 
     * @param array $field    profile fields of interest
     * @param array $values   the values for the above fields
     * @param array $details  contains all student Information
     * 
     * @access public
     * @static 
     * @return true if relevent field found, otherwise false
     */
    
    static function getValues( &$field, &$details, &$values ) {
        if ( substr($field['name'], 0, 3) === 'is_' or $field['name'] === 'financial_aid_applicant' or
             $field['name'] === 'register_standarized_tests') {  
            if ($details->$field['name'] == 1) {
                $values[$field['title']] = 'Yes';
            } 
            return true;
        } else {
            return false;
        }       
    }

    static function &getSchoolSelect( $id ) {
        // get the org id and the name of all schools that this
        // student has a relationship to
        $query = "
SELECT o.contact_id as id,
       o.organization_name as name
FROM   civicrm_organization o,
       civicrm_relationship r
WHERE  r.relationship_type_id = 8
  AND  r.contact_id_a         = $id
  AND  r.is_active            = 1
  AND  r.contact_id_b         = o.contact_id
";

        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        $schools = array( '' => ts( '- select -' ) );
        while ( $dao->fetch( ) ) {
            $schools[$dao->id] = $dao->name;
        }

        return $schools;
    }

    static function createStudentSummary( $params ,$ids ) {
        require_once "CRM/Quest/DAO/StudentSummary.php";
        $dao = & new CRM_Quest_DAO_StudentSummary();
        $dao->copyValues($params);
        if( $ids['id'] ) {
            $dao->id = $ids['id'];
        }
        $dao->save();
        return $dao;
    }
    
    static function &isPrepTestScholarshipWinner( $id ) {
        // determine if this student was a winner for SAT Prep Test Scholarship
        $query = "
SELECT count( id ) AS cnt
FROM civicrm_custom_value
WHERE custom_field_id
IN ( 13, 14 )
AND entity_table = 'civicrm_contact'
AND entity_id = $id
AND char_data = '3'
";
        
        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray ); 
        if ($dao->fetch()) {
            return $dao->cnt;
        }
        return 0;
    }
    
}
    
?>
