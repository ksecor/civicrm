<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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
 * This class contains function to build the note form.
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */


Class CRM_Contact_Form_GroupTag
{
    /**
     * constant to determine which forms we are generating
     *
     * Used by both profile and edit contact
     */
    const
        GROUP = 1,
        TAG   = 2,
        ALL   = 3;

    /**
     * This function is to build form elements
     * params object $form object of the form
     *
     * @param Object $form      the form object that we are operating on
     * @param int    $contactId contact id
     * @param int   $type     what components are we interested in 
     * 
     * @static
     * @access public
     */
    static function buildGroupTagBlock(&$form, $contactId = 0, $type = CRM_Contact_Form_GroupTag::ALL, $visibility = false, $isRequired = null) {
        $type = (int ) $type;
        if ( $type & CRM_Contact_Form_GroupTag::GROUP ) {
            $elements = array( );
            if ( $visibility ) {
                $group  =& CRM_Core_PseudoConstant::allGroup( );
            } else {
                $group  =& CRM_Core_PseudoConstant::group( );
            }
            foreach ($group as $id => $name) {
                if ( $visibility ) {
                    // make sure that this group has public visibility. not very efficient
                    $dao =& new CRM_Contact_DAO_Group( );
                    $dao->id = $id;
                    if ( $dao->find( true ) ) {
                        if ( $dao->visibility == 'User and User Admin Only' ) {
                            continue;
                        }
                    } else {
                        continue;
                    }
                }
                $elements[] =& HTML_QuickForm::createElement('checkbox', $id, null, $name);
            }
            if ( ! empty( $elements ) ) {
                $form->addGroup( $elements, 'group', ts( 'Group(s)' ), '<br />' );
                if ( $isRequired ) {
                    $form->addRule( 'group' , ts('%1 is a required field.', array(1 => ts('Group(s)'))) , 'required');   
                }
            }
        }
        
        if ( $type & CRM_Contact_Form_GroupTag::TAG ) {
            $elements = array( );
            $tag =& CRM_Core_PseudoConstant::tag  ( );
            foreach ($tag as $id => $name) {
                $elements[] =& HTML_QuickForm::createElement('checkbox', $id, null, $name);
            }
            if ( ! empty( $elements ) ) { 
                $form->addGroup( $elements, 'tag', ts( 'Tag(s)' ), '<br />' );
            }
            
            if ( $isRequired ) {
                $form->addRule( 'tag' , ts('%1 is a required field.', array(1 => ts('Tag(s)'))) , 'required');   
            }
        }
        
        
    }

    /**
     * set defaults for relevant form elements
     *
     * @param int   $id       the contact id
     * @param array $defaults the defaults array to store the values in
     * @param int   $type     what components are we interested in
     *
     * @return void
     * @access public
     * @static
     */
    static function setDefaults( $id, &$defaults, $type = CRM_Contact_Form_GroupTag::ALL ) {
        $type = (int ) $type; 
        if ( $type & CRM_Contact_Form_GroupTag::GROUP ) { 
            require_once 'CRM/Contact/BAO/GroupContact.php';
            $contactGroup =& CRM_Contact_BAO_GroupContact::getContactGroup( $id, 'Added' );  
            if ( $contactGroup ) {  
                foreach ( $contactGroup as $group ) {  
                    $defaults['group'][$group['group_id']] = 1;  
                } 
            }
        }

        if ( $type & CRM_Contact_Form_GroupTag::TAG ) {
            require_once 'CRM/Core/BAO/EntityTag.php';
            $contactTag =& CRM_Core_BAO_EntityTag::getTag('civicrm_contact', $id);  
            if ( $contactTag ) {  
                foreach ( $contactTag as $tag ) {  
                    $defaults['tag'][$tag] = 1;  
                }  
            }  
        }

    }

}


?>
