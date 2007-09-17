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

class CRM_OG_NodeAPI {

    static function update( &$params ) {
        CRM_Core_DAO::transaction( 'BEGIN' );

        // first create or update the CiviCRM group
        $groupParams           = $params;
        $groupParams['source'] = "OG Sync Group: {$params['og_id']}";
        self::updateCiviGroup( $groupParams, 'update' );

        // next create or update the CiviCRM ACL group
        $aclParams                     = $params;
        $aclParams['name']             = $aclParams['title'] = "{$aclParams['name']}: Administrator";
        $aclParams['source']           = "OG Sync ACL Group: {$params['og_id']}";
        self::updateCiviGroup        ( $aclParams, 'update' );

        $aclParams['acl_group_id']     = $aclParams['group_id'];
        $aclParams['civicrm_group_id'] = $groupParams['group_id'];
            
        self::updateCiviACLTables    ( $aclParams, 'update' );

        CRM_Core_DAO::transaction( 'COMMIT' );
    }

    static function delete( &$params ) {
        CRM_Core_DAO::transaction( 'BEGIN' );

        // first create or update the CiviCRM group
        $groupParams           = $params;
        $groupParams['source'] = "OG Sync Group: {$params['og_id']}";
        self::updateCiviGroup( $groupParams, 'delete' );

        // next create or update the CiviCRM ACL group
        $aclParams                     = $params;
        $aclParams['source']           = "OG Sync ACL Group: {$params['og_id']}";
        $aclParams['name']             = $aclParams['title'] = "{$aclParams['name']}: Administrator";
        self::updateCiviGroup        ( $aclParams, 'delete' );

        $aclParams['acl_group_id']     = $aclParams['group_id'];
        $aclParams['civicrm_group_id'] = $groupParams['group_id'];
        self::updateCiviACLTables    ( $aclParams, 'delete' );

        CRM_Core_DAO::transaction( 'COMMIT' );
    }

    static function updateCiviGroup( &$params, $op ) {
        require_once 'CRM/OG/Utils.php';
        CRM_Core_Error::debug( 'p', $params );
        $params['id'] = CRM_OG_Utils::groupID( $params['source'], $params['title'], false );

        if ( $op == 'update' ) {
            require_once 'api/Group.php';
            $group = crm_create_group( $params );
            $params['group_id'] = $group->id;
        } else {
            require_once 'CRM/Contact/BAO/Group.php';
            CRM_Contact_BAO_Group::discard( $params['id'] );
            $params['group_id'] = $params['id'];
        }
        unset( $params['id'] );
    }
        
    static function updateCiviACLTables( $aclParams, $op ) {
        self::updateCiviACLRole      ( $aclParams, $op );
        self::updateCiviACLEntityRole( $aclParams, $op );
        self::updateCiviACL          ( $aclParams, $op );
    }

    static function updateCiviACLRole( &$params, $op ) {
        require_once 'CRM/Core/DAO/OptionValue.php';

        $optionGroupID = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionGroup',
                                                      'acl_role',
                                                      'id',
                                                      'name' );
    
        $dao = new CRM_Core_DAO_OptionValue( );
        $dao->option_group_id = $optionGroupID;
        $dao->description     = $params['source'];
        
        if ( $op == 'delete' ) {
            $dao->delete( );
            return;
        }

        $dao->label           = $params['title' ];
        $dao->is_active       = 1;

        $weightParams = array( 'option_group_id' => $optionGroupID );
        $dao->weight          = CRM_Utils_Weight::getDefaultWeight( 'CRM_Core_DAO_OptionValue', 
                                                                    $weightParams );
        $dao->value           = CRM_Utils_Weight::getDefaultWeight( 'CRM_Core_DAO_OptionValue', 
                                                                    $weightParams,
                                                                    'value' );
    
        $query = "
SELECT v.id
  FROM civicrm_option_value v
 WHERE v.option_group_id = %1
   AND v.description     = %2
";
        $queryParams  = array( 1 => array( $optionGroupID   , 'Integer' ),
                               2 => array( $params['source'], 'String'  ) );
        $dao->id = CRM_Core_DAO::singleValueQuery( $query, $queryParams );
        $dao->save( );
        $params['acl_role_id'] = $dao->value;
    }

    static function updateCiviACLEntityRole( &$params, $op ) {
        require_once 'CRM/ACL/DAO/EntityRole.php';
        $dao = new CRM_ACL_DAO_EntityRole( );

        $dao->entity_table = 'civicrm_group';
        $dao->entity_id    = $params['acl_group_id'];
        $dao->domain_id    = CRM_Core_Config::domainID( );
        if ( $op == 'delete' ) {
            $dao->delete( );
            return;
        }

        $dao->acl_role_id  = $params['acl_role_id'];

        $dao->find( true );
        $dao->is_active    = true;
        $dao->save( );
        $params['acl_entity_role_id'] = $dao->id;
    }

    static function updateCiviACL( &$params, $op ) {
        require_once 'CRM/ACL/DAO/ACL.php';
        $dao = new CRM_ACL_DAO_ACL( );

        $dao->domain_id    = CRM_Core_Config::domainID( );
        $dao->object_table = 'civicrm_saved_search';
        $dao->object_id    = $params['civicrm_group_id'];

        if ( $op == 'delete' ) {
            $dao->delete( );
            return;
        }

        $dao->find( true );

        $dao->entity_table = 'civicrm_acl_role';
        $dao->entity_id    = $params['acl_role_id'];
        $dao->operation    = 'Edit';

        $dao->is_active = true;
        $dao->save( );
        $params['acl_id'] = $dao->id;
    }

}
?>
