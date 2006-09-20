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

/**
 *
 */
class CRM_Mailing_Form_Upload extends CRM_Core_Form {

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        $session =& CRM_Core_Session::singleton();
        
        $this->add('text', 'from_name', ts('From Name'));
        $this->add('text', 'from_email', ts('From Email'));
        $defaults['from_email'] = $session->get('ufEmail');
        
        $this->add('checkbox', 'forward_reply', ts('Forward Replies?'));
        $defaults['forward_reply'] = true;
        
        $this->add('checkbox', 'track_urls', ts('Track URLs?'));
        $defaults['track_urls'] = true;
        
        $this->add('checkbox', 'track_opens', ts('Track Opens?'));
        $defaults['track_opens'] = true;
        
        $this->add('checkbox', 'auto_responder', ts('Auto-respond to Replies?'));
        $defaults['auto_responder'] = false;
        
        $this->addElement('text', 'subject', ts('Mailing Subject'), 'size=30 maxlength=60');
        $defaults['subject'] = $this->get('mailing_name');
        
        $this->addElement( 'file', 'textFile', ts('Upload Text Message'), 'size=30 maxlength=60' );
        $this->setMaxFileSize( 1024 * 1024 );
        $this->addRule( 'textFile', ts('File size should be less than 1 MByte'), 'maxfilesize', 1024 * 1024 );
        $this->addRule( 'textFile', ts('File must be in UTF-8 encoding'), 'utf8File' );

        $this->addElement( 'file', 'htmlFile', ts('Upload HTML Message'), 'size=30 maxlength=60' );
        $this->setMaxFileSize( 1024 * 1024 );
        $this->addRule( 'htmlFile', ts('File size should be less than 1 MByte'), 'maxfilesize', 1024 * 1024 );
        $this->addRule( 'htmlFile', ts('File must be in UTF-8 encoding'), 'utf8File' );
        
        $this->add( 'select', 'header_id', ts( 'Mailing Header' ), array('' => ts('- none -')) + CRM_Mailing_PseudoConstant::component( 'Header' ) );
        $this->add( 'select', 'footer_id', ts( 'Mailing Footer' ), array('' => ts('- none -')) + CRM_Mailing_PseudoConstant::component( 'Footer' ) );
        $this->add( 'select', 'reply_id', ts( 'Auto-responder' ), CRM_Mailing_PseudoConstant::component( 'Reply' ), true );
        $this->add( 'select', 'unsubscribe_id', ts( 'Unsubscribe Message' ), CRM_Mailing_PseudoConstant::component( 'Unsubscribe' ), true );
        $this->add( 'select', 'optout_id', ts( 'Opt-out Message' ), CRM_Mailing_PseudoConstant::component( 'OptOut' ), true );
        
        $this->addFormRule(array('CRM_Mailing_Form_Upload', 'dataRule'));

        $this->addButtons( array(
                                 array ( 'type'      => 'back',
                                         'name'      => ts('<< Previous') ),
                                 array ( 'type'      => 'upload',
                                         'name'      => ts('Next >>'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );

        $this->setDefaults($defaults);
    }

    public function postProcess() {
        foreach (array( 'from_name', 'from_email','subject', 
                        'forward_reply', 'track_urls', 'track_opens',
                        'header_id', 'footer_id', 'reply_id', 'unsubscribe_id',
                        'optout_id', 'auto_responder', 'textFile', 'htmlFile') 
                    as $key) 
        {
            $this->set($key, $this->controller->exportvalue($this->_name, $key));
        }
    }

    /**
     * Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    static function dataRule(&$params, &$files, &$options) {
        if (CRM_Utils_Array::value('_qf_Import_refresh', $_POST)) {
            return true;
        }
        $errors = array();

        require_once 'CRM/Core/BAO/Domain.php';
        
        $domain =& CRM_Core_BAO_Domain::getCurrentDomain();
        $mailing = null;

        $session =& CRM_Core_Session::singleton();
        $values = array('contact_id' => $session->get('userID'));
        $contact = array();
        $ids = array();
        CRM_Contact_BAO_Contact::retrieve($values,$contact,$id);
        
        $verp = array_flip(array(  'optOut', 'reply', 'unsubscribe', 'owner'));
        foreach($verp as $key => $value) {
            $verp[$key]++;
        }
        
        $urls = array_flip(array( 'forward' ) );
        foreach($urls as $key => $value) {
            $urls[$key]++;
        }
        
        require_once 'CRM/Mailing/BAO/Component.php';
        
        // set $header and $footer
        foreach (array('header', 'footer') as $part) {
            if ($params["{$part}_id"]) {
                $component =& new CRM_Mailing_BAO_Component();
                $component->id = $params["{$part}_id"];
                $component->find(true);
                $$part['textFile'] = $component->body_text;
                $$part['htmlFile'] = $component->body_html;
                $component->free();
            } else {
                $$part['htmlFile'] = $$part['textFile'] = '';
            }
        }

        require_once 'CRM/Utils/Token.php';

        if (!file_exists($files['textFile']['tmp_name']) and !file_exists($files['htmlFile']['tmp_name'])) {
            $errors['textFile'] = ts('Please provide either the text or HTML message.');
        }

        foreach (array('textFile', 'htmlFile') as $file) {
            if (!file_exists($files[$file]['tmp_name'])) {
                continue;
            }
            $str = file_get_contents($files[$file]['tmp_name']);
            $name = $files[$file]['name'];
            
            /* append header/footer */
            $str = $header[$file] . $str . $footer[$file];

            $dataErrors = array();
            
            /* First look for missing tokens */
            $err = CRM_Utils_Token::requiredTokens($str);
            if ($err !== true) {
                foreach ($err as $token => $desc) {
                    $dataErrors[]   = '<li>' 
                                    . ts('This message is missing a required token - {%1}: %2',
                                         array(1 => $token, 2 => $desc))
                                    . '</li>';
                }
            }
            
            /* Do a full token replacement on a dummy verp, the current contact
             * and domain. */
            $str = CRM_Utils_Token::replaceDomainTokens($str, $domain);
            $str = CRM_Utils_Token::replaceMailingTokens($str, $mailing);
            $str = CRM_Utils_Token::replaceActionTokens($str, $verp, $urls);
            $str = CRM_Utils_Token::replaceContactTokens($str, $contact);

            $unmatched = CRM_Utils_Token::unmatchedTokens($str);
            if (! empty($unmatched)) {
                foreach ($unmatched as $token) {
                    $dataErrors[]   = '<li>'
                                    . ts('Invalid token code')
                                    .' {'.$token.'}</li>';
                }
            }
            if (! empty($dataErrors)) {
                $errors[$file] = 
                ts('The following errors were detected in %1:', array(1 => $name)) . ' <ul>' . implode('', $dataErrors) . '</ul><br /><a href="http://wiki.civicrm.org/confluence//x/nC" target="_blank">' . ts('More information on required tokens...') . '</a>';
            }
        }
        return empty($errors) ? true : $errors;
    }

    /**
     * Display Name of the form
     *
     * @access public
     * @return string
     */
    public function getTitle( ) {
        return ts( 'Upload Message' );
    }
}

?>
