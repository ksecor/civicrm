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
    static function buildEmailBlock(&$form, &$location, $locationId, $count) 
    {
        require_once 'CRM/Core/ShowHideBlocks.php';

        $showBulkMailing = true;
        //suppress Bulk Mailings (CRM-2881)
        if ( is_object( $form ) && ($form instanceof CRM_Event_Form_ManageEvent_Location ) ) {
            $showBulkMailing = false;
        }
   
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
            if ( $showBulkMailing ) {
                $location[$locationId]['email'][$i]['is_bulkmail'] = $form->addElement('advcheckbox',
                                                                                       "location[$locationId][email][$i][is_bulkmail]",ts('Use for Bulk Mailings'), ts('Use for Bulk Mailings'), array('onchange' => "email_is_bulkmail_onclick('" . $form->getName() . "', $i, $count, $locationId);" ));
            }
        }
    }
}



