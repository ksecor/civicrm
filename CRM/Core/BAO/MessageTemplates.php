<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
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

require_once 'Mail/mime.php';
require_once 'CRM/Core/DAO/MessageTemplates.php';


class CRM_Core_BAO_MessageTemplates extends CRM_Core_DAO_MessageTemplates 
{

    /**
     * static holder for the default LT
     */
    static $_defaultMessageTemplates = null;
    

    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }
    
    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Core_BAO_MessageTemplates object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $messageTemplates =& new CRM_Core_DAO_MessageTemplates( );
        $messageTemplates->copyValues( $params );
        if ( $messageTemplates->find( true ) ) {
            CRM_Core_DAO::storeValues( $messageTemplates, $defaults );
            return $messageTemplates;
        }
        return null;
    }

    /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * @static
     */
    static function setIsActive( $id, $is_active ) 
    {
        return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_MessageTemplates', $id, 'is_active', $is_active );
    }

    /**
     * function to add the Message Templates
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add(&$params, &$ids) 
    {
        $params['is_active']            =  CRM_Utils_Array::value( 'is_active', $params, false );
        // action is taken depending upon the mode
        $messageTemplates               =& new CRM_Core_DAO_MessageTemplates( );
        $messageTemplates->domain_id    = CRM_Core_Config::domainID( );
        $messageTemplates->copyValues( $params );
        
        $messageTemplates->id = CRM_Utils_Array::value( 'messageTemplate', $ids );

        $messageTemplates->save( );
        return $messageTemplates;
    }

    /**
     * function to delete the Message Templates
     *
     * @access public
     * @static 
     * @return object
     */
    static function del( $messageTemplatesID ) 
    {
        // make sure messageTemplatesID is an integer
        if ( ! CRM_Utils_Rule::positiveInteger( $messageTemplatesID ) ) {
            CRM_Core_Error::fatal( ts( 'Invalid Message template' ) );
        }

        // set membership_type to null
        $query = "UPDATE civicrm_membership_type
                  SET renewal_msg_id = NULL
                  WHERE renewal_msg_id = %1";
        $params = array( 1 => array( $messageTemplatesID, 'Integer' ) );
        CRM_Core_DAO::executeQuery( $query, $params );

        $messageTemplates =& new CRM_Core_DAO_MessageTemplates( );
        $messageTemplates->id = $messageTemplatesID;
        $messageTemplates->delete();
        CRM_Core_Session::setStatus( ts('Selected message templates has been deleted.') );
    }
    
    /**
     * function to delete the Message Templates
     *
     * @access public
     * @static 
     * @return object
     */
    static function getMessageTemplates() {
        $msgTpls =array();

        $messageTemplates =& new CRM_Core_DAO_MessageTemplates( );
        $messageTemplates->is_active = 1;
        $messageTemplates->find();
        while ( $messageTemplates->fetch() ) {
            $msgTpls[$messageTemplates->id] = $messageTemplates->msg_title;
        }
        return $msgTpls;
    }

    static function sendReminder( $contactId, $email, $domainID, $messageTemplateID ,$from) {
        require_once "CRM/Core/BAO/Domain.php";
        require_once "CRM/Utils/String.php";
        require_once "CRM/Utils/Token.php";

        $messageTemplates =& new CRM_Core_DAO_MessageTemplates( );
        $messageTemplates->id = $messageTemplateID;

        $domain = CRM_Core_BAO_Domain::getDomainByID( $domainID );
        
        if ( $messageTemplates->find(true) ) {
            $body_text = $messageTemplates->msg_text;
            $body_html = $messageTemplates->msg_html;
            if ( $body_html ) {
                $html = CRM_Utils_Token::replaceDomainTokens($html,
                                                             $domain, true);
               
            }
            if (!$body_text) {
                $body_text = CRM_Utils_String::htmlToText($body_html);
            }
            $body_text = CRM_Utils_Token::replaceDomainTokens($body_text,
                                                               $domain, false);
            $html = $body_html;
            $text = $body_text;

            $params  = array( 'contact_id' => $contactId );
            $contact =& crm_fetch_contact( $params );
            if ( is_a( $contact, 'CRM_Core_Error' ) ) {
                return null;
            }
            $message =& new Mail_Mime("\n");

            /* Do contact-specific token replacement in text mode, and add to the
             * message if necessary */
            if ( !$html || $contact['preferred_mail_format'] == 'Text' ||
                $contact['preferred_mail_format'] == 'Both') 
                {
                    $text = CRM_Utils_Token::replaceContactTokens(
                                                                  $text, $contact, false);
                    // render the &amp; entities in text mode, so that the links work
                    $text = str_replace('&amp;', '&', $text);
                }
            
            if ( !$html || $contact['preferred_mail_format'] == 'Text' ||
                $contact['preferred_mail_format'] == 'Both') 
                {
                    $message->setTxtBody($text);
                    
                    unset( $text );
                }
            
            if ($html && ( $contact['preferred_mail_format'] == 'HTML' ||
                          $contact['preferred_mail_format'] == 'Both'))
                {
                    $html = CRM_Utils_Token::replaceContactTokens(
                                                                  $html, $contact, false);
                    $message->setHTMLBody($html);
                    
                    unset( $html );
                }
            $recipient = "\"{$contact['display_name']}\" <$email>";
            
            $messageSubject = CRM_Utils_Token::replaceContactTokens(
                                                                  $messageTemplates->msg_subject, $contact, false);
            $headers = array(
                             'From'      => $from,
                             'Subject'   => $messageSubject,
                         );
            $headers['To'] = $recipient;
            
            $mailMimeParams = array(
                                    'text_encoding' => '8bit',
                                    'html_encoding' => '8bit',
                                    'head_charset'  => 'utf-8',
                                    'text_charset'  => 'utf-8',
                                    'html_charset'  => 'utf-8',
                                    );
            $message->get($mailMimeParams);
            $message->headers($headers);

            $config =& CRM_Core_Config::singleton();
            $mailer =& $config->getMailer();
            
            $body = $message->get();
            $headers = $message->headers();
            
            $result = $mailer->send($recipient, $headers, $body);
        }
        
    }
}
?>
