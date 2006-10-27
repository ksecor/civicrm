<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions      |
 | about the Affero General Public License or the licensing  of       |
 | CiviCRM, see the CiviCRM license FAQ at                            |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

require_once 'CRM/Admin/Form.php';

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */
class CRM_ACL_Form_ACL extends CRM_Admin_Form
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        parent::buildQuickForm( );

        if ($this->_action & CRM_Core_Action::DELETE ) {
            return;
        }

        $attributes = CRM_Core_DAO::getAttribute( 'CRM_ACL_DAO_ACL' );

        $this->add('text', 'name', ts('Description'), CRM_Core_DAO::getAttribute( 'CRM_ACL_DAO_ACL', 'name' ), true );
        
        require_once 'CRM/ACL/BAO/ACL.php';
        $operations   = array( '' => ts( ' -select- ' ) ) + CRM_ACL_BAO_ACL::operation( );
        $this->add( 'select', 'operation', ts( 'Operation' ),
                           $operations, true );

        require_once 'CRM/Core/OptionGroup.php';

        $label = ts( 'Role' );
        $role = array( '' => ts('- select role -')) + CRM_Core_OptionGroup::values( 'acl_role' );
        $this->add( 'select', 'entity_id', $label, $role, true );

        $label = ts( 'Permission For' );
        $group = array( '' => ts('- select group -')) + CRM_Core_PseudoConstant::group( );
        $this->add( 'select', 'object_id', $label, $group, true );

        $this->add('checkbox', 'is_active', ts('Enabled?'));
    }

       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        require_once 'CRM/ACL/BAO/ACL.php';        

        $params = $this->controller->exportValues( $this->_name );

        $params['deny'] = 0;
        $params['entity_table'] = 'civicrm_acl_role';
        $params['object_table'] = 'civicrm_saved_search';
        
        if ( $this->_id ) {
            $params['id'] = $this->_id;
        }
        CRM_ACL_BAO_ACL::create( $params );
    }

}

?>