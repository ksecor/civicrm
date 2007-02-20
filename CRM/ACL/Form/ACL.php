<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
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
        $role = array( '-1' => ts(' -select role- '),
                       '0'  => ts( 'Any Role' ) ) +
            CRM_Core_OptionGroup::values( 'acl_role' );
        $this->add( 'select', 'entity_id', $label, $role, true );

        $group       = array( '-1' => ts( '-select-' ),
                              '0'  => ts( 'All Groups' ) )        +
            CRM_Core_PseudoConstant::group( )      ;
        $customGroup = array( '-1' => ts( '-select-' ),
                              '0'  => ts( 'All Custom Groups' ) ) +
            CRM_Core_PseudoConstant::customGroup( );
        $ufGroup     = array( '-1' => ts( '-select-' ),
                              '0'  => ts( 'All Profiles' ) )      +
            CRM_Core_PseudoConstant::ufGroup( )    ;

        $this->add( 'select', 'group_id'       , ts( 'Group'        ), $group       );
        $this->add( 'select', 'custom_group_id', ts( 'Custom Group' ), $customGroup );
        $this->add( 'select', 'uf_group_id'    , ts( 'Profile'      ), $ufGroup     );

        $this->add('checkbox', 'is_active', ts('Enabled?'));

        $this->addFormRule( array( 'CRM_ACL_Form_ACL', 'formRule' ) );
    }

    /**
     * This function sets the default values for the form. MobileProvider that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        $defaults = parent::setDefaultValues( );

        if ( isset( $defaults['object_table'] ) ) {
            switch ( $defaults['object_table'] ) {
            case 'civicrm_saved_search':
                $defaults['group_id'] = $defaults['object_id'];
                break;

            case 'civicrm_custom_group':
                $defaults['custom_group_id'] = $defaults['object_id'];
                break;

            case 'civicrm_uf_group':
                $defaults['uf_group_id'] = $defaults['object_id'];
                break;
            }
        }
        return $defaults;
    }

    static function formRule( &$params ) {
        // make sure that at only one of group_id, custom_group_id and uf_group_id is selected
        $count = 0;

        $fields = array( 'group_id', 'custom_group_id', 'uf_group_id' );
        foreach ( $fields as $field ) {
            if ( isset( $params[$field] ) &&
                 $params[$field] != -1 ) {
                $count++;
            }
        }

        $errors = array( );
        if ( $count != 1 ) {
            $errors['_qf_default'] = ts( 'Please select one of Group, Custom Group and Profile' );
        }
        
        // also make sure role is not -1
        if ( $params['entity_id'] == -1 ) {
            $errors['entity_id'] = ts( 'Please select a Role' );
        }
        return empty($errors) ? true : $errors;
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

        // unset them just to make sure
        $fields = array( 'group_id'        => 'civicrm_saved_search',
                         'custom_group_id' => 'civicrm_custom_group',
                         'uf_group_id'     => 'civicrm_uf_group' );
        foreach ( $fields as $name => $table ) {
            if ( $params[$name] != -1 ) {
                $params['object_table'] = $table;
                $params['object_id']    = $params[$name];
            }
            unset( $params[$name] );
        }
        
        if ( $this->_id ) {
            $params['id'] = $this->_id;
       }
        CRM_ACL_BAO_ACL::create( $params );
    }

}

?>