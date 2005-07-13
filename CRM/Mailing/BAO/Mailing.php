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

class CRM_Mailing_BAO_Mailing extends CRM_Mailing_DAO_Mailing {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Find all intended recipients of a mailing
     *
     * @param none
     * @return array    Tuples of Contact IDs and Email IDs
     */
    function &getRecipients() {
        $mailingGroup =& new CRM_Mailing_DAO_MailingGroup();
        
        $mg         = $mailingGroup->tableName();
        $email      = CRM_Contact_DAO_Email::tableName();
        $contact    = CRM_Contact_DAO_Contact::tableName();
        $location   = CRM_Contact_DAO_Location::tableName();
       
        /* Get all the group contacts we want to include */
        $queryGroupInclude = 
                    "SELECT         $email.id as email_id,
                                    $contact.id as contact_id
                    FROM            $email
                    INNER JOIN      $location
                            ON      $email.location_id = $location.id
                    INNER JOIN      $contact
                            ON      $location.contact_id = $contact.id
                    INNER JOIN      $mg
                            ON      $contact.id = $mg.entity_id
                    WHERE           $mg.entity_table = '$contact'
                        AND         $mg.mailing_id = " . $this->id . "
                        AND         $mg.group_type = 'Include'
                        AND         $location.is_primary = 1
                        AND         $email.is_primary = 1";
        $results = array();

        $mailingGroup->query($queryGroupInclude);
        $mailingGroup->find();

        while ($mailingGroup->fetch()) {
            $results[] =    array(  'email_id'  => $mailingGroup->email_id,
                                    'contact_id'=> $mailingGroup->contact_id
                            );
        }
        return $results;
        //  TODO:   2005-07-13 13:26:23 by Brian McFee <brmcfee@gmail.com>
        //  This only handles the very simple case of mailing to groups.  It
        //  doesn't handle exclusion or prior mailings yet.

        
        /* Get all the group contacts we want to exclude*/
        $queryGroupExclude = 
                    "SELECT         $email.id as email_id,
                                    $contact.id as contact_id
                    FROM            $email
                    INNER JOIN      $location
                            ON      $email.location_id = $location.id
                    INNER JOIN      $contact
                            ON      $location.contact_id = $contact.id
                    INNER JOIN      $mg
                            ON      $contact.id = $mg.entity_id
                    WHERE           $mg.entity_table = '$contact'
                        AND         $mg.id = " . $mailingGroup->mailing_id . "
                        AND         $mg.group_type = 'Exclude'
                        AND         $location.is_primary = 1
                        AND         $email.is_primary = 1";

    }


}

?>
