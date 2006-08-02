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

    static $_validPartners = null;

    static function &partners( ) {
        if ( ! self::$_partners ) {
            self::$_partners = 
                array(
                      'Amherst' => array(
                                         'title' => 'Amherst College',
                                         'steps' => array( 'AmhApplicant' => 'Applicant Information',
                                                           'AmhEssay'     => 'Essay',
                                                           'AmhAthletics' => 'Athletics Supplement',
                                                           'AmhArts'      => 'Arts Supplement' ),
                                         ),

                      'Bowdoin' => array(
                                         'title' => 'Bowdoin College',
                                         'steps' => array( 'BowApplicant' => 'Applicant Information',
                                                           'BowAthletics' => 'Athletics Supplement',
                                                           'BowArts'      => 'Arts Supplement' ),
                                         ),
                      'Columbia' => array(
                                          'title' => 'Columbia University',
                                          'steps' => array( 'ColApplicant'    => 'Applicant Information',
                                                            'ColInterest'       => 'Interests',
                                                            'ColPersonal'       => 'Personal Essay',
                                                            'ColRecommendation' => 'Recommendations' ),
                                          ),
                      'Pomona' => array(
                                        'title' => 'Pomona College',
                                        'steps' => array( 'PomApplicant' => 'Applicant Information', ),
                                        ),
                      'Princeton'=> array(
                                          'title' => 'Princeton University',
                                          'steps' => array( 'PrApplicant' => 'Applicant Information',
                                                            'PrShortAnswer' => 'Short Answers',
                                                            'PrEssay'       => 'Essay',
                                                            'PrEnggEssay'   => 'Enginering Essay' ),
                                          ),
                      'Rice'   => array(
                                        'title' => 'Rice University',
                                        'steps' => array( 'RiceApplicant' => 'Applicant Information', ),
                                        ),
                  
                      'Stanford'=> array(
                                         'title' => 'Stanford University',
                                         'steps' => array( 'StfApplicant'  => 'Applicant Information',
                                                           'StfShortEssay' => 'Short Essay',
                                                           'StfEssay'      => 'Essay',
                                                           'StfArts'       => 'Arts Supplement', ),
                                         ),
                  
                      'Wellesley'   => array(
                                             'title' => 'Wellesley College',
                                             'steps' => array( 'WellApplicant' => 'Applicant Information', 
                                                               'WellEssay'     => 'Essay', ),
                                             ),

                      'Wheaton' => array(
                                         'title' => 'Wheaton College',
                                         'steps' => array( 'WheApplicant'      => 'Applicant Information',
                                                           'WheRecommendation' => 'Recommendations', ),
                                         ),

                      );

        }
        return self::$_partners;
    }

    public function rebuild( &$controller, $action = CRM_Core_Action::NONE ) {
        // ensure the states array is reset
        $this->_states = array( );

        $partners =& self::partners( );

        $validPartners =& $this->getValidPartners( );

        $this->_pages = array( 'CRM_Quest_Form_MatchApp_Partner_PartnerIntro' => null);
        foreach ( $partners as $name => $values ) {
            if ( $validPartners[$values['title']] ) {
                foreach ( $values['steps'] as $key => $title ) {
                    $this->_pages["{$name}-{$key}"] = array( 'className' => "CRM_Quest_Form_MatchApp_Partner_{$name}_{$key}",
                                                             'title'     => $title,
                                                             'options'   => array( ) );
                }
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

    public function getValidPartners( ) {
        if ( ! self::$_validPartners ) {
            self::$_validPartners = $this->_controller->get( 'validPartners' );
            if ( self::$_validPartners ) {
                break;
            }

            $cid = $this->_controller->get( 'contactID' );
            $query = "
SELECT p.name as name
FROM   quest_partner p,
       quest_partner_ranking r
WHERE  r.contact_id  = $cid
  AND  r.partner_id  = p.id
  AND  ( r.ranking     >= 1 OR
         r.is_forward  = 1 )
";
            self::$_validPartners = array( );
            $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            while ( $dao->fetch( ) ) {
                self::$_validPartners[$dao->name] = 1;
            }
        }
        return self::$_validPartners;
    }

}

?>
