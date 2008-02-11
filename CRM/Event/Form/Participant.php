<?PHP

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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Contact/Form/Task.php';
require_once 'CRM/Event/PseudoConstant.php';

/**
 * This class generates form components for processing a participation 
 * in an event
 */
class CRM_Event_Form_Participant extends CRM_Contact_Form_Task
{
    /**
     * the values for the contribution db object
     *
     * @var array
     * @protected
     */
    public $_values;

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
     * the id of the participation that we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_id;
    
    /**
     * the id of the note 
     *
     * @var int
     * @protected
     */
    protected $_noteId = null;

    /**
     * the id of the 
     *
     * @var int
     * @protected
     */
    protected $_eId = null;

    /**
     * the id of the contact associated with this participation
     *
     * @var int
     * @protected
     */
    protected $_contactID;
    
    /**
     * array of event values
     * 
     * @var array
     * @protected
     */
    protected $_event;

    /**
     * Are we operating in "single mode", i.e. adding / editing only
     * one participant record, or is this a batch add operation
     *
     * @var boolean
     */
    public $_single = false;
    
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    public function preProcess()  
    {
        // check for edit permission
        if ( ! CRM_Core_Permission::check( 'edit event participants' ) ) {
            CRM_Core_Error::fatal( ts( 'You do not have permission to access this page' ) );
        }
        
        $this->_id        = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );

