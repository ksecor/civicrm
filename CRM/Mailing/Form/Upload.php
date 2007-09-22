<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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

/**
 * This file is used to build the form configuring mailing details
 */
class CRM_Mailing_Form_Upload extends CRM_Core_Form 
{
    
    /**
     * This function sets the default values for the form.
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $mailingID = $this->get("mid");
        $count = $this->get('count');
        $this->assign('count',$count);
        
        $session =& CRM_Core_Session::singleton();
        $session->set('skipTextFile', false);
        $session->set('skipHtmlFile', false);

        $defaults = array( );
        if ( $mailingID ) {
            require_once "CRM/Mailing/DAO/Mailing.php";
            $dao =&new  CRM_Mailing_DAO_Mailing();
            $dao->id = $mailingID; 
            $dao->find(true);
            $dao->storeValues($dao, $defaults);
            if ( $defaults['msg_template_id'] ) {
                $msg_title = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_MessageTemplates', $defaults['msg_template_id'], 'msg_title' );
                
                $msg_text = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_MessageTemplates', $defaults['msg_template_id'], 'msg_text' );
                $msg_html = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_MessageTemplates', $defaults['msg_template_id'], 'msg_html' );
                
                $msg = str_replace(array("\n","\r"), ' ', $msg_text."^A". $msg_title."^A". $msg_html);
                $this->assign('template_value',  
                              array($msg_title, $msg, $defaults['msg_template_id']));
            }
            if ($defaults['body_text']) {
                $this->set('textFile', $defaults['body_text'] );
                $session->set('skipTextFile', true);
            }

            if (CRM_Utils_Array::value('body_html',$defaults)) {
                $this->set('htmlFile', $defaults['body_html'] );
                $session->set('skipHtmlFile', true);
            }
            
        }
        
        $domain = new CRM_Core_DAO_Domain( );
        $domain->id = CRM_Core_Config::domainID( );
        $domain->selectAdd( );
        $domain->selectAdd( 'id, email_name, email_address' );
        $domain->find( true );
        
        $defaults['from_name' ] = $domain->email_name;
        $defaults['from_email'] = $domain->email_address;
        $defaults['subject'] = $this->get('name');   
        $defaults['upload_type'] = 1; 
        $defaults['template' ]  = $defaults['subject'];
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
        $domainID = CRM_Core_Config::domainID( );
        $this->add('text', 'from_name', ts('FROM Name'));
        $this->add('text', 'from_email', ts('FROM'), NULL, true);

        $this->add('text', 'subject', ts('Mailing Subject'), 'size=30 maxlength=60', true);
        
        $attributes = array( 'onclick' => "showHideUpload();" );    
        $options = array( ts('Upload Content'),  ts('Compose On-screen') );

        $this->addRadio( 'upload_type', ts('I want to'), $options, $attributes, "&nbsp;&nbsp;");

        
        require_once 'CRM/Core/BAO/MessageTemplates.php';
        $this->_templates = CRM_Core_BAO_MessageTemplates::getMessageTemplates();
        if (! empty( $this->_templates ) ){
            $this->assign('templates', true);
            $attributes = array( 'dojoType'       => 'Select',
                                 'style'          => 'width: 300px;',
                                 'autocomplete'   => 'false',
                                 'onValueChanged' => 'selectValue',
                                 'dataUrl'        => CRM_Utils_System::url( 'civicrm/ajax/message',
                                                                            "d=$domainID" ),
                                 );
            
            $this->add('select', 'template', ts('Select Template'), null, false, $attributes );
            $this->add('checkbox','updateTemplate',ts('Update Template'), null);
        } 
        
        $this->add('text', 'saveTemplateName', ts('Template Title') );
        $this->addElement('checkbox','saveTemplate',ts('Save As New Template'), null,
                          array( 'onclick' => "showSaveDetails(this);" ));
        
        //insert message Text by selecting "Select Template option"
        $this->add( 'textarea', 
                    'text_message', 
                    ts('Text Message'),
                    array('cols' => '49', 'rows' => '8','onkeyup' => "return verify(this)"));

        $this->assign( 'dojoIncludes', "dojo.require('dojo.widget.Editor2');" );
        
        $dojoAttributes = array( 'dojoType'            => 'Editor2',
                                 'style'               => 'height:300px',
                                 'widgetId'            => 'html_message',
                                 'useActiveX'          => 'true',
                                 'toolbarTemplatePath' => 'src/widget/templates/EditorToolbarCiviMail.html',
                                 'toolbarCssPath'      => 'src/widget/templates/EditorToolbarCiviMail.css',
                                 'onkeyup'             => 'return verify(this)'
                                 );

        $this->add( 'textarea', 'html_message', ts('HTML Message'), $dojoAttributes );
       
        $this->addElement( 'file', 'textFile', ts('Upload TEXT Message'), 'size=30 maxlength=60' );
        $this->setMaxFileSize( 1024 * 1024 );
        $this->addRule( 'textFile', ts('File size should be less than 1 MByte'), 'maxfilesize', 1024 * 1024 );
        $this->addRule( 'textFile', ts('File must be in UTF-8 encoding'), 'utf8File' );
        
        $this->addElement( 'file', 'htmlFile', ts('Upload HTML Message'), 'size=30 maxlength=60' );
        $this->setMaxFileSize( 1024 * 1024 );
        $this->addRule( 'htmlFile', ts('File size should be less than 1 MByte'), 'maxfilesize', 1024 * 1024 );
        $this->addRule( 'htmlFile', ts('File must be in UTF-8 encoding'), 'utf8File' );
        
        require_once 'CRM/Mailing/PseudoConstant.php';
        $this->add( 'select', 'header_id', ts( 'Mailing Header' ), 
                    array('' => ts('- none -')) + CRM_Mailing_PseudoConstant::component( 'Header' ) );
        
        $this->add( 'select', 'footer_id', ts( 'Mailing Footer' ), 
                    array('' => ts('- none -')) + CRM_Mailing_PseudoConstant::component( 'Footer' ) );
        
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
        
        
    }
    
    public function postProcess() 
    {
        $params      = $ids  = array( );
        $uploadParams        = array( 
                                     'header_id', 'footer_id', 'subject', 'from_name', 'from_email'
                                     );
        $fileType            = array( 'textFile', 'htmlFile' );
        
        foreach ( $uploadParams as $key ) {
            $params[$key] = $this->controller->exportvalue($this->_name, $key);
            $this->set($key, $this->controller->exportvalue($this->_name, $key));
        }
        
        if ( !$this->controller->exportvalue($this->_name, 'upload_type')) {
            foreach ( $fileType as $key ) {
                if( file_get_contents($this->controller->exportvalue($this->_name, $key) ) ) { 
                    $params['body_'. substr($key,0,4 )] = file_get_contents($this->controller->exportvalue($this->_name, $key) );
                } else {
                    $params['body_'. substr($key,0,4 )] = 'NULL';
                }
                $this->set($key, $this->controller->exportvalue($this->_name, $key));
            }
            if ( $this->controller->exportvalue($this->_name, 'textFile') ) {
                $this->set('textFile', $this->controller->exportvalue($this->_name, 'textFile') );
            }
            
            if ($this->controller->exportvalue($this->_name, 'htmlFile')) {
                $this->set('htmlFile', $this->controller->exportvalue($this->_name, 'htmlFile'));
            }
        } else {
            
            $params['body_text'] = $this->controller->exportvalue($this->_name, 'text_message');
            $this->set('textFile', $params['body_text'] );
            $params['body_html'] = $this->controller->exportvalue($this->_name, 'html_message');
            $this->set('htmlFile', $params['body_html'] );
        }

        $params['name'] = $this->get('name');
        
        $session =& CRM_Core_Session::singleton();
        $params['domain_id']  = $session->get('domainID');
        $params['contact_id'] = $session->get('userID');
        $composeFields       = array ( 
                                      'template', 'text_message', 'html_message', 
                                      'saveTemplate', 'updateTemplate', 'saveTemplateName'
                                      );
        //mail template is composed 
        if ($this->controller->exportvalue($this->_name, 'upload_type') ){
            foreach ( $composeFields as $key ) {
                $composeParams[$key] = $this->controller->exportvalue($this->_name, $key);
                $this->set($key, $this->controller->exportvalue($this->_name, $key));
            }
            
            $templateParams = array( 'msg_text'    => $composeParams['text_message'],
                                     'msg_html'    => $composeParams['html_message'],
                                     'msg_subject' => $params['subject'],
                                     'is_active'   => true
                                     );
            
            if ( $composeParams['saveTemplate'] ) {
                // create a new template
                $templateIds = array();
                $templateParams['msg_title'] = $composeParams['saveTemplateName'];
                $msgTemplate = CRM_Core_BAO_MessageTemplates::add($templateParams, $templateIds);  
            } elseif ( $composeParams['updateTemplate'] ) { 
                //update the existing template
                $templateIds = array('messageTemplate' => CRM_Utils_Array::key( $_POST['template_selected'],
                                                                                $this->_templates ) 
                                     );
                $templateParams['msg_title'] = $composeParams['template_selected'];
                $msgTemplate = CRM_Core_BAO_MessageTemplates::add($templateParams, $templateIds);  
            }
            
            if ( $msgTemplate->id ) {
                $params['msg_template_id'] = $msgTemplate->id;
            }
        }

        $ids['mailing_id'] = $this->get('mailing_id');

        /* Build the mailing object */
        require_once 'CRM/Mailing/BAO/Mailing.php';
        CRM_Mailing_BAO_Mailing::create($params, $ids);
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
    static function dataRule(&$params, &$files, &$options) 
    {
        if (CRM_Utils_Array::value('_qf_Import_refresh', $_POST)) {
            return true;
        }
        $errors = array();

        require_once 'CRM/Core/BAO/Domain.php';

        $domain =& CRM_Core_BAO_Domain::getCurrentDomain();
        $mailing = null;

        $session =& CRM_Core_Session::singleton();
        $values = array('contact_id' => $session->get('userID'));
        require_once 'api/Contact.php';
        //$contact =& crm_fetch_contact( $values );
        $contact = array (
                          'contact_id'            => 102,
                          'contact_type'          => 'Individual',
                          'sort_name'             => 'pankaj.sharma@webaccess.co.in',
                          'display_name'          => 'pankaj.sharma@webaccess.co.in',
                          'location_id'           => 90,
                          'email_id'              => 152, 
                          'email'                 => 'pankaj.sharma@webaccess.co.in',
                          'on_hold'               => 0,
                          'preferred_mail_format' => 'Both'
                          );
        
        $verp = array_flip(array(  'optOut', 'reply', 'unsubscribe', 'owner'));
        foreach($verp as $key => $value) {
            $verp[$key]++;
        }

        $urls = array_flip(array( 'forward', 'optOutUrl', 'unsubscribeUrl') );
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

        $skipTextFile = $session->get('skipTextFile');
        $skipHtmlFile = $session->get('skipHtmlFile');

        if ( ( ! isset( $files['textFile'] ) || ! file_exists( $files['textFile']['tmp_name'] ) ) &&
             ( ! isset( $files['htmlFile'] ) || ! file_exists( $files['htmlFile']['tmp_name'] ) ) ) {
            if ( ! ( $skipTextFile || $skipHtmlFile ) ) {
                $errors['textFile'] = ts('Please provide either a Text or HTML formatted message - or both.');
            }
        } else {
            if ( !($params['text_message']) && !($params['html_message']) ) {
                $errors['text_message'] = ts('Please provide either a Text or HTML formatted message - or both.');
            }
            if ( $params['saveTemplate'] &&  ! $params['saveTemplateName'] ) {
                $errors['saveTemplateName'] =  ts('Please provide a Template Name.');
            }
        }

        foreach (array('textFile', 'htmlFile') as $file) {
            if (!file_exists(CRM_Utils_Array::value('tmp_name',$files[$file]))) {
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
            $dummy_mail->body_text = $str;
            $tokens = $dummy_mail->getTokens();

            $str = CRM_Utils_Token::replaceDomainTokens($str, $domain, null, $tokens['text']);
            $str = CRM_Utils_Token::replaceMailingTokens($str, $mailing, null, $tokens['text']);
            $str = CRM_Utils_Token::replaceOrgTokens($str, $org);
            $str = CRM_Utils_Token::replaceActionTokens($str, $verp, $urls, null, $tokens['text']);
            $str = CRM_Utils_Token::replaceContactTokens($str, $contact, null, $tokens['text']);

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
    public function getTitle( ) 
    {
        return ts( 'Mailing Content' );
    }
}

?>
