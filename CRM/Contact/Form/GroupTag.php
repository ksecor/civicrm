<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * This class contains function to build the note form.
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */


Class CRM_Contact_Form_GroupTag
{
    /**
     * This function is to build form elements
     * params object $form object of the form
     *
     * @static
     * @access public
     */
    
    static function buildGroupTagBlock(&$form, $contactId = 0) {
        
        // checkboxes for groups
        //get groups for the contact id
        if ($contactId) {
            $contactGroup =& CRM_Contact_BAO_GroupContact::getContactGroup( $contactId, 'Added' );
        }

        $group = array( );
        $group  =& CRM_Core_PseudoConstant::group( );
        foreach ($group as $groupID => $groupName) {
            $groupChecked = '';
            if (is_array($contactGroup)) {
                foreach ($contactGroup as $key) { 
                    if( $groupID == $key['group_id'] ) {
                        $groupChecked = 'checked';
                    }
                }
            }
            $form->addElement('checkbox', "group[$groupID]", null, $groupName, $groupChecked);
        }
        
        // checkboxes for categories
        // get tags for the contact id
        if ($contactId) {
            $contactTag =& CRM_Core_BAO_EntityTag::getTag('civicrm_contact', $contactId);
        }
        
        $tag = array( );
        $tag =& CRM_Core_PseudoConstant::tag  ( );
        foreach ($tag as $tagID => $tagName) {
            $tagChecked = '';
            if (is_array($contactTag)) {
                if( in_array($tagID, $contactTag)) {
                    $tagChecked = 'checked';
                }
            }
            $form->addElement('checkbox', "tag[$tagID]", null, $tagName, $tagChecked);
        }

    }

}


?>
