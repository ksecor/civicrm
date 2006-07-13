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
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/


/**
 * Personal Information Form Page
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_MatchApp_TranscriptGrade extends CRM_Quest_Form_App
{
    protected $_isAlternateGrading;

    protected $_grade;

    static $_gradeSelector  = null;

    static $_creditSelector = null;

    static function &gradeSelector( ) {
        if ( ! self::$_gradeSelector ) {
            self::$_gradeSelector =
                array( '' =>  ts(''),
                       'A+' => 'A+', 'A' => 'A', 'A-' => 'A-',
                       'B+' => 'B+', 'B' => 'B', 'B-' => 'B-',
                       'C+' => 'C+', 'C' => 'C', 'C-' => 'C-',
                       'D+' => 'D+', 'D' => 'D', 'D-' => 'D-',
                       'Pass' => 'Pass', 'Fail' => 'Fail' );
        }
        return self::$_gradeSelector;
    }

    static function &creditSelector( ) {
        if ( ! self::$_creditSelector ) {
            self::$_creditSelector =
                array( '' =>  ts(''), '10' => '10',
                       '9.75' => '9.75', '9.50' => '9.50', '9.25' => '9.25', '9.00' => '9.00',
                       '8.75' => '8.75', '8.50' => '8.50', '8.25' => '8.25', '8.00' => '8.00',
                       '7.75' => '7.75', '7.50' => '7.50', '7.25' => '7.25', '7.00' => '7.00',
                       '6.75' => '6.75', '6.50' => '6.50', '6.25' => '6.25', '6.00' => '6.00',
                       '5.75' => '5.75', '5.50' => '5.50', '5.25' => '5.25', '5.00' => '5.00',
                       '4.75' => '4.75', '4.50' => '4.50', '4.25' => '4.25', '4.00' => '4.00',
                       '3.75' => '3.75', '3.50' => '3.50', '3.25' => '3.25', '3.00' => '3.00',
                       '2.75' => '2.75', '2.50' => '2.50', '2.25' => '2.25', '2.00' => '2.00',
                       '1.75' => '1.75', '1.50' => '1.50', '1.25' => '1.25', '1.00' => '1.00',
                       '0.75' => '0.75', '0.50' => '0.50', '0.25' => '0.25', '0.00' => '0.00' );
        }
        return self::$_creditSelector;
    }

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess( );

        $this->_isAlternateGrading = true;
    }

    /**
     * This function sets the default values for the form. Relationship that in edit/view action
     * the default values are retrieved from the database
     * 
     * @access public
     * @return void
     */
    function setDefaultValues( ) 
    {
        require_once 'CRM/Quest/DAO/Student.php';
        $defaults       = array( );
 
        return $defaults;
    }
    

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Student' );

        $this->addSelect( 'term_system',
                          ts( 'Term System' ),
                          null, true );

        $this->assign( 'grade', $this->_grade );

        $grades  =& self::gradeSelector( );
        $credits =& self::creditSelector( );

        for ( $i = 1; $i <= 10; $i++ ) {
            $this->addSelect( 'academic_subject',
                              null,
                              "_$i" );
            $this->addElement( 'text',
                               "course_title_$i",
                               null,
                               $attributes['course_title'] );
            $this->addElement( 'select',
                               "academic_credit_$i", null,
                               $credits );
            $this->addSelect( 'academic_honor_status',
                              null,
                              "_$i", null, null, '' );
            if ( $this->_grade == 'Twelve' ) {
                continue;
            } else {
                if ( $this->_grade == 'Summer' ) {
                    $this->addElement('date', "summer_year_$i", null,
                                      CRM_Core_SelectValues::date( 'custom', 4, 1, "Y" ) );
                    $this->addRule("summer_year_$i", ts('Select a valid date.'), 'qfDate');
                    $max = 1;
                } else {
                    $max = 4;
                }
                for ( $j = 1; $j <= $max; $j++ ) {
                    if ( $this->_isAlternateGrading ) {
                        $this->addElement('text', "grade_{$i}_{$j}", null, $attributes['grade_1'] );
                    } else {
                        $this->addElement('select', "grade_{$i}_{$j}", null,
                                          $grades );
                    }
                }
            }
        }

        parent::buildQuickForm( );
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return void
     */
    public function postProcess() 
    {
        if ( ! ( $this->_action &  CRM_Core_Action::VIEW ) ) {
        }
        parent::postProcess( );
    }//end of function

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('Transcript Information');
    }
    
}

?>
