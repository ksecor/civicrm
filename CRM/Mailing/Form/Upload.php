<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * This file is used to build the form configuring mailing details
 */
class CRM_Mailing_Form_Upload extends CRM_Core_Form 
{
    public $_mailingID;

    function preProcess( ) {
        $this->_mailingID = $this->get( 'mailing_id' );
    }

    /**
     * This function sets the default values for the form.
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $mailingID = CRM_Utils_Request::retrieve('mid', 'Integer', $this, false, null ) ?
            CRM_Utils_Request::retrieve('mid', 'Integer', $this, false, null ) : $this->_mailingID;
	
        $count = $this->get('count');
        $this->assign('count',$count);
        
        $this->set('skipTextFile', false);
        $this->set('skipHtmlFile', false);
       
        $defaults = array( );

        require_once 'CRM/Core/BAO/Domain.php';
        list( $defaults['from_name' ],
              $defaults['from_email'] ) = CRM_Core_BAO_Domain::getNameAndEmail( );
        
        $htmlMessage = null;
        if ( $mailingID  ) {
            require_once "CRM/Mailing/DAO/Mailing.php";
            $dao =& new  CRM_Mailing_DAO_Mailing();
            $dao->id = $mailingID; 
            $dao->find(true);
            $dao->storeValues($dao, $defaults);

            //we don't want to retrieve template details once it is
            //set in session
            $templateId = $this->get('template');
            
            if ( isset($defaults['msg_template_id']) && !$templateId ) {
                $defaults['template'] = $defaults['msg_template_id'];
                $messageTemplate =& new CRM_Core_DAO_MessageTemplates( );
                $messageTemplate->id = $defaults['msg_template_id'];
                $messageTemplate->selectAdd( );
                $messageTemplate->selectAdd( 'msg_text, msg_html' );
                $messageTemplate->find( true );

                $defaults['text_message'] = $messageTemplate->msg_text;
                $htmlMessage = $messageTemplate->msg_html;
            }

            if ( isset( $defaults['body_text'] ) ) {
                $defaults['text_message'] = $defaults['body_text'];
                $this->set('textFile', $defaults['body_text'] );
                $this->set('skipTextFile', true);
            }

            if ( isset( $defaults['body_html'] ) ) {
                $htmlMessage = $defaults['body_html'];
                $this->set('htmlFile', $defaults['body_html'] );
                $this->set('skipHtmlFile', true);
            }
        } else {
            $textFilePath = $this->get( 'textFilePath' );
            if ( $textFilePath &&
                 file_exists( $textFilePath ) ) {
                $defaults['text_message'] = file_get_contents( $textFilePath );
                if ( strlen( $defaults['text_message'] ) > 0 ) {
                    $this->set('skipTextFile', true);
                }
            }
                 
            $htmlFilePath = $this->get( 'htmlFilePath' );
            if ( $htmlFilePath &&
                 file_exists( $htmlFilePath ) ) {
                $defaults['html_message'] = file_get_contents( $htmlFilePath );
                if ( strlen( $defaults['html_message'] ) > 0 ) {
                    $htmlMessage = $defaults['html_message'];
                    $this->set('skipHtmlFile', true);
                }
            }
        }
        
        $defaults['subject'] = $this->get('name');
        $htmlMessage = str_replace( array("\n","\r"), ' ', $htmlMessage);
        $htmlMessage = str_replace( "'", "\'", $htmlMessage);
        $this->assign('message_html', $htmlMessage );        

        $defaults['upload_type'] = 1; 
	if ( isset($defaults['body_html']) ) {
	  $defaults['html_message'] = $defaults['body_html'];
	}
	return $defaults;
    }

 
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $session =& CRM_Core_Session::singleton();
        
        require_once 'CRM/Core/PseudoConstant.php';
        $formEmailAddress = CRM_Core_PseudoConstant::fromEmailAddress( "from_email_address" );
        if ( empty( $formEmailAddress ) ) {
            //redirect user to enter from email address. 
            $url = CRM_Utils_System::url( 'civicrm/admin/options/from_email_address', 'group=from_email_address&action=add&reset=1' );
            $status = ts( "There is no valid from email address present. You can add here <a href='%1'>Add From Email Address.</a>", array( 1 => $url ) );
            $session->setStatus( $status );
        } else {
            foreach ( $formEmailAddress as $key => $email ) {
                $formEmailAddress[$key] = htmlspecialchars( $formEmailAddress[$key] );
            }
        }
        
        $this->add( 'select', 'from_email_address', 
                    ts( 'From Email Address' ), $formEmailAddress, true );
        
        $this->add('text', 'subject', ts('Mailing Subject'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Mailing_DAO_Mailing', 'subject' ), true);
        
        $attributes = array( 'onclick' => "showHideUpload();" );    
        $options = array( ts('Upload Content'),  ts('Compose On-screen') );
        
        $this->addRadio( 'upload_type', ts('I want to'), $options, $attributes, "&nbsp;&nbsp;");
        
        require_once 'CRM/Mailing/BAO/Mailing.php';
        CRM_Mailing_BAO_Mailing::commonCompose( $this );
              
        $this->addElement( 'file', 'textFile', ts('Upload TEXT Message'), 'size=30 maxlength=60' );
        $this->setMaxFileSize( 1024 * 1024 );
        $this->addRule( 'textFile', ts('File size should be less than 1 MByte'), 'maxfilesize', 1024 * 1024 );
        $this->addRule( 'textFile', ts('File must be in UTF-8 encoding'), 'utf8File' );
        $this->addElement('checkbox', 'override_verp', ts('Override VERP address?'));
        
        $this->addElement( 'file', 'htmlFile', ts('Upload HTML Message'), 'size=30 maxlength=60' );
        $this->setMaxFileSize( 1024 * 1024 );
        $this->addRule( 'htmlFile', 
                        ts( 'File size should be less than %1 MByte(s)',
                            array( 1 => 1 ) ),
                        'maxfilesize',
                        1024 * 1024 );
        $this->addRule( 'htmlFile', ts('File must be in UTF-8 encoding'), 'utf8File' );

        require_once 'CRM/Core/BAO/File.php';
        CRM_Core_BAO_File::buildAttachment( $this,
                                            'civicrm_mailing',
                                            $this->_mailingID );

        require_once 'CRM/Mailing/PseudoConstant.php';
        $this->add( 'select', 'header_id', ts( 'Mailing Header' ), 
                    array('' => ts('- none -')) + CRM_Mailing_PseudoConstant::component( 'Header' ) );
        
        $this->add( 'select', 'footer_id', ts( 'Mailing Footer' ), 
                    array('' => ts('- none -')) + CRM_Mailing_PseudoConstant::component( 'Footer' ) );
        
        $this->addFormRule(array('CRM_Mailing_Form_Upload', 'formRule'), $this );
        
        $this->addButtons( array(
                                 array ( 'type'      => 'back',
                                         'name'      => ts('<< Previous') ),
                                 array ( 'type'      => 'upload',
                                         'name'      => ts('Next >>'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 array ( 'type'      => 'upload',
                                         'name'      => ts('Save & Continue Later') )
                                 )
                           );
    }
    
    public function postProcess() 
    {
        $params = $ids = array( );
        $uploadParams  = array( 'header_id', 'footer_id', 'subject', 'from_name', 'from_email', 'override_verp' );
        $fileType      = array( 'textFile', 'htmlFile' );

        $formValues    = $this->controller->exportValues( $this->_name );

        foreach ( $uploadParams as $key ) {
            $params[$key] = $formValues[$key];
            $this->set($key, $formValues[$key]);
        }
        
        if ( ! $formValues['upload_type']) {
            foreach ( $fileType as $key ) {
                $contents = null;
                if ( isset( $formValues[$key] ) &&
                     ! empty( $formValues[$key] ) ) {
                    $contents = file_get_contents( $formValues[$key]['name'] );
                    $this->set($key, $formValues[$key]['name'] );
                }
                if ( $contents ) {
                    $params['body_'. substr($key,0,4 )] = $contents;
                } else {
                    $params['body_'. substr($key,0,4 )] = 'NULL';
                }
            }
        } else {
            $text_message = $formValues['text_message'];
            $params['body_text']     = $text_message;
            $this->set('textFile',     $params['body_text'] );
            $this->set('text_message', $params['body_text'] );
            $html_message = $formValues['html_message'];
            
            // dojo editor does some html conversion when tokens are
            // inserted as links. Hence token replacement fails.
            // this is hack to revert html conversion for { to %7B and
            // } to %7D by dojo editor
            $html_message = str_replace( '%7B', '{', str_replace( '%7D', '}', $html_message) );
            
            $params['body_html']     = $html_message;
            $this->set('htmlFile',     $params['body_html'] );
            $this->set('html_message', $params['body_html'] );
        }

        $params['name'] = $this->get('name');

        $session =& CRM_Core_Session::singleton();
        $params['contact_id'] = $session->get('userID');
        $composeFields        = array ( 'template', 'saveTemplate',
                                        'updateTemplate', 'saveTemplateName' );
        
        //mail template is composed 
        if ( $formValues['upload_type'] ) {
            foreach ( $composeFields as $key ) {
                $composeParams[$key] = $formValues[$key];
                $this->set($key, $formValues[$key]);
            }          
           
            if ( $composeParams['saveTemplate'] || $composeParams['updateTemplate'] ) {
                $templateParams = array( 'msg_text'    => $text_message,
                                         'msg_html'    => $html_message,
                                         'msg_subject' => $params['subject'],
                                         'is_active'   => true
                                         );
                
                if ( $composeParams['saveTemplate'] ) {
                    $templateParams['msg_title'] = $composeParams['saveTemplateName'];
                }
                
                if ( $composeParams['updateTemplate'] ) {
                    $templateParams['id'] = $formValues['template'];
                }

                $msgTemplate = CRM_Core_BAO_MessageTemplates::add( $templateParams );  
            } 
            
            if ( $msgTemplate->id ) {
                $params['msg_template_id'] = $msgTemplate->id;
            } else {
                $params['msg_template_id'] = $formValues['template'];
            }
        }

        CRM_Core_BAO_File::formatAttachment( $formValues,
                                             $params,
                                             'civicrm_mailing',
                                             $this->_mailingID );
        $ids['mailing_id'] = $this->_mailingID;
        
        //handle mailing from name & address.
        $formEmailAddress = CRM_Utils_Array::value( $formValues['from_email_address'],
                                                    CRM_Core_PseudoConstant::fromEmailAddress( "from_email_address" ) );
        
        //get the from email address
        require_once 'CRM/Utils/Mail.php';
        $params['from_email'] = CRM_Utils_Mail::pluckEmailFromHeader( $formEmailAddress );
        
        //get the from Name
        $params['from_name'] = CRM_Utils_Array::value( 1, explode('"', $formEmailAddress ) );
        
        /* Build the mailing object */
        require_once 'CRM/Mailing/BAO/Mailing.php';
        CRM_Mailing_BAO_Mailing::create($params, $ids);
     
