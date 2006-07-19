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
class CRM_Quest_Form_MatchApp_Scholarship extends CRM_Quest_Form_App
{
    static $_referralIDs;
    static $_alumnusIDS;
    static $_employeeIDS;
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
        $this->_alumnusIDS  = array();
        $this->_employeeIDS = array();
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
        $defaults['heard_about_qb_name_'.$defaults['heard_about_qb_id']] = $defaults['heard_about_qb_name'];
        
        require_once 'CRM/Quest/DAO/Referral.php';
        $dao = & new CRM_Quest_DAO_Referral();
        $dao->contact_id = $this->_contactID;
        $dao->application_id = 349;
        $dao->find();
        $count = $sCount = $eCount =   0;
        while ( $dao->fetch() ) {
            $type = strtolower($dao->referral_type);
            $type == 'student' ? $sCount++ : $eCount++;
            $type == 'student' ? $count = $sCount : $count = $eCount;
            
            $defaults["referral_".$type."_first_name_$count"] = $dao->first_name;
            $defaults["referral_".$type."_last_name_$count"]  = $dao->last_name;
            $defaults["referral_".$type."_school_$count"]     = $dao->school;
            $defaults["referral_".$type."_email_$count"]      = $dao->email;
            $defaults["referral_".$type."_phone_$count"]      = $dao->phone;
            $defaults["referral_".$type."_position_id_$count"]= $dao->position_id;
            $defaults["referral_".$type."_year_$count"]       = CRM_Utils_Date::unformat($dao->high_school_grad_year,'-');
            
            $this->_referralIDs[] = $dao->id;
        }

        //to set defaults for alumns and employee
        require_once 'CRM/Quest/DAO/PartnerRelative.php';
        
        $dao = & new CRM_Quest_DAO_PartnerRelative();
        $dao->contact_id      = $this->_contactID;
        $dao->connection_type = 'Alumnus';
        $dao->find();
        $count = 0;
        while ( $dao->fetch() ) {
             $count++;
             $defaults["alumni_partner_institution_id_$count"] = $dao->partner_id;
             $defaults["alumni_last_name_$count"]              = $dao->last_name;
             $defaults["alumni_first_name_$count"]             = $dao->first_name;
             $defaults["alumni_relationship_$count"]           = $dao->relationship;
             $defaults["alumni_class_year_$count"]["Y"]        = $dao->college_grad_year;  
             $this->_alumnusIDS[] = $dao->id;
        }

