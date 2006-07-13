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
class CRM_Quest_Form_MatchApp_WorkExperience extends CRM_Quest_Form_App
{
    protected $_personID;
    protected $_relationshipID;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
        $this->_personID        = $this->_options['personID'];
        $this->_relationshipID  = $this->_options['relationshipID'];
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
        if ( $this->_personID ) {
            $dao = & new CRM_Quest_DAO_Person();
            $dao->id = $this->_personID ;
            if ($dao->find(true)) {
                CRM_Core_DAO::storeValues( $dao , $defaults );
            }
            
            // format date
            require_once 'CRM/Utils/Date.php';
            $dateFields = array('start_date','end_date');
            foreach( $dateFields as $field ) {
                $date = CRM_Utils_Date::unformat( $defaults[$field],'-' );  
                if (! empty( $date) ) {
                    $defaults[$field] = $date;
                } else {
                    $defaults[$field] = '';
                }
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
        $choice = array( );
        $choice[] = $this->createElement( 'radio', null, '11', ts( 'Weekends' ) );
        $choice[] = $this->createElement( 'radio', null, '11', ts( 'After School' ));
        $choice[] = $this->createElement( 'radio', null, '11', ts( 'Both' ));
        $this->addGroup( $choice, 'school_work');

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
       self::preProcess();
        if ( ! ( $this->_action &  CRM_Core_Action::VIEW ) ) {
            $params = $this->controller->exportValues( $this->_name );
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
