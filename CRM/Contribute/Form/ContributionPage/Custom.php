<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
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
        $this->add( 'select', 'custom_pre_id' , ts('Custom Fields') . '<br />' . ts('(above billing info)'), array('' => ts('- select -')) + CRM_Core_PseudoConstant::ufGroup( ) );
        $this->add( 'select', 'custom_post_id', ts('Custom Fields') . '<br />' . ts('(below billing info)'), array('' => ts('- select -')) + CRM_Core_PseudoConstant::ufGroup( ) );

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


        $params['domain_id']             = CRM_Core_Config::domainID( );

        CRM_Core_DAO::transaction('BEGIN'); 
         
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
 
        CRM_Core_DAO::transaction('COMMIT'); 
    }

    /** 
     * Return a descriptive name for the page, used in wizard header 
     * 
     * @return string 
     * @access public 
     */ 
    public function getTitle( ) {
        return ts( 'Custom Page Elements' );
    }
}

?>
