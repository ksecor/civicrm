<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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

/**
 * form helper class for an Email object
 */
class CRM_Contact_Form_Email 
{
    /**
     * build the form elements for an email object
     *
     * @param CRM_Core_Form $form       reference to the form object
     * @param array         $location   the location object to store all the form elements in
     * @param int           $locationId the locationId we are dealing with
     * @param int           $count      the number of blocks to create
     *
     * @return void
     * @access public
     * @static
     */
    static function buildEmailBlock(&$form, &$location, $locationId, $count) {
        require_once 'CRM/Core/ShowHideBlocks.php';
       
        for ($i = 1; $i <= $count; $i++) {
            $label = ($i == 1) ? ts('Email (preferred)') : ts('Email');

            CRM_Core_ShowHideBlocks::linksForArray( $form, $i, $count, "location[$locationId][email]", ts('another email'), ts('hide this email'));
            
            $location[$locationId]['email'][$i]['email'] = $form->addElement('text', 
                                                                             "location[$locationId][email][$i][email]",
                                                                             $label,
                                                                             CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email',
                                                                                                   'email'));
            $form->addRule( "location[$locationId][email][$i][email]", ts('Email is not valid.'), 'email' );
            
            $location[$locationId]['email'][$i]['on_hold'] = $form->addElement('advcheckbox',
                                                                             "location[$locationId][email][$i][on_hold]",null, ts('On Hold'));
            $location[$locationId]['email'][$i]['is_bulkmail'] = $form->addElement('advcheckbox',
                                                                               "location[$locationId][email][$i][is_bulkmail]",null, ts('for bulkmail'));
            
        }
    }
}


?>
