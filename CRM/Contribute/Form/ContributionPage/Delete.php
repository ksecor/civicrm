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
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {

        $this->_title = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage', $this->_id, 'title' );
        $this->assign( 'title', $this->_title );

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Delete Contribution Page'),
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
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
        $dao =& new CRM_Core_DAO_CustomOption( );
        $dao->entity_table = 'civicrm_contribution_page';
        $dao->entity_id    = $this->_id;
        $dao->delete( );

        // finally delete the contribution page
        $dao =& new CRM_Contribute_DAO_ContributionPage( );
        $dao->id = $this->_id;
        $dao->delete( );

        CRM_Core_DAO::transaction('COMMIT');
        
        CRM_Core_Session::setStatus( ts('The contribution page "%1" has been deleted.', array( 1 => $this->_title ) ) );
    }
}

?>
