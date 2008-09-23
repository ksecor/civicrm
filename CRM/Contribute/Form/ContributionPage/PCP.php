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

require_once 'CRM/Contribute/Form/ContributionPage.php';

class CRM_Contribute_Form_ContributionPage_PCP extends CRM_Contribute_Form_ContributionPage 
{
    function preProcess( ) 
    {
        parent::preProcess( );
    }

    function setDefaultValues( ) 
    {
        $defaults = array( );

        return $defaults;
    }

    function buildQuickForm( ) 
    {
        $this->addElement( 'checkbox',
                           'is_active',
                           ts( 'Enable Personal Campaign Pages?' ),
                           null,
                           array( 'onclick' => "pcpBlock(this)" ) );
	
        $this->addElement('checkbox', 'approval_required', ts('Administrator approval required for new Personal Campaign Pages') );
         
        $this->addElement('checkbox', 'tell_a_friend', ts("Allow 'Tell a friend' functionality") );
        
        $this->add('text', 'max_recipient_limit', ts("'Tell a friend' maximum recipients limit") );

        $this->add('text', 'create_pcp', ts("'Create Personal Campaign Page' link text") );
        
        parent::buildQuickForm( );
        $this->addFormRule(array('CRM_Contribute_Form_ContributionPage_PCP', 'formRule') , $this );
    }

    /**
     * Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    public static function formRule( &$params, &$files, $self ) 
    { 
        $errors = array( );
        if ( CRM_Utils_Array::value( 'is_active', $params ) ) {
        }
        return empty($errors) ? true : $errors;
    }

    function postProcess( ) 
    {
        // get the submitted form values.
        $params = $this->controller->exportValues( $this->_name );
        //crm_core_error::Debug('p',$params);
        //exit();
    }

    /** 
     * Return a descriptive name for the page, used in wizard header 
     * 
     * @return string 
     * @access public 
     */ 
    public function getTitle( ) 
    {
        return ts( 'Enable Personal Campaign Pages' );
    }

}


