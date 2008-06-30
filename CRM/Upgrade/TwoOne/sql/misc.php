<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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


class CRM_Upgrade_TwoOne_sql_misc {
    
    /** 
     * function to remove the domain id from the custom table 
     * @return void
     * @access public
     * @static
     */

    static function removeDomainIdFromCustomTables( ) 
    {
        //select custom table name from custom group tables
        $query = "SELECT table_name FROM civicrm_custom_group";
        $dao   = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        
        while ( $dao->fetch( ) ) {
            //remove foreign key
            $query1  = "ALTER TABLE {$dao->table_name} DROP FOREIGN KEY FK_{$dao->table_name}_domain_id";
            CRM_Core_DAO::executeQuery( $query1, CRM_Core_DAO::$_nullArray );
            
            //remove unique key
            $query2 = "ALTER TABLE {$dao->table_name} DROP INDEX unique_domain_id_entity_id";
            CRM_Core_DAO::executeQuery( $query2, CRM_Core_DAO::$_nullArray );
            
            //add unique key
            $query3 = "ALTER TABLE {$dao->table_name} ADD UNIQUE unique_entity_id (entity_id)";
            CRM_Core_DAO::executeQuery( $query3, CRM_Core_DAO::$_nullArray );
            
            //drop domain id column
            $query4 = "ALTER TABLE {$dao->table_name} DROP domain_id";
            CRM_Core_DAO::executeQuery( $query4, CRM_Core_DAO::$_nullArray );
        } 
    }

    /** 
     * Add option group "safe_file_extension" and its option values to
     * db, if not already present. CRM-3238
     *
     * @return void
     * @access public
     * @static
     */
    static function addSafeFileExtensionList( ) 
    {
        $query    = "
SELECT id FROM civicrm_option_group WHERE name = 'safe_file_extension'";
        $sfeGroup = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        $sfeGroup->fetch();

        if ( ! $sfeGroup->id ) {
            $query = "
INSERT INTO civicrm_option_group (name, description, is_reserved, is_active)
VALUES ('safe_file_extension', 'Safe File Extension', 0, 1)";
            $dao   = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            
            $query = "
SELECT id FROM civicrm_option_group WHERE name = 'safe_file_extension'";
            $dao   = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            $dao->fetch();
            
            if ( $dao->id ) {
                $query = "
INSERT INTO `civicrm_option_value` (`option_group_id`, `label`, `value`, `name`, `grouping`, `filter`, `is_default`, `weight`, `description`, `is_optgroup`, `is_reserved`, `is_active`, `component_id`) 
VALUES 
({$dao->id}, 'jpg', '1', NULL, NULL, 0, 0, 1, NULL, 0, 0, 1, NULL),
({$dao->id}, 'jpeg', '2', NULL, NULL, 0, 0, 2, NULL, 0, 0, 1, NULL),
({$dao->id}, 'png', '3', NULL, NULL, 0, 0, 3, NULL, 0, 0, 1, NULL),
({$dao->id}, 'gif', '4', NULL, NULL, 0, 0, 4, NULL, 0, 0, 1, NULL),
({$dao->id}, 'txt', '5', NULL, NULL, 0, 0, 5, NULL, 0, 0, 1, NULL),
({$dao->id}, 'html', '6', NULL, NULL, 0, 0, 6, NULL, 0, 0, 1, NULL),
({$dao->id}, 'htm', '7', NULL, NULL, 0, 0, 7, NULL, 0, 0, 1, NULL),
({$dao->id}, 'pdf', '8', NULL, NULL, 0, 0, 8, NULL, 0, 0, 1, NULL),
({$dao->id}, 'doc', '9', NULL, NULL, 0, 0, 9, NULL, 0, 0, 1, NULL),
({$dao->id}, 'xls', '10', NULL, NULL, 0, 0, 10, NULL, 0, 0, 1, NULL),
({$dao->id}, 'rtf', '11', NULL, NULL, 0, 0, 11, NULL, 0, 0, 1, NULL),
({$dao->id}, 'csv', '12', NULL, NULL, 0, 0, 12, NULL, 0, 0, 1, NULL),
({$dao->id}, 'ppt', '13', NULL, NULL, 0, 0, 13, NULL, 0, 0, 1, NULL),
({$dao->id}, 'doc', '14', NULL, NULL, 0, 0, 14, NULL, 0, 0, 1, NULL)
";
                $dao   = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            }
        }
    }
}
?>