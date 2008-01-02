<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
require_once 'CRM/Core/BAO/CustomGroup.php';

/**
 * This class generates form components for processing a ontribution 
 * 
 */
class CRM_Contribute_Form_Contribution extends CRM_Core_Form
{
    /**
     * the id of the contribution that we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_id;

    /**
     * the id of the premium that we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_premiumId;

    /**
     * the id of the note 
     *
     * @var int
     * @protected
     */
    protected $_noteId;

    /**
     * the id of the contact associated with this contribution
     *
     * @var int
     * @protected
     */
    protected $_contactID;

    /**
     * is this contribution associated with an online
     * financial transaction
     *
     * @var boolean
     * @protected 
     */ 
    protected $_online = false;


     /**
     * Stores all producuct option
     *
     * @var boolean
     * @protected 
     */ 
    protected $_options ;

    
    /**
     * stores the honor id
     *
     * @var boolean
     * @protected 
     */ 
    protected $_honorID = null ;

    /**
     * Store the tree of custom data and fields
     *
     * @var array
     */
    protected $_groupTree;
    /**
     * Store the tree of custom data and fields
     *
     * @var array
     */
    protected $_contributionType;

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    public function preProcess()  
    {  
        $session =& CRM_Core_Session::singleton( );
        $contactID = $session->get( 'userID' );
        list( $this->userDisplayName, $this->userEmail ) = CRM_Contact_BAO_Contact::getEmailDetails( $contactID );
        // check for edit permission
        if ( ! CRM_Core_Permission::check( 'edit contributions' ) ) {
            CRM_Core_Error::fatal( ts( 'You do not have permission to access this page' ) );
        }

        // action
        $this->_action = CRM_Utils_Request::retrieve( 'action', 'String',
                                                      $this, false, 'add' );
        $contributionType = CRM_Utils_Request::retrieve( 'subType', 'Positive', $this );
        $this->_contributionType = ( $contributionType != null) ? $contributionType : "Contribution";
        
        $this->assign( 'action'  , $this->_action   ); 

        $this->_id        = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );

        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return;
        }

        // current contribution id
        if ( $this->_id ) {
            require_once 'CRM/Contribute/DAO/FinancialTrxn.php';
            $trxn =& new CRM_Contribute_DAO_FinancialTrxn( );
            $trxn->contribution_id = $this->_id;
            if ( $trxn->find( true ) ) {
                $this->_online = true;
            }
        }

        //to get Premium id
        if( $this->_id ) {
            require_once 'CRM/Contribute/DAO/ContributionProduct.php';
            $dao = & new CRM_Contribute_DAO_ContributionProduct();
            $dao->contribution_id = $this->_id;
            if ( $dao->find(true) ) {
                $this->_premiumId = $dao->id;
            }
        }

        //to get note id 
        if( $this->_id ) {
            require_once 'CRM/Core/BAO/Note.php';
            $daoNote = & new CRM_Core_BAO_Note();
            $daoNote->entity_table = 'civicrm_contribution';
            $daoNote->entity_id = $this->_id;
            if ( $daoNote->find(true) ) {
                $this->_noteId = $daoNote->id;
            }
        }

        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );

        $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree( 'Contribution', $this->_id, 0, $this->_contributionType);       
    }

    function setDefaultValues( ) 
    {
        $defaults = array( );
        require_once 'CRM/Core/ShowHideBlocks.php';
        $showHide =& new CRM_Core_ShowHideBlocks( );

        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return $defaults;
        }
        
        if ( $this->_id ) {
            $ids = array( );
            $params = array( 'id' => $this->_id );
            require_once "CRM/Contribute/BAO/Contribution.php";
            CRM_Contribute_BAO_Contribution::getValues( $params, $defaults, $ids );
            $this->_contactID = $defaults['contact_id'];
        } else {
            $now = date("Y-m-d");
            $defaults['receive_date'] = $now;
        }
        
        if ($this->_contributionType) {
            $defaults['contribution_type_id'] = $this->_contributionType;
        }

        //get Note
        if($this->_noteId) {
            $defaults['note'] = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Note', $this->_noteId, 'note' );
        }
        if (  CRM_Utils_Array::value('is_test',$defaults) ){
            $this->assign( "is_test" , true);
        } 
        if (isset ( $defaults["honor_contact_id"] ) ) {
            $honorDefault = array();
            $this->_honorID = $defaults["honor_contact_id"];
            $idParams = array( 'id' => $defaults["honor_contact_id"], 'contact_id' => $defaults["honor_contact_id"] );
            CRM_Contact_BAO_Contact::retrieve( $idParams, $honorDefault, $ids );
            $honorType = CRM_Core_PseudoConstant::honor( );   
            $defaults["honor_prefix_id"]    = $honorDefault["prefix_id"];
            $defaults["honor_first_name"] = CRM_Utils_Array::value("first_name",$honorDefault);
            $defaults["honor_last_name"]  = CRM_Utils_Array::value("last_name",$honorDefault);
            $defaults["honor_email"]     = CRM_Utils_Array::value("email",$honorDefault["location"][1]["email"][1]);
            $defaults["honor_type"]      = $honorType[$defaults["honor_type_id"]];
        }
        
        if( isset($this->_groupTree) ) {
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, false, false );
        }
        $this->assign('showOption',true);
        // for Premium section
        if( $this->_premiumId ) {
            $this->assign('showOption',false);
            require_once 'CRM/Contribute/DAO/ContributionProduct.php';
            $dao = & new CRM_Contribute_DAO_ContributionProduct();
            $dao->id = $this->_premiumId;
            $dao->find(true);
            //if($this->_options[$dao->product_id];)
            $options = isset($this->_options[$dao->product_id]) ? $this->_options[$dao->product_id] : "";
            if ( ! $options ) {
                $this->assign('showOption',true);
            }
            $options_key = CRM_Utils_Array::key($dao->product_option,$options);
            if( $options_key) {
                $defaults['product_name']   = array ( $dao->product_id , trim($options_key) );
            } else {
                $defaults['product_name']   = array ( $dao->product_id);
            }
            $defaults['fulfilled_date'] = $dao->fulfilled_date;
        }

        list( $displayName, $email ) = CRM_Contact_BAO_Contact::getEmailDetails( $this->_contactID );
        $this->assign( 'email', $email ); 
        $showAdditional = 0;
        $showPremium = 0;
        $showCancel = 0;
        $showHonoree = 0;             
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            $advFields = array('note', 'thankyou_date', 'trxn_id', 'invoice_id',
                               'non_deductible_amount', 'fee_amount', 'net_amount');
            foreach($advFields as $key) {
                if ( !empty($defaults[$key]) ) {
                    $showAdditional = 1;
                    break;
                }
            }
            if ( CRM_Utils_Array::value('0',$defaults['product_name']) || CRM_Utils_Array::value('fulfilled_date',$defaults) ) {
                $showPremium = 1;
            }
            if ( CRM_Utils_Array::value('cancel_date',$defaults) || CRM_Utils_Array::value('cancel_reason',$defaults) ) {
                $showCancel = 1;
            }
            $honorFields = array('honor_type_id', 'honor_prefix_id', 'honor_first_name', 'honor_lastname',
                                 'honor_email');
            foreach($honorFields as $key) {
                if ( !empty($defaults[$key]) ) {
                    $showHonoree = 1;
                    break;
                }
            }
        }
        if ( $showAdditional ) {
            $showHide->addShow( "id-additional" );
            $showHide->addHide( "id-additional-show" );
        } else {
            $showHide->addShow( "id-additional-show" );
            $showHide->addHide( "id-additional" );
        }
        if ( $showPremium ) {
            $showHide->addShow( "id-premium" );
            $showHide->addHide( "id-premium-show" );
        } else {
            $showHide->addShow( "id-premium-show" );
            $showHide->addHide( "id-premium" );
        }
        if ( $showHonoree ) {
            $showHide->addShow( "id-honoree" );
            $showHide->addHide( "id-honoree-show" );
        } else {
            $showHide->addShow( "id-honoree-show" );
            $showHide->addHide( "id-honoree" );
        }
        // Don't assign showHide elements to template in DELETE mode (fields to be shown and hidden don't exist)
        if ( !( $this->_action & CRM_Core_Action::DELETE )&& !( $this->_action & CRM_Core_Action::DISABLE )  ) {
            $showHide->addToTemplate( );
        }
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

        $this->applyFilter('__ALL__', 'trim');
        
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
        
        $urlParams = "reset=1&cid={$this->_contactID}&context=contribution";
        if ( $this->_id ) {
            $urlParams .= "&action=update&id={$this->_id}";
        } else {
            $urlParams .= "&action=add";
        }
        $url = CRM_Utils_System::url( 'civicrm/contact/view/contribution',
                                      $urlParams, true, null, false ); 
        $this->assign("refreshURL",$url);

        $this->buildPremiumForm($this);
        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Contribution' );
               
        $element =& $this->add('select', 'contribution_type_id', 
                               ts( 'Contribution Type' ), 
                               array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::contributionType( ),
                               true, array('onChange' => "if (this.value) reload(true); else return false"));
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $this->add('select', 'contribution_status_id',
                   ts('Contribution Status'), 
                   CRM_Contribute_PseudoConstant::contributionStatus( ),
                   false, array(
                                'onClick' => "if (this.value != 3) status(); else return false",
                                'onChange' => "return showHideByValue('contribution_status_id','3','cancelInfo','table-row','select',false);"));
        $element =& $this->add('select', 'payment_instrument_id', 
                               ts( 'Paid By' ), 
                               array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::paymentInstrument( )
                               );
        if ( $this->_online ) {
            $element->freeze( );
        }

        // add various dates
        $element =& $this->add('date', 'receive_date', ts('Received'), CRM_Core_SelectValues::date('activityDate'), false );         
        $this->addRule('receive_date', ts('Select a valid date.'), 'qfDate');
        if ( $this->_online ) {
            $this->assign("hideCalender" , true );
            $element->freeze( );
        }

        $this->addElement('date', 'receipt_date', ts('Receipt Date'), CRM_Core_SelectValues::date('activityDate')); 
        $this->addRule('receipt_date', ts('Select a valid date.'), 'qfDate');

        $this->addElement('date', 'thankyou_date', ts('Thank-you Sent'), CRM_Core_SelectValues::date('activityDate')); 
        $this->addRule('thankyou_date', ts('Select a valid date.'), 'qfDate');

        $this->addElement('date', 'cancel_date', ts('Cancelled Date'), CRM_Core_SelectValues::date('activityDate')); 
        $this->addRule('cancel_date', ts('Select a valid date.'), 'qfDate');
        
        $this->add('textarea', 'cancel_reason', ts('Cancellation Reason'), $attributes['cancel_reason'] );

        $this->addElement('checkbox','is_email_receipt', ts('Send Receipt?'),null, array('onclick' =>"return showHideByValue('is_email_receipt','','receiptDate','table-row','radio',true);") );

        $honor =CRM_Core_PseudoConstant::honor( ); 
        foreach ($honor as $key => $var) {
            $honorTypes[$key] = HTML_QuickForm::createElement('radio', null, null, $var, $key);
        }
        $this->addGroup($honorTypes, 'honor_type_id', null);

        $this->add('select','honor_prefix_id',ts('Prefix') ,array('' => ts('- prefix -')) + CRM_Core_PseudoConstant::individualPrefix());
        $this->add('text','honor_first_name',ts(' First Name'));
        $this->add('text','honor_last_name',ts('Last Name'));
        $this->add('text','honor_email',ts('Email'));
        $this->addRule( "honor_email", ts('Email is not valid.'), 'email' );

        // add various amounts
        $element =& $this->add( 'text', 'non_deductible_amount', ts('Non-deductible Amount'),
                                $attributes['non_deductible_amount'] );
        $this->addRule('non_deductible_amount', ts('Please enter a valid amount.'), 'money');
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $element =& $this->add( 'text', 'total_amount', ts('Total Amount'),
                                $attributes['total_amount'], true );
        $this->addRule('total_amount', ts('Please enter a valid amount.'), 'money');
        if ( $this->_online ) {
            $element->freeze( );
        }

        $element =& $this->add( 'text', 'fee_amount', ts('Fee Amount'),
                                $attributes['fee_amount'] );
        $this->addRule('fee_amount', ts('Please enter a valid amount.'), 'money');
        if ( $this->_online ) {
            $element->freeze( );
        }

        $element =& $this->add( 'text', 'net_amount', ts('Net Amount'),
                                $attributes['net_amount'] );
        $this->addRule('net_amount', ts('Please enter a valid amount.'), 'money');
        if ( $this->_online ) {
            $element->freeze( );
        }

        $element =& $this->add( 'text', 'trxn_id', ts('Transaction ID'), 
                                $attributes['trxn_id'] );
        if ( $this->_online ) {
            $element->freeze( );
        }

        $element =& $this->add( 'text', 'invoice_id', ts('Invoice ID'), 
                                $attributes['invoice_id'] );
        if ( $this->_online ) {
            $element->freeze( );
        }
        $element =& $this->add( 'text', 'source', ts('Source'), CRM_Utils_Array::value('source',$attributes) );
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $this->add('textarea', 'note', ts('Notes'),array("rows"=>4,"cols"=>60) );


        $session = & CRM_Core_Session::singleton( );
        $uploadNames = $session->get( 'uploadNames' );
        if ( is_array( $uploadNames ) && ! empty ( $uploadNames ) ) {
            $buttonType = 'upload';
        } else {
            $buttonType = 'next';
        }
     
        //build custom data
        CRM_Core_BAO_CustomGroup::buildQuickForm( $this, $this->_groupTree, 'showBlocks1', 'hideBlocks1' );
   
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

        $this->addFormRule( array( 'CRM_Contribute_Form_Contribution', 'formRule' ), $this );

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
      
        if (isset($fields["contribution_honor"])) {
            if ( !((  CRM_Utils_Array::value( 'honor_first_name', $fields ) && 
                      CRM_Utils_Array::value( 'honor_last_name' , $fields )) ||
                      CRM_Utils_Array::value( 'honor_email' , $fields ) )) {
                $errors['_qf_default'] = ts('Honor First Name and Last Name OR an email should be set.');
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
            require_once 'CRM/Contribute/BAO/Contribution.php';
            CRM_Contribute_BAO_Contribution::deleteContribution( $this->_id );
            return;
        }
        
        // get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );

        $config =& CRM_Core_Config::singleton( );

        $params = array( );
        $ids    = array( );

        $params['contact_id'] = $this->_contactID;
        $params['currency'  ] = $config->defaultCurrency;

        $fields = array( 'contribution_type_id',
                         'contribution_status_id',
                         'payment_instrument_id',
                         'non_deductible_amount',
                         'total_amount',
                         'fee_amount',
                         'net_amount',
                         'trxn_id',
                         'invoice_id',
                         'cancel_reason',
                         'source',
                         'honor_type_id'
                          );

        foreach ( $fields as $f ) {
            $params[$f] = CRM_Utils_Array::value( $f, $formValues );
        }

        foreach ( array( 'non_deductible_amount', 'total_amount', 'fee_amount', 'net_amount' ) as $f ) {
            $params[$f] = CRM_Utils_Rule::cleanMoney( $params[$f] );
        }

        $dates = array( 'receive_date',
                        'receipt_date',
                        'thankyou_date',
                        'cancel_date' );

        foreach ( $dates as $d ) {
            if ( ! CRM_Utils_System::isNull( $formValues[$d] ) ) {
                $formValues[$d]['H'] = '00';
                $formValues[$d]['i'] = '00';
                $formValues[$d]['s'] = '00';
                $params[$d] = CRM_Utils_Date::format( $formValues[$d] );
            } else{
                $params[$d] = 'null';
            }
        }
        if ( $formValues['is_email_receipt'] ) {
            $params['receipt_date'] = date("Y-m-d");
        }
        if ( $params["contribution_status_id"] == 3 ) {
            if ( CRM_Utils_System::isNull( CRM_Utils_Array::value( 'cancel_date', $params ) ) ) {
                $params['cancel_date'] = date("Y-m-d");
            }
        } else { 
            $params['cancel_date']   = 'null';
            $params['cancel_reason'] = 'null';
        }
   
        $ids['contribution'] = $params['id'] = $this->_id;
        if ( CRM_Utils_Array::value( 'honor_type_id', $formValues) ) {
            require_once 'CRM/Contribute/BAO/Contribution.php';
            if ( $this->_honorID ) {
                $honorId = CRM_Contribute_BAO_Contribution::createHonorContact( $formValues , $this->_honorID );
            } else {
                $honorId = CRM_Contribute_BAO_Contribution::createHonorContact( $formValues );
            }
            $params["honor_contact_id"] = $honorId;
        } else {
            $params["honor_contact_id"] = 'null';
        }

        // format custom data
        // get mime type of the uploaded file
        if ( !empty($_FILES) ) {
            foreach ( $_FILES as $key => $value) {
                $files = array( );
                if ( $formValues[$key] ) {
                    $files['name'] = $formValues[$key];
                }
                if ( $value['type'] ) {
                    $files['type'] = $value['type']; 
                }
                $formValues[$key] = $files;
            }
        }
        
        $customData = array( );
        foreach ( $formValues as $key => $value ) {
            if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID($key) ) {
                CRM_Core_BAO_CustomField::formatCustomField( $customFieldId, $customData,
                                                             $value, 'Contribution', null, $this->_id);
            }
        }
        
        if (! empty($customData) ) {
            $params['custom'] = $customData;
        }
        
        //special case to handle if all checkboxes are unchecked
        $customFields = CRM_Core_BAO_CustomField::getFields( 'Contribution' );
        
        if ( !empty($customFields) ) {
            foreach ( $customFields as $k => $val ) {
                if ( in_array ( $val[3], array ('CheckBox','Multi-Select') ) &&
                     ! CRM_Utils_Array::value( $k, $params['custom'] ) ) {
                    CRM_Core_BAO_CustomField::formatCustomField( $k, $params['custom'],
                                                                 '', 'Contribution', null, $this->_id);
                }
            }
        }

        require_once 'CRM/Contribute/BAO/Contribution.php';
        $contribution =& CRM_Contribute_BAO_Contribution::create( $params, $ids );

        //process associated membership / participant
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            require_once 'CRM/Core/Payment/BaseIPN.php';
            $baseIPN = new CRM_Core_Payment_BaseIPN( );
        
            $input = $ids = $objects = array( );
            $IdDetails = $this->getDetails( $contribution->id );
            
            $input['component']       = $IdDetails['component'];
            $ids['contact'     ]      = $contribution->contact_id;
            $ids['contribution']      = $contribution->id;
            $ids['contributionRecur'] = null;
            $ids['contributionPage']  = null;
            $ids['membership']        = $IdDetails['membership'];
            $ids['participant']       = $IdDetails['participant'];
            $ids['event']             = $IdDetails['event'];
            
            if ( ! $baseIPN->validateData( $input, $ids, $objects, false ) ) {
                CRM_Core_Error::fatal( );
            }
            
            $membership   =& $objects['membership']  ;
            $participant  =& $objects['participant'] ;
            
            if ( $contribution->contribution_status_id == 3 ) {
                if ( $membership ) {
                    $membership->status_id = 6;
                    $membership->save( );
                }
                if ( $participant ) {
                    $participant->status_id = 4;
                    $participant->save( );
                }
            } elseif ( $contribution->contribution_status_id == 4 ) {
                if ( $membership ) {
                    $membership->status_id = 4;
                    $membership->save( );
                }
                if ( $participant ) {
                    $participant->status_id = 4;
                    $participant->save( );
                }
            } elseif ( $contribution->contribution_status_id == 1 ) {
                if ( $membership ) {
                    $format       = '%Y%m%d';
                    require_once 'CRM/Member/BAO/MembershipType.php';  
                    $dates = CRM_Member_BAO_MembershipType::getDatesForMembershipType($membership->membership_type_id);
                    
                    $membership->join_date     = 
                        CRM_Utils_Date::customFormat( $dates['join_date'],     $format );
                    $membership->start_date    = 
                        CRM_Utils_Date::customFormat( $dates['start_date'],    $format );
                    $membership->end_date      = 
                        CRM_Utils_Date::customFormat( $dates['end_date'],      $format );
                    $membership->reminder_date = 
                        CRM_Utils_Date::customFormat( $dates['reminder_date'], $format );
                    
                    $membership->status_id = 2;
                    $membership->save( );
                }
                if ( $participant ) {
                    $participant->status_id = 1;
                    $participant->save( );
                }
            }
        }

        //process note
        require_once 'CRM/Core/BAO/Note.php';
        $noteParams = array('entity_table' => 'civicrm_contribution', 
                            'note'         => $formValues['note'], 
                            'entity_id'    => $contribution->id,
                            'contact_id'   => $this->_contactID);
        $noteID = array();
        if( $this->_noteId ) {
            $noteID = array("id" => $this->_noteId);
            CRM_Core_BAO_Note::add($noteParams, $noteID);
        } else {
            CRM_Core_BAO_Note::add($noteParams, $noteID);
        }
        //process premium
        if ( $formValues['product_name'][0] ) {
            require_once 'CRM/Contribute/DAO/ContributionProduct.php';
            $dao = & new CRM_Contribute_DAO_ContributionProduct();
            $dao->contribution_id = $contribution->id;
            $dao->product_id  = $formValues['product_name'][0];
            $dao->fulfilled_date  = CRM_Utils_Date::format($formValues['fulfilled_date']);
            $dao->product_option = $this->_options[$formValues['product_name'][0]][$formValues['product_name'][1]];
            if ($this->_premiumId) {
                $premoumDAO = & new CRM_Contribute_DAO_ContributionProduct();
                $premoumDAO->id  = $this->_premiumId;
                $premoumDAO->find(true);
                if( $premoumDAO->product_id == $formValues['product_name'][0] ) {
                    $dao->id = $this->_premiumId;
                    $premium = $dao->save();
                } else {
                    $premoumDAO->delete();
                    $premium = $dao->save();
                }
            } else {
                $premium = $dao->save();
            }
            
        }
        
        // Code Added to Send ReceiptMail, Assigned variables to
        // Message generating templates
        if ( $formValues['is_email_receipt'] ) {
            //Retrieve Contribution Type Name from contribution_type_id
            $formValues['contributionType_name'] = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionType',
                                                                                $formValues['contribution_type_id'] );

            // Retrieve the name and email from receipt is to be send
            $formValues['receipt_from_name'] = $this->userDisplayName;
            $formValues['receipt_from_email']= $this->userEmail;

            // assigned various dates to the templates
            $this->assign('receive_date', CRM_Utils_Date::MysqlToIso(CRM_Utils_Date::format($formValues['receive_date'])));
            $this->assign('receipt_date', CRM_Utils_Date::MysqlToIso(CRM_Utils_Date::format($formValues['receipt_date'])));
            $this->assign('thankyou_date', CRM_Utils_Date::MysqlToIso(CRM_Utils_Date::format($formValues['thankyou_date'])));
            $this->assign('cancel_date', CRM_Utils_Date::MysqlToIso(CRM_Utils_Date::format($formValues['cancel_date'])));
            
            // retrieve premium product name and assigned fulfilled
            // date to template
            require_once 'CRM/Contribute/DAO/Product.php';
            $productDAO =& new CRM_Contribute_DAO_Product();
            $productDAO->id = $formValues['product_name'][0];
            $productDAO->find(true);
            
            $formValues['product_name'] = $productDAO->name;
            
            $this->assign('fulfilled_date', CRM_Utils_Date::MysqlToIso(CRM_Utils_Date::format($formValues['fulfilled_date'])));
            
            // retrieve payment instrument name
            $paymentInstrumentGroup = array();
            $paymentInstrumentGroup['name'] = 'payment_instrument';
            require_once 'CRM/Core/BAO/OptionGroup.php';
            CRM_Core_BAO_OptionGroup::retrieve($paymentInstrumentGroup, $paymentInstrumentGroup);
            $paymentInstrument = array();
            $paymentInstrument['value']            = $formValues['payment_instrument_id'];      
            $paymentInstrument['option_group_id']  = $paymentInstrumentGroup['id'];
            require_once 'CRM/Core/BAO/OptionValue.php';
            CRM_Core_BAO_OptionValue::retrieve($paymentInstrument, $paymentInstrument);
            $formValues['paidBy'] = $paymentInstrument['label'];

            // retrieve individual prefix value for honoree
            $individualPrefixGroup = array();
            $individualPrefixGroup['name'] = 'individual_prefix';
            require_once 'CRM/Core/BAO/OptionGroup.php';
            CRM_Core_BAO_OptionGroup::retrieve($individualPrefixGroup, $individualPrefixGroup);
            $individualPrefix = array();
            $individualPrefix['value']            = $formValues['honor_prefix_id'];      
            $individualPrefix['option_group_id']  = $individualPrefixGroup['id'];
            require_once 'CRM/Core/BAO/OptionValue.php';
            CRM_Core_BAO_OptionValue::retrieve($individualPrefix,$individualPrefix );
            $formValues['honor_prefix'] = $individualPrefix['label'];

            // retrieve custom data
            $showCustom = 0;
            $customData = array( );
            foreach ( $formValues as $key => $value ) {
                if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID($key) ) {
                    $fieldID['id'] = $customFieldId;
                    CRM_Core_BAO_CustomField::retrieve( $fieldID, $customData);
                    $customField[$customData['label']] = $value;
                    if ($value) {
                        $showCustom = 1;
                    }
                }
            }
            $this->assign('showCustom',$showCustom);
            $this->assign_by_ref('customField',$customField);
            $this->assign_by_ref('formValues',$formValues);
            require_once 'CRM/Contact/BAO/Contact.php';
            list( $contributorDisplayName, $contributorEmail ) = CRM_Contact_BAO_Contact::getEmailDetails( $this->_contactID );
            $template =& CRM_Core_Smarty::singleton( );
            $message = $template->fetch( 'CRM/Contribute/Form/Message.tpl' );

            $subject = ts('Contribution Receipt');
            $receiptFrom = '"' . $formValues['receipt_from_name'] . '" <' . $formValues['receipt_from_email'] . '>';
         
            require_once 'CRM/Utils/Mail.php';
            CRM_Utils_Mail::send( $receiptFrom,
                                  $contributorDisplayName,
                                  $contributorEmail,
                                  $subject,
                                  $message);
        }

        $statusMsg = ts('The contribution record has been saved.');
        if ( $formValues['is_email_receipt'] ) {
            $statusMsg .= ts(' A receipt has been emailed to the contributor.');
        }
        CRM_Core_Session::setStatus( $statusMsg );

    }
    
    /** 
     * Function to build the form for Premium 
     * 
     * @access public 
     * @return None 
     */ 
    function buildPremiumForm( &$form )
    {
        require_once 'CRM/Contribute/DAO/Product.php';
        $sel1 = $sel2 = array();
        
        $dao = & new CRM_Contribute_DAO_Product();
        $dao->is_active = 1;
        $dao->find();
        $min_amount = array();
        $sel1[0] = '-select product-';
        while ( $dao->fetch() ) {
            $sel1[$dao->id] = $dao->name." ( ".$dao->sku." )";
            $min_amount[$dao->id] = $dao->min_contribution;
            $options = explode(',', $dao->options);
            foreach ($options as $k => $v ) {
                $options[$k] = trim($v);
            }
            if( $options [0] != '' ) {
                $sel2[$dao->id] = $options;
            }
            $form->assign('premiums', true );
            
        }
        $form->_options = $sel2;
        $form->assign('mincontribution',$min_amount);
        $sel =& $this->addElement('hierselect', "product_name", ts('Premium'),'onclick="showMinContrib();"');
        $js = "<script type='text/javascript'>\n";
        $formName = 'document.forms.' . $form->_name;
        
        for ( $k = 1; $k < 2; $k++ ) {
            if ( ! isset ($defaults['product_name'][$k] )|| (! $defaults['product_name'][$k] ) )  {
                $js .= "{$formName}['product_name[$k]'].style.display = 'none';\n"; 
            }
        }
        
        $sel->setOptions(array($sel1, $sel2 ));
        $js .= "</script>\n";
        $form->assign('initHideBoxes', $js);
        $form->addElement('date', 'fulfilled_date', ts('Fulfilled'), CRM_Core_SelectValues::date('activityDate'));
        $form->addElement('text', 'min_amount', ts('Minimum Contribution Amount'));
    }


    function &getDetails( $contributionID ) {
        $query = "
SELECT    c.id                 as contribution_id,
          mp.membership_id     as membership_id,
          m.membership_type_id as membership_type_id,
          pp.participant_id    as participant_id,
          p.event_id           as event_id
FROM      civicrm_contribution c
LEFT JOIN civicrm_membership_payment  mp ON mp.contribution_id = c.id
LEFT JOIN civicrm_participant_payment pp ON pp.contribution_id = c.id
LEFT JOIN civicrm_participant         p  ON pp.participant_id  = p.id
LEFT JOIN civicrm_membership          m  ON m.id  = mp.membership_id
WHERE     c.id = $contributionID";

        $rows = array( );
        $dao = CRM_Core_DAO::executeQuery( $query,
                                           CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch( ) ) {
            $rows = array(
                          'component'       => $dao->participant_id ? 'event' : 'contribute',
                          'membership'      => $dao->membership_id,
                          'membership_type' => $dao->membership_type_id,
                          'participant'     => $dao->participant_id,
                          'event'           => $dao->event_id,
                          );
        }
        return $rows;
    }

}

?>
