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
/**
 * This class generates form components for processing a ontribution 
 * 
 */
class CRM_Contribute_Form_PCP_Campaign extends CRM_Core_Form
{
 
    public function preProcess()  
    {
       
    }

    function setDefaultValues( ) 
    {
    }
    
    
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    {
        $this->addElement('text', 'title', ts('Page Title') );
        $this->addElement('text', 'into_text', ts('Intro Text') ); 
        $this->addElement('date', 'page_active_from', ts('Page Active From'), CRM_Core_SelectValues::date('activityDate') ); 
        $this->addElement('date', 'page_active_until', ts('Page Active Until'), CRM_Core_SelectValues::date('activityDate') );
        $this->addElement('text', 'goal_amount', ts('Goal Amount') ); 
        $this->addElement('text', 'donate_link_text', ts('Donate Button Text') ); 
        $attrib = Array ('rows' => 8, 'cols' => 60 );
        $this->addWysiwyg( 'page_text', ts('Page Text'), $attrib ); 
        $this->add( 'file', 'uploadImageI', ts('Upload Image'), 'size=30 maxlength=60');
        $this->add( 'file', 'uploadImageII', '', 'size=30 maxlength=60');
        $this->addElement( 'checkbox', 'is_thermometer', ts('Display Personal Campaign Thermometer') );
        $this->addElement( 'checkbox', 'is_honor_roll', ts('Dispaly Honour Roll') );
        $this->addButtons( array( 
                                array ( 'type'      => 'submit',
                                        'name'      => ts('Save'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'reset',
                                        'name'      => ts('Reset'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'isDefault' => true   ) 
                                )
                           ); 
        
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
    }
    
    /** 
     * Function to process the form 
     * 
     * @access public 
     * @return None 
     */ 
    public function postProcess( )  
    {
        $session =& CRM_Core_Session::singleton( );

        $params  = $this->controller->exportValues( $this->_name );
        $params['contact_id']           = $session->get('userID');
        $params['contribution_page_id'] = $this->get('contribution_page_id');

        if ( $this->get('action') == CRM_Core_Action::ADD ) {
            $params['status_id'] = 1;
        }

        require_once 'CRM/Contribute/BAO/PCP.php';
        CRM_Contribute_BAO_PCP::add( $params );
    }
            
}

?>
