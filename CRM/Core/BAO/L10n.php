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

require_once 'CRM/Core/DAO/L10n.php';

class CRM_Core_BAO_L10n extends CRM_Core_DAO_L10n
{

    /**
     * Cache entries from the civicrm_l10n table and return the relevant translations
     *
     * @param $table  string  the table for localization (civicrm_l10n.entity_table)
     * @param $column string  the column for localization (civicrm_l10n.entity_column)
     * @param $id     int     the id for localization (civicrm_l10n.entity_id)
     * @return        string  the localized string
     */
    static function cache($table, $column, $id)
    {
        static $cache = array();
        $config =& CRM_Core_Config::singleton();
        if (!isset($cache[$config->lcMessages][$table])) {
            $bao =& new self();
            $bao->locale = $config->lcMessages;
            $bao->entity_table = $table;
            $bao->find();
            while ($bao->fetch()) {
                $cache[$config->lcMessages][$table][$bao->entity_column][$bao->entity_id] = $bao->translation;
            }
            $bao->free();
        }
        return $cache[$config->lcMessages][$table][$column][$id];
    }

    /**
     * Localize (in place) a CRM_Core_BAO_CustomGroup::groupTree() output
     * 
     * @param $groupTree array  the group tree for localization
     * @return           void
     */
    static function localizeGroupTree(&$groupTree)
    {
        foreach($groupTree as $groupKey => $group) {
            if ($groupKey == 'info') continue;

            // localize group title
            if ($localized = self::cache('civicrm_custom_group', 'title', $group['id'])) {
                $groupTree[$groupKey]['title'] = $localized;
            }

            // localize field label
            foreach ($group['fields'] as $fieldKey => $field) {
                if ($localized = self::cache('civicrm_custom_field', 'label', $field['id'])) {
                    $groupTree[$groupKey]['fields'][$fieldKey]['label'] = $localized;
                }
            }
        }
    }

}
