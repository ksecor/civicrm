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

/**
 * This class provides the functionality to add contact(s) to Household
 */
class CRM_Contact_Form_Task_AddToHousehold extends CRM_Contact_Form_Task {
    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) {
        
        $sel =& $this->addElement('hierselect', 'status', ts('Household:'), null, ' / ');
        
        //build the country array
        $aCountry = CRM_Core_PseudoConstant::country();
        
        // build the state array
        $stateProvinceDAO =& new CRM_Core_DAO_StateProvince();
        $stateProvinceDAO->selectAdd();
        $stateProvinceDAO->selectAdd('id, name, country_id');
        $stateProvinceDAO->find();
        while($stateProvinceDAO->fetch()) {
            $aStateProvince[$stateProvinceDAO->country_id][$stateProvinceDAO->id] = $stateProvinceDAO->name;
            // $aStateProvince[$stateProvinceDAO->id] = $stateProvinceDAO->name;
        }
  
        //build the household array
        $lng_country_id = 0;

        $contact =& new CRM_Contact_DAO_Contact();
        
        $strSql = "SELECT crm_address.country_id as country, crm_location.contact_id as contact,
                          crm_address.state_province_id as state, crm_address.id as address, crm_contact.sort_name as name
                   FROM crm_address, crm_location, crm_contact
                   WHERE crm_address.location_id = crm_location.id
                      AND crm_location.contact_id = crm_contact.id 
                      AND crm_location.is_primary = 1
                      AND crm_contact.contact_type ='Household'";
        
        $contact->query($strSql);
        
        while ($contact->fetch()) {

            //$aHousehold[$contact->country][$contact->state][$contact->address] = stripslashes($contact->name);
            $aTempHousehold[$contact->contact][0] = $contact->state;
            $aTempHousehold[$contact->contact][1] = $contact->name;
            $aTempHousehold[$contact->contact][2] = $contact->country;

            //$aHousehold[$contact->state][$contact->address] = $contact->name;
        }

        //print_r($aTempHousehold);
        // key is address id
        // 0 element is state id
        // 1 element is name
        // 2 element is country id
        
        // building 3d array with array[country_id][state_id][address_id]
        $lng_country_id = 0;
        $aHousehold = array();
        foreach ($aTempHousehold as $lngKey => $varValue) {

            if (array_key_exists($varValue[2], $aHousehold)) {
                $aHousehold[$varValue[2]][$varValue[0]][$lngKey] = $varValue[1];
            } else {
                $aHousehold[$varValue[2]][$varValue[0]][$lngKey] = $varValue[1];
            }
        }
        
        //print_r($aHousehold);
        
        $sel->setOptions(array($aCountry, $aStateProvince, $aHousehold));
        //$sel->setOptions(array($aStateProvince, $aHousehold));


        //$this->add('select', 'status', ts('Status of the Contact'), CRM_Core_SelectValues::$groupContactStatus, true);

        $this->addDefaultButtons( ts('Add To Household') );
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        //$groupId    = $this->controller->exportValue( 'AddToGroup', 'group'  );
        $status     = $this->controller->exportValue( 'AddToGroup', 'status' );
        // get contactID's of formValues

        // get contactID's of group members

        // create an intersection of 2 arrays of contactID

        // create an array of duplicate ID's with same status

        // create an array of duplicate ID's with conflicting status

        // display results.

        //$contactIds = array_keys( $this->_rows );
        // CRM_Contact_BAO_GroupContact::addContactsToGroup( $groupId, $contactIds, $status );

    }//end of function


}

?>
