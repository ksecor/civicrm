<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
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
 * This class is to build the form for Deleting Group
 */
class CRM_Contribute_Form_ContributionPage_Delete extends CRM_Contribute_Form_ContributionPage {

    /**
     * page title
     *
     * @var string
     * @protected
     */
    protected $_title;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        //Check if there are contributions related to Contribution Page
        
        parent::preProcess();
        require_once 'CRM/Contribute/DAO/Contribution.php';
        $dao =& new CRM_Contribute_DAO_Contribution();
        $dao->contribution_page_id = $this->_id;
        
        if ( $dao->find(true) ) {
            $this->_relatedContributions = true;
            $this->assign('relatedContributions',true);
            
        }
        
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {

        $this->_title = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage', $this->_id, 'title' );
        $this->assign( 'title', $this->_title );

        //if there are contributions related to Contribution Page 
        //then onle cancel button is displayed
        $buttons = array();
        if (! $this->_relatedContributions ) {
            $buttons[]  =  array ( 'type'      => 'next',
                                   'name'      => ts('Delete Contribution Page'),
                                   'isDefault' => true   );
        }

        $buttons[] =  array ( 'type'       => 'cancel',
                              'name'      => ts('Cancel') 
                              );
            
        $this->addButtons( $buttons );
        
    }

    /**
     * Process the form when submitted
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        CRM_Core_DAO::transaction('BEGIN');
        
        // first delete the join entries associated with this contribution page
        require_once 'CRM/Core/DAO/UFJoin.php';
        $dao =& new CRM_Core_DAO_UFJoin( );
        
        $params = array( 'entity_table' => 'civicrm_contribution_page',
                         'entity_id'    => $this->_id );
        $dao->copyValues( $params );
        $dao->delete( );

        // next delete the amount option fields
        require_once 'CRM/Core/DAO/CustomOption.php';
        $dao =& new CRM_Core_DAO_CustomOption( );
        $dao->entity_table = 'civicrm_contribution_page';
        $dao->entity_id    = $this->_id;
        $dao->delete( );

        //next delete the membership block fields
        require_once 'CRM/Member/DAO/MembershipBlock.php';
        $dao =& new CRM_Member_DAO_MembershipBlock( );
        $dao->entity_table = 'civicrm_contribution_page';
        $dao->entity_id    = $this->_id;
        $dao->delete( );


        // finally delete the contribution page
        require_once 'CRM/Contribute/DAO/ContributionPage.php';
        $dao =& new CRM_Contribute_DAO_ContributionPage( );
        $dao->id = $this->_id;
        $dao->delete( );

        CRM_Core_DAO::transaction('COMMIT');
        
        CRM_Core_Session::setStatus( ts('The contribution page "%1" has been deleted.', array( 1 => $this->_title ) ) );
    }
}

?>
