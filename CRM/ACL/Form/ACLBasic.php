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
                      'view all activities'        => ts( 'view all activities' ),
                      'access CiviCRM'             => ts( 'access CiviCRM' ),
                      'access Contact Dashboard'   => ts( 'access Contact Dashboard' ),
                     );

           $config = CRM_Core_Config::singleton( );
           require_once 'CRM/Core/Component.php';
           foreach ( $config->enableComponents as $comp ) {
               $perm = CRM_Core_Component::get( $comp, 'perm' );
               if ( $perm ) {
                   sort( $perm );
                   foreach ( $perm as $p ) {
                      $this->_basicPermissions[$p] = $p;
                   }
               }
           }
           asort( $this->_basicPermissions );
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
        $defaults = array( );

	if ( $this->_id ) {
            $defaults['entity_id'] = $this->_id;

            $query = "
SELECT object_table
  FROM civicrm_acl
 WHERE domain_id = %1
   AND entity_id = %2
   AND ( object_table NOT IN ( 'civicrm_saved_search', 'civicrm_uf_group', 'civicrm_custom_group' ) )
";
            $params = array( 1 => array( CRM_Core_Config::domainID( ), 'Integer' ),
                             2 => array( $this->_id                  , 'Integer' ) );
            $dao    = CRM_Core_DAO::executeQuery( $query, $params );
            $defaults['object_table'] = array( );
            while ( $dao->fetch( ) ) {
                $defaults['object_table'][$dao->object_table] = 1;
            }
        }
        
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

        $this->addCheckBox( 'object_table',
                            ts('ACL Type'),
                            $this->basicPermissions( ),
                            null, null, true, null,
                            array( '</td><td>', '</td></tr><tr><td>' ) );

        require_once 'CRM/Core/OptionGroup.php';

        $label = ts( 'Role' );
        $role = array( '-1' => ts('- select role -'),
                       '0'  => ts( 'Everyone' ) ) +
        CRM_Core_OptionGroup::values( 'acl_role' );
        $entityID =& $this->add( 'select', 'entity_id', $label, $role, true );

        if ( $this->_id ) {
            $entityID->freeze( );
        }
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
        require_once 'CRM/ACL/BAO/Cache.php';
        CRM_ACL_BAO_Cache::resetCache( );

        require_once 'CRM/ACL/BAO/ACL.php';
        $params = $this->controller->exportValues( $this->_name );

        if ( $this->_id ) {
            $query = "
DELETE
  FROM civicrm_acl
 WHERE domain_id = %1
   AND entity_id = %2
   AND ( object_table NOT IN ( 'civicrm_saved_search', 'civicrm_uf_group', 'civicrm_custom_group' ) )
";
            $deleteParams = array( 1 => array( CRM_Core_Config::domainID( ), 'Integer' ),
                                   2 => array( $this->_id                  , 'Integer' ) );
            $dao          = CRM_Core_DAO::executeQuery( $query, $deleteParams );

            if ( $this->_action & CRM_Core_Action::DELETE ) {
                CRM_Core_Session::setStatus( ts('Selected ACL has been deleted.') );
                return;
            }
        }

        $params['operation']    = 'All';
        $params['deny']         = 0;
        $params['is_active']    = 1;
        $params['entity_table'] = 'civicrm_acl_role';
        $params['name']         = 'Core ACL';
       
        foreach ( $params['object_table'] as $object_table => $value ) {
            if ( $value ) {
                $newParams = $params;
                unset( $newParams['object_table'] );
                $newParams['object_table'] = $object_table;
                CRM_ACL_BAO_ACL::create( $newParams );
            }
        }
    }
}


