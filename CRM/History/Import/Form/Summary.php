<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/History/Import/Parser.php';

/**
 * This class summarizes the import results
 */
class CRM_History_Import_Form_Summary extends CRM_Core_Form {

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
        $relatedCount = $this->get('relatedCount');
        $totalRowCount += $relatedCount;
        $this->set('totalRowCount', $totalRowCount);

        $invalidRowCount = $this->get('invalidRowCount');
        $conflictRowCount = $this->get('conflictRowCount');
        $duplicateRowCount = $this->get('duplicateRowCount');
        $onDuplicate = $this->get('onDuplicate');
        $mismatchCount      = $this->get('unMatchCount');
        if ($duplicateRowCount > 0) {
            $this->set('downloadDuplicateRecordsUrl', CRM_Utils_System::url('civicrm/export', 'type=3&realm=history'));
        }else if($mismatchCount) {
            $this->set('downloadMismatchRecordsUrl', CRM_Utils_System::url('civicrm/export', 'type=4&realm=history'));
        } else {
            $duplicateRowCount = 0;
            $this->set('duplicateRowCount', $duplicateRowCount);
        }

        $this->assign('dupeError', false);
        
        if ($onDuplicate == CRM_History_Import_Parser::DUPLICATE_UPDATE) {
            $dupeActionString = 
                ts('These records have been updated with the imported data.');   
        } else if ($onDuplicate == CRM_History_Import_Parser::DUPLICATE_FILL) {
            $dupeActionString =
                ts('These records have been filled in with the imported data.');
        } else {
            /* Skip by default */
            $dupeActionString = 
                ts('These records have not been imported.');

            $this->assign('dupeError', true);
        
            /* only subtract dupes from succesful import if we're skipping */
            $this->set('validRowCount', $totalRowCount - $invalidRowCount -
                    $conflictRowCount - $duplicateRowCount - $mismatchCount);
        }
        $this->assign('dupeActionString', $dupeActionString);
        
        $properties = array( 'totalRowCount', 'validRowCount', 'invalidRowCount', 'conflictRowCount', 'downloadConflictRecordsUrl', 'downloadErrorRecordsUrl', 'duplicateRowCount', 'downloadDuplicateRecordsUrl','downloadMismatchRecordsUrl', 'groupAdditions', 'unMatchCount');
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
