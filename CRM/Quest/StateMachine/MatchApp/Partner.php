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

require_once 'CRM/Quest/StateMachine/MatchApp.php';

/**
 * State machine for managing different states of the Quest process.
 *
 */
class CRM_Quest_StateMachine_MatchApp_Partner extends CRM_Quest_StateMachine_MatchApp {

    static $_dependency = null;

    static $_partners   = null;

    static function &partners( ) {
        if ( ! self::$_partners ) {
            self::$_partners = 
                array(
                      'Amherst' => array(
                                         'AmhApplicant' => 'Applicant Information',
                                         'AmhEssay'     => 'Essay',
                                         'AmhAthletics' => 'Athletics Supplement',
                                         'AmhArts'      => 'Arts Supplement'
                                         ),

                      'Bowdoin' => array(
                                         'BowApplicant' => 'Applicant Information',
                                         'BowAthletics' => 'Athletics Supplement',
                                         'BowArts'      => 'Arts Supplement'
                                         ),
                      'Columbia' => array(
                                          'ColApplicant'    => 'Applicant Information',
                                          'ColInterest'       => 'Interests',
                                          'ColPersonal'       => 'Personal Essay',
                                          'ColRecommendation' => 'Recommendations'
                                          ),
                      'Pomona' => array(
                                        'PomApplicant' => 'Applicant Information',
                                        ),
                      'Princeton'=> array(
                                          'PrApplicant' => 'Applicant Information',
                                          'PrShortAnswer' => 'Short Answers',
                                          'PrEssay'       => 'Essay',
                                          'PrEnggEssay'   => 'Enginering Essay'
                                          ),
                      'Rice'   => array(
                                        'RiceApplicant' => 'Applicant Information',
                                        ),
                  
                      'Stanford'=> array(
                                         'StfApplicant'  => 'Applicant Information',
                                         'StfShortEssay' => 'Short Essay',
                                         'StfEssay'      => 'Essay',
                                         'StfArts'       => 'Arts Supplement',
                                         ),
                  
                      'Wellesley'   => array(
                                             'WellApplicant' => 'Applicant Information',
                                             'Essay'         => 'Essay'
                                             ),

                      'Wheaton' => array(
                                         'WheApplicant'      => 'Applicant Information',
                                         'WheRecommendation' => 'Recommendations'
                                         ),

                      );

        }
        return self::$_partners;
    }

    public function rebuild( &$controller, $action = CRM_Core_Action::NONE ) {
        // ensure the states array is reset
        $this->_states = array( );

        $partners =& self::partners( );

        $this->_pages = array( );
        foreach ( $partners as $name => $values ) {
            foreach ( $values as $key => $title ) {
                $this->_pages["{$name}-{$key}"] = array( 'className' => "CRM_Quest_Form_MatchApp_Partner_{$name}_{$key}",
                                                         'title'     => $title,
                                                         'options'   => array( ) );
            }
        }

        parent::rebuild( $controller, $action );
    }

    public function &getDependency( ) {
        if ( self::$_dependency == null ) {
            self::$_dependency = array( );
        }
        return self::$_dependency;
    }

}

?>
