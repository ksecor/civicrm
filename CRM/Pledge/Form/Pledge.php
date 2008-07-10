<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Contribute/PseudoConstant.php';
require_once 'CRM/Custom/Form/CustomData.php';
require_once 'CRM/Contribute/Form/AdditionalInfo.php';

/**
 * This class generates form components for processing a pledge 
 * 
 */
class CRM_Pledge_Form_Pledge extends CRM_Core_Form
{
    public $_action;
    
    /**
     * the id of the pledge that we are proceessing
     *
     * @var int
     * @public
     */
    public $_id;
    
    /**
     * the id of the contact associated with this pledge
     *
     * @var int
     * @public
     */
    public $_contactID;
    
    /**
     * The Pledge values if an existing pledge
     * @public
     */
    public $_values;

    /**
     * stores the honor id
     *
     * @var int
     * @public 
     */ 
    public $_honorID = null ;
    
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    public function preProcess()  
    {  
        // check for edit permission
        if ( ! CRM_Core_Permission::check( 'edit pledges' ) ) {
            CRM_Core_Error::fatal( ts( 'You do not have permission to access this page' ) );
        }
        
//         $this->_cdType     = CRM_Utils_Array::value( 'type', $_GET );
//         $this->assign('cdType', false);
//         if ( $this->_cdType ) {
//             $this->assign('cdType', true);
//             return CRM_Custom_Form_CustomData::preProcess( $this );
//         }
        
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, true );
        $this->_action    = CRM_Utils_Request::retrieve( 'action', 'String',
                                                         $this, false, 'add' );
        $this->assign( 'action', $this->_action );
        $this->_id        = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );
        
        require_once 'CRM/Contact/BAO/Contact/Location.php';
        list( $this->userDisplayName, 
              $this->userEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $this->_contactID );
        $this->assign( 'displayName', $this->userDisplayName );
        
        //set the post url
        $postURL = CRM_Utils_System::url( 'civicrm/contact/view',
                                          "reset=1&force=1&cid={$this->_contactID}&selectedChild=pledge" );
        $session =& CRM_Core_Session::singleton( ); 
        $session->pushUserContext( $postURL );
        
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return;
        }
        
        $this->_values = array( );
        // current pledge id
        if ( $this->_id ) {
            //get the contribution id
            $this->_contributionID = CRM_Core_DAO::getFieldValue( 'CRM_Pledge_DAO_Payment',
                                                                  $this->_id, 'contribution_id', 'pledge_id' );
            $ids    = array( );
            $params = array( 'id' => $this->_id );
            require_once "CRM/Pledge/BAO/Pledge.php";
            CRM_Pledge_BAO_Pledge::getValues( $params, $this->_values, $ids );
        }
        
