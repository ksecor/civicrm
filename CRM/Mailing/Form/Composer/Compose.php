<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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
        
    }//end of function

    public function getTitle() 
    {
        return ts('Compose');
    }

}

?>