        $dao = & new CRM_Quest_DAO_PartnerRelative();
        $dao->contact_id      = $this->_contactID;
        $dao->connection_type = 'Employee';
        $dao->find();
        $count = 0;
        while ( $dao->fetch() ) {
             $count++;
             $defaults["employee_partner_institution_id_$count"] = $dao->partner_id;
             $defaults["employee_last_name_$count"]              = $dao->last_name;
             $defaults["employee_first_name_$count"]             = $dao->first_name;
             $defaults["employee_relationship_$count"]           = $dao->relationship;
             $defaults["employee_department_$count"]             = $dao->department; 
             $this->_employeeIDS[] = $dao->id;
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
        $this->addElement('text','internet_access_other',ts('Please specify'),null);

        // computer at home
        $extra2 = array('onchange' => "return showHideByValue('is_home_computer', '1', 'is_home_internet','table-row', 'radio', false);");
        $this->addYesNo( 'is_home_computer',
                         ts( 'Do you have a computer at home?' ),null,true ,$extra2);

        // internet access at home
        $this->addYesNo( 'is_home_internet',
                         ts( 'If yes, do you have internet access at home?' ));

        // federal lunch program
        $this->addSelect( 'fed_lunch',
                          ts( 'Are you eligible for Federal Free or Reduced Price Lunches?' ),null,true);
        $extra3 = array('onchange' => "return showHideByValue('is_take_SAT_ACT', '1', 'study_method_id','table-row', 'radio', false);");
        $this->addYesNo( 'is_take_SAT_ACT',
                         ts( 'Did you study for the SAT or ACT?' ) ,null,true, $extra3);


        $this->addSelect( 'study_method',
                          ts( 'How did you study for the SAT or ACT?' ));
        // plan to be a financial aid applican
        $this->addYesNo( 'financial_aid_applicant',
                         ts( 'Do you plan on applying for financial aid??' ) ,null,false);
        // fee waivers to register for standarized tests.
        $this->addYesNo( 'register_standarized_tests',
                         ts( 'Did you use fee waivers to register for standarized tests?' ) ,null,false);

        $this->addElement('textarea','displacement', ts('If you are a resident of Alabama, Florida, Louisina, Mississippi, or Texas, are you currently displaced by Hurricane Katrina or Rita? If so, please take a moment to provide details of your displacement'), "cols=60,rows=8");
        $radioAttribute = array('onclick' => "return show_element('heard_about_qb_id');");
        
        $this->addRadio( 'heard_about_qb_id',
                         ts('How did you hear about QuestBridge?'),
                         CRM_Core_OptionGroup::values('heard_about_qb'),
                         $radioAttribute);
        
        require_once 'CRM/Core/ShowHideBlocks.php';
        $showHide = new CRM_Core_ShowHideBlocks();
        
        $name_array = array( 3,4,5,6,7,9);
        foreach ( $name_array as $value ) {
            $this->addElement('text','heard_about_qb_name_'.$value,null,null);
            $showHide->addHide('heard_about_name_' . $value);
        }
        $showHide->addToTemplate();
        
        for($i=1;$i<=3;$i++) {
            $this->addElement('text', 'referral_student_first_name_'.$i, null, null );
            $this->addElement('text', 'referral_student_last_name_'.$i, null, null );
            $this->addElement('text', 'referral_student_school_'.$i, null, null );
            $this->addElement('date', 'referral_student_year_'.$i,null, CRM_Core_SelectValues::date( 'custom', 0, 2, "Y" ) );
            $this->addElement('text', 'referral_student_email_'.$i, null, null );
            $this->addElement('text', 'referral_student_phone_'.$i, null, null );
        }

        for($i=1;$i<=3;$i++) {
            $this->addElement('text', 'referral_educator_first_name_'.$i, null, null );
            $this->addElement('text', 'referral_educator_last_name_'.$i, null, null );
            $this->addElement('text', 'referral_educator_school_'.$i, null, null );
            $this->addElement('select','referral_educator_position_id_'.$i,null,array('' => ts('- select -')) + CRM_Core_OptionGroup::values( 'school_position' ));
            $this->addElement('text', 'referral_educator_email_'.$i, null, null );
            $this->addElement('text', 'referral_educator_phone_'.$i, null, null );
        }
        
        include_once 'CRM/Quest/BAO/Partner.php';
        $partners = CRM_Quest_BAO_Partner::getPartners();
        for($i=1;$i<=6;$i++) {
           $this->addElement('select','alumni_partner_institution_id_'.$i ,ts('Partner Institution') ,
                              array('' => ts('- select -')) + $partners,null ); 
           $this->addElement('text', 'alumni_last_name_'.$i, ts('Last Name'), null );
           $this->addElement('text', 'alumni_first_name_'.$i, ts('First Name'), null );
           $this->addElement('date', 'alumni_class_year_'.$i, ts('Class Year'),CRM_Core_SelectValues::date( 'custom',25, 25, "Y" ));
           $this->addElement('text', 'alumni_relationship_'.$i, ts('Relationship'), null );
           
        }
        for($i=1;$i<=6;$i++) {
            $this->addElement('select','employee_partner_institution_id_'.$i,ts('Partner Institution') ,
                              array('' => ts('- select -')) + $partners ,null ); 
            
            $this->addElement('text', 'employee_last_name_'.$i, ts('Last Name'), null );
            $this->addElement('text', 'employee_first_name_'.$i, ts('First Name'), null );
            $this->addElement('text', 'employee_department_'.$i, ts('Department'), null );
            $this->addElement('text', 'employee_relationship_'.$i, ts('Relationship'), null );
            
        }
        
        // did parent graduate from college
        $this->addYesNo( 'parent_grad_college_id',
                         ts( 'Have either of your parents/guardians graduated from a four-year college?' ),1,true );
        
        // wheather dismissed
        $extra4 = array('onchange' => "return showHideByValue('is_dismissed', '1', 'explain_dismissed','table-row', 'radio', false);");
        $this->addYesNo( 'is_dismissed',
                         ts( 'Have you ever violated an Honor code, or been dismissed, suspended from school, put on probation or subjected to any school-related or legal disciplinary action?' ),null,false, $extra4 );
        
        $this->addElement('textarea', 'explain_dismissed', ts( 'Please explain' ), $attributes['explain_dismissed'] );
        // wheather convicted
        $extra5 = array('onchange' => "return showHideByValue('is_convicted', '1', 'explain_convicted','table-row', 'radio', false);");
        $this->addYesNo( 'is_convicted',
                         ts( 'Have you ever been been convicted of a crime, had a criminal charge sustained against you in a juvenile proceeding, or been placed on court-supervised probation?' ),null,false, $extra5);
        
        $this->addElement('textarea', 'explain_convicted', ts( 'Please explain' ), $attributes['explain_convicted'] );
        
        
        
        $this->addFormRule(array('CRM_Quest_Form_MatchApp_Scholarship', 'formRule'));
        parent::buildQuickForm( );   

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
            $params['heard_about_qb_name'] = $params['heard_about_qb_name_'.$params['heard_about_qb_id']];  
            $ids = array( 'id'         => $this->_studentID,
                          'contact_id' => $this->_contactID );
            $student = CRM_Quest_BAO_Student::create( $params, $ids);

            //delete prvious records before iserting new
            if ( is_array($this->_referralIDs) ) {
                foreach ( $this->_referralIDs as $key => $referralID ) {
                    $dao     = & new CRM_Quest_DAO_Referral();
                    $dao->id = $referralID;
                    $dao->delete();
                }
            }
           
            if ( is_array($this->_alumnusIDS) ) {
                foreach ( $this->_alumnusIDS as $key => $relativeID) {
                    $dao     = & new CRM_Quest_DAO_PartnerRelative();
                    $dao->id = $relativeID;
                    $dao->delete();
                }
            }
            
            if ( is_array($this->_employeeIDS) ) {
                foreach ($this->_employeeIDS as $key => $relativeID) {
                    $dao     = & new CRM_Quest_DAO_PartnerRelative();
                    $dao->id = $relativeID;
                    $dao->delete();
                }
            }
    
            $referrel = array( "student","educator");
            foreach (  $referrel as $value ) {
                for ($i=1;$i<=3;$i++) {  
                      $ids = array();
                      $referralParams = array();
                      $referralParams['contact_id'] = $this->_contactID;
                      if ($params['referral_'.$value.'_first_name_'.$i] || $params['referral_'.$value.'_email_'.$i]) {
                          $referralParams['referral_type']  = ucwords($value);
                          $referralParams['application_id'] = 349;
                          $referralParams['first_name']     = $params['referral_'.$value.'_first_name_'.$i]; 
                          $referralParams['last_name']     = $params['referral_'.$value.'_last_name_'.$i]; 
                          $referralParams['school']         = $params['referral_'.$value.'_school_'.$i]; 
                          $referralParams['email']          = $params['referral_'.$value.'_email_'.$i];
                          $referralParams['phone']          = $params['referral_'.$value.'_phone_'.$i];
                          $referralParams['position_id']    = $params['referral_'.$value.'_position_id_'.$i];
                          $referralParams['high_school_grad_year']    = CRM_Utils_Date::format($params['referral_'.$value.'_year_'.$i]);
                          $referral = CRM_Quest_BAO_Referral::create( $referralParams, $ids );
                      }

                }
            }
            
            require_once 'CRM/Quest/BAO/Partner.php';
            for ($i=1;$i<=6;$i++) {  
                $ids = array();
                $alumnusParams = array();
                $alumnusParams['contact_id'] = $this->_contactID;
                if ($params['alumni_partner_institution_id_'.$i] ) {
                    $alumnusParams['connection_type']  = 'Alumnus';
                    $alumnusParams['partner_id']       = $params['alumni_partner_institution_id_'.$i];
                    $alumnusParams['first_name']       = $params['alumni_first_name_'.$i];    
                    $alumnusParams['last_name']        = $params['alumni_last_name_'.$i]; 
                    $alumnusParams['relationship']     = $params['alumni_relationship_'.$i]; 
                    $alumnusParams['college_grad_year']= $params['alumni_class_year_'.$i]['Y'];
                    $alumnus = CRM_Quest_BAO_Partner::createRelative( $alumnusParams, $ids );
                }
            }

            for ($i=1;$i<=6;$i++) {  
                $ids = array();
                $employeeParams = array();
                $employeeParams['contact_id'] = $this->_contactID;
                if ($params['employee_partner_institution_id_'.$i] ) {
                    $employeeParams['connection_type']  = 'Employee';
                    $employeeParams['partner_id']       = $params['employee_partner_institution_id_'.$i];
                    $employeeParams['first_name']       = $params['employee_first_name_'.$i];    
                    $employeeParams['last_name']        = $params['employee_last_name_'.$i]; 
                    $employeeParams['relationship']     = $params['employee_relationship_'.$i]; 
                    $employeeParams['department']       = $params['employee_department_'.$i];  
                    $employee = CRM_Quest_BAO_Partner::createRelative( $employeeParams, $ids );
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