//         // when custom data is included in this page
//         if ( CRM_Utils_Array::value( "hidden_custom", $_POST ) ) {
//             CRM_Custom_Form_Customdata::preProcess( $this );
//             CRM_Custom_Form_Customdata::buildQuickForm( $this );
//             CRM_Custom_Form_Customdata::setDefaultValues( $this );
//         }
        
        // also set the post url
        $postURL = CRM_Utils_System::url( 'civicrm/contact/view',
                                          "reset=1&force=1&cid={$this->_contactID}&selectedChild=pledge" );
        $session =& CRM_Core_Session::singleton( ); 
        $session->pushUserContext( $postURL );
    }
    
    /**
     * This function sets the default values for the form. 
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
//         if ( $this->_cdType ) {
//             return CRM_Custom_Form_CustomData::setDefaultValues( $this );
//         }
        
        $defaults = $this->_values;
        $fields   = array( );
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return $defaults;
        }
        
        if (  CRM_Utils_Array::value( 'is_test', $defaults ) ) {
            $this->assign( "is_test" , true );
        } 
        $frequencyUnit = array_search('monthly',CRM_Core_OptionGroup::values("recur_frequency_units"));
       
        //default values.
        if ( !$this->_id ) { 
            $now = date("Y-m-d");
            $defaults['create_date']             = $now;
            $defaults['start_date']              = $now;
            $defaults['installments']            = 1;
            $defaults['frequency_day']           = 3;
            $defaults['initial_reminder_day']    = 5;
            $defaults['max_reminders']           = 1;
            $defaults['additional_reminder_day'] = 5;
            $defaults['frequency_unit']          = $frequencyUnit;
            $defaults['status_id']               = array_search( 'Pending', CRM_Contribute_PseudoConstant::contributionStatus());
            $defaults['contribution_type_id']    = array_search( 'Donation', CRM_Contribute_PseudoConstant::contributionType());
        }
        
        //honoree contact.
        if ( isset ( $defaults["honor_contact_id"] ) ) {
            require_once 'CRM/Contact/BAO/Contact.php';
            $honorDefault = array();
            $this->_honorID = $defaults["honor_contact_id"];
            $idParams = array( 'id' => $defaults["honor_contact_id"], 'contact_id' => $defaults["honor_contact_id"] );
            CRM_Contact_BAO_Contact::retrieve( $idParams, $honorDefault, $ids );
            $honorType = CRM_Core_PseudoConstant::honor( );   
            $defaults["honor_prefix_id"]  = $honorDefault["prefix_id"];
            $defaults["honor_first_name"] = CRM_Utils_Array::value( "first_name", $honorDefault );
            $defaults["honor_last_name"]  = CRM_Utils_Array::value( "last_name", $honorDefault );
            $defaults["honor_email"]      = CRM_Utils_Array::value( "email", $honorDefault["location"][1]["email"][1] );
            $defaults["honor_type"]       = $honorType[$defaults["honor_type_id"]];
        }
        
        $this->assign( 'email', $this->userEmail );
        
        return $defaults;
    }
    
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    {   
//         if ( $this->_cdType ) {
//             return CRM_Custom_Form_CustomData::buildQuickForm( $this );
//         }
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            $this->addButtons(array( 
                                    array ( 'type'      => 'next', 
                                            'name'      => ts('Delete'), 
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                            'isDefault' => true   ), 
                                    array ( 'type'      => 'cancel', 
                                            'name'      => ts('Cancel') ), 
                                    ) 
                              );
            return;
        }
        
//         //need to assign custom data type and subtype to the template
//         $this->assign('customDataType', 'Pledge');
//         $this->assign('entityId',  $this->_id );
        
        $showAdditionalInfo = false;
        $this->_formType = CRM_Utils_Array::value( 'formType', $_GET );
        
        $paneNames =  array ( 'Honoree Information' => 'Honoree', 
                              'Payment Reminders'   => 'PaymentReminders'
                              );
        foreach ( $paneNames as $name => $type ) {
            $urlParams = "snippet=1&formType={$type}";
            $allPanes[$name] = array( 'url'  => CRM_Utils_System::url( 'civicrm/contact/view/pledge', $urlParams ),
                                      'open' => 'false',
                                      'id'   => $type,
                                      );
            //see if we need to include this paneName in the current form
            if ( $this->_formType == $type ||
                 CRM_Utils_Array::value( "hidden_{$type}", $_POST ) ||
                 CRM_Utils_Array::value( "hidden_{$type}", $defaults ) ) {
                $showAdditionalInfo = true;
                $allPanes[$name]['open'] = 'true';
                eval( 'CRM_Contribute_Form_AdditionalInfo::build' . $type . '( $this );' );
            }
        }
        
        $this->assign( 'allPanes', $allPanes );
        $this->assign( 'dojoIncludes', "dojo.require('civicrm.TitlePane');dojo.require('dojo.parser');" );
        $this->assign( 'showAdditionalInfo', $showAdditionalInfo );
        
        if ( $this->_formType ) {
            $this->assign('formType', $this->_formType );
            return;
        }
        
        $this->applyFilter('__ALL__', 'trim');
        //pledge fields.
        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Pledge_DAO_Pledge' );
        $element =& $this->add( 'text', 'amount', ts('Total Pledge Amount'),
                                $attributes['amount'], true );
        $this->addRule( 'amount', ts('Please enter a valid monetary amount.'), 'money');
        if ( $this->_id ) {
            $element->freeze( );
        }
        
        $element =& $this->add( 'text', 'installments', ts('To be paid in'), $attributes['installments'], true, array('onkeyup' => "calculatedPaymentAmount( );") );
        $this->addRule('installments', ts('Please enter a valid number of installments.'), 'integer');
        if ( $this->_id ) {
            $element->freeze( );
        }
        $frequencyUnit = CRM_Core_OptionGroup::values("recur_frequency_units");
     
        $element =& $this->add( 'select', 'frequency_unit', 
                                ts( 'Frequency' ), 
                                array(''=>ts( '- select -' )) + $frequencyUnit, 
                                true, array('onkeyup' => "calculatedPaymentAmount( );") );
                                
        if ( $this->_id ) {
            $element->freeze( );
        }
        
        $element =& $this->add( 'text', 'frequency_day', ts('Payments are due on the'), $attributes['frequency_day'], true );
        $this->addRule('frequency_day', ts('Please enter a valid payment due day.'), 'integer');
        if ( $this->_id ) {
            $element->freeze( );
        }
        
        $this->add( 'text', 'eachPaymentAmount', ts('each'), array('size'=>10, 'style'=> "background-color:#EBECE4", 'READONLY') );

        //add various dates
        $element =& $this->add('date', 'create_date', ts('Pledge Made'), CRM_Core_SelectValues::date('activityDate'));    
        $this->addRule('create_date', ts('Select a valid date for the day the pledge was made.'), 'qfDate');
        if ( $this->_id ) {
            $eachPaymentAmount = $this->_values['amount'] / $this->_values['installments'];
            $this->assign("eachPaymentAmount" , $eachPaymentAmount );
            $this->assign("hideCalender" , true );
            $element->freeze( );
        }
        
        $element =& $this->addElement('date', 'start_date', ts('Payments Start'), CRM_Core_SelectValues::date('activityDate')); 
        $this->addRule('start_date', ts('Select a valid payments start date.'), 'qfDate');
        if ( $this->_id ) {
            $element->freeze( );
        }
        
        $this->addElement('checkbox','is_acknowledge', ts('Send Acknowledgment?'),null, array('onclick' =>"return showHideByValue('is_acknowledge','','acknowledgeDate','table-row','radio',true);") );
        
        $this->addElement('date', 'acknowledge_date', ts('Acknowledgment Date'), CRM_Core_SelectValues::date('activityDate')); 
        $this->addRule('acknowledge_date', ts('Select a valid date.'), 'qfDate');
        
        $element =& $this->add('select', 'contribution_type_id', 
                               ts( 'Contribution Type' ), 
                               array(''=>ts( '- select -' )) + CRM_Contribute_PseudoConstant::contributionType( ),
                               true );
        
        $this->add('select', 'status_id',
                   ts('Pledge Status'), 
                   CRM_Contribute_PseudoConstant::contributionStatus( ),
                   false, array(
                                'onClick'  => "if (this.value != 3) status(); else return false",
                                'onChange' => "return showHideByValue('status_id','3','cancelDate','table-row','select',false);")); 
        $this->addElement('date', 'cancel_date', ts('Cancelled Date'), CRM_Core_SelectValues::date('activityDate')); 
        $this->addRule('cancel_date', ts('Select a valid date.'), 'qfDate');
        
        $session = & CRM_Core_Session::singleton( );
        $uploadNames = $session->get( 'uploadNames' );
        if ( is_array( $uploadNames ) && ! empty ( $uploadNames ) ) {
            $buttonType = 'upload';
        } else {
            $buttonType = 'next';
        }      
        
        $this->addButtons(array( 
                                array ( 'type'      => $buttonType, 
                                        'name'      => ts('Save'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'js'        => array( 'onclick' => "return verify( );" ),
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );
        
        $this->addFormRule( array( 'CRM_Pledge_Form_Pledge', 'formRule' ), $this );
        
        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $this->freeze( );
        }
    }
    
    /**  
     * global form rule  
     *  
     * @param array $fields  the input form values  
     * @param array $files   the uploaded files if any  
     * @param array $options additional user data  
     *  
     * @return true if no errors, else array of errors  
     * @access public  
     * @static  
     */  
    static function formRule( &$fields, &$files, $self ) 
    {  
        $errors = array( );
        if ( isset( $fields["honor_type_id"] ) ) {
            if ( !((  CRM_Utils_Array::value( 'honor_first_name', $fields ) && 
                      CRM_Utils_Array::value( 'honor_last_name' , $fields )) ||
                   CRM_Utils_Array::value( 'honor_email' , $fields ) )) {
                $errors['honor_first_name'] = ts('Honor First Name and Last Name OR an email should be set.');
            }
        }
        return $errors;
    }
    
    /** 
     * Function to process the form 
     * 
     * @access public 
     * @return None 
     */ 
    public function postProcess( )  
    {
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            require_once 'CRM/Pledge/BAO/Pledge.php';
            CRM_Pledge_BAO_Pledge::deletePledge( $this->_id );
            return;
        }
        
        //get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );
        $config  =& CRM_Core_Config::singleton( );
        $session =& CRM_Core_Session::singleton( );
        
        $fields = array( 'status_id',
                         'frequency_unit',
                         'frequency_day',
                         'installments',
                         'eachPaymentAmount',
                         'contribution_type_id',
                         'initial_reminder_day',
                         'max_reminders',
                         'additional_reminder_day',
                         'honor_type_id',
                         'honor_prefix_id',
                         'honor_first_name',
                         'honor_last_name',
                         'honor_email'
                         );
        foreach ( $fields as $f ) {
            $params[$f] = CRM_Utils_Array::value( $f, $formValues );
        }

        //format amount
        $params['amount'] = CRM_Utils_Rule::cleanMoney( CRM_Utils_Array::value( 'amount', $formValues ) );
        
        $dates = array( 'create_date', 'start_date', 'acknowledge_date', 'cancel_date' );
        foreach ( $dates as $d ) {
            if ( ! CRM_Utils_System::isNull( $formValues[$d] ) ) {
                $formValues[$d]['H'] = '00';
                $formValues[$d]['i'] = '00';
                $formValues[$d]['s'] = '00';             
                if ( $d == 'start_date' ) {
                    $params['scheduled_date'] =  $formValues[$d];
                }
                $params[$d] = CRM_Utils_Date::format( $formValues[$d] );
                
            } else {
                $params[$d] = 'null';
            }
        }
        if ( $formValues['is_acknowledge'] ) {
            $params['acknowledge_dat'] = date("Y-m-d");
        }
        if ( $params["status_id"] == 3 ) {
            if ( CRM_Utils_System::isNull( CRM_Utils_Array::value( 'cancel_date', $params ) ) ) {
                $params['cancel_date'] = date("Y-m-d");
            }
        } else { 
            $params['cancel_date']   = 'null';
        }
        
        $params['id'] = $this->_id;
        $params['contact_id'] = $this->_contactID;
        
        //handle Honoree contact.
        if ( CRM_Utils_Array::value( 'honor_type_id', $params ) ) {
            require_once 'CRM/Contribute/BAO/Contribution.php';
            if ( $this->_honorID ) {
                $honorID = CRM_Contribute_BAO_Contribution::createHonorContact( $params , $this->_honorID );
            } else {
                $honorID = CRM_Contribute_BAO_Contribution::createHonorContact( $params );
            }
            $params["honor_contact_id"] = $honorID;
        } else {
            $params["honor_contact_id"] = 'null';
        }
      
        require_once 'CRM/Pledge/BAO/Pledge.php';
        $pledge =& CRM_Pledge_BAO_Pledge::create( $params ); 
        $this->_id = $pledge->id;
        
        //handle Acknowledgment.
        if ( CRM_Utils_Array::value( 'is_acknowledge', $formValues ) ) {
            self::sendAcknowledgment( $params, $pledge );
        }
        
        //set the status msg.
        if ( $this->_action & CRM_Core_Action::UPDATE ) { 
            if ( $params['status_id'] == array_search( 'Cancelled', CRM_Contribute_PseudoConstant::contributionStatus()) ) {
                $statusMsg = ts('Pledge has been Cancelled and all scheduled (not completed) payments have been cancelled.<br />');
            } else {
                $statusMsg = ts('Pledge has been updated.<br />');
            }
        } else {
            $statusMsg = ts('Pledge has been recorded and payment schedule has been created.<br />');
        }
        
        //build the urls.
        $urlParams  = "reset=1&action=add&cid={$this->_contactID}&ppid={$this->_id}&context=pledge";
        $contribURL = CRM_Utils_System::url( 'civicrm/contact/view/contribution', $urlParams );
        $urlParams .= "&mode=live";
        $creditURL  = CRM_Utils_System::url( 'civicrm/contact/view/contribution', $urlParams );
        
        if ( CRM_Utils_Array::value( 'is_acknowledge', $formValues ) ) {
            $statusMsg .= ' ' . ts( "An acknowledgment email has been sent to %1.<br />", array( 1 => $this->userEmail ) );
            $statusMsg .= ' ' . ts( "If a payment is due now, you can record <a href='%1'>a Cash or Check payment for this pledge</a> OR <a href='%2'>submit a credit card payment</a>.", array( 1 =>$contribURL, 2 => $creditURL ) );
        }
        CRM_Core_Session::setStatus( $statusMsg );
        
    }
    
    /** 
     * Function to send Acknowledgment and create activity.
     * 
     * @param array  $params (reference ) an assoc array of name/value pairs.
     * @param object $pledge object of created pledge.
     * @access public. 
     * @return None.
     */ 
    function sendAcknowledgment( $params, &$pledge )
    {
        //assign values to templates
        $this->assignToTemplate( $params );
        
        require_once 'CRM/Contact/BAO/Contact.php';
        list( $pledgerDisplayName, 
              $pledgerEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $params['contact_id'] );
        $template =& CRM_Core_Smarty::singleton( );
        $message = $template->fetch( 'CRM/Pledge/Form/AcknowledgeMessage.tpl' );
        $session =& CRM_Core_Session::singleton( );
        $userID = $session->get( 'userID' );
        list( $userName, $userEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $userID );
        $receiptFrom = '"' . $userName . '" <' . $userEmail . '>';
        $subject = $template->fetch( 'CRM/Pledge/Form/AcknowledgeSubject.tpl' );
        
        require_once 'CRM/Utils/Mail.php';
        CRM_Utils_Mail::send( $receiptFrom,
                              $pledgerDisplayName,
                              $pledgerEmail,
                              $subject,
                              $message);
        
        //check if activity record exist for this pledge
        //Acknowledgment, if exist do not add activity.
        require_once "CRM/Activity/DAO/Activity.php";
        $activityType = 'Pledge Acknowledgment';
        $activity =& new CRM_Activity_DAO_Activity( );
        $activity->source_record_id = $this->_id;
        $activity->activity_type_id = CRM_Core_OptionGroup::getValue( 'activity_type',
                                                                      $activityType,
                                                                      'name' );
        if ( ! $activity->find( ) ) {
            $activityParams = array( 'subject'            => $subject,
                                     'source_contact_id'  => $params['contact_id'],
                                     'source_record_id'   => $pledge->id,
                                     'activity_type_id'   => CRM_Core_OptionGroup::getValue( 'activity_type',
                                                                                             $activityType,
                                                                                             'name' ),
                                     'activity_date_time' => CRM_Utils_Date::isoToMysql( $pledge->acknowledge_date ),
                                     'is_test'            => $pledge->is_test,
                                     'status_id'          => 2
                                     );
            require_once 'api/v2/Activity.php';
            if ( is_a( civicrm_activity_create( $activityParams ), 'CRM_Core_Error' ) ) {
                CRM_Core_Error::fatal("Failed creating Activity for acknowledgment");
            }
        }
    }
    
    /** 
     * assign the minimal set of variables to the template
     *                                                           
     * @return void
     * @access public 
     */ 
    function assignToTemplate( $params ) 
    {
        //assign pledge fields.
        $pledgeFields = array( 'create_date', 'amount', 'eachPaymentAmount', 'frequency_interval', 
                               'frequency_unit', 'installments', 'frequency_day', );
        foreach ( $pledgeFields as $field ) {
            if ( CRM_Utils_Array::value( $field, $params ) ) {
                $this->assign( $field, $params[$field] );
            }
        }
        
        //assign honor fields.
        $honor_block_is_active = false;
        //make sure we have values for it
        if (  CRM_Utils_Array::value( 'honor_type_id', $params ) &&
              ( ( ! empty( $params["honor_first_name"] ) && ! empty( $params["honor_last_name"] ) ) ||
                ( ! empty( $params["honor_email"] ) ) ) ) {
            $honor_block_is_active = true;
            $this->assign("honor_block_title", $this->_values['honor_block_title']);
            require_once "CRM/Core/PseudoConstant.php";
            $prefix = CRM_Core_PseudoConstant::individualPrefix();
            $honor  = CRM_Core_PseudoConstant::honor( );             
            $this->assign("honor_type",$honor[$params["honor_type_id"]]);
            $this->assign("honor_prefix",$prefix[$params["honor_prefix_id"]]);
            $this->assign("honor_first_name",$params["honor_first_name"]);
            $this->assign("honor_last_name",$params["honor_last_name"]);
            $this->assign("honor_email",$params["honor_email"]);
        }
        $this->assign('honor_block_is_active', $honor_block_is_active );
        
        //handle domain token values
        require_once 'CRM/Core/BAO/Domain.php';
        $domain =& CRM_Core_BAO_Domain::getDomain( );
        $tokens = array ( 'domain'  => array( 'name', 'phone', 'address', 'email'),
                          'contact' => CRM_Core_SelectValues::contactTokens());
        require_once 'CRM/Utils/Token.php';
        foreach( $tokens['domain'] as $token ){ 
            $domainValues[$token] = CRM_Utils_Token::getDomainTokenReplacement( $token, $domain );
        }
        $this->assign('domain', $domainValues );
        
        //handle contact token values.
        require_once 'CRM/Contact/BAO/Contact.php';
        require_once 'CRM/Mailing/BAO/Mailing.php';
        $ids = array( $this->_contactID );
        $fields = array_merge( array_keys(CRM_Contact_BAO_Contact::importableFields( ) ),
                               array( 'display_name', 'checksum', 'contact_id'));
        foreach( $fields as $key => $val) {
            $returnProperties[$val] = true;
        }
        $details =  CRM_Mailing_BAO_Mailing::getDetails( $ids, $returnProperties );
        $this->assign('contact', $details[0][$this->_contactID] );
        
    }
    
}

