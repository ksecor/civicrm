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
require_once 'CRM/Quest/BAO/Essay.php';

/**
 * This class generates form components for Work Experiance 
 * 
 */
class CRM_Quest_Form_CPS_WorkExperience extends CRM_Quest_Form_App
{
    protected $_essays;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
        $this->_grouping = 'cm_extracurricular_workexp';
        $this->_essays = CRM_Quest_BAO_Essay::getFields( $this->_grouping, $this->_contactID, $this->_contactID );
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
        $defaults = array( );
        require_once 'CRM/Quest/DAO/WorkExperience.php';
        $dao = &new CRM_Quest_DAO_WorkExperience();
        $dao->contact_id = $this->_contactID;
        $dao->find() ;
        $count = 0;
        while ( $dao->fetch() ){
            $count++;
            $defaults['nature_of_work_'.$count] = $dao->description;
            $defaults['employer_'.$count] = $dao->employer;
            $defaults['start_date_'.$count] = CRM_Utils_Date::unformat( $dao->start_date,'-' );
            $defaults['end_date_'.$count]   = CRM_Utils_Date::unformat( $dao->end_date,'-' );
            $defaults['hrs_'.$count] = $dao->weekly_hours;
        }
               
        $studentFields = array( 'school_work' );
        $dao = & new CRM_Quest_DAO_Student();
        $dao->id = $this->_studentID;;
        if ( $dao->find( true ) ) {
            foreach ( $studentFields as $stu ) {
                if ( $dao->$stu ) {
                    $defaults[$stu] = $dao->$stu;
                }
            }
        }        
        CRM_Quest_BAO_Essay::setDefaults( $this->_essays, $defaults['essay'] );
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
        for( $i = 1; $i <= 6; $i++ ){
            if ( $i == 1 ) {
                $extra = array('onchange' => "return show_element('nature_of_work_1','employer_1');");
            } else {
                $extra = null;
            }
            $this->addElement('text', 'nature_of_work_'.$i, ts( 'Specific Nature of Work' ), $extra);
            $this->addRule('nature_of_work_'.$i,'Maximum length 128 characters','maxlength',128);
            $this->addElement('text', 'employer_'.$i, ts( 'Employer' ), $extra);
            $this->addRule('employer_'.$i,'Maximum length 128 characters','maxlength',128);
            $this->addElement('date', 'start_date_'.$i, ts( 'Start Date' ), 
                              CRM_Core_SelectValues::date( 'custom', 7, 0, "M\001Y" ));
            $this->addRule( 'start_date_'.$i, ts('Please enter valid date for Start Date.'), 'qfDate');
            $this->addElement('date', 'end_date_'.$i, ts( 'End Date' ), 
                              CRM_Core_SelectValues::date( 'custom', 7, 2, "M\001Y" ) );
            $this->addRule( 'end_date_'.$i, ts('Please enter valid date for End Date.'), 'qfDate');
            $this->addElement('text', 'hrs_'.$i, ts( 'Aproximate hours/week' ));
            $this->addRule('hrs_'.$i , ts('Please enter a numeric value for hours/week'),'numeric');
            $this->addElement('checkbox','summer_jobs_'.$i,
                            ts( 'Check if Summer jobs only'),
                            null);
        }
	// $this->addElement('textarea', 'earnings', ts( 'To what use have you put your earnings? ' ), array("rows"=>5,"cols"=>60));

        CRM_Quest_BAO_Essay::buildForm( $this, $this->_essays );
        $schoolWork = array( 'weekends'    => 'Weekends', 
                             'after_school'=> 'After School', 
                             'both'        => 'Both' );
        $this->addRadio( 'school_work', null, $schoolWork );
         
        $maxWork = 6;
        $this->assign( 'maxWork', $maxWork);
        
        require_once 'CRM/Core/ShowHideBlocks.php';
        $showHide = new CRM_Core_ShowHideBlocks();
        $showHide->addHide('id_earnings');
        $showHide->addHide('id_school_work');    
        $showHide->addToTemplate();

