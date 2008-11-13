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
 * This class contains all the function that are called using AJAX (dojo)
 */
class CRM_Mailing_Page_AJAX
{
    /**
     * Function to fetch the template text/html messages
     */
    function template( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $templateId = CRM_Utils_Type::escape( $_GET['tid'], 'Integer' );

        require_once "CRM/Core/DAO/MessageTemplates.php";
        $messageTemplate =& new CRM_Core_DAO_MessageTemplates( );
        $messageTemplate->id = $templateId;
        $messageTemplate->selectAdd( );
        $messageTemplate->selectAdd( 'msg_text, msg_html, msg_subject' );
        $messageTemplate->find( true );
        
        echo $messageTemplate->msg_text . "^A" . $messageTemplate->msg_html . "^A" . $messageTemplate->msg_subject;
    }

}
