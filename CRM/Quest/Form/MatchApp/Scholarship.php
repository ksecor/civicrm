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

require_once 'CRM/Quest/Form/App/Scholarship.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_MatchApp_Scholarship extends CRM_Quest_Form_App_Scholarship
{
    static $_referralIDs;
    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {/*
        parent::preProcess();
        $this->_referralIDs = array();*/
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
    }
    

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) 
    {


        parent::buildQuickForm( );   
     
        $this->addSelect( 'study_method',
                          ts( 'Did you study for the SAT or ACT? If so, how?' ));

        $this->addElement('text','displacement', ts('If you are a resident of Alabama, Florida, Louisina, Mississippi, or Texas, are you currently displaced by Hurricane Katrina or Rita? If so, please take a moment to provide details of your displacement'), null);

        $this->addRadio( 'heard_about_qb_id',
                         ts('How did you hear about QuestBridge?'),
                         CRM_Core_OptionGroup::values('heard_about_qb') );


        for($i=1;$i<=6;$i++) {
           $this->addSelect( 'partner_institution_'.$i, ts('Partner Institution') );
           $this->addElement('text', 'last_name_'.$i, ts('Last Name'), null );
           $this->addElement('text', 'first_name_'.$i, ts('First Name'), null );
           $this->addElement('text', 'class_year_'.$i, ts('Class Year'), null );
           $this->addElement('text', 'relationship_'.$i, ts('Relationship'), null );

       }
        for($i=1;$i<=6;$i++) {
           $this->addSelect('partner_institution_'.$i, ts('Partner Institution'), null );
           $this->addElement('text', 'last_name_'.$i, ts('Last Name'), null );
           $this->addElement('text', 'first_name_'.$i, ts('First Name'), null );
           $this->addElement('text', 'department_'.$i, ts('Department'), null );
           $this->addElement('text', 'relationship_'.$i, ts('Relationship'), null );

       }

    }//end of function

    
    /**
     * Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    public function formRule(&$params) {
        
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return void
     */
    public function postProcess() 
    {
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
        parent::getTitle();

    }
}

?>
