<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
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

require_once 'CRM/Friend/BAO/Friend.php';
require_once 'CRM/Contribute/Form/ContributionPage.php';

/**
 * This class generates form components for Tell A Friend
 * 
 */
class CRM_Friend_Form_Contribute extends CRM_Contribute_Form_ContributionPage
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
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    public function setDefaultValues( ) 
    {
        $title = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage', $this->_id, 'title' );
        CRM_Utils_System::setTitle(ts('Tell A Friend (%1)', array(1 => $title)));       

        $defaults = array( );           
        
        if ( isset($this->_id)  ) {
            $defaults['entity_table'] = 'civicrm_contribution_page';            
            $defaults['entity_id']    = $this->_id; 
            CRM_Friend_BAO_Friend::getValues($defaults);
            $this->_friendId = CRM_Utils_Array::value('id',$defaults);
        } 

         if ( !$this->_friendId ) {
            $defaults['intro'] = ts('Help us spread the word and leverage the power of your contribution by telling your friends. Use the space below to personalize your email message - let your friends know why you support us. Then fill in the name(s) and email address(es) and click \'Send Your Message\'.');
            $defaults['suggested_message'] = ts('Thought you might be interested in learning about and helping this organization. I think they do important work.');
            $defaults['thankyou_text'] = ts('Thanks for telling your friends about us and supporting our efforts. Together we can make a difference.');
            $defaults['title'] = ts('Tell A Friend');
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
           
        $formValues['entity_table'] = 'civicrm_contribution_page';            
        $formValues['entity_id']    = $this->_id;

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

