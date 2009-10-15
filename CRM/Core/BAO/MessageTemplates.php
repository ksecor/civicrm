<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
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

require_once 'Mail/mime.php';
require_once 'CRM/Core/DAO/MessageTemplates.php';


class CRM_Core_BAO_MessageTemplates extends CRM_Core_DAO_MessageTemplates 
{
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
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add( &$params ) 
    {
        $params['is_active']            =  CRM_Utils_Array::value( 'is_active', $params, false );

        $messageTemplates               =& new CRM_Core_DAO_MessageTemplates( );
        $messageTemplates->copyValues( $params );
        
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
        
        $query = "UPDATE civicrm_mailing
                  SET msg_template_id = NULL
                  WHERE msg_template_id = %1";
        CRM_Core_DAO::executeQuery( $query, $params );
        
        $messageTemplates =& new CRM_Core_DAO_MessageTemplates( );
        $messageTemplates->id = $messageTemplatesID;
        $messageTemplates->delete();
        CRM_Core_Session::setStatus( ts('Selected message templates has been deleted.') );
    }
    
    /**
     * function to get the Message Templates
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

    static function sendReminder( $contactId, $email, $messageTemplateID ,$from) {
        require_once "CRM/Core/BAO/Domain.php";
        require_once "CRM/Utils/String.php";
        require_once "CRM/Utils/Token.php";

        $messageTemplates =& new CRM_Core_DAO_MessageTemplates( );
        $messageTemplates->id = $messageTemplateID;

        $domain = CRM_Core_BAO_Domain::getDomain( );
        $result = null;

        if ( $messageTemplates->find(true) ) {
            $body_text = $messageTemplates->msg_text;
            $body_html = $messageTemplates->msg_html;
            $body_subject = $messageTemplates->msg_subject;
            if (!$body_text) {
                $body_text = CRM_Utils_String::htmlToText($body_html);
            }
            
            $params  = array( 'contact_id' => $contactId );
            require_once 'api/v2/Contact.php';
            $contact =& civicrm_contact_get( $params );

            //CRM-4524
            $contact = reset( $contact );
            
            if ( !$contact || is_a( $contact, 'CRM_Core_Error' ) ) {
                return null;
            }
            
            $type = array('html', 'text');
            
            foreach( $type as $key => $value ) {
                require_once 'CRM/Mailing/BAO/Mailing.php';
                $dummy_mail = new CRM_Mailing_BAO_Mailing();
                $bodyType = "body_{$value}";
                $dummy_mail->$bodyType = $$bodyType;
                $tokens = $dummy_mail->getTokens();
                
                if ( $$bodyType ) {
                    $$bodyType = CRM_Utils_Token::replaceDomainTokens($$bodyType, $domain, true, $tokens[$value] );
                    $$bodyType = CRM_Utils_Token::replaceContactTokens($$bodyType, $contact, false, $tokens[$value] );
                }
            }
            $html = $body_html;
            $text = $body_text;
            
            $message =& new Mail_Mime("\n");
            
            /* Do contact-specific token replacement in text mode, and add to the
             * message if necessary */
            if ( !$html || $contact['preferred_mail_format'] == 'Text' ||
                 $contact['preferred_mail_format'] == 'Both') 
                {
                    // render the &amp; entities in text mode, so that the links work
                    $text = str_replace('&amp;', '&', $text);
                    $message->setTxtBody($text);
                    
                    unset( $text );
                }
            
            if ($html && ( $contact['preferred_mail_format'] == 'HTML' ||
                           $contact['preferred_mail_format'] == 'Both'))
                {
                    $message->setHTMLBody($html);
                    
                    unset( $html );
                }
            $recipient = "\"{$contact['display_name']}\" <$email>";
            
            $matches = array();
            preg_match_all( '/(?<!\{|\\\\)\{(\w+\.\w+)\}(?!\})/',
                            $body_subject,
                            $matches,
                            PREG_PATTERN_ORDER);
            
            $subjectToken = null;
            if ( $matches[1] ) {
                foreach ( $matches[1] as $token ) {
                    list($type,$name) = split( '\.', $token, 2 );
                    if ( $name ) {
                        if ( ! isset( $subjectToken['contact'] ) ) {
                            $subjectToken['contact'] = array( );
                        }
                        $subjectToken['contact'][] = $name;
                    }
                }
            }
            
            $messageSubject = CRM_Utils_Token::replaceContactTokens($body_subject, $contact, false, $subjectToken);
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
            
            CRM_Core_Error::ignoreException( );
            $result = $mailer->send($recipient, $headers, $body);
            CRM_Core_Error::setCallback();
        }
        
        return $result;
    }

    /**
     * Fetch subject, text and HTML templates based on option
     * group, option value and an array of template parameters
     *
     * @param string $group      option group name
     * @param string $value      option value name
     * @param array  $tplParams  template variables
     *
     * @return array  a subject-, text- and html-carrying array with the templates (evaluated)
     */
    static function getSubjectTextHTML($group, $value, $tplParams)
    {
        // fetch the three elements from the db based on option_group and option_value names
        $query = 'SELECT msg_subject subject, msg_text text, msg_html html
                  FROM civicrm_msg_template mt
                  JOIN civicrm_option_value ov ON workflow_id = ov.id
                  JOIN civicrm_option_group og ON ov.option_group_id = og.id
                  WHERE og.name = %1 AND ov.name = %2 AND mt.is_default = 1';
        $params = array(1 => array($group, 'String'), 2 => array($value, 'String'));
        $dao = CRM_Core_DAO::executeQuery($query, $params);
        $dao->fetch();

        // TODO: replace tokens in the three elements

        // parse the three elements with Smarty
        require_once 'CRM/Core/Smarty/resources/String.php';
        civicrm_smarty_register_string_resource();
        $smarty =& CRM_Core_Smarty::singleton();
        // FIXME: we should clear the template variables, but this would break 
        // way too much existing code which shares the singleton Smarty object 
        // for both web and email templates; clearing assigns here would mean 
        // things like CRM_Event_BAO_Event::buildCustomDisplay() would need to 
        // set template variables *and* set array keys for $tplParams
        // $smarty->clear_all_assign();
        foreach ($tplParams as $name => $value) {
            $smarty->assign($name, $value);
        }
        foreach (array('subject', 'text', 'html') as $elem) {
            $dao->$elem = $smarty->fetch("string:{$dao->$elem}");
        }

        return array(trim($dao->subject), $dao->text, $dao->html);
    }
}
