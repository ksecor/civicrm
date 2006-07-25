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
 *  this file contains functions for Transcript
 */


require_once 'CRM/Quest/DAO/Transcript.php';

class CRM_Quest_BAO_Transcript extends CRM_Quest_DAO_Transcript {

    
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
        
        $dao = & new CRM_Quest_DAO_Transcript();
        $dao->copyValues($params);
        if( $ids['id'] ) {
            $dao->id = $ids['id'];
        }
        $dao->save();
        
        return $dao;
    }



    /**
     * function to add/update transcript  Information
     *
     * @param array  $params reference array contains the values submitted by the form
     * @param String $school_year
     * 
     * @access public
     * @static 
     * @return null
     */
    static function postProcess( $params , $school_year , $contactID ) {

        //create the transcript record
        $transcript = array();
        $transcript["contact_id"]     = $contactID;
        $transcript["school_year"]    = $school_year;
        $transcript["term_system_id"] = $params["term_system_id"];
        $ids = array();
      
        $dao = &new CRM_Quest_DAO_Transcript();
        $dao->contact_id  = $contactID;
        $dao->school_year = $school_year;
        if ( $dao->find(true) ) {
            $ids['id'] = $dao->id;
        }
        $trans = CRM_Quest_BAO_Transcript::create($transcript,$ids );

        //delete all transcript cources before inserting new one
        require_once 'CRM/Quest/DAO/TranscriptCourse.php';
        $dao = &new CRM_Quest_DAO_TranscriptCourse();
        $dao->transcript_id = $trans->id;
        $dao->delete();
 
        //add transcript course
        for ( $i = 1 ; $i <=9; $i++ ) {
            $transcriptParams = array();
            if ( $params['academic_subject_id_'.$i] ) {
                $transcriptParams['transcript_id']       = $trans->id;
                $transcriptParams['academic_subject_id'] = $params['academic_subject_id_'.$i];
                $transcriptParams['course_title']        = $params['course_title_'.$i];
                $transcriptParams['academic_credit']     = $params['academic_credit_'.$i];
                $transcriptParams['academic_honor_status_id'] = $params['academic_honor_status_id_'.$i];
                $transcriptParams['summer_year']         = CRM_Utils_Date::format($params['summer_year_'.$i]);
                for ($j = 1; $j<=4; $j++ ) {
                    $transcriptParams['term_'.$j] = $params['grade_'.$i."_".$j];
                }
                
                $dao = &new CRM_Quest_DAO_TranscriptCourse();
                $dao->copyValues($transcriptParams);
                $dao->save();
            }
        }
        
        
    }

}
    
?>