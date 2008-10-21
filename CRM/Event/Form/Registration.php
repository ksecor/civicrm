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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_Registration extends CRM_Core_Form
{
    /**
     * how many locationBlocks should we display?
     *
     * @var int
     * @const
     */
    const LOCATION_BLOCKS = 1;

    /**
     * the id of the event we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_eventId;
    
    /**
     * the mode that we are in
     * 
     * @var string
     * @protect
     */
    public $_mode;

    /**
     * the values for the contribution db object
     *
     * @var array
     * @protected
     */
    public $_values;

    /**
     * the paymentProcessor attributes for this page
     *
     * @var array
     * @protected
     */
    public $_paymentProcessor;

    /**
     * The params submitted by the form and computed by the app
     *
     * @var array
     * @protected
     */
    protected $_params;

    /** 
     * The fields involved in this contribution page
     * 
     * @var array 
     * @protected 
     */ 
    public $_fields;

    /**
     * The billing location id for this contribiution page
     *
     * @var int
     * @protected
     */
    public $_bltID;

    /**
     * Price Set ID, if the new price set method is used
     *
     * @var int
     * @protected
     */
    public $_priceSetId;

    /**
     * Array of fields for the price set
     *
     * @var array
     * @protected
     */
    public $_priceSet;
          
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) 
    {
        $this->_eventId     = CRM_Utils_Request::retrieve( 'id'    , 'Positive', $this, true  );
        $this->_action = CRM_Utils_Request::retrieve( 'action', 'String'  , $this, false );

        // current mode
        $this->_mode = ( $this->_action == 1024 ) ? 'test' : 'live';
        
        $this->_values           = $this->get( 'values' );
        $this->_fields           = $this->get( 'fields' );
        $this->_bltID            = $this->get( 'bltID'  );
        $this->_paymentProcessor = $this->get( 'paymentProcessor' );
        $this->_priceSetId       = $this->get( 'priceSetId' );
        $this->_priceSet         = $this->get( 'priceSet' ) ;

        $config  =& CRM_Core_Config::singleton( );
        
        if ( ! $this->_values ) {
            // create redirect URL to send folks back to event info page is registration not available
            $infoUrl = CRM_Utils_System::url( 'civicrm/event/info',"reset=1&id={$this->_eventId}",
                                             true, null, false, true );
            
            // this is the first time we are hitting this, so check for permissions here
            if ( ! CRM_Core_Permission::event( CRM_Core_Permission::EDIT,
                                               $this->_eventId ) ) {
                CRM_Core_Error::statusBounce( ts( 'You do not have permission to register for this event' ), $infoUrl );
            }

            // get all the values from the dao object
            $this->_values = array( );
            $this->_fields = array( );

            //retrieve event information
            $params = array( 'id' => $this->_eventId );
            $ids = array();


            require_once 'CRM/Event/BAO/Participant.php';
            $eventFull = CRM_Event_BAO_Participant::eventFull( $this->_eventId );
            if ( $eventFull ) {
                CRM_Utils_System::redirect( $infoUrl );            
            }

            require_once 'CRM/Event/BAO/Event.php';
            CRM_Event_BAO_Event::retrieve($params, $this->_values['event']);
            
            if( isset( $this->_values['event']['default_role_id'] ) ) {
                require_once 'CRM/Core/OptionGroup.php';
                $participant_role = CRM_Core_OptionGroup::values('participant_role');
                $this->_values['event']['participant_role'] = $participant_role["{$this->_values['event']['default_role_id']}"];
            }
            
            // is the event active (enabled)?
            if ( ! $this->_values['event']['is_active'] ) {
                // form is inactive, die a fatal death
                CRM_Core_Error::statusBounce( ts( 'The event you requested is currently unavailable (contact the site administrator for assistance).' ) );
            }
            
            // is online registration is enabled?
            if ( ! $this->_values['event']['is_online_registration'] ) {
                CRM_Core_Error::statusBounce( ts( 'Online registration is not currently available for this event (contact the site administrator for assistance).' ), $infoUrl );
            }
            $now = time( );

            $startDate = CRM_Utils_Date::unixTime( CRM_Utils_Array::value( 'registration_start_date',
                                                                           $this->_values['event'] ) );
            if ( $startDate &&
                 $startDate >= $now ) {
                CRM_Core_Error::statusBounce( ts( 'Registration for this event begins on %1', array( 1 => CRM_Utils_Date::customFormat( CRM_Utils_Array::value( 'registration_start_date', $this->_values['event'] ) ) ) ), $infoUrl );
            }

            $endDate = CRM_Utils_Date::unixTime( CRM_Utils_Array::value( 'registration_end_date',
                                                                         $this->_values['event'] ) );
            if ( $endDate &&
                 $endDate < $now ) {
                CRM_Core_Error::statusBounce( ts( 'Registration for this event ended on %1', array( 1 => CRM_Utils_Date::customFormat( CRM_Utils_Array::value( 'registration_end_date', $this->_values['event'] ) ) ) ), $infoUrl );
            }


            // check for is_monetary status
            $isMonetary = CRM_Utils_Array::value( 'is_monetary', $this->_values['event'] );
            
            //retrieve custom information
            $eventPageID = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_EventPage', $this->_eventId, 'id', 'event_id' );
            
            $isPayLater  = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_EventPage', $eventPageID, 'is_pay_later' );
            //check for variour combination for paylater, payment
            //process with paid event.
            if ( $isMonetary && 
                 ( ! $isPayLater || CRM_Utils_Array::value( 'payment_processor_id', $this->_values['event'] ) ) ) {
                $ppID = CRM_Utils_Array::value( 'payment_processor_id',
                                                $this->_values['event'] );
                if ( ! $ppID ) {
                    CRM_Core_Error::statusBounce( ts( 'A payment processor must be selected for this event registration page, or the event must be configured to give users the option to pay later (contact the site administrator for assistance).' ), $infoUrl );
                }
                
                require_once 'CRM/Core/BAO/PaymentProcessor.php';
                $this->_paymentProcessor =
                    CRM_Core_BAO_PaymentProcessor::getPayment( $ppID,
                                                               $this->_mode );
                
                // make sure we have a valid payment class, else abort
                if ( $this->_values['event']['is_monetary'] ) {
                    if ( ! $this->_paymentProcessor ) {
                        CRM_Core_Error::fatal( ts( 'The site administrator must set a Payment Processor for this event in order to use online registration.' ) );
                    }
                    
                    // ensure that processor has a valid config
                    $payment =& CRM_Core_Payment::singleton( $this->_mode, 'Event', $this->_paymentProcessor );
                    $error = $payment->checkConfig( );
                    if ( ! empty( $error ) ) {
                        CRM_Core_Error::fatal( $error );
                    }
                }
                
                $this->set( 'paymentProcessor', $this->_paymentProcessor );
            }
            
            self::initPriceSet( $this, $eventPageID );
            
            // get price info
            require_once 'CRM/Core/BAO/PriceSet.php';
            $priceSetId = CRM_Core_BAO_PriceSet::getFor( 'civicrm_event_page', $eventPageID );
            if ( $priceSetId ) {
                $this->_priceSetId = $priceSetId;
                $priceSet = CRM_Core_BAO_PriceSet::getSetDetail($priceSetId);

                $this->_priceSet = CRM_Utils_Array::value($priceSetId,$priceSet);
                $this->_values['custom'] = CRM_Utils_Array::value($priceSetId,$priceSet);
                $this->set('priceSetId', $this->_priceSetId);
                $this->set('priceSet', $this->_priceSet);
            } else {
                if ( ! isset( $this->_values['custom'] ) ) {
                    $this->_values['custom'] = array( );
                }
                require_once 'CRM/Core/OptionGroup.php'; 
                CRM_Core_OptionGroup::getAssoc( "civicrm_event_page.amount.{$eventPageID}", $this->_values['custom'] );
            }
            
            // get the profile ids
            require_once 'CRM/Core/BAO/UFJoin.php'; 
            $ufJoinParams = array( 'entity_table' => 'civicrm_event',   
                                   'entity_id'    => $this->_eventId );
            list( $this->_values['custom_pre_id'],
                  $this->_values['custom_post_id'] ) =
                CRM_Core_BAO_UFJoin::getUFGroupIds( $ufJoinParams ); 
    
            $params = array( 'event_id' => $this->_eventId );
            require_once 'CRM/Event/BAO/EventPage.php';
            CRM_Event_BAO_EventPage::retrieve($params, $this->_values['event_page']);
            
            // get the billing location type
            $locationTypes =& CRM_Core_PseudoConstant::locationType( );
            $this->_bltID = array_search( 'Billing',  $locationTypes );
            if ( ! $this->_bltID ) {
                CRM_Core_Error::fatal( ts( 'Please set a location type of %1', array( 1 => 'Billing' ) ) );
            }
            $this->set( 'bltID', $this->_bltID );

            if ( $this->_values['event']['is_monetary'] &&
                 ( $this->_paymentProcessor['billing_mode'] & CRM_Core_Payment::BILLING_MODE_FORM ) ) {
                require_once 'CRM/Core/Payment/Form.php';
                CRM_Core_Payment_Form::setCreditCardFields( $this );
            }
            
            $params = array( 'entity_id' => $this->_eventId ,'entity_table' => 'civicrm_event');
            require_once 'CRM/Core/BAO/Location.php';
            $location = CRM_Core_BAO_Location::getValues($params, $this->_values, true );

            $this->set( 'values', $this->_values );
            $this->set( 'fields', $this->_fields );
        }

        $this->assign_by_ref( 'paymentProcessor', $this->_paymentProcessor );

        // check if this is a paypal auto return and redirect accordingly
        if ( CRM_Core_Payment::paypalRedirect( $this->_paymentProcessor ) ) {
            $url = CRM_Utils_System::url( 'civicrm/event/register',
                                          "_qf_ThankYou_display=1&qfKey={$this->controller->_key}" );
            CRM_Utils_System::redirect( $url );
        }
        
        $this->_contributeMode = $this->get( 'contributeMode' );
        $this->assign( 'contributeMode', $this->_contributeMode );

        // setting CMS page title
        CRM_Utils_System::setTitle($this->_values['event']['title']);  
        $this->assign( 'title', $this->_values['event']['title'] );

        $this->assign('paidEvent', $this->_values['event']['is_monetary']);

        // we do not want to display recently viewed items on Registration pages
        $this->assign( 'displayRecent'  , false );
        // Registration page values are cleared from session, so can't use normal Printer Friendly view.
        // Use Browser Print instead.
        $this->assign( 'browserPrint', true  );

        // assign all event properties so wizard templates can display event info.
        $this->assign('event', $this->_values['event']);
        $this->assign('eventPage', $this->_values['event_page']);
        $this->assign('location',$this->_values['location']);
        $this->assign( 'bltID', $this->_bltID );
        $isShowLocation = CRM_Utils_Array::value('is_show_location',$this->_values['event'])  ;
        $this->assign( 'isShowLocation',$isShowLocation );
    }

    /** 
     * assign the minimal set of variables to the template
     *                                                           
     * @return void 
     * @access public 
     */ 
    function assignToTemplate( ) 
    {
        //process only primary participant params
        $this->_params = $this->get( 'params' );
        if( isset( $this->_params[0] ) ){
            $params = $this->_params[0];
        }
        $name = '';
        if ( CRM_Utils_Array::value( 'billing_first_name', $params ) ) {
            $name = $params['billing_first_name'];
        }
        
        if ( CRM_Utils_Array::value( 'billing_middle_name', $params ) ) {
            $name .= " {$params['billing_middle_name']}";
        }
        
        if ( CRM_Utils_Array::value( 'billing_last_name', $params ) ) {
            $name .= " {$params['billing_last_name']}";
        }       
        $this->assign( 'billingName', $name );
        $this->set( 'name', $name );
        
        $vars = array( 'amount', 'currencyID', 'credit_card_type', 
                       'trxn_id', 'amount_level', 'receive_date' );
        
        foreach ( $vars as $v ) {
            if ( CRM_Utils_Array::value( $v, $params ) ) { 
                if ( $v == 'receive_date' ) {
                    $this->assign( $v,  CRM_Utils_Date::mysqlToIso( $params[$v] ) );
                } else {
                    $this->assign( $v, $params[$v] );
                }
            } else if ( $params['amount'] == 0 ) {
                $this->assign( $v, $params[$v] );
            }
        }
        
        // assign the address formatted up for display
        $addressParts  = array( "street_address-{$this->_bltID}",
                                "city-{$this->_bltID}",
                                "postal_code-{$this->_bltID}",
                                "state_province-{$this->_bltID}",
                                "country-{$this->_bltID}");
        $addressFields = array( );
        foreach ($addressParts as $part) {
            list( $n, $id ) = explode( '-', $part );
            if ( isset ( $params[$part] ) ) {
                $addressFields[$n] = $params[$part];
            }
        }
        require_once 'CRM/Utils/Address.php';
        $this->assign('address', CRM_Utils_Address::format($addressFields));
        
        if ( $this->_contributeMode == 'direct' &&
             ! CRM_Utils_Array::value( 'is_pay_later', $params ) ) {
            $date = CRM_Utils_Date::format( $params['credit_card_exp_date'] );
            $date = CRM_Utils_Date::mysqlToIso( $date );
            $this->assign( 'credit_card_exp_date', $date );
            $this->assign( 'credit_card_number',
                           CRM_Utils_System::mungeCreditCard( $params['credit_card_number'] ) );
        }
        
        $this->assign( 'email', $this->controller->exportValue( 'Register', "email-{$this->_bltID}" ) );
        
        // assign is_email_confirm to templates
        if ( isset ($this->_values['event_page']['is_email_confirm'] ) ) {
            $this->assign( 'is_email_confirm', $this->_values['event_page']['is_email_confirm'] );
        }
        
        // assign pay later stuff
        $params['is_pay_later'] = CRM_Utils_Array::value( 'is_pay_later', $params, false );
        $this->assign( 'is_pay_later', $params['is_pay_later'] );
        if ( $params['is_pay_later'] ) {
            $this->assign( 'pay_later_text'   , $this->_values['event_page']['pay_later_text']    );
            $this->assign( 'pay_later_receipt', $this->_values['event_page']['pay_later_receipt'] );
        }
        
    }

    /**  
     * Function to add the custom fields
     *  
     * @return None  
     * @access public  
     */ 
    function buildCustom( $id, $name, $viewOnly = false ) 
    {
        if ( $id ) {
            $button = substr( $this->controller->getButtonName(), -4 );
            require_once 'CRM/Core/BAO/UFGroup.php';
            require_once 'CRM/Profile/Form.php';
            $session =& CRM_Core_Session::singleton( );
            $contactID = $session->get( 'userID' );
            
            $fields = null;
            if ( $contactID ) {
                if ( CRM_Core_BAO_UFGroup::filterUFGroups($id, $contactID)  ) {
                    $fields = CRM_Core_BAO_UFGroup::getFields( $id, false, CRM_Core_Action::ADD ); 
                }
            } else {
                $fields = CRM_Core_BAO_UFGroup::getFields( $id, false, CRM_Core_Action::ADD ); 
            }

            if ( is_array( $fields ) ) {
                // unset any email-* fields since we already collect it, CRM-2888
                foreach ( array_keys( $fields ) as $fieldName ) {
                    if ( substr( $fieldName, 0, 6 ) == 'email-' ) {
                        unset( $fields[$fieldName] );
                    }
                }
            }
            
            $addCaptcha = false;
            $this->assign( $name, $fields );
            foreach($fields as $key => $field) {
                if ( $viewOnly &&
                     isset( $field['data_type'] ) &&
                     $field['data_type'] == 'File' ) {
                    // ignore file upload fields
                    continue;
                }
                //make the field optional if primary participant 
                //have been skip the additional participant.
                if ( $button == 'skip' ) {
                    $field['is_required'] = false;
                } else if ( $field['add_captcha'] ) {
                    // only add captcha for first page
                    $addCaptcha = true;
                }
                CRM_Core_BAO_UFGroup::buildProfile($this, $field,CRM_Profile_Form::MODE_CREATE);
                $this->_fields[$key] = $field;
            }

            if ( $addCaptcha &&
                 ! $viewOnly ) {
                require_once 'CRM/Utils/ReCAPTCHA.php';
                $captcha =& CRM_Utils_ReCAPTCHA::singleton( );
                $captcha->add( $this );
                $this->assign( "isCaptcha" , true );
            }

        }
    }
    
    static function initPriceSet( &$form, $eventPageID ) {
        // get price info
        require_once 'CRM/Core/BAO/PriceSet.php';
        
        if ( $priceSetId = CRM_Core_BAO_PriceSet::getFor( 'civicrm_event_page', $eventPageID ) ) {
            $form->_priceSetId = $priceSetId;
            $priceSet = CRM_Core_BAO_PriceSet::getSetDetail($priceSetId);
            $form->_priceSet = CRM_Utils_Array::value($priceSetId,$priceSet);
            $form->_values['custom'] = CRM_Utils_Array::value($priceSetId,$priceSet);
            $form->set('priceSetId', $form->_priceSetId);
            $form->set('priceSet', $form->_priceSet);
        } else {
            require_once 'CRM/Core/OptionGroup.php'; 
            CRM_Core_OptionGroup::getAssoc( "civicrm_event_page.amount.{$eventPageID}", $form->_values['custom'] );
            require_once 'CRM/Core/BAO/Discount.php';
            $discountedEvent = CRM_Core_BAO_Discount::getOptionGroup( $eventPageID, "civicrm_event");
            if ( is_array( $discountedEvent ) ) {
                foreach ( $discountedEvent as $key => $optionGroupId ) {
                    $name = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionGroup', $optionGroupId );
                    CRM_Core_OptionGroup::getAssoc( $name, $form->_values['discount'][$key] );
                    $form->_values['discount'][$key]["name"] = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionGroup', $optionGroupId, 'label');;
                }
            }
        }
    }

    /**  
     * Function to handle  process after the confirmation of payment by User
     *  
     * @return None  
     * @access public  
     */ 
    function confirmPostProcess( $contactID = null, $contribution = null, $payment = null )
    {
        // add/update contact information
        $fields = array( );
        unset($this->_params['note']);

        //to avoid conflict overwrite $this->_params
        $this->_params = $this->get('value');
              
        // create CMS user
        if ( CRM_Utils_Array::value( 'cms_create_account', $this->_params ) ) {
            $this->_params['contactID'] = $contactID;
            require_once "CRM/Core/BAO/CMSUser.php";
            //in case of Pay later option we skipped 'email-5' so we should use 'email-Primary'
            if ( ! CRM_Core_BAO_CMSUser::create( $this->_params, 'email-Primary' ) ) {
                CRM_Core_Error::statusBounce( ts('Your profile is not saved and Account is not created.') );
            }
        }
        //get the amount of primary participant
        if( CRM_Utils_Array::value('is_primary', $this->_params ) ) {
            $this->_params['fee_amount'] = $this->get( 'primaryParticipantAmount' );
        }
        // add participant record
        $participant  = $this->addParticipant( $this->_params, $contactID );

        //setting register_by_id field and primaryContactId
        if( CRM_Utils_Array::value('is_primary', $this->_params ) ) {
            $this->set( 'registerByID', $participant->id );
            $this->set( 'primaryContactId', $contactID );
        }
        require_once 'CRM/Core/BAO/CustomValueTable.php';
        CRM_Core_BAO_CustomValueTable::postProcess( $this->_params,
                                                    CRM_Core_DAO::$_nullArray,
                                                    'civicrm_participant',
                                                    $participant->id,
                                                    'Participant' );
        

   
        if ( $this->_values['event']['is_monetary'] && ( $this->_params['amount'] != 0 )
                                                         &&  CRM_Utils_Array::value( 'contributionID', $this->_params ) ) {
            require_once 'CRM/Event/BAO/ParticipantPayment.php';
            $paymentParams = array( 'participant_id'  => $participant->id ,
                                    'contribution_id' => $contribution->id, ); 
            $ids = array();       
            
            $paymentPartcipant = CRM_Event_BAO_ParticipantPayment::create($paymentParams, $ids);
        }
        //set only primary participant's params for transfer checkout.
        if ( ($this->_contributeMode == 'checkout'||  $this->_contributeMode == 'notify') 
             && CRM_Utils_Array::value( 'is_primary', $this->_params ) ) {
            $this->_params['participantID'] = $participant->id;
            $this->set ( 'primaryParticipant',  $this->_params );
        } 
        $this->assign('action',$this->_action); 
    }

    /**
     * Process the participant 
     *
     * @return void
     * @access public
     */
    public function addParticipant( $params, $contactID ) 
    {
        require_once 'CRM/Core/Transaction.php';
      
        $transaction = new CRM_Core_Transaction( );
        
        $groupName = "participant_role";
        $query = "
SELECT  v.label as label ,v.value as value
FROM   civicrm_option_value v, 
       civicrm_option_group g 
WHERE  v.option_group_id = g.id 
  AND  g.name            = %1 
  AND  v.is_active       = 1  
  AND  g.is_active       = 1  
";
        $p = array( 1 => array( $groupName , 'String' ) );
               
        $dao =& CRM_Core_DAO::executeQuery( $query, $p );
        if ( $dao->fetch( ) ) {
            $roleID = $dao->value;
        }
        
        $participantParams = array('contact_id'    => $contactID,
                                   'event_id'      => $this->_eventId ? $this->_eventId : $params['event_id'],
                                   'status_id'     => CRM_Utils_Array::value( 'participant_status_id',
                                                                              $params, 1 ),
                                   'role_id'       => CRM_Utils_Array::value( 'participant_role_id',
                                                                              $params, $roleID ),
                                   'register_date' => isset( $params['participant_register_date'] ) ?
                                   CRM_Utils_Date::format( $params['participant_register_date'] ) :
                                   date( 'YmdHis' ),
                                   'source'        => isset( $params['participant_source'] ) ?
                                                      $params['participant_source']:$params['description'],
                                   'fee_level'     => $params['amount_level'],
                                   'is_pay_later'  => CRM_Utils_Array::value( 'is_pay_later', $params, 0 ),
                                   'fee_amount'    => CRM_Utils_Array::value( 'fee_amount', $params ),
                                   'registered_by_id' => $params['registered_by_id'],
                                   'discount_id'    => $params['discount_id']
                                   );
       
        if ( $this->_action & CRM_Core_Action::PREVIEW || $params['mode'] == 'test' ) {
            $participantParams['is_test'] = 1;
        } else {
            $participantParams['is_test'] = 0;
        }

        if ( $this->_params['note'] ) {
            $participantParams['note'] = $this->_params['note'];
        } else if ( $this->_params['participant_note'] ) {
            $participantParams['note'] = $this->_params['participant_note'];
        }
        
        // reuse id if one already exists for this one (can happen
        // with back button being hit etc)
        if ( $this->_eventId ) {        
            $sql = "
SELECT id
FROM   civicrm_participant
WHERE  contact_id = $contactID
  AND  event_id   = {$this->_eventId}
  AND  is_test    = {$participantParams['is_test']}
";
            $pID = CRM_Core_DAO::singleValueQuery( $sql,
                                                   CRM_Core_DAO::$_nullArray );
            if ( $pID ) {
                $participantParams['id'] = $pID;
            }
        }
        require_once 'CRM/Core/BAO/Discount.php';
        $participantParams['discount_id'] = CRM_Core_BAO_Discount::findSet( $this->_eventId, 'civicrm_event' );
        if ( !$participantParams['discount_id'] ) {
            $participantParams['discount_id'] = "null";            
        }

        require_once 'CRM/Event/BAO/Participant.php';
        $participant = CRM_Event_BAO_Participant::create($participantParams);
        
        $transaction->commit( );
        
        return $participant;
    }


    function getTemplateFileName() 
    {
        if ( $this->_eventId ) {
            $templateFile = "CRM/Event/Form/Registration/{$this->_eventId}/{$this->_name}.tpl";
            $template =& CRM_Core_Form::getTemplate( );
            if ( $template->template_exists( $templateFile ) ) {
                return $templateFile;
            }
        }
        return parent::getTemplateFileName( );
    }
}


