<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contribute/Form/ContributionPage.php';
require_once 'CRM/Contribute/PseudoConstant.php';

/**
 * form to process actions fo adding product to contribution page                            
 */
class CRM_Contribute_Form_ContributionPage_AddProduct extends CRM_Contribute_Form_ContributionPage {
    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        $this->addElement('select', 'product_id', ts('Select the Prodcut ') ,CRM_Contribute_PseudoConstant::products());
        $this->addElement('text','sort_position', ts('Weight'),CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_PremiumsProduct', 'sort_position') );
             
        $session =& CRM_Core_Session::singleton();
        $single = $session->get('singleForm');
        $session->pushUserContext( CRM_Utils_System::url('civicrm/admin/contribute', 'action=update&reset=1&id=' . $this->_id .'&subPage=Premium') );
      
        if ( $single ) {
            $this->addButtons(array(
                                    array ( 'type'      => 'next',
                                            'name'      => ts('Save'),
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;',
                                            'isDefault' => true   ),
                                    array ( 'type'      => 'cancel',
                                            'name'      => ts('Cancel') ),
                                    )
                              );
        } else {
            parent::buildQuickForm( );
        }
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

        $pageID = CRM_Utils_Request::retrieve('id', $this, false, 0);
        require_once 'CRM/Contribute/DAO/Premium.php';
        $dao =& new CRM_Contribute_DAO_Premium();
        $dao->entity_table = 'civicrm_contribution_page';
        $dao->entity_id = $pageID; 
        $dao->find(true);
        $premiumID = $dao->id;
        $params['premiums_id'] = $premiumID;
        

        require_once 'CRM/Contribute/DAO/PremiumsProduct.php';
        $dao =& new CRM_Contribute_DAO_PremiumsProduct();
        $dao->copyValues($params);
        $dao->save();

    }

    /** 
     * Return a descriptive name for the page, used in wizard header 
     * 
     * @return string 
     * @access public 
     */ 
    public function getTitle( ) {
        return ts( 'Add Premium to Contibtion Page' );
    }
}
?>
