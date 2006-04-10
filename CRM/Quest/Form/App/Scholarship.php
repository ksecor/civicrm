<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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
class CRM_Quest_Form_App_Scholarship extends CRM_Quest_Form_App
{
    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
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

        $params = array( 'contact_id' => $this->_contactID );
        $ids = array( );
        CRM_Quest_BAO_Student::retrieve( $params, $defaults, $ids );
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Student');

        // primary method to access internet
        $this->addSelectOther('internet_access',
                              ts('What is your primary method of accessing the Internet?'),
                              array('' => ts('- select -')) + CRM_Core_OptionGroup::values( 'internet_access' ),
                              $attributes ,true, array('onChange' =>"showTextField()") );

        // computer at home
        $this->addYesNo( 'is_home_computer',
                         ts( 'Do you have a computer at home?' ),null,true );

        // internet access at home
        $this->addYesNo( 'is_home_internet',
                         ts( 'If yes, do you have internet access at home?' ));

        // federal lunch program
        $this->addSelect( 'fed_lunch',
                          ts( 'Are you eligible for Federal Free or Reduced Price Lunches?' ),null,true);

        // plan on taking SAT or ACT
        $this->addYesNo( 'is_take_SAT_ACT',
                         ts( 'Do you plan on taking the SAT or ACT?' ) ,null,true);

        $this->addSelect( 'study_method',
                          ts( 'If yes, do you plan to study? If so, how?' ));
        // plan to be a financial aid applican
        $this->addYesNo( 'financial_aid_applicant',
                         ts( 'Do you plan to be a financial aid applicant to colleges?' ) ,null,false);
        // fee waivers to register for standarized tests.
        $this->addYesNo( 'register_standarized_tests',
                         ts( 'Do you plan to use, or have you already used, fee waivers to register for standarized tests?' ) ,null,false);

        
        $this->addFormRule(array('CRM_Quest_Form_App_Scholarship', 'formRule'));
        
        parent::buildQuickForm();
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
        
        $errors = array( );
        if ( $params['internet_access_id'] == 23 && $params['internet_access_other'] == '') {
            $errors["internet_access_other"] = "Please describe your other method for accessing the internet.";
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
        if ($this->_action !=  CRM_Core_Action::VIEW ) {
            $params = $this->controller->exportValues( $this->_name );
            
            $ids = array( 'id'         => $this->_studentID,
                          'contact_id' => $this->_contactID );
            $student = CRM_Quest_BAO_Student::create( $params, $ids);
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
        return ts('Scholarship Information');
    }
}

?>
