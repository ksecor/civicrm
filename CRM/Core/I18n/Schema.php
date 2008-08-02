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
     * Switch database from single-lang to multi (by adding 
     * the first language and dropping the original columns).
     *
     * @return void
     */
    function makeMultilingual()
    {
        require_once 'CRM/Core/Config.php';
        $config =& CRM_Core_Config::singleton();
        $locale = $config->lcMessages;

        // build the column-adding SQL queries
        $columns =& CRM_Core_I18n_SchemaStructure::columns();
        $queries = array();
        foreach ($columns as $table => $hash) {
            foreach ($hash as $column => $type) {
                $queries[] = "ALTER TABLE {$table} ADD {$column}_{$locale} {$type}";
                $queries[] = "UPDATE {$table} SET {$column}_{$locale} = {$column}";
                $queries[] = "ALTER TABLE {$table} DROP {$column}";
                $queries[] = "CREATE VIEW {$table}_{$locale} AS SELECT *, {$column} = {$column}_{$locale} FROM {$table}";
            }
        }

        // execute the queries without i18n rewriting
        foreach ($queries as $query) {
            CRM_Core_DAO::executeQuery($query, array(), true, null, true, false);
        }

        // update civicrm_domain.locales
        $domain =& CRM_Core_DAO_Domain();
        $domain->find(true);
        $domain->locales = $locale;
        $domain->save();
    }

    /**
     * Add a new locale to a multi-lang db, setting 
     * its values to the current default locale.
     *
     * @param $locale string  the new locale to add
     * @return void
     */
    function addLocale($locale)
    {
        // get the current default locale and the supported locales 
        require_once 'CRM/Core/Config.php';
        $config =& CRM_Core_Config::singleton();
        $source = $config->lcMessages;
        $domain =& CRM_Core_DAO_Domain();
        $domain->find(true);
        $locales = explode(CRM_Core_DAO::VALUE_SEPARATOR, $domain->locales);

        // build the column-adding SQL queries
        $columns =& CRM_Core_I18n_SchemaStructure::columns();
        $queries = array();
        foreach ($columns as $table => $hash) {
            foreach ($hash as $column => $type) {
                $queries[] = "ALTER TABLE {$table} ADD {$column}_{$locale} {$type}";
                $queries[] = "UPDATE {$table} SET {$column}_{$locale} = {$column}_{$source}";
                $queries[] = "CREATE VIEW {$table}_{$locale} AS SELECT *, {$column} = {$column}_{$locale} FROM {$table}";
            }
        }

        // take care of the ON INSERT triggers
        foreach ($columns as $table => $hash) {
            $queries[] = "DROP TRIGGER IF EXISTS {$table}_i18n";

            $trigger = array();
            $trigger[] = 'DELIMITER ;;';
            $trigger[] = "CREATE TRIGGER {$table}_i18n BEFORE INSERT ON {$table} FOR EACH ROW BEGIN";

            foreach ($hash as $column => $_) {
                $trigger[] = "IF NEW.{$column}_{$locale} IS NOT NULL THEN";
                foreach ($locales as $old) {
                    $trigger[] = "SET NEW.{$column}_{$old} = NEW.{$column}_{$locale};";
                }
                foreach ($locales as $old) {
                    $trigger[] = "ELSEIF NEW.{$column}_{$old} IS NOT NULL THEN";
                    foreach (array_merge($locales, $locale) as $loc) {
                        if ($loc == $old) continue;
                        $trigger[] = "SET NEW.{$column}_{$loc} = NEW.{$column}_{$old};";
                    }
                }
                $trigger[] = 'END IF;';
            }

            $trigger[] = 'END;;';
            $trigger[] = 'DELIMITER ;';

            $queries[] = implode("\n", $trigger);
        }

        // execute the queries without i18n rewriting
        foreach ($queries as $query) {
            CRM_Core_DAO::executeQuery($query, array(), true, null, true, false);
        }

        // update civicrm_domain.locales
        $locales[] = $locale;
        $domain->locales = implode(CRM_Core_DAO::VALUE_SEPARATOR, $locales);
        $domain->save();
    }
}
