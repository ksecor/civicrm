<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for Membership Type
 * 
 */
class CRM_Friend_Form_Friend extends CRM_Core_Form
{

    public function preProcess()  
    {  
        
        $this->_action    = CRM_Utils_Request::retrieve( 'action', 'String',
                                                         $this, false, 'add' );
        $this->_id        = CRM_Utils_Request::retrieve( 'id', 'Positive',
                                                         $this );
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive',
                                                         $this );
        $this->_memType   = CRM_Utils_Request::retrieve( 'subType', 'Positive',
                                                         $this );

        
    }

    /**
     * This function sets the default values for the form. MobileProvider that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    public function setDefaultValues( ) {
        $defaults = array( );
        
        $this->_requestURI = explode('/', $_SERVER['REQUEST_URI']);        
        
        if( isset($this->_id)  ) {
            if( in_array('event',$this->_requestURI) ) {
                $defaults['entity_table'] = 'civicrm_event';
            } 
            else {
                $defaults['entity_table'] = 'civicrm_contribution_page';
            }
            
            $defaults['entity_id']    = $this->_id;
            require_once 'CRM/Friend/BAO/Friend.php';
            $friend =& new CRM_Friend_BAO_Friend( );
            $friend->copyValues( $defaults );        
            $friend->find(true) ;           
            
            CRM_Core_DAO::storeValues( $friend, $defaults );
        }    
    

        if ( ! $defaults['title']) {
            if( in_array('event',$this->_requestURI) ) {
                $defaults['intro'] = 'Help us spread the word about this event. Use the space below to personalize your email message - let your friends know why you\'re attending. Then fill in the name(s) and email address(es) and click "Send Your Message"';
                $defaults['suggested_message'] = 'Thought you might be interested in checking out this event.I\'m planning on attending.'.
                    $defaults['thankyou_text'] = 'Thanks for telling spreading the word about this event to your friends.';
            } else {
                $defaults['intro'] = 'Help us spread the word and leverage the power of your contribution by telling your friends. Use the space below to personalize your email message - let your friends know why you support us. Then fill in the name(s) and email address(es) and click "Send Your Message';
                $defaults['suggested_message'] = 'Thought you might be interested in learning about and helping this organization. I think they do important work.';
                
                $defaults['thankyou_text'] = 'Thanks for telling your friends about us and supporting our efforts. Together we can make a difference.';
            }
            
            $defaults['title'] = 'Tell A Friend';
            $defaults['thankyou_title'] = 'Thanks for Spreading the Word';
        }


        $this->_defaults = $defaults;
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
       
        $this->addElement('checkbox', 'is_active', ts( 'Tell A Friend enabled?' ),null,array('onclick' =>"friendBlock(this)") );
        // name
        $this->add('text', 'title', ts('Title'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'title'), true);

        
        // intro_text and footer_text
        $this->add('textarea', 'intro', ts('Introductory'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'intro'), true);

        $this->add('textarea', 'suggested_message', ts('Suggested Message'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'suggested_message'), false);

        $this->add('text','general_link',ts('Info Page Link'));
        
        $this->add('text', 'thankyou_title', ts('Thank-you Title'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'thankyou_title'), true );

       
        
        $this->add('textarea', 'thankyou_text', ts('Thank-you Message'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'thankyou_text') , true);

       $this->addButtons(array( 
                                    array ( 'type'      => 'next',
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
        require_once 'CRM/Friend/BAO/Friend.php';
       
        // get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );
        
        if( in_array('event',$this->_requestURI) ) {
            $formValues['entity_table'] = 'civicrm_event';
        } else {
            $formValues['entity_table'] = 'civicrm_contribution_page';            
        }
        $formValues['entity_id']    = $this->_id;
        
        $ids = CRM_Friend_BAO_Friend::getTrue( $this->_id, $formValues['entity_table'] );
        //crm_core_error::debug('$ids',$ids);
         CRM_Friend_BAO_Friend::create( $formValues, $ids );
    }
}
?>