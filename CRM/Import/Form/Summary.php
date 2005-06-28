<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Import/Parser.php';

/**
 * This class summarizes the import results
 */
class CRM_Import_Form_Summary extends CRM_Core_Form {

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess( ) {

        // set the error message path to display
        $errorFile = $this->assign('errorFile', $this->get('errorFile') );
        $totalRowCount = $this->get('totalRowCount');
        $invalidRowCount = $this->get('invalidRowCount');
        $conflictRowCount = $this->get('conflictRowCount');
        $duplicateRowCount = $this->get('duplicateRowCount');
        $onDuplicate = $this->get('onDuplicate');
        if ($duplicateRowCount > 0) {
            $this->set('downloadDuplicateRecords',
                '<a href="'
                . CRM_Utils_System::url('civicrm/export', 'type=3')
                . '">' . ts('Download Duplicates') . '</a>');
        } else {
            $duplicateRowCount = 0;
            $this->set('duplicateRowCount', $duplicateRowCount);
        }

        if ($onDuplicate == CRM_Import_Parser::DUPLICATE_UPDATE) {
            $dupeActionString = 
                ts('These records have been updated with the imported data.');   
        } else if ($onDuplicate == CRM_Import_Parser::DUPLICATE_REPLACE) {
            $dupeActionString =
                ts('These records have been replaced with the imported data.');
        } else if ($onDuplicate == CRM_Import_Parser::DUPLICATE_FILL) {
            $dupeActionString =
                ts('These records have been filled in with the imported data.');
        } else {
            /* Skip by default */
            $dupeActionString = 
                ts('These records have not been imported.');
        }
        $this->assign('dupeActionString', $dupeActionString);
        
        $this->set('validRowCount', $totalRowCount - $invalidRowCount -
                    $conflictRowCount - $duplicateRowCount);

        $properties = array( 'totalRowCount', 'validRowCount', 'invalidRowCount', 'conflictRowCount', 'downloadConflictRecords', 'downloadErrorRecords', 'duplicateRowCount', 'downloadDuplicateRecords' );
        foreach ( $properties as $property ) {
            $this->assign( $property, $this->get( $property ) );
        }
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Done'),
                                         'isDefault' => true   ),
                                 )
                           );
    }

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) {
        return ts('Summary');
    }

}

?>
