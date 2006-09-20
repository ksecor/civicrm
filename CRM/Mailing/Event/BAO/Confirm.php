<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'Mail/mime.php';

require_once 'CRM/Mailing/Event/DAO/Confirm.php';

class CRM_Mailing_Event_BAO_Confirm extends CRM_Mailing_Event_DAO_Confirm {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Confirm a pending subscription
     *
     * @param int $contact_id       The id of the contact
     * @param int $subscribe_id     The id of the subscription event
     * @param string $hash          The hash
     * @return boolean              True on success
     * @access public
     * @static
     */
    public static function confirm($contact_id, $subscribe_id, $hash) {
        $se =& CRM_Mailing_Event_BAO_Subscribe::verify($contact_id,
                                            $subscribe_id, $hash);
        
        if (! $se) {
            return false;
        }

        CRM_Core_DAO::transaction('BEGIN');
        
        $ce =& new CRM_Mailing_Event_BAO_Confirm();
        $ce->event_subscribe_id = $se->id;
        $ce->time_stamp = date('YmdHis');
        $ce->save();
        
        CRM_Contact_BAO_GroupContact::updateGroupMembershipStatus(
                $contact_id, $se->group_id,'Email',$ce->id);
        
        CRM_Core_DAO::transaction('COMMIT');

        $config =& CRM_Core_Config::singleton();
        $domain =& CRM_Mailing_Event_BAO_Subscribe::getDomain($subscribe_id);
        
        list($display_name, $email) =
                CRM_Contact_BAO_Contact::getEmailDetails($se->contact_id);
                
        $group =& new CRM_Contact_DAO_Group();
        $group->id = $se->group_id;
        $group->find(true);
        
        require_once 'CRM/Mailing/BAO/Component.php';
        $component =& new CRM_Mailing_BAO_Component();
        $component->domain_id = $domain->id;
        $component->is_default = 1;
        $component->is_active = 1;
        $component->component_type = 'Welcome';

        $component->find(true);
        
        $headers = array(
            'Subject'   => $component->subject,
            'From'      => ts('"%1 Administrator" <do-not-reply@%2>',
                            array(  1 => $domain->name,
                                    2 => $domain->email_domain)),
            'To'        => $email,
            'Reply-To'  => "do-not-reply@{$domain->email_domain}",
            'Return-Path'  => "do-not-reply@{$domain->email_domain}",
        );

        $html = $component->body_html;
        require_once 'CRM/Utils/Token.php';
        $html = CRM_Utils_Token::replaceDomainTokens($html, $domain, true);
        $html = CRM_Utils_Token::replaceWelcomeTokens($html, $group->name, true);

        if ($component->body_text) {
            $text = $component->body_text;
        } else {
            $text = CRM_Utils_String::htmlToText($component->body_html);
        }
        $text = CRM_Utils_Token::replaceDomainTokens($text, $domain, false);
        $text = CRM_Utils_Token::replaceWelcomeTokens($text, $group->name, false);

        $message =& new Mail_Mime("\n");
        $message->setHTMLBody($html);
        $message->setTxtBody($text);
        $b = $message->get();
        $h = $message->headers($headers);
        $mailer =& $config->getMailer();

        PEAR::setErrorHandling(PEAR_ERROR_CALLBACK,
                                array('CRM_Mailing_BAO_Mailing', 'catchSMTP'));
        $mailer->send($email, $h, $b);
        CRM_Core_Error::setCallback();
        
        return true;
    }
}

?>
