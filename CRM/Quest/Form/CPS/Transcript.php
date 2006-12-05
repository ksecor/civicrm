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
 | http://www.civicrm.org/licensing/                                 |
 +--------------------------------------------------------------------+
*/


/**
 * Personal Information Form Page
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_CPS_Transcript extends CRM_Quest_Form_App
{
    protected $_isAlternateGrading;

    protected $_grade;
    protected $_previousGrade = null;

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
                array( '' =>  ts(''), '0.00' => '0.00',
                       '0.25' => '0.25', '0.50' => '0.50', '0.75' => '0.75', '1.00' => '1.00',
                       '1.25' => '1.25', '1.50' => '1.50', '1.75' => '1.75', '2.00' => '2.00',
                       '2.25' => '2.25', '2.50' => '2.50', '2.75' => '2.75', '3.00' => '3.00',
                       '3.25' => '3.25', '3.50' => '3.50', '3.75' => '3.75', '4.00' => '4.00',
                       '4.25' => '4.25', '4.50' => '4.50', '4.75' => '4.75', '5.00' => '5.00',
                       '5.25' => '5.25', '5.50' => '5.50', '5.75' => '5.75', '6.00' => '6.00',
                       '6.25' => '6.25', '6.50' => '6.50', '6.75' => '6.75', '7.00' => '7.00',
                       '7.25' => '7.25', '7.50' => '7.50', '7.75' => '7.75', '8.00' => '8.00',
                       '8.25' => '8.25', '8.50' => '8.50', '8.75' => '8.75', '9.00' => '9.00',
                       '9.25' => '9.25', '9.50' => '9.50', '9.75' => '9.75', '10.00' => '10.00' );
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
        // need to set AlternateGrading
        $params = array( 'contact_id' => $this->_contactID,
                         'id'         => $this->_contactID );
        $values =  $ids = array();
        require_once "CRM/Quest/BAO/Student.php";
        CRM_Quest_BAO_Student::retrieve( $params, $values, $ids );
        if ( $values['is_alternate_grading'] ) {
            $this->_isAlternateGrading = true;
        } else {
            $this->_isAlternateGrading = false;
        }
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

        $defaults       = array( );
        
        require_once 'CRM/Quest/DAO/Transcript.php';
        $dao = &new CRM_Quest_DAO_Transcript();
        $dao->contact_id  = $this->_contactID;
        $dao->school_year = $this->_grade;
        if ( $dao->find( true ) ) {
            $defaults['term_system_id'] = $dao->term_system_id;
            $transcriptId = $dao->id;
            require_once 'CRM/Quest/DAO/TranscriptCourse.php';
            $dao = &new CRM_Quest_DAO_TranscriptCourse();
            $dao->transcript_id = $transcriptId;
            $dao->find();
            $count = 0;
            while( $dao->fetch() ) {
                $count++;
                $defaults['academic_subject_id_'.$count] = $dao->academic_subject_id;
                $defaults['course_title_'.$count]        = $dao->course_title; 
                $defaults['academic_credit_'.$count]     = $dao->academic_credit;
                $defaults['academic_honor_status_id_'.$count] = $dao->academic_honor_status_id;
                $defaults['summer_year_'.$count] = CRM_Utils_Date::unformat( $dao->summer_year ,'-' );
                for ($j = 1; $j<=4; $j++ ) {
                    $defaults['grade_'.$count."_".$j] = $dao->{"term_".$j};
                }
            }
        } else {
            // see if we can get the term system id from the previous year
            if ( $this->_previousGrade ) {
                $defaults['term_system_id'] = $this->controller->exportValue( "Transcript-{$this->_previousGrade}", 'term_system_id' );
            }
        }
       
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

        // Summer Transcript is optional form and term is not required.
        if ( $this->_grade == 'Summer' ) {
            $this->addSelect( 'term_system',
                              ts( 'Term System' ),
                              null, false );
        } else {
            $this->addSelect( 'term_system',
                              ts( 'Term System' ),
                              null, true );
        }
        
        $this->assign( 'grade', $this->_grade );

        $grades  =& self::gradeSelector( );
        $credits =& self::creditSelector( );

        for ( $i = 1; $i <= 12; $i++ ) {
            $this->addSelect( 'academic_subject', null, "_$i" );
            $this->addElement( 'text', "course_title_$i", null, $attributes['course_title'] );
            $this->addElement( 'select', "academic_credit_$i", null, $credits );
            $this->addSelect( 'academic_honor_status', null, "_$i", null, null, '' );

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
        $this->addElement( 'hidden', "gradeTitle", $this->_grade);

        $this->addFormRule( array('CRM_Quest_Form_CPS_Transcript', 'formRule'), $this->_grade );

        parent::buildQuickForm( );
    }

    /**
     * Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    public function formRule( &$params, $current )
    {
        $errors = array( );

        $academicFields = array( 'academic_subject_id_'     => 'academic subject',
                                 'course_title_'            => 'course title',
                                 'academic_credit_'         => 'credit' );

        if ( $params['gradeTitle'] != 'Eleven' ) {
            for ( $i = 1; $i <= 12; $i++ ) {
                $filled = false;
                $allFilled = true;
                $gradeFilled = false;
                
                foreach ( $academicFields as $name => $title ) {
                    if ($params[$name . $i]) {
                        $filled = true;
                    } else {
                        $allFilled = false;
                    }
                }
                for ( $j = 1; $j <= 4; $j++ ) {
                    if ($params['grade_' . $i . '_' . $j]) {
                        $filled = true;
                        $gradeFilled = true;
                    }
                }
                if ( $params['gradeTitle'] == 'Twelve' ) {
                    $gradeFilled = true;
                }
                if ( $params['gradeTitle'] == 'Summer' ) {
                    if ($params['summer_year_' . $i]['Y']) {
                        $filled = true;
                    } else {
                        $allFilled = false;
                    }
                }
                if ( ($filled && ! $allFilled) || ( $filled && ! $gradeFilled ) ) {
                    $errors["academic_subject_id_$i"] = "Please fill all the fields in this row";
                }
            }
        }
        return empty($errors) ? true : $errors;
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
            $params = $this->controller->exportValues( $this->_name );
            require_once "CRM/Quest/BAO/Transcript.php";
             CRM_Quest_BAO_Transcript::postProcess( $params, $this->_grade , $this->_contactID );
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
