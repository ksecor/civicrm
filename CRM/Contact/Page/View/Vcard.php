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

require_once 'CRM/Contact/Page/View.php';
require_once 'Contact/Vcard/Build.php';

/**
 * vCard export class
 *
 */
class CRM_Contact_Page_View_Vcard extends CRM_Contact_Page_View {

    /**
     * Heart of the vCard data assignment process. The runner gets all the meta
     * data for the contact and calls the writeVcard method to output the vCard
     * to the user.
     *
     * @return void
     */
    function run()
    {
        $this->preProcess();

        $params   = array();
        $defaults = array();
        $ids      = array();

        $params['id'] = $params['contact_id'] = $this->_contactId;
        $contact = CRM_Contact_BAO_Contact::retrieve($params, $defaults, $ids);

        CRM_Contact_BAO_Contact::resolveDefaults($defaults);

        // now that we have the contact's data - let's build the vCard
        // TODO: non-US-ASCII support (requires changes to the Contact_Vcard_Build class)

        $vcard =& new Contact_Vcard_Build('2.1');

        if ($defaults['contact_type'] == 'Individual') {
            $vcard->setName($defaults['last_name'], $defaults['first_name'], $defaults['middle_name'], $defaults['prefix'], $defaults['suffix']);
        } elseif ($defaults['contact_type'] == 'Organization') {
            $vcard->setName($defaults['organization_name'], '', '', '', '');
        } elseif ($defaults['contact_type'] == 'Household') {
            $vcard->setName($defaults['household_name'], '', '', '', '');
        }
        $vcard->setFormattedName($defaults['display_name']);
        $vcard->setSortString($defaults['sort_name']);

        if ($defaults['nick_name'])  $vcard->addNickname($defaults['nick_name']);
        if ($defaults['job_title'])  $vcard->setTitle($defaults['job_title']);
        if ($defaults['birth_date']) $vcard->setBirthday($defaults['birth_date']);
        if ($defaults['home_URL'])   $vcard->setURL($defaults['home_URL']);
        // TODO: $vcard->setGeo($lat, $lon);


        foreach ($defaults['location'] as $location) {

            // we don't keep PO boxes in separate fields
            $pob = '';
            $extend = $location['address']['supplemental_address_1'];
            if ($location['address']['supplemental_address_2']) $extend .= ', ' . $location['address']['supplemental_address_2'];
            $street = $location['address']['street_address'];
            $locality = $location['address']['city'];
            $region = $location['address']['state_province'];
            $postcode = $location['address']['postal_code'];
            if ($location['address']['postal_code_suffix']) $postcode .= '-' . $location['address']['postal_code_suffix'];
            $country = $location['address']['country'];
            $vcard->addAddress($pob, $extend, $street, $locality, $region, $postcode, $country);
            if ($location['vcard_name']) $vcard->addParam('TYPE', $location['vcard_name']);
            if ($location['is_primary']) $vcard->addParam('TYPE', 'PREF');

            if ( CRM_Utils_Array::value( 'phone', $location ) ) {
                foreach ($location['phone'] as $phone) {
                    $vcard->addTelephone($phone['phone']);
                    if ($location['vcard_name']) $vcard->addParam('TYPE', $location['vcard_name']);
                    if ($phone['is_primary']) $vcard->addParam('TYPE', 'PREF');
                }
            }
            
            if ( CRM_Utils_Array::value( 'email', $location ) ) {
                foreach ($location['email'] as $email) {
                    $vcard->addEmail($email['email']);
                    if ($location['vcard_name']) $vcard->addParam('TYPE', $location['vcard_name']);
                    if ($email['is_primary']) $vcard->addParam('TYPE', 'PREF');
                }
            }
        }

        // all that's left is sending the vCard to the browser
        $filename = CRM_Utils_String::munge($defaults['display_name']);
        $vcard->send($filename . '.vcf', 'attachment', 'utf-8');
        exit( );
    }

}


