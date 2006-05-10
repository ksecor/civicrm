<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contact/Form/StateCountry.php';

class CRM_Contact_Form_Address
{


    /**
     * build form for address input fields 
     *
     * @param object $form - CRM_Core_Form (or subclass)
     * @param array reference $location - location array
     * @param int $locationId - location id whose block needs to be built.
     * @return none
     *
     * @access public
     * @static
     */
    static function buildAddressBlock(&$form, &$location, $locationId)
    {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Core_DAO_Address');
        $location[$locationId]['address']['street_address']         =
            $form->addElement('text', "location[$locationId][address][street_address]", ts('Street Address'),
                              $attributes['street_address']);
        $location[$locationId]['address']['supplemental_address_1'] =
            $form->addElement('text', "location[$locationId][address][supplemental_address_1]", ts('Addt\'l Address 1'),
                              $attributes['supplemental_address_1']);
        $location[$locationId]['address']['supplemental_address_2'] =
            $form->addElement('text', "location[$locationId][address][supplemental_address_2]", ts('Addt\'l Address 2'),
                              $attributes['supplemental_address_2']);

        $location[$locationId]['address']['city']                   =
            $form->addElement('text', "location[$locationId][address][city]", ts('City'),
                              $attributes['city']);
        $location[$locationId]['address']['postal_code']            =
            $form->addElement('text', "location[$locationId][address][postal_code]", ts('Zip / Postal Code'),
                              $attributes['postal_code']);
        $location[$locationId]['address']['postal_code_suffix']            =
            $form->addElement('text', "location[$locationId][address][postal_code_suffix]", ts('Add-on Code'),
                              array( 'size' => 4, 'maxlength' => 12 ));
        $location[$locationId]['address']['state_province_id']      =
            $form->addElement('select', "location[$locationId][address][state_province_id]", ts('State / Province'),
                              array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince());
         if ($form->getAction() & ( CRM_Core_Action::UPDATE )) {
             $location[$locationId]['address']['country_id']             =
                 $form->addElement('select', "location[$locationId][address][country_id]", ts('Country'),
                                   array('' => ts('- select -')) + CRM_Core_PseudoConstant::country());
         } else {
             $location[$locationId]['address']['country_id']             =
                 $form->addElement('select', "location[$locationId][address][country_id]", ts('Country'),
                                   CRM_Core_PseudoConstant::country());
         }

         $location[$locationId]['address']['geo_code_1']             =
            $form->addElement('text', "location[$locationId][address][geo_code_1]", ts('Latitude'), array('size' => 4, 'maxlength' => 8));
         $location[$locationId]['address']['geo_code_2']             =
             $form->addElement('text', "location[$locationId][address][geo_code_2]", ts('Longitude'), array('size' => 4, 'maxlength' => 8));
    }
    
    

    /**
     * check for correct state / country mapping.
     *
     * @param array reference $fields - submitted form values.
     * @param array reference $errors - if any errors found add to this array. please.
     * @return true if no errors
     *         array of errors if any present.
     *
     * @access public
     * @static
     */
    static function formRule(&$fields, &$errors)
    {
        // check for state/country match if not report error to user.
        for ($i=1; $i<=CRM_Contact_Form_Location::BLOCKS; $i++) {
            if ( ! CRM_Utils_Array::value( $i, $fields['location'] ) &&
                 ! CRM_Utils_Array::value( 'address', $fields['location'][$i] ) ) {
                continue;
            }

            $stateProvinceId = $fields['location'][$i]['address']['state_province_id'];
            $countryId = $fields['location'][$i]['address']['country_id'];

            if ($stateProvinceId && $countryId) {
                $stateProvinceDAO =& new CRM_Core_DAO_StateProvince();
                $stateProvinceDAO->id = $stateProvinceId;
                $stateProvinceDAO->find(true);

                if ($stateProvinceDAO->country_id != $countryId) {
                    // countries mismatch hence display error
                    $stateProvinces = CRM_Core_PseudoConstant::stateProvince();
                    $countries =& CRM_Core_PseudoConstant::country();
                    $errors["location[$i][address][state_province_id]"] = "State/Province " . $stateProvinces[$stateProvinceId] . " is not part of ". $countries[$countryId] . ". It belongs to " . $countries[$stateProvinceDAO->country_id] . "." ;
                }
            }
        }
    }
}

?>
