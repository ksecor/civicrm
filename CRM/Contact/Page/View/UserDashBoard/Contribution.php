<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Contact/Page/View/UserDashBoard.php';

class CRM_Contact_Page_View_UserDashBoard_Contribution extends CRM_Contact_Page_View_UserDashBoard 
{
   /**
    * This function is called when action is browse
    * 
    * return null
    * @access public
    */
    function listContribution( ) 
    {
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Contribute_Form_Search', ts('Contributions'), null );
        $controller->setEmbedded( true );
        $controller->reset( );
        $controller->set( 'limit', 12 ); 
        $controller->set( 'cid'  , $this->_contactId );
        $controller->set( 'context'  , 'user' );
        $controller->set( 'force'  , 1 );
        $controller->process( );
        $controller->run( );
        
        //add honor block
        require_once 'CRM/Contribute/BAO/Contribution.php';
        $params = array( );
        $params =  CRM_Contribute_BAO_Contribution::getHonorContacts( $this->_contactId );
        if ( ! empty($params) ) {
            foreach($params as $ids => $honorId){
                $contributionId = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_Contribution', $honorId['honorId'],'id','contact_id' );
                $subType     = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionType', $honorId['type'], 'id','name' );
            }
            
            // assign vars to templates
            $this->assign('honorRows', $params);
            $this->assign('honor', true);
        }

        require_once 'CRM/Contribute/DAO/ContributionRecur.php';
        $recur =& new CRM_Contribute_DAO_ContributionRecur( );
        $recur->contact_id = $this->_contactId;
        $recur->find();

        require_once 'api/utils.php';
        $recurRow = array();
        while( $recur->fetch() ) {
            _crm_object_to_array($recur, $values);
            $recurRow[] = $values;
        }
        $this->assign('recurRows',$recurRow);
        $this->assign('recur', true); 
        
        $config =& CRM_Core_Config::singleton( );
        
        require_once 'CRM/Contribute/Form/ContributionBase.php';
        $url = CRM_Contribute_Form_ContributionBase::cancelSubscriptionURL( $config,
                                                                            $recur->is_test ? 'test' : 'live' );
        $this->assign( 'cancelSubscriptionUrl', $url );
	}
    
    
    /**
     * This function is the main function that is called when the page
     * loads, it decides the which action has to be taken for the page.
     * 
     * return null
     * @access public
     */
    function run( ) 
    {
        parent::preProcess( );
        $this->listContribution( );
    }
}
?>