        $this->addFormRule(array('CRM_Quest_Form_CPS_WorkExperience', 'formRule'));
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
    public function formRule(&$params)
    {
        $errors = array( );
        $fields = array('nature_of_work_' => 'Nature of work',
                        'employer_'       => 'Employer',
                        'hrs_'            => 'Approximate hours/week'
                        );
        $dates  = array('start_date_'     => 'Start date',
                        'end_date_'       => 'End date'
                        );

        for ( $i = 1; $i <= 6; $i++ ) {
            $filled = false;
            foreach ( $fields as $field => $title) {
                if ($params[$field . $i]) {
                    $filled = true;
                }
            }
            foreach ( $dates as $date => $title ) {
                if ($params[$date . $i]['M'] || $params[$date . $i]['Y']) {
                    $filled = true;
                }
            }
            
            if ($filled) {
                foreach ( $fields as $field => $title ) {
                    if (!$params[$field . $i]) {
                        $errors[$field . $i] = "Please enter the $title";
                    }
                }
                
                if (!$params['start_date_' . $i]['M'] || !$params['start_date_' . $i]['Y']) {
                    $errors[$date . $i] = "Please enter a valid Start date.";
                } elseif ( $params['end_date_' . $i]['M'] && $params['end_date_' . $i]['Y'] ) {
                    if ( $params['start_date_' . $i]['M'] < 10 ) {
                        $params['start_date_' . $i]['M'] = '0' . $params['start_date_' . $i]['M'];
                    }
                    if ( $params['end_date_' . $i]['M'] < 10 ) {
                        $params['end_date_' . $i]['M'] = '0' . $params['end_date_' . $i]['M'];
                    }
                    $sDate = strtotime( $params['start_date_' . $i]['Y'] . $params['start_date_' . $i]['M'] );
                    $eDate = strtotime( $params['end_date_' . $i]['Y'].$params['end_date_' . $i]['M'] );
                    if ( $sDate > $eDate ) {
                        $errors['end_date_' . $i] = "End Date can not be earlier than Start Date.";
                    }
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
        if ( $this->_action &  CRM_Core_Action::VIEW ) {
            return;
        }

        require_once 'CRM/Quest/BAO/WorkExperience.php';
        $params = $this->controller->exportValues( $this->_name );
        $ids = array();
        CRM_Quest_BAO_Essay::create( $this->_essays, $params["essay"], $this->_contactID, $this->_contactID );
        //delete all the entries before inserting new one 
        $dao = &new CRM_Quest_DAO_WorkExperience();
        $dao->contact_id = $this->_contactID;
        $dao->delete();
        $workExpParams = array();
        for( $i = 1; $i <= 6; $i++  ) {
            if ($params['nature_of_work_'.$i]) {
                $workExpParams['contact_id']  = $this->_contactID;
                $workExpParams['description'] = $params['nature_of_work_'.$i];  
                $workExpParams['employer']    = $params['employer_'.$i];
                $workExpParams['start_date']  = CRM_Utils_Date::format($params['start_date_'.$i]);
                $workExpParams['end_date']    = CRM_Utils_Date::format($params['end_date_'.$i]);
                $workExpParams['weekly_hours']= $params['hrs_'.$i];
                if ( ! CRM_Utils_System::isNull( $workExpParams ) ) {
                    CRM_Quest_BAO_WorkExperience::create( $workExpParams, $ids );
                }
            }
        }

        // CRM_Quest_BAO_Essay::create( $this->_essays, $params, $this->_contactID, $this->_contactID );

        //$ids['id'] = $this->_studentID;
        $ids = array( 'id'         => $this->_studentID,
                      'contact_id' => $this->_contactID );
            
        // make sure the school_work stuff is set
        if ( array_key_exists( 'school_work', $params ) ) {
            require_once "CRM/Quest/BAO/Student.php";
            CRM_Quest_BAO_Student::create( $params, $ids );
        }

        parent::postProcess( );
    }

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('Work Experience');
    }
}

?>
