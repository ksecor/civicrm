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

require_once 'Mail/mime.php';

class CRM_Mailing_BAO_Mailing extends CRM_Mailing_DAO_Mailing {

    /**
     * The header associated with this mailing
     */
    private $header = null;

    /**
     * The footer associated with this mailing
     */
    private $footer = null;


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

    /**
     * Retrieve the header and footer for this mailing
     *
     * @param void
     * @return void
     * @access private
     */
    private function getHeaderFooter() {
        $this->header =& new CRM_Mailing_BAO_Component();
        $this->header->id = $this->header_id;
        $this->header->find(true);
        
        $this->footer =& new CRM_Mailing_BAO_Component();
        $this->footer->id = $ this->footer_id;
        $this->footer->find(true);
    }


    /**
     * Compose a message
     *
     * @param int $job_id           ID of the Job associated with this message
     * @param int $event_queue_id   ID of the EventQueue
     * @param int $email            Destination address
     * @return object               The mail object
     * @access public
     */
    public function &compose($job_id, $event_queue_id, $email) {
    
        if ($this->header == null || $this->footer == null) {
            $this->getHeaderFooter();
        }
        
        $html   = $this->header->body_html 
                . $this->body_html 
                . $this->footer->body_html;
                        
        $text   = $this->header->body_text
                . $this->body_text
                . $this->footer->body_text;

        /* TODO VERP this stuff */
        $headers = array(
            'To'        => $email,
            'Subject'   => $this->subject,
            'From'      => $this->from_name . ' <' . $this->from_email . '>',
            'Reply-To'  => $this->reply_to_email
        );

        
        /* TODO Token replacement */

        $message =& new Mail_Mime("\n");

        $message->setTxtBody($text);
        $message->setHTMLBody($html);
        $message->headers($headers);

        return $message;
    }
}

?>
