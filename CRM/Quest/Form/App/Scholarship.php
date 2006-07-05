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
class CRM_Quest_Form_App_Scholarship extends CRM_Quest_Form_App
{
    static $_referralIDs;
    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
        $this->_referralIDs = array();
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

        require_once 'CRM/Quest/DAO/Referral.php';
        $dao = & new CRM_Quest_DAO_Referral();
        $dao->contact_id = $this->_contactID;
        $dao->find();
        $count = 0;
        while ( $dao->fetch() ) {
            $count++;
            $defaults["sophomores_name_$count"] = $dao->name;
            $defaults["sophomores_email_$count"] = $dao->email;
            $this->_referralIDs[] = $dao->id;
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Student');

        // primary method to access internet
        
        $extra1 = array( 'onchange' => "return showHideByValue('internet_access_id','23','internet_access_other','','select',false);");
        $this->addSelectOther('internet_access',
                              ts('What is your primary method of accessing the Internet?'),
                              array('' => ts('- select -')) + CRM_Core_OptionGroup::values( 'internet_access' ),
                              $attributes ,true, $extra1 );
        $this->addElement('text','internet_access_other',null,null);

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
       for($i=1;$i<=3;$i++) {
           $this->addElement('select','award_ranking_'.$i.'_id',
                              ts('Rank the 3 awards you are interested in receiving, if you are awarded the scholarship'),
                              array('' => ts('- select -')) + CRM_Core_OptionGroup::values( 'award_ranking' )
                              );
       }

       for($i=1;$i<=3;$i++) {
           $this->addElement('text', 'sophomores_name_'.$i, ts('Name:'), null );
           $this->addElement('text', 'sophomores_email_'.$i, ts('Email:'), null );
           $this->addRule('sophomores_email_'.$i, ts('Email not valid'), 'email' );
       }
       
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
        require_once 'CRM/Quest/BAO/Referral.php';

        if ( ! ( $this->_action &  CRM_Core_Action::VIEW ) ) {
            $params = $this->controller->exportValues( $this->_name );
            
            $ids = array( 'id'         => $this->_studentID,
                          'contact_id' => $this->_contactID );
            $student = CRM_Quest_BAO_Student::create( $params, $ids);

            if ( is_array($this->_referralIDs) ) {
                foreach ( $this->_referralIDs as $key => $referralID ) {
                    $dao     = & new CRM_Quest_DAO_Referral();
                    $dao->id = $referralID;
                    $dao->delete();
                }
            }
            
            for ($i=1;$i<=3;$i++) {  
                $ids = array();
                $referralParams = array();
                $referralParams['contact_id'] = $this->_contactID;
                if ($params['sophomores_name_'.$i] || $params['sophomores_email_'.$i]) {
                    $referralParams['name'] = $params['sophomores_name_'.$i];
                    $referralParams['email'] = $params['sophomores_email_'.$i];
                    $referral = CRM_Quest_BAO_Referral::create( $referralParams, $ids );
                }
            }
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
        return ts('Additional Information');
    }
}

?>
