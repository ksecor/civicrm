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
 *  this file contains functions for Extracurricular
 */


require_once 'CRM/Quest/DAO/Extracurricular.php';

class CRM_Quest_BAO_Extracurricular extends CRM_Quest_DAO_Extracurricular {
    
    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
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
    static function &create(&$relativeParams, &$ids) {
        $dao = & new CRM_Quest_DAO_Extracurricular();
        $dao->copyValues($relativeParams);
        if( $ids['id'] ) {
            $dao->id = $ids['id'];
        }
        $dao->save();
        
        return $dao;
    }

    static function buildForm( &$form, $type = 'Extracurricular' ) {
        
        $maxGrades = ( $type == 'Extracurricular' ) ? 5 : 6;

        for ( $i = 1; $i <= 7; $i++ ) {
            $form->addElement('text', "activity_$i", null,, null );

            for ( $j = 1; $j <= $maxGrades; $j++ ) {
                $form->addElement('checkbox', "grade_level_{$j}_{$i}", null, null ); 
            }

            if ( $type == 'Extracurricular' ) {
                for ( $j = 1; $j <= 2; $j++ ) {
                    $form->addElement('text', "time_spent_{$j}_{$i}", null, null );
                    if ( $j == 1 ) {
                        $form->addRule("time_spent_{$j}_{$i}", ts('Please enter the integer/decimal value'), 'numeric');
                    } else {
                        $form->addRule("time_spent_{$j}_{$i}", ts('Please enter the integer value'), 'integer');
                    }
                }
            }

            $form->addElement('text', "positions_$i", null, null );
            
            if ( $type != 'Extracurricular' ) {
                $form->addElement('text', "coach_$i", null, null );
                $form->addElement('text', "varsity_captain_$i", null, null );
            }
        }
    }

    static function formRule( &$params, $type ) {

        $errors = array( );
        
        for ( $i = 1; $i <= 7; $i++ ) {

            $filled = $anyGrade = false;
            if ($params["activity_$i"]) {
                $filled = true;
            }

            $maxGrades = ( $type == 'Extracurricular' ) ? 5 : 6;
            
            for ( $j = 1; $j <= $maxGrades; $j++ ) {
                if ($params["grade_level_{$j}_{$i}"]) {
                    $filled = true;
                    $anyGrade = true;
                }
            }

            if ( $type == 'Extracurricular' ) {
                for ( $j = 1; $j <= 2; $j++ ) {
                    if ($params["time_spent_{$j}_{$i}"]) {
                        $filled = true;
                    }
                }
            }

            if ($params["positions_$i"]) {
                $filled = true;
            }
            
            if ( $type != 'Extracurricular' ) {
                if ( $params["coach_$i"] || $params["varsity_captain_$i"] ) {
                    $filled = true;
                }
            }

            if ($filled) {
                
                if (!$params["activity_$i"]) {
                    $errors["activity_$i"] = "Please enter the activity.";
                }
                if (!$anyGrade) {
                    for ( $j = 1; $j <= $maxGrades; $j++ ) {
                        $errors["grade_level_{$j}_{$i}"] = "Please specify any grade level.";
                    }
                }
                
                if ( $type == 'Extracurricular' ) {
                    for ( $j = 1; $j <= 2; $j++ ) {
                        if (!$params["time_spent_{$j}_{$i}"]) {
                            $errors["time_spent_{$j}_{$i}"] = "Please enter the time spent.";
                        }
                    }
                }

                if (!$params["positions_$i"]) {
                    $errors["positions_$i"] = "Please specify the position held.";
                }

                if ( $type != 'Extracurricular' ) {
                    if ( ! $params["coach_$i"] ) {
                        $errors["coach_$i"] = "Please enter the coach name.";
                    }
                    if ( ! $params["varsity_captain_$i"] ) {
                        $errors["varsity_captain_$i"] = "Please enter the varsity captain name.";
                    }
                }
            }
        }

        return empty($errors) ? true : $errors;
        
    }
}
    
?>