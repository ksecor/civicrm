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

        // first create or update the CiviCRM group
        $groupParams           = $params;
        $groupParams['source'] = "OG Sync Group: {$params['og_id']}";
        self::updateCiviGroup( $groupParams );

        // next create or update the CiviCRM ACL group
        $aclParams                     = $params;
        $aclParams['civicrm_group_id'] = $groupParams['group_id'];
        $aclParams['name']             = $aclParams['title'] = "{$aclParams['name']}: Administrator";
        $aclParams['source']           = "OG Sync Group: {$params['og_id']}";

        self::updateCiviGroup        ( $aclParams );
        $aclParams['acl_group_id']   = $aclParams['group_id'];
        self::updateCiviACLRole      ( $aclParams );
        self::updateCiviACLEntityRole( $aclParams );
        self::updateCiviACL          ( $aclParams );
    }

    static function delete( &$params ) {
    }

    static function updateCiviGroup( &$params ) {
        $query  = "
SELECT id
  FROM civicrm_group
 WHERE name   = %1
    OR source = %2";
        $params = array( 1 => array( $params['name'  ], 'String' ),
                         2 => array( $params['source'], 'String' ) );
        $params['id'] = CRM_Core_DAO::singleValueQuery( $query, $params );

        require_once 'api/Group.php';
        $group = crm_create_group( $params );
        $params['group_id'] = $group->id;
    }

    static function updateCiviACLRole( &$params ) {
        require_once 'CRM/Core/DAO/OptionValue.php';

        $optionGroupID = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionGroup',
                                                      'acl_role',
                                                      'id',
                                                      'name' );
    
        $dao = new CRM_Core_DAO_OptionValue( );
        $dao->option_group_id = $optionGroupID;
        $dao->label           = $params['title' ];
        $dao->description     = $params['source'];
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
        $params  = array( 1 => array( $optionGroupID   , 'Integer' ),
                          2 => array( $params['source'], 'String'  ) );
        $dao->id = CRM_Core_DAO::singleValueQuery( $query, $params );
        dao->save( );
        $params['acl_role_id'] = $dao->id;
    }

    static function updateCiviACLEntityRole( &$params ) {
        require_once 'CRM/ACL/DAO/EntityRole.php';
        $dao = new CRM_ACL_DAO_EntityRole( );

        $dao->acl_role_id  = $params['acl_role_id'];
        $dao->entity_table = 'civicrm_group';
        $dao->entity_id    = $params['acl_group_id'];
        $dao->domain_id    = CRM_Core_Config::domainID( );

        $dao->find( true );
        $dao->is_active    = true;
        $dao->save( );
        $params['acl_entity_role_id'] = $dao->id;
    }

    static function updateCiviACL( &$params ) {
        require_once 'CRM/ACL/DAO/ACL.php';
        $dao = new CRM_ACL_DAO_ACL( );

        $dao->domain_id    = CRM_Core_Config::domainID( );
        $dao->entity_table = 'civicrm_acl_role';
        $dao->entity_id    = $params['acl_role_id'];
        $dao->operation    = 'Edit';
        $dao->object_table = 'civicrm_saved_search';
        $dao->object_id    = $params['civicrm_group_id'];
        $dao->find( true );

        $dao->is_active = true;
        $dao->save( );
        $params['acl_id'] = $dao->id;
    }

}
?>
