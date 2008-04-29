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
require_once 'CRM/Core/BAO/CustomGroup.php';
require_once 'CRM/Contribute/Form/AdditionalInfo.php';
require_once 'CRM/Custom/Form/CustomData.php';

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
     * Store the contribution Type ID
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
        $this->_cdType     = CRM_Utils_Array::value( 'type', $_GET );

        $this->assign('cdType', false);
        if ( $this->_cdType ) {
            $this->assign('cdType', true);
            return CRM_Custom_Form_CustomData::preProcess( $this );
        }
        
        require_once 'CRM/Contact/BAO/Contact.php';
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

            //to get Premium id
            require_once 'CRM/Contribute/DAO/ContributionProduct.php';
            $dao = & new CRM_Contribute_DAO_ContributionProduct();
            $dao->contribution_id = $this->_id;
            if ( $dao->find(true) ) {
                $this->_premiumId = $dao->id;
            }

            //to get note id 
            require_once 'CRM/Core/BAO/Note.php';
            $daoNote = & new CRM_Core_BAO_Note();
            $daoNote->entity_table = 'civicrm_contribution';
            $daoNote->entity_id = $this->_id;
            if ( $daoNote->find(true) ) {
                $this->_noteId = $daoNote->id;
            }
            
            $this->_contributionType = CRM_Core_DAO::getFieldValue( "CRM_Contribute_DAO_Contribution", 
                                                                    $this->_id, 
                                                                    'contribution_type_id' );
        }

        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );

        // when custom data is included in this page
        if ( CRM_Utils_Array::value( "hidden_custom", $_POST ) ) {
            eval( 'CRM_Custom_Form_Customdata::preProcess( $this );' );
            eval( 'CRM_Custom_Form_Customdata::buildQuickForm( $this );' );
            eval( 'CRM_Custom_Form_Customdata::setDefaultValues( $this );' );
        }
    }

    function setDefaultValues( ) 
    {
        if ( $this->_cdType ) {
            return CRM_Custom_Form_CustomData::setDefaultValues( $this );
        }
       
        $defaults = array( );
        
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return $defaults;
        }
        
        if ( $this->_id ) {
            $ids = array( );
            $params = array( 'id' => $this->_id );
            require_once "CRM/Contribute/BAO/Contribution.php";
            CRM_Contribute_BAO_Contribution::getValues( $params, $defaults, $ids );
            
            // throw out a warning if pay later contrib in pending state
            // check if its an online contrib or event registration
            if ( $defaults['contribution_status_id'] == 2 &&
                 ( strpos( $defaults['contribution_source'], ts( 'Online Contribution' ) ) !== false ||
                   strpos( $defaults['contribution_source'], ts( 'Online Event Registration' ) ) !== false ) ) {
                $message = ts( 'If you have received payment for this Pending online contribution, record it using <strong>Update Pending Contribution Status</strong> from <strong><a href=\'%1\'>CiviContribute &raquo; Find Contributions</a></strong>. If you update the status from here the contributor may not got complete information on their receipt. Also, if there is an associated membership or event registration record - it\'s status will not be updated.',
                               array( 1 => CRM_Utils_System::url( 'civicrm/contribute/search', "reset=1" )) );
                CRM_Core_Session::setStatus( $message );
            }
            
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
        
        
        $this->assign('showOption',true);
        // for Premium section
        if( $this->_premiumId ) {
            $this->assign('showOption',false);
            require_once 'CRM/Contribute/DAO/ContributionProduct.php';
            $dao = & new CRM_Contribute_DAO_ContributionProduct();
            $dao->id = $this->_premiumId;
            $dao->find(true);
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
        if ( $this->_cdType ) {
            return CRM_Custom_Form_CustomData::buildQuickForm( $this );
        }
        
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

        //need to assign custom data type and subtype to the template
        $this->assign('customDataType', 'Contribution');
        $this->assign('customDataSubType',  $this->_contributionType );
        $this->assign('entityId',  $this->_id );
        
        $urlParams = "reset=1&cid={$this->_contactID}&context=contribution";
        if ( $this->_id ) {
            $urlParams .= "&action=update&id={$this->_id}";
        } else {
            $urlParams .= "&action=add";
        }
        $url = CRM_Utils_System::url( 'civicrm/contact/view/contribution',
                                      $urlParams, true, null, false ); 
        $this->assign("refreshURL",$url);
        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Contribution' );
        
        $element =& $this->add('select', 'contribution_type_id', 
                               ts( 'Contribution Type' ), 
                               array(''=>ts( '- select -' )) + CRM_Contribute_PseudoConstant::contributionType( ),
                               true, array('onChange' => "buildCustomData( this.value );"));
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
                               array(''=>ts( '- select -' )) + CRM_Contribute_PseudoConstant::paymentInstrument( )
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
        
        $this->addElement('date', 'cancel_date', ts('Cancelled Date'), CRM_Core_SelectValues::date('activityDate')); 
        $this->addRule('cancel_date', ts('Select a valid date.'), 'qfDate');
        
        $this->add('textarea', 'cancel_reason', ts('Cancellation Reason'), $attributes['cancel_reason'] );
        
        $this->addElement('checkbox','is_email_receipt', ts('Send Receipt?'),null, array('onclick' =>"return showHideByValue('is_email_receipt','','receiptDate','table-row','radio',true);") );
        
        $element =& $this->add( 'text', 'total_amount', ts('Total Amount'),
                                $attributes['total_amount'], true );
        $this->addRule('total_amount', ts('Please enter a valid amount.'), 'money');
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $element =& $this->add( 'text', 'source', ts('Source'), CRM_Utils_Array::value('source',$attributes) );
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $session = & CRM_Core_Session::singleton( );
        $uploadNames = $session->get( 'uploadNames' );
        if ( is_array( $uploadNames ) && ! empty ( $uploadNames ) ) {
            $buttonType = 'upload';
        } else {
            $buttonType = 'next';
        }
        
        $this->_formType = CRM_Utils_Array::value( 'formType', $_GET );
        
        require_once 'CRM/Contribute/Form/AdditionalInfo.php';
        
        if ( $this->_id ) {
            $ids = array( );
            $params = array( 'id' => $this->_id );
            require_once "CRM/Contribute/BAO/Contribution.php";
            CRM_Contribute_BAO_Contribution::getValues( $params, $defaults, $ids );
        }
        
        $additionalDetailFields = array( 'note', 'thankyou_date', 'invoice_id', 'trxn_id',
                                         'non_deductible_amount', 'fee_amount', 'net_amount');
        
        foreach ( $additionalDetailFields as $key ) {
            if ( ! empty( $defaults[$key] ) ) {
                $defaults['hidden_buildAdditionalDetail'] = 1;
                break;
            }
        }
        
        $honorFields = array('honor_type_id', 'honor_prefix_id', 'honor_first_name', 
                             'honor_lastname','honor_email');
        
        foreach ( $honorFields as $key ) {
            if ( ! empty( $defaults[$key] ) ) {
                $defaults['hidden_buildHonoree'] = 1;
                break;
            }
        }
        
        if ( $this->_premiumId ) {
            require_once 'CRM/Contribute/DAO/ContributionProduct.php';
            $dao = & new CRM_Contribute_DAO_ContributionProduct();
            $dao->id = $this->_premiumId;
            $dao->find(true);
            if ( $dao->product_id ) {
                $defaults['hidden_buildPremium'] = 1;
            }
        }
        
        if ( $this->_noteId ) {
            $defaults['note'] = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Note', $this->_noteId, 'note' );
            if ( ! empty( $defaults['note'] ) ) {
                $defaults['hidden_buildAdditionalDetail'] = 1;
            }
        }
        
        $paneNames =  array ( 'Additional Details'  => 'buildAdditionalDetail',
                              'Honoree Information' => 'buildHonoree', 
                              'Premium Information' => 'buildPremium'
                              );
        
        foreach ( $paneNames as $name => $type ) {
            
            if ( $this->_id ) {
                $dojoUrlParams = "&reset=1&action=update&id={$this->_id}&snippet=1&cid={$this->_contactID}&formType={$type}";  
            } else {
                $dojoUrlParams = "&reset=1&action=add&snippet=1&cid={$this->_contactID}&formType={$type}";
            }
            
            $allPanes[$name] = array( 'url'  => CRM_Utils_System::url( 'civicrm/contribute/additionalinfo',
                                                                       $dojoUrlParams ),
                                      'open' => 'false',
                                      'id'   => $type,
                                      );
            
            // see if we need to include this paneName in the current form
            if ( $this->_formType == $type ||
                 CRM_Utils_Array::value( "hidden_{$type}", $_POST ) ||
                 CRM_Utils_Array::value( "hidden_{$type}", $defaults ) ) {
                $allPanes[$name]['open'] = 'true';
                eval( 'CRM_Contribute_Form_AdditionalInfo::' . $type . '( $this );' );
            }
        }
        
        $this->assign( 'allPanes', $allPanes );
        $this->assign( 'dojoIncludes', "dojo.require('civicrm.TitlePane');dojo.require('dojo.parser');" );
        
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
    
    function getTemplateFileName( ) 
    {
        if ( ! $this->_formType ) {
            return parent::getTemplateFileName( );
        } else {
            $name = substr( ucfirst( $this->_formType ), 5 );
            return "CRM/Contribute/Form/AdditionalInfo/{$name}.tpl";
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
        return CRM_Contribute_Form_AdditionalInfo::formRule( $fields, $files, $self );
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
                         'cancel_reason',
                         'source'
                         );
        
        foreach ( $fields as $f ) {
            $params[$f] = CRM_Utils_Array::value( $f, $formValues );
        }
        
        $dates = array( 'receive_date',
                        'receipt_date',
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
        
        //Add Additinal common information  to formatted params
        CRM_Contribute_Form_AdditionalInfo::postProcessCommon( $formValues, $params );
        
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
        
        //process  note
        if ( $contribution->id && isset( $formValues['note'] ) ) {
            CRM_Contribute_Form_AdditionalInfo::processNote( $formValues, $this->_contactID, $contribution->id, $this->_noteId );
        }
        
        //process premium
        if ( $contribution->id && isset( $formValues['product_name'][0] ) ) {
            CRM_Contribute_Form_AdditionalInfo::processPremium( $formValues, $contribution->id, $this->_premiumId, $this->_options ); 
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
            
            $honor  = CRM_Core_PseudoConstant::honor( );             
            $formValues["honor_type"] = $honor[$formValues["honor_type_id"]];

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
            $statusMsg .= ' ' . ts('A receipt has been emailed to the contributor.');
        }
        CRM_Core_Session::setStatus( $statusMsg );
        
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
