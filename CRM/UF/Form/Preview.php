<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components 
 * for previewing Civicrm Profile Group
 * 
 */
class CRM_UF_Form_Preview extends CRM_Core_Form
{

    /** 
     * The group id that we are editing
     * 
     * @var int 
     */ 
    protected $_gid; 
 
    /** 
     * the fields needed to build this form 
     * 
     * @var array 
     */ 
    protected $_fields; 

    /**
     * pre processing work done here.
     *
     * gets session variables for group or field id
     *
     * @param
     * @return void
     *
     * @access public
     *
     */
    function preProcess()
    {     
        require_once 'CRM/Core/BAO/UFGroup.php';
        $flag = false;
        $field = CRM_Utils_Request::retrieve('field', 'Boolean',
                                             $this, true , 0);
       
        $fid             = $this->get( 'fieldId' ); 
        $this->_gid      = $this->get( 'id' );
        
        if ($field) {
            $this->_fields   = CRM_Core_BAO_UFGroup::getFields( $this->_gid, false, null, null, null, true);
        } else {
            $this->_fields   = CRM_Core_BAO_UFGroup::getFields( $this->_gid );
        }
        
        // preview for field
        $specialFields = array ('street_address','supplemental_address_1', 'supplemental_address_2', 'city', 'postal_code', 'postal_code_suffix', 'geo_code_1', 'geo_code_2', 'state_province', 'country', 'county', 'phone', 'email', 'im' );
        
        if( $field ) {
            require_once 'CRM/Core/DAO/UFField.php';
            $fieldDAO = & new CRM_Core_DAO_UFField();
            $fieldDAO->id = $fid;
            $fieldDAO->find(true);
            
            $name = $fieldDAO->field_name;
            if ($fieldDAO->location_type_id) {
                $name .= '-' . $fieldDAO->location_type_id;
            } else if ( in_array( $name, $specialFields ) ) {
                $name .= '-Primary';
            }
            
            if ($fieldDAO->phone_type) {
                $name .= '-'.$fieldDAO->phone_type;
            }
            
            $fieldArray[$name]= $this->_fields[$name];
            $this->_fields = $fieldArray;
            if (! is_array($this->_fields[$name])) {
                $flag = true;
            }
            $this->assign('previewField',true);
        }
        if ( $flag ) {
            $this->assign('viewOnly',false);
        } else {
            $this->assign('viewOnly',true);
        }
        
        $this->set('fieldId',null);
        $this->assign("fields",$this->_fields); 
    }


    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues()
    {
        $defaults = array();
        require_once "CRM/Profile/Form.php";
        foreach ($this->_fields as $name => $field ) {
            if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($field['name'])) {
                CRM_Core_BAO_CustomField::setProfileDefaults( $customFieldID, $name, $defaults, null, CRM_Profile_Form::MODE_REGISTER );
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
    public function buildQuickForm()
    {                                             
        $scoreAttribs = array('SAT_composite', 'SAT_composite_alt', 'SAT_reading', 'SAT_math' ,'SAT_writing', 'ACT_composite', 'ACT_english', 'ACT_reading', 'ACT_math', 'ACT_science', 'PSAT_composite', 'PLAN_composite', 'household_income_total', 'household_member_count');
        
        $readers = array('cmr_first_generation_id', 'cmr_income_increase_id', 'cmr_need_id', 'cmr_grade_id', 'cmr_class_id', 'cmr_score_id', 'cmr_academic_id', 'cmr_disposition_id');
        
        $student = array('scholarship_type_id', 'highschool_gpa_id', 'household_income_efc', 'applicant_status_id', 'reader_score_avg', 'interview_rank' );
        // add the form elements
        require_once "CRM/Contribute/PseudoConstant.php";
        require_once 'CRM/Core/OptionGroup.php';

        require_once 'CRM/Core/BAO/Preferences.php';
        $addressOptions = CRM_Core_BAO_Preferences::valueOptions( 'address_options', true, null, true );

        // cache the state country fields. based on the results, we could use our javascript solution
        // in create or register mode
        $stateCountryMap = array( );

        foreach ($this->_fields as $name => $field ) {
            $required = $field['is_required'];
            
            // array build for using it for ajax state-county widget
            list( $prefixName, $index ) = CRM_Utils_System::explode( '-', $name, 2 );
            if ( $prefixName == 'state_province' || $prefixName == 'country' ) {
                if ( ! array_key_exists( $index, $stateCountryMap ) ) {
                    $stateCountryMap[$index] = array( );
                }
                $stateCountryMap[$index][$prefixName] = $name;
            }
            
            if ( substr($field['name'],0,14) === 'state_province' ) {
                $this->add('select', $name, $field['title'],
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince(), $required);
            } else if ( substr($field['name'],0,7) === 'country' ) {
                $this->add('select', $name, $field['title'], 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::country(), $required);
            } else if ( substr($field['name'],0,6) === 'county' ) {
                if ( $addressOptions['county'] ) {
                    $this->add('select', $name, $field['title'], 
                               array('' => ts('- select -')) + CRM_Core_PseudoConstant::county(), $required);
                }
            } else if ( $field['name'] === 'birth_date' ) {  
                $this->add('date', $field['name'], $field['title'], CRM_Core_SelectValues::date('birth'), $required );  
            } else if ( $field['name'] === 'is_deceased' ) {  
                $this->add('checkbox', $field['name'], $field['title'], $field['attributes'], $required );
            } else if ( $field['name'] === 'deceased_date' ) {  
                $this->add('date', $field['name'], $field['title'], CRM_Core_SelectValues::date('birth'), $required );  
            } else if ( $field['name'] === 'gender' ) {  
                $genderOptions = array( );   
                $gender = CRM_Core_PseudoConstant::gender();   
                foreach ($gender as $key => $var) {   
                    $genderOptions[$key] = HTML_QuickForm::createElement('radio', null, ts('Gender'), $var, $key);   
                }   
                $this->addGroup($genderOptions, $field['name'], $field['title'] );  
                if ($required) {
                    $this->addRule($field['name'], ts('%1 is a required field.', array(1 => $field['title'])) , 'required');
                }
            } else if ( $field['name'] === 'individual_prefix' ){
                $this->add('select', $name, $field['title'], 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::individualPrefix(), $required);
            } else if ( $field['name'] === 'individual_suffix' ){
                $this->add('select', $name, $field['title'], 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::individualSuffix(), $required);
            } else if ( $field['name'] === 'greeting_type' ) {
                $this->add('select', $name, $field['title'], 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::greeting(), $required, array( 'onchange' => "showGreeting();" ));

                //adding Custom Greeting element alongwith greeting type
                $this->add('text', 'custom_greeting', ts('Custom Greeting'), null, false);
            } else if ($field['name'] === 'preferred_communication_method') {
                $values = CRM_Core_PseudoConstant::pcm();
                foreach ( $values as $key => $var ) {
                    if ( $key == '' ) {
                        continue;
                    }
                    $options[] =& HTML_QuickForm::createElement( 'checkbox', $key, null, $var );
                }
                $this->addGroup($options, $name, $field['title'], '<br/>' );
            } else if ( substr($field['name'], 0, 2) === 'im' ) {
                $this->add('select',  $name . '-provider_id', 'IM Provider', 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::IMProvider(), $required);
                $this->add('text', $name, $field['title'], $field['attributes'], $required );
            } else if ($field['name'] === 'preferred_mail_format') {
                $this->add('select', $name, $field['title'], CRM_Core_SelectValues::pmf());
            } else if ( substr($field['name'], 0, 7) === 'do_not_' ) {  
                $this->add('checkbox', $name, $field['title'], $field['attributes'], $required );
            } else if ( $field['name'] === 'group' ) {
                require_once 'CRM/Contact/Form/GroupTag.php';
                CRM_Contact_Form_GroupTag::buildGroupTagBlock($this, $this->_id, CRM_Contact_Form_GroupTag::GROUP, true, $required, $field['title'], null );
            } else if ( $field['name'] === 'tag' ) {
                require_once 'CRM/Contact/Form/GroupTag.php';
                CRM_Contact_Form_GroupTag::buildGroupTagBlock($this, $this->_id,  CRM_Contact_Form_GroupTag::TAG, true, $required, null, $field['title'] );
            } else if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($field['name'])) {
                CRM_Core_BAO_CustomField::addQuickFormElement($this,
                                                              $name,
                                                              $customFieldID,
                                                              false,
                                                              $required,
                                                              false,
                                                              $field['title']);
            } else if ( in_array($field['name'], array('receive_date', 'receipt_date', 'thankyou_date', 'cancel_date', 'membership_expiration_date', 'membership_start_date','join_date' )) ) {  
                $this->add('date', $field['name'], $field['title'], CRM_Core_SelectValues::date('manual', 3, 1), $required );  
            } else if ($field['name'] == 'payment_instrument' ) {
                $this->add('select', 'payment_instrument', ts( 'Paid By' ),
                           array(''=>ts( '- select -' )) + CRM_Contribute_PseudoConstant::paymentInstrument( ), $required );
            } else if ($field['name'] == 'membership_type_id' ) { 
                require_once 'CRM/Member/PseudoConstant.php';
                $this->add('select', 'membership_type_id', ts( 'Membership Type' ),
                           array(''=>ts( '- select -' )) + CRM_Member_PseudoConstant::membershipType( ), $required );
            } else if ($field['name'] == 'status_id' ) { 
                require_once 'CRM/Member/PseudoConstant.php';
                $this->add('select', 'status_id', ts( 'Membership Status' ),
                           array(''=>ts( '- select -' )) + CRM_Member_PseudoConstant::membershipStatus( ), $required );
            } else if ($field['name'] == 'contribution_type' ) {
                $this->add('select', 'contribution_type', ts( 'Contribution Type' ), 
                           array(''=>ts( '- select -' )) + CRM_Contribute_PseudoConstant::contributionType( ), $required);
            } else if ( $field['name'] == 'contribution_status_id' ) {
                require_once "CRM/Contribute/PseudoConstant.php";
                $this->add('select', $name, $field['title'],
                           array(''=>ts( '- select -' )) + CRM_Contribute_PseudoConstant::contributionStatus( ), $required);
            } else if ( $field['name'] == 'participant_register_date' ) {
                require_once "CRM/Event/PseudoConstant.php";
                $this->add('date', $name, $field['title'], CRM_Core_SelectValues::date('birth'), $required );  
            } else if ($field['name'] == 'participant_status_id' ) {
                require_once "CRM/Event/PseudoConstant.php";
                $this->add('select', $name, $field['title'],
                           array(''=>ts( '- select -' )) + CRM_Event_PseudoConstant::participantStatus( ), $required);
            } else if ($field['name'] == 'participant_role_id' ) {
                require_once "CRM/Event/PseudoConstant.php";
                $this->add('select', $name, $field['title'],
                           array(''=>ts( '- select -' )) + CRM_Event_PseudoConstant::participantRole( ), $required);
            } else if ($field['name'] == 'world_region' ) {
                require_once "CRM/Core/PseudoConstant.php";
                $this->add('select', $name, $field['title'],
                           array(''=>ts( '- select -' )) + CRM_Core_PseudoConstant::worldRegion( ), $required);
            } else if ($field['name'] == 'gpa_id' ) {
                require_once 'CRM/Core/OptionGroup.php';
                $this->add('select', 'gpa_id', $field['title'],
                           array(''=>ts( '- select -' )) + CRM_Core_OptionGroup::values('gpa'), $required);
            } else if ($field['name'] == 'ethnicity_id_1' ) {
                require_once 'CRM/Core/OptionGroup.php';
                $this->add('select', 'ethnicity_id_1', $field['title'],
                           array(''=>ts( '- select -' )) + CRM_Core_OptionGroup::values('ethnicity'), $required);
            } else if ($field['name'] == 'cmr_comment' ) {
                require_once 'CRM/Core/OptionGroup.php';
                $this->add('textarea', $name, $field['title'], array('cols' => '30', 'rows' => '2'), $required);
            } else if (in_array($field['name'], $readers) || 
                       ($field['name'] == 'gpa_unweighted_calc') || ($field['name'] == 'gpa_weighted_calc')) {
                $readerParts = explode('_', $field['name']);
                for($i = 0; $i < (count($readerParts)-1); $i++) {
                    if ($i == 0) {
                        $readerGroup = $readerParts[$i];
                    } else {
                        $readerGroup = $readerGroup . '_' . $readerParts[$i];
                    }
                }
                
                $this->add('select', $field['name'], $field['title'],
                           array(''=>ts( '- select -' )) + CRM_Core_OptionGroup::values($readerGroup), $required);
            } else if ($field['name'] == 'scholarship_type_id' ) {
                $this->add('select', $field['name'], $field['title'], array( "" => "-- Select -- " )+ array_flip( CRM_Core_OptionGroup::values( 'scholarship_type', true ) ) );
            } else if ($field['name'] == 'applicant_status_id' ) {
                $this->add('select', $field['name'], $field['title'], array( "" => "-- Select -- " )+ array_flip( CRM_Core_OptionGroup::values( 'applicant_status', true ) ) );
            } else if ($field['name'] == 'highschool_gpa_id' ) {
                $this->add('select', $name, $field['title'], array( "" => "-- Select -- ") + CRM_Core_OptionGroup::values( 'highschool_gpa' ) );
            } else {
                if (in_array($field['name'], $scoreAttribs) && (! $field['attributes'])) {
                    $field['attributes'] = array('maxlength' => 8, 'size' => 4);
                }
                $this->add('text', $name, $field['title'], $field['attributes'], $required);
            }
        }
        
        if ( CRM_Utils_Array::value('email-Primary',$this->_fields ) ) {  
            $emailPresent =true;
            require_once 'CRM/Core/BAO/CMSUser.php';
            CRM_Core_BAO_CMSUser::buildForm($this,$this->_gid,$emailPresent,CRM_Core_Action::PREVIEW);
        }
        $dao = new CRM_Core_DAO_UFGroup();
        $dao->id = $this->_gid;
        $dao->find(true);
        if ( $dao->add_captcha ) {
            require_once 'CRM/Utils/ReCAPTCHA.php';
            $captcha =& CRM_Utils_ReCAPTCHA::singleton( );
            $captcha->add( $this );
            $this->assign( 'addCAPTCHA' , true );
        }
        
        // also do state country js
        require_once 'CRM/Core/BAO/Address.php';
        CRM_Core_BAO_Address::addStateCountryMap( $stateCountryMap );
        
        $this->addButtons(array(
                                array ('type'      => 'cancel',
                                       'name'      => ts('Done with Preview'),
                                       'isDefault' => true),
                                )
                          );
    }
}


