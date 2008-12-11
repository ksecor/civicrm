<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
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
     * @param $locale string  the first locale to create (migrate to)
     * @return void
     */
    function makeMultilingual($locale)
    {
        $domain =& new CRM_Core_DAO_Domain();
        $domain->find(true);

        // break early if the db is already multi-lang
        if ($domain->locale) return;

        $dao =& new CRM_Core_DAO();

        // build the column-adding SQL queries
        $columns = CRM_Core_I18n_SchemaStructure::columns();
        $indices = CRM_Core_I18n_SchemaStructure::indices();
        $queries = array();
        foreach ($columns as $table => $hash) {
            // drop old indices
            if (isset($indices[$table])) {
                foreach ($indices[$table] as $index) {
                    $queries[] = "DROP INDEX {$index['name']} ON {$table}";
                }
            }
            // deal with columns
            foreach ($hash as $column => $type) {
                $queries[] = "ALTER TABLE {$table} ADD {$column}_{$locale} {$type}";
                $queries[] = "UPDATE {$table} SET {$column}_{$locale} = {$column}";
                $queries[] = "ALTER TABLE {$table} DROP {$column}";
            }

            // add views
            $cols = array();
            $dao->query("DESCRIBE {$table}", false);
            while ($dao->fetch()) {
                if (!in_array($dao->Field, array_keys($columns[$table]))) {
                    $cols[] = $dao->Field;
                }
            }
            foreach ($hash as $column => $_) {
                $cols[] = "{$column}_{$locale} {$column}";
            }
            $queries[] = "CREATE OR REPLACE VIEW {$table}_{$locale} AS SELECT ". implode(', ', $cols) . " FROM {$table}";

            // add new indices
            if (isset($indices[$table])) {
                foreach ($indices[$table] as $index) {
                    $unique = $index['unique'] ? 'UNIQUE' : '';
                    foreach ($index['field'] as $i => $col) {
                        // if a given column is localizable, extend its name with the locale
                        if (isset($columns[$table][$col])) $index['field'][$i] = "{$col}_{$locale}";
                    }
                    $cols = implode(', ', $index['field']);
                    $queries[] = "CREATE {$unique} INDEX {$index['name']}_{$locale} ON {$table} ({$cols})";
                }
            }
        }

        // execute the queries without i18n rewriting
        foreach ($queries as $query) {
            $dao->query($query, false);
        }

        // update civicrm_domain.locales
        $domain->locales = $locale;
        $domain->save();
    }

    /**
     * Add a new locale to a multi-lang db, setting 
     * its values to the current default locale.
     *
     * @param $locale string  the new locale to add
     * @param $source string  the locale to copy from
     * @return void
     */
    function addLocale($locale, $source)
    {
        // get the current supported locales 
        $domain =& new CRM_Core_DAO_Domain();
        $domain->find(true);
        $locales = explode(CRM_Core_DAO::VALUE_SEPARATOR, $domain->locales);

        // break early if the locale is already supported
        if (in_array($locale, $locales)) return;

        $dao =& new CRM_Core_DAO();

        // build the required SQL queries
        $columns = CRM_Core_I18n_SchemaStructure::columns();
        $indices = CRM_Core_I18n_SchemaStructure::indices();
        $queries = array();
        foreach ($columns as $table => $hash) {
            // add new columns
            foreach ($hash as $column => $type) {
                $queries[] = "ALTER TABLE {$table} ADD {$column}_{$locale} {$type}";
                $queries[] = "UPDATE {$table} SET {$column}_{$locale} = {$column}_{$source}";
            }

            // add views
            $cols = array();
            $dao->query("DESCRIBE {$table}", false);
            while ($dao->fetch()) {
                if (!preg_match('/_[a-z][a-z]_[A-Z][A-Z]$/', $dao->Field)) {
                    $cols[] = $dao->Field;
                }
            }
            foreach ($hash as $column => $_) {
                $cols[] = "{$column}_{$locale} {$column}";
            }
            $queries[] = "CREATE OR REPLACE VIEW {$table}_{$locale} AS SELECT ". implode(', ', $cols) . " FROM {$table}";

            // add new indices
            if (isset($indices[$table])) {
                foreach ($indices[$table] as $index) {
                    $unique = $index['unique'] ? 'UNIQUE' : '';
                    foreach ($index['field'] as $i => $col) {
                        // if a given column is localizable, extend its name with the locale
                        if (isset($columns[$table][$col])) $index['field'][$i] = "{$col}_{$locale}";
                    }
                    $cols = implode(', ', $index['field']);
                    $queries[] = "CREATE {$unique} INDEX {$index['name']}_{$locale} ON {$table} ({$cols})";
                }
            }
        }

        // take care of civicrm_contact.{display,sort}_name...
        $namesTrigger = array();
        foreach (array_merge($locales, array($locale)) as $loc) {

            $namesTrigger[] = "IF NEW.contact_type = 'Household' THEN";
            $namesTrigger[] = "SET NEW.display_name_{$loc} = NEW.household_name_{$loc};";
            $namesTrigger[] = "SET NEW.sort_name_{$loc} = NEW.household_name_{$loc};";

            $namesTrigger[] = "ELSEIF NEW.contact_type = 'Organization' THEN";
            $namesTrigger[] = "SET NEW.display_name_{$loc} = NEW.organization_name_{$loc};";
            $namesTrigger[] = "SET NEW.sort_name_{$loc} = NEW.organization_name_{$loc};";

            $namesTrigger[] = "ELSEIF NEW.contact_type = 'Individual' THEN";
            $namesTrigger[] = "SELECT v.label_{$loc} INTO @prefix FROM civicrm_option_value v JOIN civicrm_option_group g ON (v.option_group_id = g.id) WHERE g.name = 'individual_prefix' AND v.value = NEW.prefix_id;";
            $namesTrigger[] = "SELECT v.label_{$loc} INTO @suffix FROM civicrm_option_value v JOIN civicrm_option_group g ON (v.option_group_id = g.id) WHERE g.name = 'individual_suffix' AND v.value = NEW.suffix_id;";
            $namesTrigger[] = "SET NEW.display_name_{$loc} = TRIM(REPLACE(CONCAT_WS(' ', @prefix, NEW.first_name_{$loc}, NEW.middle_name_{$loc}, NEW.last_name_{$loc}, @suffix), '  ', ' '));";
            $namesTrigger[] = "SET NEW.sort_name_{$loc} = TRIM(', ' FROM CONCAT_WS(', ', NEW.last_name_{$loc}, NEW.first_name_{$loc}));";

            $namesTrigger[] = 'END IF;';

            $namesTrigger[] = "SELECT email INTO @email FROM civicrm_email WHERE is_primary = 1 AND contact_id = NEW.id LIMIT 1;";
            $namesTrigger[] = "IF NEW.display_name_{$loc} = '' THEN SET NEW.display_name_{$loc} = @email; END IF;";
            $namesTrigger[] = "IF NEW.sort_name_{$loc}    = '' THEN SET NEW.sort_name_{$loc}    = @email; END IF;";
        }

        // ...for UPDATE it's a separate trigger, for INSERT it has to be merged into the below, general one
        $queries[] = "DROP TRIGGER IF EXISTS civicrm_contact_before_update";
        $queries[] = "CREATE TRIGGER civicrm_contact_before_update BEFORE UPDATE ON civicrm_contact FOR EACH ROW BEGIN " . implode(' ', $namesTrigger) . ' END';

        // take care of the ON INSERT triggers
        foreach ($columns as $table => $hash) {
            $queries[] = "DROP TRIGGER IF EXISTS {$table}_before_insert";

            $trigger = array();
            $trigger[] = "CREATE TRIGGER {$table}_before_insert BEFORE INSERT ON {$table} FOR EACH ROW BEGIN";
            foreach ($hash as $column => $_) {
                $trigger[] = "IF NEW.{$column}_{$locale} IS NOT NULL THEN";
                foreach ($locales as $old) {
                    $trigger[] = "SET NEW.{$column}_{$old} = NEW.{$column}_{$locale};";
                }
                foreach ($locales as $old) {
                    $trigger[] = "ELSEIF NEW.{$column}_{$old} IS NOT NULL THEN";
                    foreach (array_merge($locales, array($locale)) as $loc) {
                        if ($loc == $old) continue;
                        $trigger[] = "SET NEW.{$column}_{$loc} = NEW.{$column}_{$old};";
                    }
                }
                $trigger[] = 'END IF;';
            }
            if ($table == 'civicrm_contact') {
                $trigger = array_merge($trigger, $namesTrigger);
            }
            $trigger[] = 'END';

            $queries[] = implode(' ', $trigger);
        }

        // execute the queries without i18n rewriting
        foreach ($queries as $query) {
            $dao->query($query, false);
        }

        // update civicrm_domain.locales
        $locales[] = $locale;
        $domain->locales = implode(CRM_Core_DAO::VALUE_SEPARATOR, $locales);
        $domain->save();
    }

    /**
     * Rewrite SQL query to use views to access tables with localized columns.
     *
     * @param $query string  the query for rewrite
     * @return string        the rewritten query
     */
    static function rewriteQuery($query)
    {
        static $tables = null;
        if ($tables === null) {
            $tables = CRM_Core_I18n_SchemaStructure::tables();
        }
        global $dbLocale;
        foreach ($tables as $table) {
            $query = preg_replace("/([^'\"])({$table})([^_'\"])/", "\\1\\2{$dbLocale}\\3", $query);
        }
        // uncomment the below to rewrite the civicrm_value_* queries
        // $query = preg_replace("/(civicrm_value_[a-z0-9_]+_\d+)([^_])/", "\\1{$dbLocale}\\2", $query);
        return $query;
    }
}
