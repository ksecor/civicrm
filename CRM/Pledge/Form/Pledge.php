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
    public $_honorID = null;
    
    /**
     * The Pledge frequency Units
     * @public
     */
    public $_freqUnits;
    
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
        
        //handle custom data.
        $this->_cdType = CRM_Utils_Array::value( 'type', $_GET );
        $this->assign('cdType', false);
        if ( $this->_cdType ) {
            $this->assign('cdType', true);
            return CRM_Custom_Form_CustomData::preProcess( $this );
        }
        
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
            $params = array( 'id' => $this->_id );
            require_once "CRM/Pledge/BAO/Pledge.php";
            CRM_Pledge_BAO_Pledge::getValues( $params, $this->_values );
        }

        //get the pledge frequency units.
        require_once 'CRM/Core/OptionGroup.php';
        $this->_freqUnits = CRM_Core_OptionGroup::values("recur_frequency_units");
        
        //when custom data is included in this page
        if ( CRM_Utils_Array::value( "hidden_custom", $_POST ) ) {
            CRM_Custom_Form_Customdata::preProcess( $this );
            CRM_Custom_Form_Customdata::buildQuickForm( $this );
            CRM_Custom_Form_Customdata::setDefaultValues( $this );
        }
        
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
        //set default custom data.
        if ( $this->_cdType ) {
            return CRM_Custom_Form_CustomData::setDefaultValues( $this );
        }
        
        $defaults = $this->_values;
        $fields   = array( );
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return $defaults;
        }
        
        if (  CRM_Utils_Array::value( 'is_test', $defaults ) ) {
            $this->assign( "is_test" , true );
        } 
        
        if ( $this->_id ) {
            $start_date =  CRM_Utils_Array::value( 'start_date', $this->_values );
            $create_date =  CRM_Utils_Array::value( 'start_date', $this->_values );
            if ( $this->_values['acknowledge_date'] ) {
                $defaults['acknowledge_date'] = CRM_Utils_Array::value( 'acknowledge_date', $this->_values );
            }
            $this->assign( 'start_date', $start_date );
            $this->assign( 'create_date', $create_date );
            
        } else {
            //default values.
            $now = date("Y-m-d");
            $defaults['create_date']             = $now;
            $defaults['start_date']              = $now;
            $defaults['installments']            = 1;
            $defaults['frequency_day']           = 3;
            $defaults['initial_reminder_day']    = 5;
            $defaults['max_reminders']           = 1;
            $defaults['additional_reminder_day'] = 5;
            $defaults['frequency_unit']          = array_search('monthly', $this->_freqUnits );
            $defaults['contribution_type_id']    = array_search( 'Donation', CRM_Contribute_PseudoConstant::contributionType());
        }
        
        //assign status.
        $this->assign( 'status', CRM_Utils_Array::value( CRM_Utils_Array::value( 'status_id', $this->_values ),
                                                         CRM_Contribute_PseudoConstant::contributionStatus( ),
                                                         'Pending' ) );
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
        //build custom data form.
        if ( $this->_cdType ) {
            return CRM_Custom_Form_CustomData::buildQuickForm( $this );
        }
        
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
        
        //need to assign custom data type to the template
        $this->assign('customDataType', 'Pledge');
        $this->assign('entityId',  $this->_id );
        
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
        
        $js =  array( 'onblur'  => "calculatedPaymentAmount( );",
                      'onkeyup' => "calculatedPaymentAmount( );");
        
        $element =& $this->add( 'text', 'amount', ts('Total Pledge Amount'),
                                array_merge( $attributes['amount'], $js ), true );
        $this->addRule( 'amount', ts('Please enter a valid monetary amount.'), 'money');
        if ( $this->_id ) {
            $element->freeze( );
        }

        $element =& $this->add( 'text', 'installments', ts('To be paid in'), 
                                array_merge( $attributes['installments'], $js ), true ); 
        $this->addRule('installments', ts('Please enter a valid number of installments.'), 'positiveInteger');
        if ( $this->_id ) {
            $element->freeze( );
        }
        
        $element =& $this->add( 'select', 'frequency_unit', 
                                ts( 'Frequency' ), 
                                array(''=>ts( '- select -' )) + $this->_freqUnits, 
                                true );
        
        if ( $this->_id ) {
            $element->freeze( );
        }
        
        $element =& $this->add( 'text', 'frequency_day', ts('Payments are due on the'), $attributes['frequency_day'], true );
        $this->addRule('frequency_day', ts('Please enter a valid payment due day.'), 'positiveInteger');
        if ( $this->_id ) {
            $element->freeze( );
        }
        
        $this->add( 'text', 'eachPaymentAmount', ts('each'), array('size'=>10, 'style'=> "background-color:#EBECE4", 'READONLY') );

        //add various dates
        if ( !$this->_id ) {
            $element =& $this->add('date', 'create_date', ts('Pledge Made'), CRM_Core_SelectValues::date('activityDate'), true );    
            $this->addRule('create_date', ts('Select a valid date for the day the pledge was made.'), 'qfDate');

            $element =& $this->add('date', 'start_date', ts('Payments Start'), CRM_Core_SelectValues::date('activityDate'), true ); 
            $this->addRule('start_date', ts('Select a valid payments start date.'), 'qfDate');
        }
        
        if ( $this->_id ) {
            $eachPaymentAmount = $this->_values['amount'] / $this->_values['installments'];
            $this->assign("eachPaymentAmount" , $eachPaymentAmount );
            $this->assign("hideCalender" , true );
        }
        
        if ( CRM_Utils_Array::value('status_id', $this->_values) != 
             array_search( 'Cancelled', CRM_Contribute_PseudoConstant::contributionStatus( )) ) { 
          
            $this->addElement('checkbox','is_acknowledge', ts('Send Acknowledgment?'),null, array('onclick' =>"return showHideByValue('is_acknowledge','','acknowledgeDate','table-row','radio',true);") );
        }
        $this->addElement('date', 'acknowledge_date', ts('Acknowledgment Date'), CRM_Core_SelectValues::date('activityDate')); 
        $this->addRule('acknowledge_date', ts('Select a valid date.'), 'qfDate');
        
        $this->add('select', 'contribution_type_id', 
                   ts( 'Contribution Type' ), 
                   array(''=>ts( '- select -' )) + CRM_Contribute_PseudoConstant::contributionType( ),
                   true );
        $pageIds = array( );
        CRM_Core_DAO::commonRetrieveAll( 'CRM_Pledge_DAO_PledgeBlock', 'entity_table', 
                                         'civicrm_contribution_page', $pageIds, array( 'entity_id' ) );
        $pages = CRM_Contribute_PseudoConstant::contributionPage( );
        foreach ( $pageIds as $key => $value ) {
            $pledgePages[$value['entity_id']] = $pages[$value['entity_id']];
        }
        
        $ele = $this->add('select', 'contribution_page_id', ts( 'Self-service Payments Page' ), 
                          array( '' => ts( '- select -' ) ) + $pledgePages );
        if ( isset ( $this->_id ) && ( CRM_Core_DAO::getFieldValue( 'CRM_Pledge_DAO_Pledge', $this->_id, 'contribution_page_id' ) ) ) { 
            $ele->freeze();
        }
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
        if ( $fields["amount"] < 0 ) {
            $errors['amount'] = ts('Total Pledge Amount should be greater than zero.');
        }
        if ( $fields["installments"] < 0 ) {
            $errors['installments'] = ts('Installments should be greater than zero.');
        }
     
        if ( $fields["frequency_unit"] != 'week' ) {
            if ( $fields["frequency_day"] > 31 || $fields["frequency_day"] == 0 ) {
                $errors['frequency_day'] =  ts('Please enter a valid frequency day ie. 1 through 31.');
            }

        }else if ( $fields["frequency_unit"] == 'week' ) {
            if ( $fields["frequency_day"] > 7 || $fields["frequency_day"] == 0 ) {
                $errors['frequency_day'] =  ts('Please enter a valid frequency day ie. 1 through 7.');
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
            if ( !CRM_Pledge_BAO_Pledge::deletePledge( $this->_id ) ) {
                CRM_Core_Session::setStatus( ts( 'This pledge can not be deleted because there are payment records (with status completed) linked to it.' ) );
            }
            return;
        }
        
        //get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );

        $config  =& CRM_Core_Config::singleton( );
        $session =& CRM_Core_Session::singleton( );
        
        //get All Payments status types.
        $paymentStatusTypes = CRM_Contribute_PseudoConstant::contributionStatus( );
        
        $fields = array(
                         'frequency_unit',
                         'frequency_day',
                         'installments',
                         'contribution_type_id',
                         'initial_reminder_day',
                         'max_reminders',
                         'additional_reminder_day',
                         'honor_type_id',
                         'honor_prefix_id',
                         'honor_first_name',
                         'honor_last_name',
                         'honor_email',
                         'contribution_page_id'
                         );
        foreach ( $fields as $f ) {
            $params[$f] = CRM_Utils_Array::value( $f, $formValues );
        }
        
        //defaults status is "Pending".
        //if update get status.
        if ( $this->_id ) {
            $params['pledge_status_id'] = $params['status_id'] = $this->_values['status_id'];
        } else {
            $params['pledge_status_id'] = $params['status_id'] = array_search( 'Pending', $paymentStatusTypes );
        }
        //format amount
        $params['amount'] = CRM_Utils_Rule::cleanMoney( CRM_Utils_Array::value( 'amount', $formValues ) );
        
        $dates = array( 'create_date', 'start_date', 'acknowledge_date', 'cancel_date' );
        foreach ( $dates as $d ) {
            if ( $this->_id ) {
                if ( $d == 'start_date' ) {
                    $params['scheduled_date'] = CRM_Utils_Date::isoToMysql( $this->_values[$d] );
                }
                $params[$d] = CRM_Utils_Date::isoToMysql( $this->_values[$d]);
            } else if ( !CRM_Utils_System::isNull( $formValues[$d] ) ) {
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
            $params['acknowledge_date'] = date("Y-m-d");
        }
        
        // assign id only in update mode
        if ( $this->_action & CRM_Core_Action::UPDATE ) { 
            $params['id'] = $this->_id;
        }

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
        
        $params['frequency_interval'] = 1;

        //format custom data
        if ( CRM_Utils_Array::value( 'hidden_custom', $formValues ) ) {
            $params['hidden_custom'] = 1;
            //get mime type of the uploaded file
            if ( !empty($_FILES) ) {
                foreach ( $_FILES as $key => $value) {
                    $files = array( );
                    if ( $formValues[$key] ) {
                        $files['name'] = $formValues[$key];
                    }
                    if ( $value['type'] ) {
                        $files['type'] = $value['type']; 
                    }
                    $params[$key] = $files;
                }
            }
            
            $customData = array( );
            foreach ( $formValues as $key => $value ) {
                if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID( $key ) ) {
                    $params[$key] = $value;
                    CRM_Core_BAO_CustomField::formatCustomField( $customFieldId, $customData,
                                                                 $value, 'Pledge', null, $this->_id );
                }
            }
            
            if ( !empty($customData) ) {
                $params['custom'] = $customData;
            }
            
            //special case to handle if all checkboxes are unchecked
            $customFields = CRM_Core_BAO_CustomField::getFields( 'Pledge' );
            
            if ( !empty($customFields) ) {
                foreach ( $customFields as $k => $val ) {
                    if ( in_array ( $val[3], array ('CheckBox','Multi-Select') ) &&
                         ! CRM_Utils_Array::value( $k, $params['custom'] ) ) {
                        CRM_Core_BAO_CustomField::formatCustomField( $k, $params['custom'],
                                                                     '', 'Pledge', null, $this->_id );
                    }
                }
            }
        }
        
        //create pledge record.
        require_once 'CRM/Pledge/BAO/Pledge.php';
        $pledge =& CRM_Pledge_BAO_Pledge::create( $params );
        $statusMsg = null;
        
        if ( $pledge->id ) {
            //set the status msg.
            if ( $this->_action & CRM_Core_Action::ADD ) {
                $statusMsg = ts('Pledge has been recorded and the payment schedule has been created.<br />');
            } else if ( $this->_action & CRM_Core_Action::UPDATE ) {
                $statusMsg = ts('Pledge has been updated.<br />');  
            }
        }
        
        //handle Acknowledgment.
        if ( CRM_Utils_Array::value( 'is_acknowledge', $formValues ) && $pledge->id ) {
            $allPayments = $payments = array( );
            $returnProperties = array( 'status_id', 'scheduled_amount', 'scheduled_date' );
            //get all paymnets details.
            CRM_Core_DAO::commonRetrieveAll( 'CRM_Pledge_DAO_Payment', 'pledge_id', $pledge->id, $allPayments, $returnProperties );
            $paymentId = null;
            if ( !empty( $allPayments )) {
                foreach( $allPayments as $payID => $values ) {
                    $payments[$payID] = array( 'amount'   => CRM_Utils_Array::value( 'scheduled_amount', $values ),
                                               'due_date' => CRM_Utils_Array::value( 'scheduled_date', $values ) );
                    
                    //get the first valid payment id.
                    if ( !$paymentId && ($paymentStatusTypes[$values['status_id']] == 'Pending' || 
                                         $paymentStatusTypes[$values['status_id']] == 'Overdue' ) ) {
                        $paymentId = $values['id'];
                    }
                }
            }
            
            //assign all payments details.
            $this->assign( 'payments', $payments );
            
            //send Acknowledgment mail.
            self::sendAcknowledgment( $params, $pledge );
            
            $statusMsg .= ' ' . ts( "An acknowledgment email has been sent to %1.<br />", array( 1 => $this->userEmail ) );
            
            //build the payment urls.
            if ( $paymentId ) {
                $urlParams  = "reset=1&action=add&cid={$this->_contactID}&ppid={$paymentId}&context=pledge";
                $contribURL = CRM_Utils_System::url( 'civicrm/contact/view/contribution', $urlParams );
                $urlParams .= "&mode=live";
                $creditURL  = CRM_Utils_System::url( 'civicrm/contact/view/contribution', $urlParams );
                
                //check if we can process credit card payment.
                $processors = CRM_Core_PseudoConstant::paymentProcessor( false, false,
                                                                         "billing_mode IN ( 1, 3 )" );
                if ( count( $processors ) > 0 ) {
                    $statusMsg .= ' ' . ts( "If a payment is due now, you can record <a href='%1'>a check, EFT, or cash payment for this pledge</a> OR <a href='%2'>submit a credit card payment</a>.", array( 1 =>$contribURL, 2 => $creditURL ) );
                } else {
                    $statusMsg .= ' ' . ts( "If a payment is due now, you can record <a href='%1'>a check, EFT, or cash payment for this pledge</a>.", array( 1 =>$contribURL ) );
                }
            }
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
                                     'status_id'          => 1
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
        $pledgeFields = array( 'create_date', 'amount', 'frequency_interval', 'frequency_unit', 'installments', 'frequency_day' );
        foreach ( $pledgeFields as $field ) {
            if ( CRM_Utils_Array::value( $field, $params ) ) {
                $this->assign( $field, $params[$field] );
            }
        }
        $eachPaymentAmount = ( $params['amount'] / $params['installments'] );
        $this->assign( 'eachPaymentAmount', $eachPaymentAmount );
        
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
        
        //handle custom data.
        $showCustom = 0;
        if ( CRM_Utils_Array::value( 'hidden_custom', $params ) ) {
            $fieldIDs = array( );
            $customFieldValues = array( );
            foreach ( $params as $key => $value ) {
                if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID( $key ) ) {
                    $fieldIDs[] = $customFieldId;
                    $customFieldValues[$customFieldId] = $value;
                }
            }
            
            //get custom group and fields label.
            if ( !empty( $fieldIDs ) ) {
                require_once 'CRM/Core/BAO/CustomField.php';
                $customFields = CRM_Core_BAO_CustomField::getCustomFieldsLabel( $fieldIDs );
                $customData = array( );
                if ( !empty( $customFields ) ) {
                    foreach( $customFields as $gID => $values ) {
                        $customData[$gID]['group_id'] = $gID;
                        $customData[$gID]['group_title'] = $values['group_title'];
                        foreach ( $values['customFields'] as $fId => $val ) {
                            $customData[$gID]['customFields'][$val['field_label']] = $customFieldValues[$fId];
                        }
                    }
                }
                $showCustom = 1;
            }
            //assign custom data.
            $this->assign_by_ref( 'customData', $customData );
        }
        $this->assign( 'showCustom', $showCustom );
    }
    
}

