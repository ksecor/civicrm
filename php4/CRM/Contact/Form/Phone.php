<?php
/**
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 * form helper class for a phone object 
 */


require_once 'CRM/Core/ShowHideBlocks.php';
require_once 'CRM/Core/DAO.php';
class CRM_Contact_Form_Phone 
{
    /**
     * build the form elements for a phone object
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
     function buildPhoneBlock(&$form, &$location, $locationId, $count) {

        for ($i = 1; $i <= $count; $i++) {
            $label = ($i == 1) ? ts('Phone (preferred)') : ts('Phone');

            CRM_Core_ShowHideBlocks::linksForArray( $form, $i, $count, "location[$locationId][phone]", ts('another phone'), ts('hide this phone'));

            $location[$locationId]['phone'][$i]['phone_type'] = $form->addElement('select',
                                                                                  "location[$locationId][phone][$i][phone_type]",
                                                                                  null,
                                                                                  $GLOBALS['_CRM_CORE_SELECTVALUES']['phoneType']);

            $location[$locationId]['phone'][$i]['phone']      = $form->addElement('text',
                                                                                  "location[$locationId][phone][$i][phone]", 
                                                                                  $label,
                                                                                  CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Phone',
                                                                                                        'phone'));

            // TODO: set this up as a group, we need a valid phone_type_id if we have a  phone number
            $form->addRule( "location[$locationId][phone][$i][phone]", ts('Phone number is not valid.'), 'phone' );
        }
    }

}


?>