        //check the mode when this form is called either single or as
        //search task action
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );
        if ( $this->_id || $this->_contactID ) {
            $this->_single = true;
        } else {
            //set the appropriate action
            $advanced = null;
            $builder  = null;

            $session =& CRM_Core_Session::singleton();
            $advanced = $session->get('isAdvanced');
            $builder  = $session->get('isSearchBuilder');
            
            if ( $advanced == 1 ) {
                $this->_action = CRM_Core_Action::ADVANCED;
            } else if ( $advanced == 2 && $builder = 1) {
                $this->_action = CRM_Core_Action::PROFILE;
            }
            
            parent::preProcess( );
            $this->_single    = false;
            $this->_contactID = null;
        }

        // current contribution id
        if ( $this->_id ) { 
            require_once 'CRM/Event/BAO/ParticipantPayment.php';
            $particpant =& new CRM_Event_BAO_ParticipantPayment( );
            $particpant->participant_id = $this->_id;
            if ( $particpant->find( true ) ) {
                $this->_online = true;
            }
        }
        
        $this->assign( 'single', $this->_single );
        
        $this->_action = CRM_Utils_Request::retrieve( 'action', 'String', $this, false, 'add' );

        $this->assign( 'action'  , $this->_action   ); 

        if ( $this->_id ) {
            $ids = array( );
            $params = array( 'id' => $this->_id );
            require_once "CRM/Event/BAO/Participant.php";
            CRM_Event_BAO_Participant::getValues( $params, $defaults, $ids );
            $this->_eId = $defaults[$this->_id]['event_id'];
        }

        if ( CRM_Utils_Request::retrieve( 'eid', 'Positive', $this ) ) {
            $this->_eId       = CRM_Utils_Request::retrieve( 'eid', 'Positive', $this );     
        }
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return;
        }
        $this->_roleId = CRM_Utils_Request::retrieve( 'rid', 'Positive', $this );

        if ( $this->_id) {
            require_once 'CRM/Core/BAO/Note.php';
            $noteDAO               = & new CRM_Core_BAO_Note();
            $noteDAO->entity_table = 'civicrm_participant';
            $noteDAO->entity_id    = $this->_id;
            if ( $noteDAO->find(true) ) {
                $this->_noteId = $noteDAO->id;
            }
        }

        if ( ! $this->_roleId ) {
            if ( $this->_id ) {
                $this->_roleId = CRM_Core_DAO::getFieldValue( "CRM_Event_DAO_Participant", $this->_id, 'role_id' );
            } else {
                $this->_roleId = 'Role';
            }
        }

        require_once 'CRM/Core/BAO/CustomGroup.php';
        $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree( "Participant", $this->_id, 0, $this->_roleId );
    }

    
    /**
     * This function sets the default values for the form in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    public function setDefaultValues( ) 
    { 
        $defaults = array( );
        
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return $defaults;
        }
       
        $rId  = CRM_Utils_Request::retrieve( 'rid', 'Positive', CRM_Core_DAO::$_nullObject );
       
        if ( $this->_id ) {
            $ids    = array( );
            $params = array( 'id' => $this->_id );
            
            require_once "CRM/Event/BAO/Participant.php";
            CRM_Event_BAO_Participant::getValues( $params, $defaults, $ids );            
            $this->_contactID = $defaults[$this->_id]['contact_id'];
        }

        if ( $rId ) {
            $defaults[$this->_id]["role_id"] = $rId;
        }
        
        if ( $this->_eId ) {
            $defaults[$this->_id]["event_id"] = $this->_eId;
        }
        
        if ( $this->_noteId ) {
            $defaults[$this->_id]['note'] = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Note', $this->_noteId, 'note' );
        }
        
        if ($this->_action & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
            $inactiveNeeded = true;
            $viewMode = true;
        } else {
            $viewMode = false;
            $inactiveNeeded = false;
        }
        
        //setting default register date
        if ($this->_action == CRM_Core_Action::ADD) {
            $registerDate = getDate();
            $defaults[$this->_id]['register_date']['A'] = 'AM';
            $defaults[$this->_id]['register_date']['M'] = $registerDate['mon'];
            $defaults[$this->_id]['register_date']['d'] = $registerDate['mday'];
            $defaults[$this->_id]['register_date']['Y'] = $registerDate['year'];
            if( $registerDate['hours'] > 12 ) {
                $registerDate['hours'] -= 12;
                $defaults[$this->_id]['register_date']['A'] = 'PM';
            }
            
            $defaults[$this->_id]['register_date']['h'] = $registerDate['hours'];
            $defaults[$this->_id]['register_date']['i'] = (integer)($registerDate['minutes']/15) *15;
            if ( CRM_Utils_Array::value( 'event_id' , $defaults[$this->_id] ) ) {
                $contributionTypeId =  CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event',
                                                                    $defaults[$this->_id]['event_id'], 
                                                                    'contribution_type_id' );
                if ( $contributionTypeId ){
                    $defaults[$this->_id]['contribution_type_id'] = $contributionTypeId;
                }
            }
            $defaults[$this->_id]['send_receipt'] = 1;
        } else {
            $defaults[$this->_id]['register_date'] = CRM_Utils_Date::unformat($defaults[$this->_id]['register_date']);
            $defaults[$this->_id]['register_date']['i']  = (integer)($defaults[$this->_id]['register_date']['i']/15)*15;
            $defaults[$this->_id]['record_contribution'] = 0;
            $recordContribution = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_ParticipantPayment', 
                                                               $defaults[$this->_id]['id'], 
                                                               'contribution_id', 
                                                               'participant_id' );
            
            //contribution record exists for this participation
            if ( $recordContribution ) {
                foreach( array('contribution_type_id', 'payment_instrument_id','contribution_status_id' ) 
                         as $field ) {
                    $defaults[$this->_id][$field] =  CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_Contribution', 
                                                                                  $recordContribution, $field );
                }
            }
            $defaults[$this->_id]['send_receipt'] = 0;
        }
        
        if( isset( $defaults[$this->_id]['event_id'] ) ) {
            $defaults[$this->_id]['receipt_text'] = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event', 
                                                                                 $defaults[$this->_id]['event_id'], 
                                                                                 'receipt_text' );
        }
        if( isset($this->_groupTree) ) {
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults[$this->_id], $viewMode, $inactiveNeeded );
        }
        
        //require_once "CRM/Core/BAO/CustomOption.php";
        require_once 'CRM/Core/BAO/PriceSet.php';
        if ( $priceSetId = CRM_Core_BAO_PriceSet::getFor( 'civicrm_event_page', $this->_eId ) ) {
            $fields = array( );
    
            $eventLevel = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, 
                                   substr( $defaults[$this->_id]['event_level'], 1, -1 ) );
            foreach ( $eventLevel as $id => $name ) {
                $optionValue         = new CRM_Core_BAO_OptionValue( );
                $optionValue->label  = $name;
                $optionValue->find( true );
                
                if ($optionValue->option_group_id ){
                    $groupName       = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionGroup', $optionValue->option_group_id, 'name' );
                    $fieldName       = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_PriceField', substr( $groupName, -1, 1 ), 'label') ;
                    $eventLevel[$id] = array( 'fieldName'   => $fieldName,
                                              'optionLabel' => $name );
                }
            }
            //for the texfield default value
            foreach ( $eventLevel as $id => $values ) {
                if( !is_array( $values ) ){
                    $textLevel       = explode( ' - ', $values );
                    $eventLevel[$id] = array( 'fieldName'   => $textLevel[0],
                                              'optionLabel' => $textLevel[1] );
                }       
            }
            
            require_once 'CRM/Core/BAO/PriceField.php';
            foreach ( $eventLevel as $values ) {
                
                $priceField        = new CRM_Core_BAO_PriceField( );
                $priceField->label = $values['fieldName'];
                
                $priceField->find( true );
                
                // FIXME: we are not storing qty for text type (for
                // offline mode). Hence can not set defaults for Text
                // type price field
                if ( $priceField->html_type == 'Text' ) {
                    $defaults[$this->_id]["price_{$priceField->id}"] = $values['optionLabel'];
                    continue;
                }
                
                $optionId = CRM_Core_BAO_PriceField::getOptionId( $values['optionLabel'], $priceField->id );
                
                if ( $priceField->html_type == 'CheckBox' ) {
                    $defaults[$this->_id]["price_{$priceField->id}"][$optionId] = 1;
                    continue;
                }
                $defaults[$this->_id]["price_{$priceField->id}"] = $optionId;
            }
        } else {
            if ( $defaults[$this->_id]['event_id'] && ($this->_action == CRM_Core_Action::UPDATE ) ) {
                $optionGroupId = CRM_Core_DAO::getFieldValue( "CRM_Core_DAO_OptionGroup", 
                                                              'civicrm_event_page.amount.' . 
                                                              CRM_Core_DAO::getFieldValue( "CRM_Event_DAO_EventPage", $defaults[$this->_id]['event_id'], 'id', 'event_id' ), 
                                                              'id', 
                                                              'name' );
                $optionParams = array( 'option_group_id' => $optionGroupId,
                                       'label'           => CRM_Utils_Array::value('event_level',$defaults[$this->_id]) );
                
                CRM_Core_BAO_CustomOption::retrieve( $optionParams, $params );
                $defaults[$this->_id]['amount'] = $params['id'];
            } elseif ( $defaults[$this->_id]['event_id'] && ($this->_action == CRM_Core_Action::ADD ) ) {
                $defaults[$this->_id]['amount'] = CRM_Core_DAO::getFieldValue( "CRM_Event_DAO_EventPage", 
                                                                               $defaults[$this->_id]['event_id'], 
                                                                               'default_fee_id', 
                                                                               'event_id' );
            }
        }
        $this->assign( 'event_is_test', CRM_Utils_Array::value('event_is_test',$defaults[$this->_id]) );
        return $defaults[$this->_id];
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
        

        if ( $this->_single ) {
            $urlParams = "reset=1&cid={$this->_contactID}&context=participant";
            if ( $this->_id ) {
                $urlParams .= "&action=update&id={$this->_id}";
            } else {
                $urlParams .= "&action=add";
            }
            
            if (CRM_Utils_Request::retrieve( 'past', 'Boolean', $this ) ) {
                $urlParams .= "&past=true";
            }
            
            $url = CRM_Utils_System::url( 'civicrm/contact/view/participant',
                                          $urlParams, true, null, false ); 
        } else {
            $currentPath = CRM_Utils_System::currentPath( );

            $url = CRM_Utils_System::url( $currentPath, '_qf_Participant_display=true',
                                          true, null, false  );
        }

        $this->assign("refreshURL",$url);
        $url .= "&past=true";
        $this->assign("pastURL", $url);
        
        $events = array( );
        $this->assign("past", false);
        
        require_once "CRM/Event/BAO/Event.php";
        if ( CRM_Utils_Request::retrieve( 'past', 'Boolean', $this ) || ( $this->_action & CRM_Core_Action::UPDATE ) ) {
            $events = CRM_Event_BAO_Event::getEvents( true );
            $this->assign("past", true);
        } else {
            $events = CRM_Event_BAO_Event::getEvents( );
        }
        
        $this->add('select', 'event_id',  ts( 'Event' ),  
                   array( '' => ts( '-select-' ) ) + $events,
                   true,
                   array('onchange' => "if (this.value) reload(true); else return false") );
        
        $this->add( 'date', 'register_date', ts('Registration Date and Time'),
                    CRM_Core_SelectValues::date('activityDatetime' ),
                    true);   
        $this->addRule('register_date', ts('Select a valid date.'), 'qfDate');
         
        $this->add( 'select', 'role_id' , ts( 'Participant Role' ),
                    array( '' => ts( '-select-' ) ) + CRM_Event_PseudoConstant::participantRole( ),
                    true,
                    array('onchange' => "if (this.value) reload(true); else return false") );
        
        $this->add( 'select', 'status_id' , ts( 'Participant Status' ),
                    array( '' => ts( '-select-' ) ) + CRM_Event_PseudoConstant::participantStatus( ),
                    true );
        
        $this->add( 'text', 'source', ts('Event Source') );
        if ( isset( $this->_eId ) ) {
            $params = array( 'id' => $this->_eId );
            CRM_Event_BAO_Event::retrieve( $params, $this->_event );
        }
        
        if ( $this->_event['is_monetary'] ) {
            require_once "CRM/Event/BAO/EventPage.php";
            $params = array( 'event_id' => $this->_eId );
            CRM_Event_BAO_EventPage::retrieve( $params, $eventPage );

            //retrieve custom information
            $this->_values = array( );
            require_once "CRM/Event/Form/Registration/Register.php";
            CRM_Event_Form_Registration::initPriceSet($this, $eventPage['id'] );
            CRM_Event_Form_Registration_Register::buildAmount( $this, false );
        } else {
            $this->add( 'text', 'amount', ts('Event Fee(s)') );
        }
        $this->assign("paid", $this->_event['is_monetary'] );

        $noteAttributes = CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Note' );
        $this->add('textarea', 'note', ts('Notes'), $noteAttributes['note']);
        

        $session = & CRM_Core_Session::singleton( );
        $uploadNames = $session->get( 'uploadNames' );
        if ( is_array( $uploadNames ) && ! empty ( $uploadNames ) ) {
            $buttonType = 'upload';
        } else {
            $buttonType = 'next';
        }

        if ( $this->_single ) {        
            $this->addElement('checkbox', 'record_contribution', ts('Record Payment?'), null, 
                              array('onclick' =>"return showHideByValue('record_contribution','','recordContribution','block','radio',false);"));
            
            require_once 'CRM/Contribute/PseudoConstant.php';
            $this->add('select', 'contribution_type_id', 
                       ts( 'Contribution Type' ), 
                       array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::contributionType( ) );
            
            $this->add('select', 'payment_instrument_id', 
                       ts( 'Paid By' ), 
                       array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::paymentInstrument( )
                       );
            
            $this->add('select', 'contribution_status_id',
                       ts('Payment Status'), 
                       CRM_Contribute_PseudoConstant::contributionStatus( )
                       );
            
            
            $this->addElement('checkbox', 
                              'send_receipt', 
                              ts('Send Confirmation?'), null, 
                              array('onclick' =>"return showHideByValue('send_receipt','','notice','block','radio',false);") );
            $this->add('textarea', 'receipt_text', ts('Confirmation Message') );
        }

        // Retrieve the name and email of the contact - this will be the TO for receipt email
        if ( $this->_contactID ) {
            list( $this->_contributorDisplayName, $this->_contributorEmail ) = CRM_Contact_BAO_Contact::getEmailDetails( $this->_contactID );
            $this->assign( 'email', $this->_contributorEmail );
        }

        //build custom data
        CRM_Core_BAO_CustomGroup::buildQuickForm( $this, $this->_groupTree, 'showBlocks1', 'hideBlocks1' );
        
        $this->addButtons(array( 
                                array ( 'type'      => $buttonType, 
                                        'name'      => ts('Save'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );

        if ($this->_action == CRM_Core_Action::VIEW) { 
            $this->freeze();
        }
    }
    
    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) 
    {
        $this->addFormRule( array( 'CRM_Event_Form_Participant', 'formRule'), $this->_id );
    }
    
    /**
     * global validation rules for the form
     *
     * @param array $fields posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @static
     * @access public
     */
    static function formRule( &$values, $form, $id ) 
    {
        // If $values['_qf_Participant_next'] is Delete or 
        // $values['event_id'] is empty, then return 
        // instead of proceeding further.
        
        if ( ( $values['_qf_Participant_next'] == 'Delete' ) ||  
             ( ! $values['event_id'] ) 
             ) {
            return true;
        }

        if ( $values['status_id'] == 1 || $values['status_id'] == 2 ) {
            if ( $id ) {
                $previousStatus = CRM_Core_DAO::getFieldValue( "CRM_Event_DAO_Participant", $id, 'status_id' );
            }
            if ( !( $previousStatus == 1 || $previousStatus == 2 ) ) {
                require_once "CRM/Event/BAO/Participant.php";
                $message = CRM_Event_BAO_Participant::eventFull( $values['event_id'] );
            }
        }
        if( $message ) {
            $errorMsg["_qf_default"] = $message;  
        }

        return empty( $errorMsg ) ? true : $errorMsg;
    }    
       
    /** 
     * Function to process the form 
     * 
     * @access public 
     */ 
    public function postProcess( )
    {
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            require_once "CRM/Event/BAO/Participant.php";
            CRM_Event_BAO_Participant::deleteParticipant( $this->_id );
            return;
        }
        // get the submitted form values.  
        $params = $this->controller->exportValues( $this->_name );
        if ( $this->_event['is_monetary'] ) {
            if ( empty( $params['priceSetId'] ) ) {
                $params['amount_level'] = $this->_values['custom']['label'][array_search( $params['amount'], 
                                                                                          $this->_values['custom']['amount_id'])];
                $params['amount']       = $this->_values['custom']['value'][array_search( $params['amount'], 
                                                                                          $this->_values['custom']['amount_id'])];
            } else {
                $lineItem = array( );
                CRM_Event_Form_Registration_Register::processPriceSetAmount( $this->_values['custom']['fields'], 
                                                                             $params, $lineItem );
                $this->set( 'lineItem', $lineItem );
            }
	    
            $params['event_level']              = $params['amount_level'];
            $contributionParams                 = array( );
            $contributionParams['total_amount'] = $params['amount'];
        }
        
        unset($params['amount']);
        $params['register_date'] = CRM_Utils_Date::format($params['register_date']);
        $params['contact_id']    = $this->_contactID;
        if ( $this->_id ) {
            $ids['participant']  = $params['id'] = $this->_id;
        }
        
        $ids['note'] = array( );
        if ( $this->_noteId ) {
            $ids['note']['id']   = $this->_noteId;
        }
        
        $status = null;
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            $participantBAO     =& new CRM_Event_BAO_Participant( );
            $participantBAO->id = $this->_id;
            $participantBAO->find( );
            while ( $participantBAO->fetch() ) {
                $status = $participantBAO->status_id;
            }
        }
        
        // format custom data
        // get mime type of the uploaded file
        if ( !empty($_FILES) ) {
            foreach ( $_FILES as $key => $value) {
                $files = array( );
                if ( $params[$key] ) {
                    $files['name'] = $params[$key];
                }
                if ( $value['type'] ) {
                    $files['type'] = $value['type']; 
                }
                $params[$key] = $files;
            }
        }
       
        $customData = array( );
        foreach ( $params as $key => $value ) {
            if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID($key) ) {
                CRM_Core_BAO_CustomField::formatCustomField( $customFieldId, $customData,
                                                             $value, 'Participant', null, $this->_id);
            }
        }
        if (! empty($customData) ) {
            $params['custom'] = $customData;
        }
        
        //special case to handle if all checkboxes are unchecked
        $customFields = CRM_Core_BAO_CustomField::getFields( 'Participant' );
        if ( !empty($customFields) ) {
            foreach ( $customFields as $k => $val ) {
                if ( in_array ( $val[3], array ('CheckBox','Multi-Select') ) &&
                     ! CRM_Utils_Array::value( $k, $params['custom'] ) ) {
                    CRM_Core_BAO_CustomField::formatCustomField( $k, $params['custom'],
                                                                 '', 'Participant', null, $this->_id);
                }
            }
        }
        
        require_once 'CRM/Contact/BAO/Contact.php';
        // Retrieve the name and email of the current user - this will be the FROM for the receipt email
        $session =& CRM_Core_Session::singleton( );
        $userID  = $session->get( 'userID' );
        list( $userName, $userEmail ) = CRM_Contact_BAO_Contact::getEmailDetails( $userID );
        require_once "CRM/Event/BAO/Participant.php";
        if ( $this->_single ) {
            $participant = CRM_Event_BAO_Participant::create( $params, $ids );   
        } else {
            $ids = array( );
            foreach ( $this->_contactIds as $contactID ) {
                $params['contact_id'] = $contactID;
                CRM_Event_BAO_Participant::create( $params, $ids );   
            }           
        }

        if ( $params['record_contribution'] && $this->_single ) {
            if( $ids['participant'] ) {
                $ids['contribution'] = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_ParticipantPayment', 
                                                                    $ids['participant'], 
                                                                    'contribution_id', 
                                                                    'participant_id' );
            }
            unset($params['note']);

            //building contribution params 
            
            $config =& CRM_Core_Config::singleton();
            $contributionParams['currency'             ] = $config->defaultCurrency;
            $contributionParams['contact_id'           ] = $params['contact_id'];
            $contributionParams['source'               ] = "Offline registration (by {$userName})";
            $contributionParams['non_deductible_amount'] = 'null';
            $contributionParams['receive_date'         ] = date( 'Y-m-d H:i:s' );
            $contributionParams['receipt_date'         ] = $params['send_receipt'] ? 
                $contributionParams['receive_date'] : 'null';
            $recordContribution = array( 'contribution_type_id', 
                                         'payment_instrument_id', 'contribution_status_id' );

            foreach ( $recordContribution as $f ) {
                $contributionParams[$f] = CRM_Utils_Array::value( $f, $params );
            }   
            require_once 'CRM/Contribute/BAO/Contribution.php';
            $contribution =& CRM_Contribute_BAO_Contribution::create( $contributionParams, $ids );

            //insert payment record for this participation
            if( !$ids['contribution'] ) {
                require_once 'CRM/Event/DAO/ParticipantPayment.php';
                $ppDAO =& new CRM_Event_DAO_ParticipantPayment();    
                $ppDAO->participant_id  = $participant->id;
                $ppDAO->contribution_id = $contribution->id;
                $ppDAO->save(); 
            }
        }

        if ( $params['send_receipt'] && $this->_single ) {
            require_once 'CRM/Core/DAO.php';
            CRM_Core_DAO::setFieldValue( 'CRM_Event_DAO_Event', 
                                         $params['event_id'],
                                         'receipt_text',
                                         $params['receipt_text'] );
            // Retrieve the name and email of the contact - this will be the TO for receipt email
            list( $this->_contributorDisplayName, $this->_contributorEmail ) = CRM_Contact_BAO_Contact::getEmailDetails( $this->_contactID );

            // retrieve custom data
            require_once "CRM/Core/BAO/UFGroup.php";
            $customFields = $customValues = array( );
            foreach ( $this->_groupTree as $groupID => $group ) {
                if ( $groupID == 'info' ) {
                    continue;
                }
                foreach ( $group['fields'] as $k => $field ) {
                    $field['title'] = $field['label'];
                    $customFields["custom_{$k}"] = $field;
                }
            }
            
            CRM_Core_BAO_UFGroup::getValues( $this->_contactID, $customFields, $customValues , false, 
                                             array( array( 'participant_id', '=', $participant->id, 0, 0 ) ) );
            $receiptFrom = '"' . $userName . '" <' . $userEmail . '>';
            
            $this->assign( 'module', 'Event Registration' );
            $this->assign( 'event', CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event',
                                                                 $params['event_id'],
                                                                 'title') );
            $this->assign( 'receipt_text', $params['receipt_text'] );
            $role = CRM_Event_PseudoConstant::participantRole();
            $this->assign( 'role', $role[$params['role_id']] );
            $status = CRM_Event_PseudoConstant::participantStatus();
            if ( $this->_event['is_monetary'] ) {
                $paymentInstrument = CRM_Contribute_PseudoConstant::paymentInstrument();
                $this->assign( 'paidBy', $paymentInstrument[$params['payment_instrument_id']] );
                $this->assign( 'total_amount', $contributionParams['total_amount'] );
            }
            $this->assign( 'status', $status[$params['status_id']] );

            $this->assign( 'register_date', CRM_Utils_Date::customFormat($params['register_date']) );
            $this->assign( 'receive_date', $contributionParams['receive_date'] );            
            $this->assign( 'subject', ts('Event Confirmation') );
            $this->assign( 'customValues', $customValues );

            $template =& CRM_Core_Smarty::singleton( );
            $subject = trim( $template->fetch( 'CRM/Contribute/Form/ReceiptSubjectOffline.tpl' ) );
            $message = $template->fetch( 'CRM/Contribute/Form/ReceiptMessageOffline.tpl' );

            require_once 'CRM/Utils/Mail.php';
            CRM_Utils_Mail::send( $receiptFrom,
                                  $this->_contributorDisplayName,
                                  $this->_contributorEmail,
                                  $subject,
                                  $message);
        }      
        
        if ( ( $this->_action & CRM_Core_Action::UPDATE ) ) {
            $statusMsg = ts('Event registration information for %1 has been updated.', array(1 => $this->_contributorDisplayName));
            if ( $params['send_receipt'] ) {
                $statusMsg .= ts('A confirmation email has been sent to %1', array(1 => $this->_contributorEmail));
            }
        } elseif ( ( $this->_action & CRM_Core_Action::ADD ) ) {
            if ( $this->_single ) {
                $statusMsg = ts('Event registration for %1 has been added. ', array(1 => $this->_contributorDisplayName));
                if ( $params['send_receipt'] ) {
                    $statusMsg .= ts('A confirmation email has been sent to %1.', array(1 => $this->_contributorEmail));
                }
            } else {
                $statusMsg = ts('Total Participant(s) added to event: %1', array(1 => count($this->_contactIds)));
            }
        }
        require_once "CRM/Core/Session.php";
        CRM_Core_Session::setStatus( "{$statusMsg}" );
    }
}
?>