        if ($this->_submitValues['_qf_Upload_upload'] == 'Save & Continue Later') {
            CRM_Core_Session::setStatus( ts("Your mailing has been saved. Click the 'Continue' action to resume working on it.") );
            $url = CRM_Utils_System::url( 'civicrm/mailing/browse/unscheduled', 'scheduled=false&reset=1' );
            CRM_Utils_System::redirect($url);
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
    static function formRule( &$params, &$files, &$self )
    {
        if (CRM_Utils_Array::value('_qf_Import_refresh', $_POST)) {
            return true;
        }
        $errors = array();
        $template =& CRM_Core_Smarty::singleton( );
       

        if (isset($params['html_message'])){
             $htmlMessage = str_replace( array("\n","\r"), ' ', $params['html_message']);
             $htmlMessage = str_replace( "'", "\'", $htmlMessage);
             $template->assign('htmlContent',$htmlMessage );
        }
        require_once 'CRM/Core/BAO/Domain.php';

        $domain =& CRM_Core_BAO_Domain::getDomain();

        require_once 'CRM/Mailing/BAO/Mailing.php';
        $mailing = & new CRM_Mailing_BAO_Mailing();
        $mailing->id = $self->_mailingID;
        $mailing->find(true);

        $session =& CRM_Core_Session::singleton();
        $values = array('contact_id' => $session->get('userID'));
        require_once 'api/v2/Contact.php';
        $contact =& civicrm_contact_get( $values );

        $verp = array_flip(array(  'optOut', 'reply', 'unsubscribe', 'resubscribe', 'owner'));
        foreach($verp as $key => $value) {
            $verp[$key]++;
        }

        $urls = array_flip(array( 'forward', 'optOutUrl', 'unsubscribeUrl', 'resubscribeUrl') );
        foreach($urls as $key => $value) {
            $urls[$key]++;
        }

        require_once 'CRM/Mailing/BAO/Component.php';
        
        // set $header and $footer
        foreach (array('header', 'footer') as $part) {
            $$part = array( );
            if ($params["{$part}_id"]) {
	        //echo "found<p>";
                $component =& new CRM_Mailing_BAO_Component();
                $component->id = $params["{$part}_id"];
                $component->find(true);
                ${$part}['textFile'] = $component->body_text;
                ${$part}['htmlFile'] = $component->body_html;
                $component->free();
            } else {
                ${$part}['htmlFile'] = ${$part}['textFile'] = '';
            }
        }

        require_once 'CRM/Utils/Token.php';

        $skipTextFile = $self->get('skipTextFile');
        $skipHtmlFile = $self->get('skipHtmlFile');
        
        if( !$params['upload_type'] ) { 
            if ( ( ! isset( $files['textFile'] ) || ! file_exists( $files['textFile']['tmp_name'] ) ) &&
                 ( ! isset( $files['htmlFile'] ) || ! file_exists( $files['htmlFile']['tmp_name'] ) ) ) {
                if ( ! ( $skipTextFile || $skipHtmlFile ) ) {
                    $errors['textFile'] = ts('Please provide either a Text or HTML formatted message - or both.');
                }
            }
        } else {
            if ( ! CRM_Utils_Array::value( 'text_message', $params ) && ! CRM_Utils_Array::value( 'html_message', $params ) ) {
                $errors['text_message'] = ts('Please provide either a Text or HTML formatted message - or both.');
            }
            if ( CRM_Utils_Array::value( 'saveTemplate', $params ) &&  ! CRM_Utils_Array::value( 'saveTemplateName', $params ) ) {
                $errors['saveTemplateName'] =  ts('Please provide a Template Name.');
            }
        }

        foreach (array('text', 'html') as $file) {
            if (!$params['upload_type'] && !file_exists(CRM_Utils_Array::value('tmp_name',$files[$file . 'File']))) {
                continue;
            }
            if ($params['upload_type'] && !$params[$file . '_message']) {
                continue;
            }
            
            if ( !$params['upload_type'] ) {
                $str  = file_get_contents($files[$file . 'File']['tmp_name']);
                $name = $files[$file . 'File']['name'];
            } else {
                $str  = $params[$file . '_message'];
                $str  = ($file == 'html') ? str_replace( '%7B', '{', str_replace( '%7D', '}', $str) ) : $str;
                $name = $file . ' message';
            }

            /* append header/footer */
            $str = $header[$file . 'File'] . $str . $footer[$file . 'File'];
            
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

            /* Do a full token replacement on a dummy verp, the current
             * contact and domain, and the first organization. */
            
            // here we make a dummy mailing object so that we
            // can retrieve the tokens that we need to replace
            // so that we do get an invalid token error
            // this is qute hacky and I hope that there might
            // be a suggestion from someone on how to
            // make it a bit more elegant
            
            require_once 'CRM/Mailing/BAO/Mailing.php';
            $dummy_mail = new CRM_Mailing_BAO_Mailing();
            $mess = "body_{$file}";
            $dummy_mail->$mess = $str;
            $tokens = $dummy_mail->getTokens();

            $str = CRM_Utils_Token::replaceSubscribeInviteTokens($str);
            $str = CRM_Utils_Token::replaceDomainTokens($str, $domain, null, $tokens[$file]);
            $str = CRM_Utils_Token::replaceMailingTokens($str, $mailing, null, $tokens[$file]);
            $str = CRM_Utils_Token::replaceOrgTokens($str, $org);
            $str = CRM_Utils_Token::replaceActionTokens($str, $verp, $urls, null, $tokens[$file]);
            $str = CRM_Utils_Token::replaceContactTokens($str, $contact, null, $tokens[$file]);
            
            $unmatched = CRM_Utils_Token::unmatchedTokens($str);

            if (! empty($unmatched)) {
                foreach ($unmatched as $token) {
                    $dataErrors[]   = '<li>'
                        . ts('Invalid token code')
                        .' {'.$token.'}</li>';
                }
            }
            if (! empty($dataErrors)) {
                $errors[$file . 'File'] = 
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
    public function getTitle( ) 
    {
        return ts( 'Mailing Content' );
    }
}


