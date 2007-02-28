<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Event/PseudoConstant.php';
require_once 'CRM/Core/BAO/CustomGroup.php';

/**
 * This class generates form components for processing a participation 
 * in an event
 */
class CRM_Event_Form_Participant extends CRM_Core_Form
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
    protected $_noteId;
    
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

        // action
        $this->_action = CRM_Utils_Request::retrieve( 'action', 'String', $this, false, 'add' );
            
        $this->assign( 'action'  , $this->_action   ); 
        
        $this->_id        = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );

        if ( $this->_id ) {
            $ids = array( );
            $params = array( 'id' => $this->_id );
            require_once "CRM/Event/BAO/Participant.php";
            CRM_Event_BAO_Participant::getValues( $params, $defaults, $ids );
            $this->_eId = $defaults[$this->_id]['event_id'];
        }
        
        if( CRM_Utils_Request::retrieve( 'eid', 'Positive', $this ) ) {
            $this->_eId       = CRM_Utils_Request::retrieve( 'eid', 'Positive', $this );            
        }
                
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return;
        }
        
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );
        
        $this->_roleId = CRM_Utils_Request::retrieve( 'rid', 'Positive',$this );
        
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
                $this->_roleId = CRM_Core_DAO::getFieldValue( "CRM_Event_DAO_Participant", $this->_id, "role_id" );
            } else {
                $this->_roleId = "Role";
            }
        }     
        $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree( "Participant", $this->_id, 0,$this->_roleId );
        parent::preProcess( );        
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
               
        if ( $this->_id ) {
            $ids = array( );
            $params = array( 'id' => $this->_id );
            require_once "CRM/Event/BAO/Participant.php";
            CRM_Event_BAO_Participant::getValues( $params, $defaults, $ids );
            $this->_contactID = $defaults[$this->_id]['contact_id'];
        } 
        
        $rId  = CRM_Utils_Request::retrieve( 'rid', 'Positive', CRM_Core_DAO::$_nullObject );
        if ( $rId ) {
            $defaults[$this->_id]["role_id"] = $rId;
        }

        if ( $this->_eId ) {
            $defaults[$this->_id]["event_id"] = $this->_eId;
        }
        
        if($this->_noteId) {
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
        } else {
            $defaults[$this->_id]['register_date']['i'] = ( $defaults[$this->_id]['register_date']['i'] )/15 *15;
        }
        
        if( isset($this->_groupTree) ) {
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults[$this->_id], $viewMode, $inactiveNeeded );
        }
        
        require_once "CRM/Core/BAO/CustomOption.php";
        $eventPage = array( 'entity_table' => 'civicrm_event_page',
                            'label'        => $defaults[$this->_id]['event_level']);
        CRM_Core_BAO_CustomOption::retrieve( $eventPage, $params );
        
        $defaults[$this->_id]['amount'] = $params['id'];

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

        if ($this->_action == CRM_Core_Action::VIEW) { 
            $this->freeze();
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
                   array( '' => ts( '-select-' ) ) + $events, true, array('onchange' => "reload(true)") );
        
        $this->add( 'date', 'register_date', ts('Registration Date and Time'),CRM_Core_SelectValues::date('datetime' ),true);   
        $this->addRule('register_date', ts('Select a valid date.'), 'qfDate');
         
        $this->add( 'select', 'role_id' , ts( 'Participant Role' ),
                    array( '' => ts( '-select-' ) ) + CRM_Event_PseudoConstant::participantRole( ), true, array('onchange' => "reload(true)") );
        
        $this->add( 'select', 'status_id' , ts( 'Participant Status' ),
                    array( '' => ts( '-select-' ) ) + CRM_Event_PseudoConstant::participantStatus( ),true );
        
        $this->add( 'text', 'source', ts('Event Source') );
        
        $params = array( 'id' => $this->_eId );
        CRM_Event_BAO_Event::retrieve( $params, $this->_event );
        
        if ( $this->_event['is_monetary'] ) {
            
            require_once "CRM/Event/BAO/EventPage.php";
            $params = array( 'event_id' => $this->_eId );
            CRM_Event_BAO_EventPage::retrieve( $params, $eventPage );
            
            //retrieve custom information
            require_once 'CRM/Core/BAO/CustomOption.php';
            CRM_Core_BAO_CustomOption::getAssoc( 'civicrm_event_page', $eventPage['id'], $this->_values['custom'] );
            
            require_once "CRM/Event/Form/Registration/Register.php";
            CRM_Event_Form_Registration_Register::buildAmount( );
        } else {
            $this->add( 'text', 'amount', ts('Event Fee(s)') );
        }
        
        $noteAttributes = CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Note' );
        
        $this->add('textarea', 'note', ts('Notes'), $noteAttributes['note']);
        
        $session = & CRM_Core_Session::singleton( );
        $uploadNames = $session->get( 'uploadNames' );
        if ( is_array( $uploadNames ) && ! empty ( $uploadNames ) ) {
            $buttonType = 'upload';
        } else {
            $buttonType = 'next';
        }
        
        if ($this->_action & CRM_Core_Action::VIEW ) {
            CRM_Core_BAO_CustomGroup::buildViewHTML( $this, $this->_groupTree );
        } else {
            CRM_Core_BAO_CustomGroup::buildQuickForm( $this, $this->_groupTree, 'showBlocks1', 'hideBlocks1' );
        }
        
        $this->addButtons(array( 
                                array ( 'type'      => $buttonType, 
                                        'name'      => ts('Save'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );
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
        $params = $_POST;
        
        if( ! $this->_event['is_monetary'] ) {
            $params['event_level']    = $params['amount'];
        } else {
            require_once "CRM/Core/BAO/CustomOption.php";
            $eventPage = array( 'id' => $params['amount'] );
            CRM_Core_BAO_CustomOption::retrieve( $eventPage, $pageInfo);
            
            $params['event_level']    = $pageInfo['label'];
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
        
        $participant =  CRM_Event_BAO_Participant::create( $params, $ids );   
        
        // do the updates / insert with custom data
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $groupTree =& CRM_Core_BAO_CustomGroup::getTree("Participant", $participant->id, 0, $params['role_id']);

        CRM_Core_BAO_CustomGroup::postProcess( $groupTree, $params );
        CRM_Core_BAO_CustomGroup::updateCustomData($groupTree, "Participant", $participant->id); 
                
        if ( ( $this->_action & CRM_Core_Action::ADD ) || ( $status != $params['status_id'] ) ) {
            CRM_Event_BAO_Participant::setActivityHistory( $participant );
        }       
    }
}
?>