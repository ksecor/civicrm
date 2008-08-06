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

require_once 'CRM/PledgeBank/Form/ManagePledgeBank.php';

/**
 * This class is to build the form for Deleting Group
 */
class CRM_PledgeBank_Form_ManagePledgeBank_Delete extends CRM_PledgeBank_Form_ManagePledgeBank
{
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
        parent::preProcess();
        $this->_title = CRM_Core_DAO::getFieldValue( 'CRM_PledgeBank_BAO_Pledge',
                                                     $this->_id, 'creator_pledge_desc' );
    }
    
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( )
    {
        $this->assign( 'title', $this->_title );
     
        $buttons = array();
        $buttons[] =  array ( 'type'      => 'next',
                              'name'      => ts('Delete Pledge'),
                              'isDefault' => true   );
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
    public function postProcess( )
    {
        require_once 'CRM/PledgeBank/DAO/Signer.php';
        $signer = new CRM_PledgeBank_DAO_Signer( );
        $signer->pledge_id = $this->_id;
        
        if ( $signer->find( ) ) {
            $searchURL = CRM_Utils_System::url('civicrm/pb/search', 'reset=1');
            CRM_Core_Session::setStatus( ts( 'This pledge cannot be deleted because there are signer records linked to it. If you want to delete this pledge, you must first find the signers linked to this pledge and delete them. You can use use <a href=\'%1\'> PledgeBank >> Find Signers page </a>.', 
                                             array( 1 => $searchURL ) ) );
            return;
        } 
        CRM_PledgeBank_BAO_Pledge::del( $this->_id );
        CRM_Core_Session::setStatus( ts('The pledge \'%1\' has been deleted.', array( 1 => $this->_title ) ) );
    }
}
