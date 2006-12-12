<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                |
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
 | Foundation at info[AT]civicrm[DOT]org. If you have questions       |
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
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_ManageEvent_Registration extends CRM_Core_Form
{

  /**
     * This function sets the default values for the form. 
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        if ( $tempDefaults['regLinkText'] ) {
            $this->assign("showReg" , true );
            $this->assign("confirmReg" , true );
            $this->assign("mailReg" , true );
        }
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

        $this->addElement('checkbox', 'is_online_registration', ts('Allow Online Registration?') );

        $this->add('text','regLinkText',ts('Registration Link Text'));
       
        self::buildRegistrationBlock( $this, $this->_id);
        self::buildConfirmationBlock( $this, $this->_id);
        self::buildMailBlock( $this, $this->_id);
            
        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Save'),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );
        return;
    }
    
    /**
     * Function to build Registration Block  
     * 
     * @param int $pageId 
     * @static
     */

    function buildRegistrationBlock( $form ) 
    {
        $form->add('textarea','intro_text',ts('Intro Text'), array("rows"=>6,"cols"=>80));
        $form->add('textarea','footer_text',ts('Footer Text'), array("rows"=>6,"cols"=>80));
        $form->add('select', 'participant_info_1', ts('Custom Data 1'),array(''=>'-select-'));
        $form->add('select', 'participant_info_2', ts('Custom Data 2'),array(''=>'-select-'));
    }
    

    /**
     * Function to build Confirmation Block  
     * 
     * @param int $pageId 
     * @static
     */

    function buildConfirmationBlock( $form) 
    {
        $form->add('text','confirm_title',ts('Title '));   
        $form->add('textarea','confirm_text',ts('Intro Text'), array("rows"=>6,"cols"=>80));
        $form->add('textarea','confirm_footer_text',ts('Footer Text'), array("rows"=>6,"cols"=>80));
    }

    /**
     * Function to build Email Block  
     * 
     * @param int $pageId 
     * @static
     */

    function buildMailBlock( $form ) 
    {
        $form->addYesNo( 'is_email_confirm', ts( 'Send Confirmation Email?' ) , null, false);
        $form->add('textarea','confirm_email_text',ts('Text'), array("rows"=>2,"cols"=>60));
        $form->add('text','cc_confirm',ts('CC Confirmation To '));  
        $form->add('text','cc_confirm',ts('BCC Confirmation To '));  
    }

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) 
    {
        return ts('Online Registration');
    }

}
?>
