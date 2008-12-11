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
 | Version 3, 19 November 2009.                                       |
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

require_once 'CRM/Import/DataSource.php';

class CRM_Import_DataSource_CSV extends CRM_Import_DataSource
{
    function getInfo()
    {
        return array('title' => 'CSV Import');
    }

    function preProcess(&$form)
    {
    }

    function buildQuickForm(&$form)
    {
        $form->add('hidden', 'hidden_dataSource', 'CRM_Import_DataSource_CSV');

        $config =& CRM_Core_Config::singleton();

        // FIXME: why do we limit the file size to 8 MiB if it's larger in config?
        $uploadFileSize = $config->maxImportFileSize >= 8388608 ? 8388608 : $config->maxImportFileSize;
        $uploadSize = round(($uploadFileSize / (1024*1024)), 2);
        $form->assign('uploadSize', $uploadSize);
        $form->add('file', 'uploadFile', ts('Import Data File'), 'size=30 maxlength=60', true);

        $form->setMaxFileSize($uploadFileSize);
        $form->addRule('uploadFile', ts('File size should be less than %1 MBytes (%2 bytes)', array(1 => $uploadSize, 2 => $uploadFileSize)), 'maxfilesize', $uploadFileSize);
        $form->addRule('uploadFile', ts('Input file must be in CSV format'), 'utf8File');
        $form->addRule('uploadFile', ts('A valid file must be uploaded.'), 'uploadedfile');

        $form->addElement('checkbox', 'skipColumnHeader', ts('First row contains column headers'));
    }

    function postProcess(&$params, &$db)
    {
        $file = $params['uploadFile']['name'];

        $table = self::_CsvToTable($db, $file, $params['skipColumnHeader']);

        require_once 'CRM/Import/ImportJob.php';
        $importJob = new CRM_Import_ImportJob($table);
        $this->set('importTableName', $importJob->getTableName());
    }

    /**
     * Create a table that matches the CSV file and populate it with the file's contents
     *
     * @param object $db     handle to the database connection
     * @param string $file   file name to load
     * @param bool $headers  whether the first row contains headers
     *
     * @return string  name of the created table
     */
    private static function _CsvToTable(&$db, $file, $headers = false)
    {
        $fd = fopen($file, 'r');
        if (!$fd) CRM_Core_Error::fatal("Could not read $file");

        $config =& CRM_Core_Config::singleton();
        $firstrow = fgetcsv($fd, 0, $config->fieldSeparator);

        // create the column names from the CSV header or as col_0, col_1, etc.
        if ($headers) {
            $columns = array_map('strtolower', $firstrow);
            $columns = str_replace(' ', '_', $columns);
            $columns = preg_replace('/[^a-z_]/', '', $columns);
        } else {
            $columns = array();
            foreach ($firstrow as $i => $_) $columns[] = "col_$i";
        }

        // FIXME: we should regen this table's name if it exists rather than drop it
        $table = 'civicrm_import_job_' . md5(uniqid(rand(), true));
        $db->query("DROP TABLE IF EXISTS $table");

        $create = "CREATE TABLE $table (" . implode(' text, ', $columns) . " text)";
        $db->query($create);

        // the proper approach, but some MySQL installs do not have this enabled
        // $load = "LOAD DATA LOCAL INFILE '$file' INTO TABLE $table FIELDS TERMINATED BY '$config->fieldSeparator' OPTIONALLY ENCLOSED BY '\"'";
        // if ($headers) $load .= ' IGNORE 1 LINES';
        // $db->query($load);

        // parse the CSV line by line and build one big INSERT (while MySQL-escaping the CSV contents)
        if (!$headers) rewind($fd);
        $sql = "INSERT IGNORE INTO $table VALUES ";
        $first = true;
        while ($row = fgetcsv($fd, 0, $config->fieldSeparator)) {
            if (!$first) $sql .= ', ';
            $first = false;
            $row = array_map('mysql_real_escape_string', $row);
            $sql .= "('" . implode("', '", $row) . "')";
        }
        $db->query($sql);

        fclose($fd);

        return $table;
    }
}
