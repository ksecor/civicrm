<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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

require_once 'CRM/Core/Page/Basic.php';

/**
 * Page for displaying list of Premiums
 */
class CRM_Contribute_Page_Premium extends CRM_Core_Page_Basic 
{
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    /**
     * Get BAO Name
     *
     * @return string Classname of BAO.
     */
    function getBAOName() 
    {
        return 'CRM_Contribute_BAO_Premium';
    }

    /**
     * Get action Links
     *
     * @return array (reference) of action links
     */
    function &links()
    {            
        if (!(self::$_links)) {
            // helper variable for nicer formatting
            $deleteExtra = ts('Are you sure you want to remove this product form this page.?');

            self::$_links = array(
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/admin/contribute',
                                                                    'qs'    => 'action=update&id=%%id%%&pid=%%pid%%&reset=1&subPage=AddProductToPage',
                                                                    'title' => ts('Edit Premium') 
                                                                   ),
                                  CRM_Core_Action::PREVIEW => array(
                                                                    'name'  => ts('Preview'),
                                                                    'url'   => 'civicrm/admin/contribute',
                                                                    'qs'    => 'action=preview&id=%%id%%&pid=%%pid%%&subPage=AddProductToPage',
                                                                    'title' => ts('Preview Premium') 
                                                                   ),
                                  CRM_Core_Action::DELETE => array(
                                                                    'name'  => ts('Remove'),
                                                                    'url'   => 'civicrm/admin/contribute',
                                                                    'qs'    => 'action=delete&id=%%id%%&pid=%%pid%%&subPage=AddProductToPage',                    
                                                                    'extra' => 'onclick = "if (confirm(\'' . $deleteExtra . '\') ) this.href+=\'&amp;confirmed=1\'; else return false;"',
                                                                   
                                                                    'title' => ts('Disable Premium') 
                                                                   ),
                                 
                                 );
        }
        return self::$_links;
        
      
    }

    /**
     * Run the page.
     *
     * This method is called after the page is created. It checks for the  
     * type of action and executes that action.
     * Finally it calls the parent's run method.
     *
     * @return void
     * @access public
     *
     */
    function run()
    {
        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', $this, false, 'browse'); // default to 'browse'

        // assign vars to templates
        $this->assign('action', $action);
        $id = CRM_Utils_Request::retrieve('id', $this, false, 0);
        
        // what action to take ?
        //if ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD)) {
            $this->edit($action, $id) ;
            //} 
        // finally browse the custom groups
        $this->browse();
        
        // parent run 
        parent::run();
    }

    /**
     * Browse all custom data groups.
     *  
     * 
     * @return void
     * @access public
     * @static
     */
    function browse()
    {
        // get all custom groups sorted by weight
        $premiums = array();
        require_once 'CRM/Contribute/DAO/Product.php';
        $pageID = CRM_Utils_Request::retrieve('id', $this, false, 0);
        $dao =& new CRM_Contribute_DAO_Premium();
        $dao->entity_table = 'civicrm_contribution_page';
        $dao->entity_id = $pageID; 
        $dao->find(true);
        $premiumID = $dao->id;

        require_once 'CRM/Contribute/DAO/PremiumsProduct.php';
        $dao =& new CRM_Contribute_DAO_PremiumsProduct();
        $dao->premiums_id = $premiumID;
        $dao->orderBy('sort_position');
        $dao->find();

        while ($dao->fetch()) {
            $productDAO =& new CRM_Contribute_DAO_Product();
            $productDAO->id = $dao->product_id;
            $productDAO->is_active = 1;
           
            if ($productDAO->find(true) ) {
                $premiums[$productDAO->id] = array();
                $premiums[$productDAO->id]['weight'] = $dao->sort_position;
                CRM_Core_DAO::storeValues( $productDAO, $premiums[$productDAO->id]);
                
                $action = array_sum(array_keys($this->links()));
                
                $premiums[$dao->product_id]['action'] = CRM_Core_Action::formLink(self::links(), $action, 
                                                                                  array('id' => $pageID,'pid'=> $dao->id));
            }
        }
        require_once 'CRM/Contribute/PseudoConstant.php';
        
        if ( count(CRM_Contribute_PseudoConstant::products($pageID)) == 0  ) {
            $this->assign( 'products', false );
        } else {
            $this->assign( 'products', true );
        }
        
        $this->assign('rows', $premiums);
    }

    /**
     * Get name of edit form
     *
     * @return string Classname of edit form.
     */
    function editForm() 
    {
        return 'CRM_Contribute_Form_ContributionPage_Premium';
    }
    
    /**
     * Get edit form name
     *
     * @return string name of this page.
     */
    function editName() 
    {
        return 'Configure Premiums';
    }
    
    /**
     * Get user context.
     *
     * @return string user context.
     */
    function userContext($mode = null) 
    {
        return 'civicrm/admin/contribute';
    }

}

?>
