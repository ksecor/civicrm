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
    /**
     * Function to pre process the form
     *
     * @access public
     * @return None
     */
    function preProcess( ) 
    {
        parent::preProcess( );
    }
    
    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     *
     * @access public
     * @return void
     */
    function setDefaultValues( ) 
    {
        $defaults = array();
        if ( isset($this->_id ) ) {
            require_once 'CRM/Contribute/DAO/PCPBlock.php';
            $dao =& new CRM_Contribute_DAO_PCPBlock();
            $dao->entity_table = 'civicrm_contribution_page';
            $dao->entity_id = $this->_id; 
            $dao->find(true);
            
            CRM_Core_DAO::storeValues( $dao, $defaults );
        }

        if ( !$defaults['id'] ) { 
            $defaults['is_approval_needed'] = 1;
            $defaults['is_tellfriend_enabled'] = 1;
            $defaults['tellfriend_limit'] = 5;
            $defaults['link_text'] = ts('Become a Supporter!');
        } 
        return $defaults;
    }
    
    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    function buildQuickForm( ) 
    {
        $this->addElement( 'checkbox', 'is_active', ts('Enable Personal Campaign Pages?'), null, array('onclick' => "pcpBlock(this)") );
	
        $this->addElement( 'checkbox', 'is_approval_needed', ts('Administrator approval required for new Personal Campaign Pages') );
        
        CRM_Core_DAO::commonRetrieveAll('CRM_Core_DAO_UFGroup', 'is_cms_user', 1, $profiles, array ( 'title' ) );
        if ( !empty( $profiles ) ) {
            foreach ( $profiles as $key => $value ) {
                $profile[$key] = $value['title'];
            }
            $this->add('select', 'supporter_profile_id', ts( 'Supporter profile' ), $profile );    
            $this->assign('profile',$profile);
        }
        
        $this->addElement( 'checkbox', 'is_tellfriend_enabled', ts("Allow 'Tell a friend' functionality") );
        
        $this->add( 'text', 
                    'tellfriend_limit', 
                    ts("'Tell a friend' maximum recipients limit"), 
                    CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_ContributionPage' , 'pcp_tellfriend_limit') );
        $this->addRule( 'tellfriend_limit', ts( 'Please enter a valid limit.' ), 'integer' );

        $this->add( 'text', 
                    'link_text', 
                    ts("'Create Personal Campaign Page' link text"), 
                    CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_ContributionPage' , 'pcp_link_text') );
        
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
    
    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    function postProcess( ) 
    {
        // get the submitted form values.
        $params = $this->controller->exportValues( $this->_name );
        
        $params['entity_id'] = $this->_id;
        $params['entity_table'] = 'civicrm_contribution_page';
       
        $dao =& new CRM_Contribute_DAO_PCPBlock();
        $dao->entity_table = 'civicrm_contribution_page';
        $dao->entity_id = $this->_id; 
        $dao->find(true);
        $params['id'] = $dao->id;

        require_once 'CRM/Contribute/BAO/PCP.php'; 
        $dao = CRM_Contribute_BAO_PCP::add( $params ); 
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


