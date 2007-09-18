<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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

require_once 'CRM/Contribute/Form/ContributionPage.php';

/**
 * form to process actions on the group aspect of Custom Data
 */
class CRM_Contribute_Form_ContributionPage_Custom extends CRM_Contribute_Form_ContributionPage {
    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        $this->add( 'select', 'custom_pre_id' , ts('Profile Fields') . '<br />' . ts('(top of page)'), array('' => ts('- select -')) + CRM_Core_PseudoConstant::ufGroup( ) );
        $this->add( 'select', 'custom_post_id', ts('Profile Fields') . '<br />' . ts('(bottom of page)'), array('' => ts('- select -')) + CRM_Core_PseudoConstant::ufGroup( ) );

        parent::buildQuickForm( );
    }

    /** 
     * This function sets the default values for the form. Note that in edit/view mode 
     * the default values are retrieved from the database 
     * 
     * @access public 
     * @return void 
     */ 
    function setDefaultValues() 
    { 
        $defaults = parent::setDefaultValues( );

         if ( $this->_id ) {
             $title = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage', $this->_id, 'title' );
             CRM_Utils_System::setTitle(ts('Custom Elements (%1)', array(1 => $title)));
         }
            
        require_once 'CRM/Core/BAO/UFJoin.php';

        $ufJoinParams = array( 'entity_table' => 'civicrm_contribution_page',  
                               'entity_id'    => $this->_id,  
                               'weight'       => 1 );
        $defaults['custom_pre_id'] = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams );

        $ufJoinParams['weight'] = 2;
        $defaults['custom_post_id'] = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams );
        
        return $defaults;
    }

    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    public function postProcess()
    {
        // get the submitted form values.
        $params = $this->controller->exportValues( $this->_name );

        if ($this->_action & CRM_Core_Action::UPDATE) {
            $params['id'] = $this->_id;
        }


        $params['domain_id'] = CRM_Core_Config::domainID( );

        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );
         
        // also update the ProfileModule tables 
        $ufJoinParams = array( 'is_active'    => 1, 
                               'module'       => 'CiviContribute',
                               'entity_table' => 'civicrm_contribution_page', 
                               'entity_id'    => $this->_id, 
                               'weight'       => 1, 
                               'uf_group_id'  => $params['custom_pre_id'] ); 

        require_once 'CRM/Core/BAO/UFJoin.php';
        CRM_Core_BAO_UFJoin::create( $ufJoinParams ); 

        unset( $ufJoinParams['id'] );
        $ufJoinParams['weight'     ] = 2; 
        $ufJoinParams['uf_group_id'] = $params['custom_post_id'];  
        CRM_Core_BAO_UFJoin::create( $ufJoinParams ); 

        $transaction->commit( ); 
    }

    /** 
     * Return a descriptive name for the page, used in wizard header 
     * 
     * @return string 
     * @access public 
     */ 
    public function getTitle( ) 
    {
        return ts( 'Custom Fields' );
    }
}

?>
