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

/*
	CiviCRM CiviMail Composer
	=========================
	This application was designed to be implemented with CiviCRM's CiviMail.
	It was designed with the intention of simplifying the e-mail process and
	eliminating the need for users to upload e-mails.
	
	The user is able to open an external editor in which they are able to
	create/delete e-mails before selecting one to send. Uploaded e-mails are
	also copied to the composer directory for future editing or resending.
	
*/

/*
 * Copyright (C) 2007 legal.consult pty ltd ABN 84 002 413 078
 * Licensed to CiviCRM under the Academic Free License version 3.0.
 *
 */


require_once 'CRM/Core/Form.php';

/**
 * This class is to integrate dojo WYSIWYG editor
 * 
 */
class CRM_Mailing_Form_Composer_Compose extends CRM_Core_Form
{
    function preProcess( ) 
    {
 
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    { 
        $this->add('text', 'file', ts('Name') );
        $this->assign( 'dojoIncludes', "dojo.require('dojo.widget.Editor2');" );

        $dojoAttributes = " dojoType='Editor2' htmlEditing=true useActiveX=true	shareToolbar=false
                           id='editor4' toolbarAlwaysVisible=true 
                           toolbarTemplatePath='src/widget/templates/EditorToolbarCiviMail.html' 
                           toolbarTemplateCssPath='src/widget/templates/EditorToolbarCiviMail.css' ";
     
        $text =& $this->add('textarea', 'edit_text', ts('Text Version'), 'rows=3, cols=70');

        $html =& $this->add('textarea', 'edit_html', ts('Html Version'), $dojoAttributes);

        $this->addButtons(array( 
                                array ( 'type'      => 'submit',
                                        'name'      => ts('Save'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'js'         => array( 'onclick' => 'window.close();' ),
                                        'isDefault' => true ),
                                ) );

    }

    /**
     * This function sets the default values for the form.
     *
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        
    }
       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // get the submitted form values.
        $formValues = $this->controller->exportValues( $this->_name );
        
        $config =& CRM_Core_Config::singleton( );
        $dir    = $config->uploadDir; 
        
        //	Replaces
        foreach (array("text","html") as $version) {
            $formValues['edit_'.$version] = str_replace("\\\"","&quot;",$formValues['edit_'.$version]);
            $formValues['edit_'.$version] = str_replace("\\'","&#39;",$formValues['edit_'.$version]);
        }
        
        // Set time
        if(!$formValues['time']) {
            $time = time();
        } else {
            $time = $formValues['time'];
        }

        
        /*	Write
         *		The user has chosen to save a file. If the file currently
         *		exists, the existing file will be overwritten.
         */

        $file_load = $formValues['file'];
        
        if ($formValues['file']) {
            $file_name = $formValues['file']."_";
        } else {
            $file_name = "compose_".$time."_";
        }
        $file_dir = $dir;
        
        foreach(array("text","html") as $file_type) {
            $file = $file_dir.$file_name.$file_type;
            $content = $formValues['edit_'.$file_type];
            
            //  Clear the file if it exists. If so, delete.
            if (file_exists($file)) {
                unlink ($file);
            }
            
            if (!file_exists($file)) {
                touch($file); // Create blank file
                chmod($file,0666);
            }
            
            //** Write to the file ****************************
            if (is_writable($file)) {
                
                if (!$handle = fopen($file, 'a')) {
                    CRM_Core_Session::setStatus("Cannot open file $file<br>");
                    exit;
                }
                if (fwrite($handle, $content) === FALSE) {
                    CRM_Core_Session::setStatus("Cannot write to file $file<br>");
                    exit;
                }
                CRM_Core_Session::setStatus("Success, wrote to file $file<br>");
                fclose($handle);
                
            } else {
                CRM_Core_Session::setStatus("The file $file is not writable.<br>");
            }
            $contents["_".$file_type] = $content;
        }
       
      }//end of function
    
    public function getTitle() 
    {
        return ts('Compose');
    }

}

?>
