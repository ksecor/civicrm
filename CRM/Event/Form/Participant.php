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

        $urlParams = "reset=1&cid={$this->_contactID}&context=event";
        if ( $this->_id ) {
            $urlParams .= "&action=update&id={$this->_id}";
        } else {
            $urlParams .= "&action=add";
        }

        $url = CRM_Utils_System::url( 'civicrm/contact/view/event',
                                      $urlParams, true, null, false ); 

        $this->assign("refreshURL",$url);

        $url = CRM_Utils_System::url( 'civicrm/contact/view/participant',
                                      $urlParams, true, null, false ); 

        $this->assign("pastURL",$url."&past=true");

        $past   = CRM_Utils_Request::retrieve( 'past', 'Boolean', $this );
        $st     = CRM_Event_PseudoConstant::event( );
        $params = array( );

        require_once "CRM/Event/BAO/Event.php";
        foreach( $st as $key => $val ) {
            $params['id'] = $key;
            CRM_Event_BAO_Event::retrieve($params, $def);
            if ( !CRM_Utils_Date::overdue($def['start_date']) ) {
                $st[$key] .=  '  ('.CRM_Utils_Date::customFormat($def['start_date']).' )';
            } else {
                $extra[$key] = $st[$key] . '  ('.CRM_Utils_Date::customFormat($def['start_date']).' )';
                unset( $st[$key] );
            }
        }        

        if ( ( $past && CRM_Core_Action::ADD ) || ( $this->_action & CRM_Core_Action::UPDATE ) ) {
            $event = array_merge($st,$extra);
        } else {
            $event = $st;
        }
        
        $element =& $this->add('select', 'event_id',  ts( 'Event' ),  array( '' => ts( '-select-' ) ) + $event, 'true' );
        
        $element =& $this->add( 'date', 'register_date', 
                                ts('Registration Date'), 
                                CRM_Core_SelectValues::date('manual', 3, 1), false );   
         
        $element =& $this->add( 'select', 'role_id' , 
                                ts( 'Participant Role' ),
                                array( '' => ts( '-select-' ) ) + CRM_Event_PseudoConstant::participantRole( ) 
                                );
        
        $element =& $this->add( 'select', 'status_id' , 
                                ts( 'Participant Status' ),
                                array( '' => ts( '-select-' ) ) + CRM_Event_PseudoConstant::participantStatus( )
                                );

        $element =& $this->add( 'text', 'source', ts('Event Source') );
        $element =& $this->add( 'text', 'event_level', ts('Event Level') );
        
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
        $formValues           = $_POST;
        $config               =& CRM_Core_Config::singleton( );
        $params               = array( );
        $ids                  = array( );
        $params['contact_id'] = $this->_contactID;

        $fields               = array( 'event_id',
                                       'fee_amount',
                                       'register_date',
                                       'role_id',
                                       'status_id',
                                       'source',
                                       'event_level'
                                       );

        foreach ( $fields as $f ) {
            if( $f == 'event_id' ) {
                $params[$f] = CRM_Utils_Array::value( $f, $formValues );
            } else if ( $f == 'fee_amount' ) {
                $params[$f] = CRM_Utils_Rule::cleanMoney( $formValues[$f] );    
            } else if ( $f == 'register_date' ) {
                if ( ! CRM_Utils_System::isNull( $formValues[$f] ) ) {
                    $params[$f]      = array( );
                    $params[$f]['H'] = '00';
                    $params[$f]['i'] = '00';
                    $params[$f]['s'] = '00';
                    $params[$f]      =  CRM_Utils_Date::format( $formValues[$f] );
                }   
            } else if ( $f == 'role_id' ) {
                $params[$f] = $formValues[$f];
            } else if ( $f == 'status_id' ) {
                $params[$f] = $formValues[$f];
            } else if ( $f == 'source' ) {
                $params[$f] = $formValues[$f];
            } else if ( $f == 'event_level' ) {
                $params[$f] = $formValues[$f];
            }            
        }
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            $ids['participant'] = $this->_id;
        }
        require_once "CRM/Event/BAO/Participant.php";
        CRM_Event_BAO_Participant::create( $params ,$ids );   
    }
}

?>
