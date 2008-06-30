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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/PledgeBank/Form/ManagePledgeBank.php';
require_once "CRM/Core/BAO/CustomGroup.php";
require_once "CRM/Custom/Form/CustomData.php";
require_once "CRM/Core/BAO/CustomField.php";

/**
 * This class generates form components for processing Pledges 
 * 
 */
class CRM_PledgeBank_Form_ManagePledgeBank_PledgeInfo extends CRM_PledgeBank_Form_ManagePledgeBank
{
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( )
    {
        parent::preProcess( );
    }
    
    /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     *
     * @access public
     * @return None
     */
    
    function setDefaultValues( )
    {
        $defaults = parent::setDefaultValues();
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
        $this->assign('entityId',  $this->_id );
        
        $this->_first = true;
        $this->applyFilter('__ALL__', 'trim');
        $attributes = CRM_Core_DAO::getAttribute('CRM_PledgeBank_DAO_Pledge');
        
        $this->add('text', 'creator_name', ts('Creators name'), $attributes['creator_name'], true);
        $this->add('text', 'creator_pledge_desc', ts('Creators pledge'), $attributes['creator_pledge_desc'], true);
        $this->add('text','signers_limit', ts('Required number of signers'), $attributes['signers_limit'], true );
        $this->addRule('signers_limit', ts('is a positive field') , 'positiveInteger');
        
        $this->add('text', 'signer_description_text', ts('Description of signers'), $attributes['signer_description_text'], true);

        $this->add('text', 'signer_pledge_desc', ts('Signers pledge'), $attributes['signer_pledge_desc'], true);
        $this->add( 'date', 'deadline', ts('Pledge deadline'), CRM_Core_SelectValues::date('relative'), true );
        $this->addRule('deadline', ts('Please select a valid deadline.'), 'qfDate');
        $this->addWysiwyg( 'description', ts('Pledge detailed Description'),$attributes['description']);
      
        
        $this->add('text', 'creator_description', ts('Pledge owners description'), $attributes['creator_description']);
       
        
        $this->addElement('checkbox', 'is_active', ts('Is this Pledge Active?') );
        $this->addFormRule( array( 'CRM_PledgeBank_Form_ManagePledgeBank_PledgeInfo', 'formRule' ) );
        
        parent::buildQuickForm();
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
        return true;
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $params = array();
        $params = $this->controller->exportValues( $this->_name );
        
        //format params
        $params['deadline'    ] = CRM_Utils_Date::format($params['deadline']);
        $params['is_active'   ] = CRM_Utils_Array::value('is_active', $params, false);
        $params['created_date'] = date( 'YmdHis' );
        $session =& CRM_Core_Session::singleton( );
        $params['creator_id' ]  = $session->get( 'userID' );
        
        $params['id']       = $this->_id;
        
        require_once 'CRM/PledgeBank/BAO/Pledge.php';
        $pledge =  CRM_PledgeBank_BAO_Pledge::add($params);
        
        $this->set( 'id', $pledge->id );
        
    }//end of function
    
    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) 
    {
        return ts('Pledge Settings');
    }
}

