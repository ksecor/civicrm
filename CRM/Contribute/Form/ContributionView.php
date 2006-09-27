<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
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
 * This class generates form components for Payment-Instrument
 * 
 */
class CRM_Contribute_Form_ContributionView extends CRM_Core_Form
{
    /**  
     * Function to set variables up before form is built  
     *                                                            
     * @return void  
     * @access public  
     */
    public function preProcess( ) {
        require_once 'CRM/Contribute/BAO/Contribution.php';

        $values = array( ); 
        $ids    = array( ); 
        $params = array( 'id' => $this->get( 'id' ) ); 
        CRM_Contribute_BAO_Contribution::getValues( $params, 
                                                    $values, 
                                                    $ids );             
        CRM_Contribute_BAO_Contribution::resolveDefaults( $values ); 
        
        if ( $values["honor_contact_id"] ) {
            $sql = "SELECT display_name FROM civicrm_contact WHERE id = " . $values["honor_contact_id"];
            $dao = &new CRM_Core_DAO();
            $dao->query($sql);
            if ( $dao->fetch() ) {
                $url = CRM_Utils_System::url( 'civicrm/contact/view/basic', "reset=1&cid=$values[honor_contact_id]" );
                $values["honor_display"] = "<A href = $url>". $dao->display_name ."</A>"; 
            }
        }

        $groupTree =& CRM_Core_BAO_CustomGroup::getTree( 'Contribution', $this->get( 'id' ) );
        CRM_Core_BAO_CustomGroup::buildViewHTML( $this, $groupTree );
        $id = $this->get( 'id' );
        if( $id ) {
            require_once 'CRM/Contribute/DAO/ContributionProduct.php';
            $dao = & new CRM_Contribute_DAO_ContributionProduct();
            $dao->contribution_id = $id;
            if ( $dao->find(true) ) {
               $premiumId = $dao->id;
               $productID = $dao->product_id; 
            }
            
        }
        
        if( $premiumId ) {
                       
            require_once 'CRM/Contribute/DAO/Product.php';
            $productDAO = & new CRM_Contribute_DAO_Product();
            $productDAO->id  = $productID;
            $productDAO->find(true);
           
            $this->assign('premium' , $productDAO->name );
            $this->assign('option',$dao->product_option);
            $this->assign('fulfilled',$dao->fulfilled_date);
                     
        }

        $this->assign( $values ); 
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->addButtons(array(  
                                array ( 'type'      => 'next',  
                                        'name'      => ts('Done'),  
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',  
                                        'isDefault' => true   )
                                )
                          );
    }

}

?>
