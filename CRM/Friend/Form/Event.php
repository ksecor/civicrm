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

require_once 'CRM/Friend/BAO/Friend.php';
require_once 'CRM/Event/Form/ManageEvent.php';

/**
 * This class generates form components for Tell A Friend
 * 
 */
class CRM_Friend_Form_Event extends CRM_Event_Form_ManageEvent
{
    /** 
     * tell a friend id in db
     * 
     * @var int 
     */ 
    private $_friendId; 

    public function preProcess()  
    {  
        parent::preProcess();        
    }

    /**
     * This function sets the default values for the form. 
     * 
     * @access public
     * @return None
     */
    public function setDefaultValues( ) 
    {   
        $defaults = array( );         
        
        if ( isset($this->_id)  ) {
            $defaults['entity_table'] = 'civicrm_event_page';            
            $defaults['entity_id']    = $this->_id; 
            CRM_Friend_BAO_Friend::getValues($defaults);
            $this->_friendId = CRM_Utils_Array::value( 'id', $defaults );
        } 
       
        if ( !$this->_friendId ) {
            $defaults['intro'] = ts('Help us spread the word about this event. Use the space below to personalize your email message - let your friends know why you\'re attending. Then fill in the name(s) and email address(es) and click \'Send Your Message\'.');
            $defaults['suggested_message'] = ts('Thought you might be interested in checking out this event. I\'m planning on attending.');
            $defaults['thankyou_text'] = ts('Thanks for spreading the word about this event to your friends.');
            $defaults['title'] = ts('Tell a Friend');
            $defaults['thankyou_title'] = ts('Thanks for Spreading the Word');
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
        CRM_Friend_BAO_Friend::buildFriendForm($this);
        parent::buildQuickForm( );
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );     
        
        $formValues['entity_table'     ] = 'civicrm_event_page';
        $formValues['entity_id'        ] = $this->_id;

        if ( ($this->_action & CRM_Core_Action::UPDATE) && $this->_friendId ) {
            $formValues['id'] = $this->_friendId ;
        }

        CRM_Friend_BAO_Friend::addTellAFriend( $formValues );
    }

     /** 
     * Return a descriptive name for the page, used in wizard header 
     * 
     * @return string 
     * @access public 
     */ 
    public function getTitle( ) {
        return ts( 'Tell a Friend' );
    }
}

