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
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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
require_once 'CRM/Core/BAO/CustomGroup.php';

/**
 * This class generates form components for processing a participation 
 * in an event
 */
class CRM_Event_Form_Participant extends CRM_Contact_Form_Task
{
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

            $defaults[$this->_id]['register_date'] = array( );
            CRM_Utils_Date::getAllDefaultValues( $defaults[$this->_id]['register_date'] );
            $defaults[$this->_id]['register_date']['i'] = (int ) ( $defaults[$this->_id]['register_date']['i'] / 15 ) * 15;
        } else {
            $defaults[$this->_id]['register_date'] = CRM_Utils_Date::unformat($defaults[$this->_id]['register_date']);
            $defaults[$this->_id]['register_date']['i'] = (integer)($defaults[$this->_id]['register_date']['i']/15) *15;
        }

        if( isset($this->_groupTree) ) {
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults[$this->_id], $viewMode, $inactiveNeeded );
        }
        
        require_once "CRM/Core/BAO/CustomOption.php";
        $eventPage = array( 'entity_table' => 'civicrm_event_page',
                            'label'        => CRM_Utils_Array::value( 'event_level', $defaults[$this->_id] ) );
        CRM_Core_BAO_CustomOption::retrieve( $eventPage, $params );
        
        $defaults[$this->_id]['amount'] = $params['id'];
        $this->assign( 'event_is_test', CRM_Utils_Array::value( 'event_is_test', $defaults[$this->_id] ) );

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
            
            $this->assign("refreshURL",$url);
            
            $url .= "&past=true";
            
            $this->assign("pastURL", $url);
        } else {
            $currentPath = CRM_Utils_System::currentPath( );
            $url = CRM_Utils_System::url( $currentPath, '_qf_Participant_display=true',
                                          true, null, false  );
            $this->assign("refreshURL",$url);
        }
        
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
                    CRM_Core_SelectValues::date('datetime' ),
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

            $this->_values = array( );

            require_once "CRM/Event/Form/Registration/Register.php";

            CRM_Event_Form_Registration::initPriceSet( $this, $eventPage['id'] );
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
        $this->addFormRule( array( 'CRM_Event_Form_Participant', 'formRule' ) );
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
    static function formRule( &$values ) 
    {
        // If $values['_qf_Participant_next'] is Delete or 
        // $values['event_id'] is empty, then return 
        // instead of proceeding further.
        
        if ( ( $values['_qf_Participant_next'] == 'Delete' ) ||  
             ( ! $values['event_id'] ) 
             ) {
            return true;
        }
        
        require_once "CRM/Event/BAO/Participant.php";
        $message = CRM_Event_BAO_Participant::eventFull( $values['event_id'] );
        
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
        require_once "CRM/Event/BAO/Participant.php";
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            CRM_Event_BAO_Participant::deleteParticipant( $this->_id );
            return;
        }
        
        // get the submitted form values.  
        $params = $this->controller->exportValues( $this->_name );
        
        if(  $this->_event['is_monetary'] ) {
            CRM_Event_Form_Registration_Register::processPriceSetAmount( $this, $params );
            $params['event_level']    = $params['amount_level'];
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

        if ( $this->_single ) {
            CRM_Event_BAO_Participant::create( $params, $ids );   
        } else {
            $ids = array( );
            foreach ( $this->_contactIds as $contactID ) {
                $params['contact_id'] = $contactID;
                CRM_Event_BAO_Participant::create( $params, $ids );   
            }

            CRM_Core_Session::setStatus( ts('Total Participant(s) added to event: %1', 
                                            array(1 => count($this->_contactIds)))  );
            
        }
    }
}

?>
