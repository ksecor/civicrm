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


class CRM_Contact_Form_Address
{

    static function buildAddressBlock($form, &$location, $locationId) {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Address');
        $location[$locationId]['address']['street_address']         =
            $form->addElement('text', "location[$locationId][address][street_address]", 'Street Address:',
                              $attributes['street_address']);
        $location[$locationId]['address']['supplemental_address_1'] =
            $form->addElement('text', "location[$locationId][address][supplemental_address_1]", 'Addt\'l Address 1:',
                              $attributes['supplemental_address_1']);
        $location[$locationId]['address']['supplemental_address_2'] =
            $form->addElement('text', "location[$locationId][address][supplemental_address_2]", 'Addt\'l Address 2:',
                              $attributes['supplemental_address_2']);

        $location[$locationId]['address']['city']                   =
            $form->addElement('text', "location[$locationId][address][city]", 'City:',
                              $attributes['city']);
        $location[$locationId]['address']['postal_code']            =
            $form->addElement('text', "location[$locationId][address][postal_code]", 'Zip / Postal Code:',
                              $attributes['postal_code']);
        $location[$locationId]['address']['state_province_id']      =
            $form->addElement('select', "location[$locationId][address][state_province_id]", 'State / Province:',
                              array('' => '- select -') + CRM_Core_PseudoConstant::stateProvince());
        $location[$locationId]['address']['country_id']             =
            $form->addElement('select', "location[$locationId][address][country_id]", 'Country:',
                              array('' => '- select -') + CRM_Core_PseudoConstant::country());
    }



    /**
     * check for correct state / country mapping.
     *
     *
     * @param array reference $fields - submitted form values.
     * @param array reference $errors - if any errors found add to this array. please.
     *
     * @return true if no errors
     *         array of errors if any present.
     *
     * @access public
     *
     * @static
     *
     */

    static function formRule(&$fields, &$errors)
    {
        // check for state/country match if not report error to user.
        for ($i=1; $i<=CRM_Contact_Form_Location::BLOCKS; $i++) {
            $stateProvinceId = $fields['location'][$i]['address']['state_province_id'];
            $countryId = $fields['location'][$i]['address']['country_id'];

            if ($stateProvinceId) {
                $stateProvinceDAO = new CRM_Core_DAO_StateProvince();
                $stateProvinceDAO->id = $stateProvinceId;
                $stateProvinceDAO->country_id = $countryId;
                if (!$stateProvinceDAO->find(1)) {
                    $stateProvinces = CRM_Core_PseudoConstant::stateProvince();
                    $countries = CRM_Core_PseudoConstant::country();
                    $stateProvince = $stateProvinces[$stateProvinceId];
                    $country = $countries[$countryId];
                    CRM_Core_Error::debug_log_message("$stateProvince does not belong to $country"); 
                    $errors["location[$i][address][state_province_id]"] = "State " .  $stateProvince . " does not belong to ". $country;
                }
            }
        }
    }
}

?>