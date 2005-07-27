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
        $this->addElement( 'file', 'textFile', ts('Upload Text Message'), 'size=30 maxlength=60' );
        $this->setMaxFileSize( 1024 * 1024 );
        $this->addRule( 'textFile', ts('File size should be less than 1 MByte'), 'maxfilesize', 1024 * 1024 );
        $this->addRule( 'textFile', ts('File must be in ascii format'), 'asciiFile' );

        $this->addElement( 'file', 'htmlFile', ts('Upload HTML Message'), 'size=30 maxlength=60' );
        $this->setMaxFileSize( 1024 * 1024 );
        $this->addRule( 'htmlFile', ts('File size should be less than 1 MByte'), 'maxfilesize', 1024 * 1024 );
        $this->addRule( 'htmlFile', ts('File must be in ascii format'), 'asciiFile' );
        $this->addFormRule(array('CRM_Mailing_Form_Upload', 'dataRule'));

        $this->addButtons( array(
                                 array ( 'type'      => 'back',
                                         'name'      => ts('Previous <<') ),
                                 array ( 'type'      => 'upload',
                                         'name'      => ts('Next >>'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    public function postProcess() {
        $textFile = $this->controller->exportValue($this->_name, 'textFile');
        $htmlFile = $this->controller->exportValue($this->_name, 'htmlFile');

        $this->set('textFile', $textFile);
        $this->set('htmlFile', $htmlFile);

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
        
        $domain =& CRM_Core_BAO_Domain::getCurrentDomain();
        
        $session =& CRM_Core_Session::singleton();
        $values = array('contact_id' => $session->get('userID'));
        $contact = array();
        $ids = array();
        CRM_Contact_BAO_Contact::retrieve($values,$contact,$id);
        
        $verp = array_flip(array(  'optOut', 'reply', 'unsubscribe', 'owner'));
                        
        foreach (array('textFile', 'htmlFile') as $file) {
            $str = file_get_contents($files[$file]['tmp_name']);
            $name = $files[$file]['name'];
            
            $dataErrors = array();
            
            /* First look for missing tokens */
            $err = CRM_Utils_Token::requiredTokens($str);
            if ($err !== true) {
                foreach ($err as $token => $desc) {
                    $dataErrors[]   = '<li>' 
                                    . ts('Missing required token') 
                                    .' {' . $token . "}: $desc</li>";
                }
            }
            
            /* Do a full token replacement on a dummy verp, the current contact
             * and domain. */
            $str = CRM_Utils_Token::replaceDomainTokens($str, $domain);
            $str = CRM_Utils_Token::replaceActionTokens($str, $verp);
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
                ts('The following errors were detected in %1: <ul>%2</ul>',
                array('1' => $name, '2' => implode('', $dataErrors)));
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
