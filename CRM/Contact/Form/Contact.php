<?php
/**
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Form.php';
require_once 'CRM/SelectValues.php';


/**
 * The class which generates form components generic to all the contact types
 */
class CRM_Contact_Form_Contact extends CRM_Form
{
    public static function buildCommunicationBlock(&$form)
    {
        $privacy = array( );

        // checkboxes for DO NOT phone, email, mail
        $privacy[] = HTML_QuickForm::createElement('checkbox', 'do_not_phone', null, 'Do not call');
        $privacy[] = HTML_QuickForm::createElement('checkbox', 'do_not_email', null, 'Do not contact by email');
        $privacy[] = HTML_QuickForm::createElement('checkbox', 'do_not_mail' , null, 'Do not contact by postal mail');

        $form->addGroup( $privacy, 'privacy', 'Privacy' );

        // preferred communication method 
        $form->add('select', 'preferred_communication_method', 'Prefers:', CRM_SelectValues::$pcm);
    }

    function createHideShowLinks( $form, $index, $maxIndex, $prefix, $showLinkText, $hideLinkText ) {
        if ( $index == $maxIndex ) {
            $showCode = $hideCode = "return false;";
        } else {
            $next = $index + 1;
            $showCode = "show('$prefix[$next]'); return false;";
            $hideCode = "hide('$prefix[$next][show]'); return false;";
        }

        $form->addElement('link', "${prefix}[${index}][show]", null, "#${prefix}[${index}]", $showLinkText,
                          array( 'onclick' => "hide('${prefix}[${index}][show]'); show('${prefix}[${index}]');" . $showCode)); 
        $form->addElement('link', "${prefix}[${index}][hide]", null, "#${prefix}[${index}]", $hideLinkText,
                          array('onclick' => "hide('${prefix}[${index}]'); show('${prefix}[${index}][show]');" . $hideCode));
    }

}

?>
