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
 * @copyright CiviCRM LLC (c) 2004-2008
 * $Id$
 *
 */

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Core/DAO/Domain.php';
require_once 'CRM/Core/I18n/SchemaStructure.php';

class CRM_Core_I18n_Schema
{
    /**
     * Switch database from single-lang to multi (by adding the first language and dropping the original columns).
     *
     * @param $locale string  the locale of the first language to create
     * @return void
     */
    function makeMultilingual($locale)
    {
        // build the column-adding SQL queries and execute them without rewriting
        $columns =& CRM_Core_I18n_SchemaStructure::columns();
        $queries = array();
        foreach ($columns as $table => $column) {
            // FIXME: make the type of the column the proper one
            $queries[] = "ALTER TABLE {$table} ADD {$column}_{$locale} text";
            $queries[] = "UPDATE {$table} SET {$column}_{$locale} = {$column}";
            $queries[] = "ALTER TABLE {$table} DROP {$column}";
        }
        foreach ($queries as $query) {
            CRM_Core_DAO::executeQuery($query, array(), true, null, true, false);
        }

        // update civicrm_domain.locales
        $domain =& CRM_Core_DAO_Domain();
        $domain->find(true);
        $domain->locales = $locale;
        $domain->save();
    }
}
