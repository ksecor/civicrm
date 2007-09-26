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

require_once 'CRM/Admin/Form.php';

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */
class CRM_ACL_Form_ACLBasic extends CRM_Admin_Form
{
    protected $_basicPermissions = null;

    function &basicPermissions( ) {
       if ( ! $this->_basicPermissions ) {
           $this->_basicPermissions =
                array(
                      'add contacts'               => ts( 'add contacts' ),
                      'view all contacts'          => ts( 'view all contacts' ),
                      'edit all contacts'          => ts( 'edit all contacts' ),
                      'import contacts'            => ts( 'import contacts' ),
                      'edit groups'                => ts( 'edit groups' ),
                      'administer CiviCRM'         => ts( 'administer CiviCRM' ),
                      'access uploaded files'      => ts( 'access uploaded files' ),
                      'profile listings and forms' => ts( 'profile listings and forms' ),
                      'access all custom data'     => ts( 'access all custom data' ),
                      'access CiviCRM'             => ts( 'access CiviCRM' ),
                      'access Contact Dashboard'   => ts( 'access Contact Dashboard' ),
                     );
       }
       return $this->_basicPermissions;
    }

    /**
    * This function sets the default values for the form.
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        $defaults = parent::setDefaultValues( );
        
        return $defaults;
    }


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
        
        $this->add( 'select',
                    'object_table',
                    ts('ACL Type'),
                    $this->basicPermissions( ),
                    true );

        require_once 'CRM/Core/OptionGroup.php';

        $label = ts( 'Role' );
        $role = array( '-1' => ts(' -select role- '),
                       '0'  => ts( 'Everyone' ) ) +
        CRM_Core_OptionGroup::values( 'acl_role' );
        $this->add( 'select', 'entity_id', $label, $role, true );

        $this->add('checkbox', 'is_active', ts('Enabled?'));

        $this->addFormRule( array( 'CRM_ACL_Form_ACLBasic', 'formRule' ) );
    }


    static function formRule( &$params ) {
        return true;
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

        if ( $this->_action & CRM_Core_Action::DELETE ) {
            CRM_ACL_BAO_ACL::del($this->_id);
            CRM_Core_Session::setStatus( ts('Selected ACL has been deleted.') );
        } else {
            $params = $this->controller->exportValues( $this->_name );

            $params['operation']    = 'All';
            $params['deny']         = 0;
            $params['entity_table'] = 'civicrm_acl_role';
           
            if ( $this->_id ) {
                $params['id'] = $this->_id;
            }
            
            CRM_ACL_BAO_ACL::create( $params );
        }
    }

}

?>
