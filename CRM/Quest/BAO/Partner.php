<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

/** 
 *  this file contains functions for Partners
 */


require_once 'CRM/Quest/DAO/Partner.php';

class CRM_Quest_BAO_Partner extends CRM_Quest_DAO_Partner {

    
    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * function to get all parnters
     *
     */
    function getPartners( $type = 'College')
    {
        $partners = array();
        $dao = &new CRM_Quest_DAO_Partner();
        if ( $type != 'All' ) {
            $dao->partner_type =  $type ;
        }
        $dao->orderBy('weight');
        $dao->find();
        while( $dao->fetch() ) {
            $partners[$dao->id] = $dao->name;
        }

        return $partners;
    }
    
     /**
     * function to add/update partner Information
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function &createRelative(&$relativeParams, &$ids) {
        $dao = & new CRM_Quest_DAO_PartnerRelative();
        $dao->copyValues($relativeParams);
        if( $ids['id'] ) {
            $dao->id = $ids['id'];
        }
        $dao->save();
        
        return $dao;
    }

    static function &getPartnersForContact( $cid, $is_supplement = null ) {
        $query = "
SELECT p.name as name
FROM   quest_partner p,
       quest_partner_ranking r
WHERE  r.contact_id  = $cid
  AND  r.partner_id  = p.id
  AND  ( r.ranking     >= 1 OR
         r.is_forward  = 1 )
";

        if ( $is_supplement !== null ) {
            $query .= " AND p.is_supplement = $is_supplement";
        }

        $partners = array( );
        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch( ) ) {
            $partners[$dao->name] = 1;
        }
        return $partners;
    }

    static function &getPartnersDetails ($cid , &$details) {
        $partnersList  = self::getPartnersForContact( $cid , true);

        if ( $partnersList['Amherst College'] ) {
            self::partner_amherst($cid ,$details );
        }

        if ( $partnersList['Bowdoin College'] ) {
            self::partner_bowdoin($cid ,$details );
        }

        if ( $partnersList['Columbia University'] ) {
            self::partner_columbia($cid ,$details );
        }

        if ( $partnersList['Pomona College'] ) {
            self::partner_pomona($cid ,$details);
        }

        if ( $partnersList['Princeton University'] ) {
            self::partner_princeton($cid ,$details);
        }

        if ( $partnersList['Rice University'] ) {
            self::partner_rice($cid ,$details);
        }

        if ( $partnersList['Stanford University'] ) {
            self::partner_stanford($cid ,$details);
        }

        if ( $partnersList['Wellesley College'] ) {
            self::partner_wellesley($cid ,$details);
        }
        
        return true;
    }

    static function partner_amherst($cid ,&$details ) {
        require_once 'CRM/Quest/Partner/DAO/Amherst.php';
        $dao =& new CRM_Quest_Partner_DAO_Amherst( );
        $dao->contact_id = $cid;
        $fields =
            array(
                  'publication'       => array( 'Amherst Publication'           , 'Publication Name'        ),
                  'representative'    => array( 'Amherst Representative'        , 'Representative Name'     ),
                  'campus_visit'      => array( 'Campus Visit'                  , 'Whom did you Meet?'      ),
                  'college_counselor' => array( 'College Counselor'             , 'Counselor Name'          ),
                  'website'           => array( 'Amherst College Website'       , 'Site URL'                ),
                  'guidebook'         => array( 'Guide Books/Magazines/Websites', 'Name(s)'                 ),
                  'siblings'          => array( 'Siblings, parents, or grandparents who attended', 'Name(s)'),
                  'quest'             => array( 'QuestBridge'			, 'Specify how'),
                  'other'             => array( 'Other'                         , 'Name(s)'                 ),
                  );

        $partnerDetails["AthleticsSupplement"] = array();
        
        if ( $dao->find( true ) ) {
            foreach ( $fields as $name => $titles ) {
                $cond = "is_{$name}";
                if ( $dao->$cond ) {
                    $partnerDetails["ApplicantInformation"][$cond] = 1;
                }
                $partnerDetails["ApplicantInformation"][$name] = $dao->$name;
            }

            $partnerDetails["AthleticsSupplement"]["height"] = $dao->height;
            $partnerDetails["AthleticsSupplement"]["weight"] = $dao->weight;
            
        }
        //Essay Information 
        require_once "CRM/Quest/BAO/Essay.php";
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_amherst_essay', $cid, $cid );
        $partnerDetails["Essay"]['essay'] = array();
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDetails["Essay"]['essay'] );

        //Athletics Supplement
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_amherst_athletic', $cid, $cid );
        
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDetails["AthleticsSupplement"] );

        require_once 'CRM/Quest/BAO/Extracurricular.php';
        CRM_Quest_BAO_Extracurricular::setDefaults( $cid, 'Amherst', $partnerDetails["AthleticsSupplement"] );
        
        $details["AmherstCollege"] = $partnerDetails;

    }
    
    static function partner_bowdoin($cid ,&$details ) {
        
        //Applicant Information
        $partnerBowdoin["ApplicantInformation"] = array();
        require_once 'CRM/Quest/Partner/DAO/Bowdoin.php';
        $dao =& new CRM_Quest_Partner_DAO_Bowdoin( );
        $dao->contact_id = $cid;
        if ( $dao->find( true ) ) {
            $partnerBowdoin["ApplicantInformation"]['learn'] = $dao->learn;
            $partnerBowdoin["AthleticsSupplement"]['height'] = $dao->height;
            $partnerBowdoin["AthleticsSupplement"]['weight'] = $dao->weight;
        }
        require_once "CRM/Quest/BAO/Essay.php";
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_bowdoin_applicant', $cid, $cid );
        
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerBowdoin["ApplicantInformation"] );


        //Athletics Supplement
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_bowdoin_athletic', $cid, $cid );
        
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerBowdoin["AthleticsSupplement"] );

        require_once 'CRM/Quest/BAO/Extracurricular.php';
        CRM_Quest_BAO_Extracurricular::setDefaults( $cid, 'Bowdoin', $partnerBowdoin["AthleticsSupplement"] );


        
        $details["BowdoinCollege"] = $partnerBowdoin;
    }

    static function partner_columbia($cid ,&$details ) {

        //Applicant Information
        $partnerDetails["ApplicantInformation"] = array();
        
        require_once 'CRM/Quest/Partner/DAO/Columbia.php';
        $dao =& new CRM_Quest_Partner_DAO_Columbia( );
        $dao->contact_id = $cid;
        if ( $dao->find( true ) ) {
            $dao->storeValues($dao, $partnerDetails["ApplicantInformation"]);
        }

        require_once "CRM/Quest/BAO/Essay.php";
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_columbia_applicant', $cid, $cid );
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDetails["ApplicantInformation"] );
        
        $fields = array( 'career' => 'columbia_career', 'interest' => 'columbia_interest');
        
        $names = array('career'                => array( 'newName' => 'columbia_career',
                                                         'groupName' => 'columbia_career' ),
                       'interest'              => array( 'newName' => 'columbia_interest',
                                                         'groupName' => 'columbia_career' ),
                       );
        
        require_once "CRM/Core/OptionGroup.php";
        CRM_Core_OptionGroup::lookupValues( $partnerDetails["ApplicantInformation"], $names, false);

        foreach ( $names as $key => $value )  {
             $partnerDetails["ApplicantInformation"][$key] = str_replace( "\001", ",",$partnerDetails["ApplicantInformation"][$key]  );
        }
        
        //Interest

        $partnerDetails["Interests"] = array();
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_columbia_interest', $cid, $cid );
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDetails["Interests"] );

        //Personal Essay
        $partnerDetails["PersonalEssay"] =array();
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_columbia_personal', $cid, $cid );
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDetails["PersonalEssay"] );
        
        $details["ColumbiaUniversity"] = $partnerDetails;
        
    }

    static function partner_pomona($cid ,&$details) {
        $partnerDetails["ApplicantInformation"] = array();
        $fields =
            array( 'name_1', 'department_1', 'relationship_1',
                   'name_2', 'department_2', 'relationship_2',
                   'name_3', 'department_3', 'relationship_3',
                   'is_broader_context', 'is_factors_work' 
                   );
        require_once 'CRM/Quest/Partner/DAO/Pomona.php';
        $dao =& new CRM_Quest_Partner_DAO_Pomona( );
        $dao->contact_id = $cid;
        
        if ( $dao->find( true ) ) {
            foreach ($fields as $name ) {
                if ($dao->$name) {
                    $partnerDetails["ApplicantInformation"][$name] = $dao->$name;
                }
            }
        }
        
        require_once "CRM/Quest/BAO/Essay.php";
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_pomona_applicant', $cid, $cid );
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDetails["ApplicantInformation"]);
        $details["PomonaCollege"] = $partnerDetails;
    }

    static function partner_princeton($cid ,&$details) {
        //Applicant Information
        $partnerDetails["ApplicantInformation"] = array();
        $allChecks = array('princeton_activities', 'ab_department', 'bse_department', 'certificate_programs');
        $testTypes = CRM_Core_OptionGroup::values( 'princeton_test' ,true);
        
        require_once 'CRM/Quest/BAO/Test.php';
        $test =& new CRM_Quest_DAO_Test( );
        $test->contact_id = $cid;
        $test->test_id = $testTypes['Princeton test'];
        $test->find();
        $count = 0;
        while ( $test->fetch( ) ) {
            $count++;
            $partnerDetails["ApplicantInformation"]["Baccalaureate_tests"]['subject_'.$count]   = $test->subject;   
            $partnerDetails["ApplicantInformation"]["Baccalaureate_tests"]['test_date_'.$count] = $test->test_date;   
            $partnerDetails["ApplicantInformation"]["Baccalaureate_tests"]['slhl_'.$count]      = $test->sl_hl;   
            $partnerDetails["ApplicantInformation"]["Baccalaureate_tests"]['score_'.$count]     = $test->score_composite;   
        }

        require_once 'CRM/Quest/Partner/DAO/Princeton.php';
        $dao =& new CRM_Quest_Partner_DAO_Princeton( );
        $dao->contact_id = $cid;
        if ( $dao->find( true ) ) {
            CRM_Core_DAO::storeValues( $dao , $partnerDetails["ApplicantInformation"] );
        }
        
        $names = array('princeton_activities'   => array( 'newName' => 'princeton_activities_names',
                                                         'groupName'=> 'princeton_activities' ),
                       'ab_department'          => array( 'newName' => 'ab_department_names',
                                                        'groupName' => 'ab_department' ),
                       'bse_department'         => array( 'newName' => 'bse_department_names',
                                                        'groupName' => 'bse_department' ),
                       'certificate_programs'   => array( 'newName' => 'certificate_programs_names',
                                                        'groupName' => 'certificate_programs' ),
                       );
        require_once "CRM/Core/OptionGroup.php";
        CRM_Core_OptionGroup::lookupValues( $partnerDetails["ApplicantInformation"], $names, false);
       
        foreach ( $names as $key => $value )  {
             $partnerDetails["ApplicantInformation"][$key] = str_replace( "\001", ",",$partnerDetails["ApplicantInformation"][$key]  );
        }
        
        //Essay
        $partnerDetails["Essay"] = array();
        
        require_once "CRM/Quest/BAO/Essay.php";
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_princeton_essay', $cid, $cid );
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDetails["Essay"]);
        
        //Engineering Essay
        $partnerDetails["EngineeringEssay"] = array();
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_princeton_engg_essay', $cid, $cid );
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDetails["EngineeringEssay"]);
        
        $details["PrincetonUniversity"] = $partnerDetails;


    }

     static function partner_rice($cid ,&$details) {
         //Applicant Information
        $partnerDetails["ApplicantInformation"] = array();
        require_once 'CRM/Quest/Partner/DAO/Rice.php';
        $dao =& new CRM_Quest_Partner_DAO_Rice( );
        $dao->contact_id = $cid;
        if ( $dao->find( true ) ) {
            CRM_Core_DAO::storeValues( $dao , $partnerDetails["ApplicantInformation"] );
        }
        
        require_once "CRM/Quest/BAO/Essay.php";
        $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_rice_applicant', $cid, $cid );
        CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDetails["ApplicantInformation"] );
        
        $names = array('architecture'      => array( 'newName'   => 'architecture_names',
                                                     'groupName' => 'rice_architecture' ),
                       'engineering'       => array( 'newName'   => 'engineering_names',
                                                     'groupName' => 'rice_engineering' ),
                       'humanities'        => array( 'newName'   => 'humanities_names',
                                                     'groupName' => 'rice_humanities' ),
                       'music'             => array( 'newName'   => 'rice_music_names',
                                                     'groupName' => 'rice_music' ),
                       'natural_sciences'  => array( 'newName'   => 'natural_sciences_names',
                                                     'groupName' => 'rice_natural_sciences' ),
                       'social_sciences'   => array( 'newName'   => 'social_sciences_names',
                                                     'groupName' => 'rice_social_sciences' ),
                       'other'             => array( 'newName'   => 'other_names',
                                                     'groupName' => 'rice_other' ),
                       'contacts'          => array( 'newName'   => 'contacts_names',
                                                     'groupName' => 'rice_contacts' ),
                       
                       );
         CRM_Core_OptionGroup::lookupValues( $partnerDetails["ApplicantInformation"], $names, false);

         foreach ( $names as $key => $value )  {
             $partnerDetails["ApplicantInformation"][$key] = str_replace( "\001", ",",$partnerDetails["ApplicantInformation"][$key]  );
         }
        
         $details["RiceUniversity"] = $partnerDetails;


     }
    
     static function partner_stanford($cid ,&$details) {
         //Applicant Information
         $partnerDetails["ApplicantInformation"] = array();
        
         require_once "CRM/Quest/BAO/Essay.php";
         require_once 'CRM/Quest/Partner/DAO/Stanford.php';
         require_once "CRM/Core/BAO/Location.php";
         $dao  = &new CRM_Quest_Partner_DAO_Stanford();
         $dao->contact_id = $cid;
         if ( $dao->find( true ) ) {
             CRM_Core_DAO::storeValues( $dao , $partnerDetails["ApplicantInformation"]);
             $locParams = array('entity_id' => $dao->id, 'entity_table' => 'quest_stanford');
             CRM_Core_BAO_Location::getValues( $locParams, $partnerDetails["ApplicantInformation"], $ids, 3);
         }
         
         $partnerDetails["ApplicantInformation"]["BirthPlace"] = $partnerDetails["ApplicantInformation"]["location"][1] ;
         unset($partnerDetails["ApplicantInformation"]["location"]);
         //short Answers 
         $partnerDetails["ShortAnswers"] = array();
         $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_stanford_short_essay', $cid, $cid );
         CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDetails["ShortAnswers"] );


         //Essay
         $partnerDetails["Essay"] = array();
         $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_stanford_essay', $cid, $cid );
         CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDetails["Essay"] );

         $details["StanfordUniversity"] = $partnerDetails;
         
     }

     static function partner_wellesley($cid ,&$details) {
         //Applicant Information
         $partnerDetails["ApplicantInformation"] = array();
         require_once 'CRM/Quest/Partner/DAO/Wellesley.php';
         require_once "CRM/Quest/BAO/Essay.php";
         $dao =& new CRM_Quest_Partner_DAO_Wellesley( );
         $dao->contact_id = $cid;
         if ( $dao->find( true ) ) {
             CRM_Core_DAO::storeValues( $dao, $partnerDetails["ApplicantInformation"]);
             
         }
         $fields = array ("departmental_majors" ,"interdepartmental_major","preprofessional_interest" ) ;
         
         $names = array('departmental_majors'     => array( 'newName' => 'departmental_majors_names',
                                                            'groupName'=> 'departmental_majors' ),
                        'interdepartmental_major' => array( 'newName' => 'interdepartmental_major_names',
                                                            'groupName' => 'interdepartmental_major' ),
                        'preprofessional_interest'=> array( 'newName' => 'preprofessional_interest_names',
                                                            'groupName' => 'preprofessional_interest' ),
                        
                        );
         CRM_Core_OptionGroup::lookupValues( $partnerDetails["ApplicantInformation"], $names, false);
         foreach ( $fields as $value )  {
             $partnerDetails["ApplicantInformation"][$value] = str_replace( "\001", ",",$partnerDetails["ApplicantInformation"][$value]  );
         }
         //Essay
         $partnerDetails["Essay"] = array();
         $essays = CRM_Quest_BAO_Essay::getFields( 'cm_partner_wellesley_essay', $cid, $cid );
         CRM_Quest_BAO_Essay::setDefaults( $essays, $partnerDetails["Essay"] );
         
        
        $details["WellesleyCollege"] = $partnerDetails;
        
     }


     static function &xml( $id ) {
         $details = array( );

         $xml = array( );
         if ( self::getPartnersDetails( $id, $details ) ) {
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