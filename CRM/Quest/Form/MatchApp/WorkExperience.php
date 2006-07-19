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
 * This class generates form components for Work Experiance 
 * 
 */
class CRM_Quest_Form_MatchApp_WorkExperience extends CRM_Quest_Form_App
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
        CRM_Quest_BAO_Essay::setDefaults( $this->_grouping, $defaults );
        
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
        CRM_Quest_BAO_Essay::setDefaults( $this->_essays, $defaults );
        
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
            $this->addElement('text', 'nature_of_work_'.$i, ts( 'Specific Nature of Work' ));
            $this->addElement('text', 'employer_'.$i, ts( 'Employer' ));
            $this->addElement('date', 'start_date_'.$i, ts( 'Start Date' ), 
                              CRM_Core_SelectValues::date( 'custom', 7, 0, "M\001Y" ) );
            $this->addElement('date', 'end_date_'.$i, ts( 'End Date' ), 
                              CRM_Core_SelectValues::date( 'custom', 7, 2, "M\001Y" ) );
            $this->addElement('text', 'hrs_'.$i, ts( 'Aproximate hours/week' ));
            $this->addElement('checkbox','summer_jobs_'.$i,
                            ts( 'Check if Summer jobs only'),
                            null);
        }
         $this->addElement('textarea', 'earnings', ts( 'To what use have you put your earnings? ' ), array("rows"=>5,"cols"=>60));

         $this->addRadio( 'school_work', null, array( 'weekends'    => 'Weekends', 
                                                      'after_school'=> 'After School', 
                                                      'both'        => 'Both' ) );
         
        $maxWork = 6;
        $this->assign( 'maxWork', $maxWork);
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
            require_once 'CRM/Quest/BAO/WorkExperience.php';
            $params = $this->controller->exportValues( $this->_name );
            $ids = array();
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
                    CRM_Quest_BAO_WorkExperience::create( $workExpParams, $ids );
                }
            }

            CRM_Quest_BAO_Essay::create( $this->_essays, $params, 
                                         $this->_contactID, $this->_contactID );

            $ids['id'] = $this->_studentID;
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
