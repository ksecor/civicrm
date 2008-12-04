<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
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
        $config =& CRM_Core_Config::singleton();

        // FIXME: why do we limit the file size to 8 MiB if it's larger in config?
        $uploadFileSize = $config->maxImportFileSize >= 8388608 ? 8388608 : $config->maxImportFileSize;
        $uploadSize = round(($uploadFileSize / (1024*1024)), 2);
        $this->assign('uploadSize', $uploadSize);
        $this->add('file', 'uploadFile', ts('Import Data File'), 'size=30 maxlength=60', true);

        $this->setMaxFileSize($uploadFileSize);
        $this->addRule('uploadFile', ts('File size should be less than %1 MBytes (%2 bytes)', array(1 => $uploadSize, 2 => $uploadFileSize)), 'maxfilesize', $uploadFileSize);
        $this->addRule('uploadFile', ts('Input file must be in CSV format'), 'utf8File');
        $this->addRule('uploadFile', ts('A valid file must be uploaded.'), 'uploadedfile');

        $this->addElement('checkbox', 'skipColumnHeader', ts('First row contains column headers'));

        // FIXME: test this
        // FIXME: perhaps this should go to the common form?
        if (!empty($config->geocodeMethod)) {
            $this->addElement('checkbox', 'doGeocodeAddress', ts('Lookup mapping info during import?'));
        }

        require_once 'CRM/Core/Form/Date.php';
        CRM_Core_Form_Date::buildAllowedDateFormats($this);

    }

    function postProcess(&$params, &$db)
    {
    }
}
