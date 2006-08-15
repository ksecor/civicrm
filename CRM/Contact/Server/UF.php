<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 * This class is for UF Help Text widget using JPSpan.
 *
 */
class CRM_Contact_Server_UF
{
    /**
     * This function is to get the custom field id and return the helptext
     * @param string $fragment this is the custom field id string ex 'custom_1' where 1 is the id
     *
     * @return string $helpText 
     * @access public
     */
    function getHelpText($fragment='') 
    {
        if (substr($fragment, 0, 6) == 'custom' ) {
            
            $helpText = '';
            
            //get the custom field id
            $customFieldId = substr($fragment,7,strlen($fragment));
            
            require_once 'CRM/Core/DAO/CustomField.php';
            $field = & new CRM_Core_DAO_CustomField();
            $field->id = $customFieldId;
            $field->find(true);
            
            $helpText = $field->help_post;
            return $helpText;
        }
    }
}
?>
