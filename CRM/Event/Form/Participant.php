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
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    public function preProcess()  
    {  
        // action
        $this->_action = CRM_Utils_Request::retrieve( 'action', 'String',
                                                      $this, false, 'add' );
            
        $this->assign( 'action'  , $this->_action   ); 
        
        $this->_id        = CRM_Utils_Request::retrieve( 'id', 'Positive', 
                                                         $this );
        
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return;
        }
        
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );
        
        $this->_roleId = CRM_Utils_Request::retrieve( 'subType', 'Positive',$this );
        
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
            $this->_contactID = $defaults['contact_id'];
        } 
        
        $subType = CRM_Utils_Request::retrieve( 'subType', 'Positive', CRM_Core_DAO::$_nullObject );
        if ( $subType ) {
            $defaults["role_id"] = $subType;
        }
        
        if($this->_noteId) {
            $defaults['note'] = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Note', $this->_noteId, 'note' );
        }
        
        if ($this->_action & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
            $inactiveNeeded = true;
            $viewMode = true;
        } else {
            $viewMode = false;
            $inactiveNeeded = false;
        }

        if( isset($this->_groupTree) ) {
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, $viewMode, $inactiveNeeded );
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
        
        $this->add('select', 'event_id',  ts( 'Event' ),  array( '' => ts( '-select-' ) ) + $events, 'true' );
        
        $this->add( 'date', 'register_date', ts('Registration Date and Time'),CRM_Core_SelectValues::date('datetime' ),true);   
        $this->addRule('register_date', ts('Select a valid date.'), 'qfDate');
         
        $this->add( 'select', 'role_id' , ts( 'Participant Role' ),
                    array( '' => ts( '-select-' ) ) + CRM_Event_PseudoConstant::participantRole( ), true, array('onchange' => "reload(true)") );
        
        $this->add( 'select', 'status_id' , ts( 'Participant Status' ),
                    array( '' => ts( '-select-' ) ) + CRM_Event_PseudoConstant::participantStatus( ),true );
        
        $this->add( 'text', 'source', ts('Event Source') );
        $this->add( 'text', 'event_level', ts('Event Level') );
        
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
     * Function to process the form 
     * 
     * @access public 
     * @return None 
     */ 
    public function postProcess()  
    { 
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            require_once 'CRM/Event/BAO/Participant.php';
            CRM_Event_BAO_Participant::deleteParticipant( $this->_id );
            return;
        }
                
        // get the submitted form values.  
        $params = $_POST;
        $params['contact_id'] = $this->_contactID;
        $params['register_date'] = CRM_Utils_Date::format($params['register_date']);
        
        if ( $this->_id ) {
            $ids['participant'] = $params['id'] = $this->_id;
        }
        
        $ids['note'] = array();
        if ( $this->_noteId ) {
            $ids['note']['id'] = $this->_noteId;
        }
        
        require_once "CRM/Event/BAO/Participant.php";
        $participant =  CRM_Event_BAO_Participant::create( $params, $ids );   
        
        $status = null;
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            $participantBAO =& new CRM_Event_BAO_Participant();
            $participantBAO->id = $this->_id;
            $participantBAO->find();
            while ( $participantBAO->fetch() ) {
                $status = $participantBAO->status_id;
            }
        }
                
        if ( ($this->_action & CRM_Core_Action::ADD) || ($status != $params['status_id']) ) {
            CRM_Event_BAO_Participant::setActivityHistory( $participant );
        } 
    }
}
?>