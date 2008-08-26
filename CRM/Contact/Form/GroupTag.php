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

class CRM_Contact_Form_GroupTag
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
     * @param Object  $form        the form object that we are operating on
     * @param int     $contactId   contact id
     * @param int     $type        what components are we interested in 
     * @param boolean $visibility  visibility of the field
     * @param string  $groupName   if used for building group block
     * @param string  $tagName     if used for building tag block
     * @param string  $fieldName   this is used in batch profile(i.e to build multiple blocks)
     * 
     * @static
     * @access public
     */
    static function buildGroupTagBlock(&$form,
                                       $contactId = 0,
                                       $type = CRM_Contact_Form_GroupTag::ALL,
                                       $visibility = false,
                                       $isRequired = null,
                                       $groupName = 'Groups(s)',
                                       $tagName   = 'Tag(s)',
                                       $fieldName = null ) 
    {
        
        $type = (int ) $type;
        if ( $type & CRM_Contact_Form_GroupTag::GROUP ) {

            $fName = 'group';
            if ($fieldName) {
                $fName = $fieldName; 
            }

            $elements = array( );
            if ( $visibility ) {
                $group  =& CRM_Core_PseudoConstant::allGroup( );
            } else {
                $group  =& CRM_Core_PseudoConstant::group( );
            }
            require_once 'CRM/Contact/DAO/Group.php';
            foreach ($group as $id => $name) {
		        if ( $visibility ) {
		            // make sure that this group has public visibility. not very efficient
                    $visibilityValue = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Group',
                                                                    $id,
                                                                    'visibility' );
                    if ( $visibilityValue == 'User and User Admin Only' ) {
			                continue;
                    }
		        }
		        $elements[] =& HTML_QuickForm::createElement('checkbox', $id, null, $name );
		    }

	        if ( ! empty( $elements ) ) {
                $form->addGroup( $elements, $fName, $groupName, '<br />' );
                if ( $isRequired ) {
                    $form->addRule( $fName , ts('%1 is a required field.', array(1 => $groupName)) , 'required');   
                }
            }
        }
        
        if ( $type & CRM_Contact_Form_GroupTag::TAG ) {
            $fName = 'tag';
            if ($fieldName) {
                $fName = $fieldName; 
            }

            $elements = array( );
            $tag =& CRM_Core_PseudoConstant::tag  ( );
            foreach ($tag as $id => $name) {
                $elements[] =& HTML_QuickForm::createElement('checkbox', $id, null, $name);
            }
            if ( ! empty( $elements ) ) { 
                $form->addGroup( $elements, $fName, $tagName, '<br />' );
            }
            
            if ( $isRequired ) {
                $form->addRule( $fName , ts('%1 is a required field.', array(1 => $tagName)) , 'required');   
            }
        }
    }

    /**
     * set defaults for relevant form elements
     *
     * @param int    $id        the contact id
     * @param array  $defaults  the defaults array to store the values in
     * @param int    $type      what components are we interested in
     * @param string $fieldName this is used in batch profile(i.e to build multiple blocks)
     *
     * @return void
     * @access public
     * @static
     */
    static function setDefaults( $id, &$defaults, $type = CRM_Contact_Form_GroupTag::ALL, $fieldName = null ) 
    {
        $type = (int ) $type; 
        if ( $type & CRM_Contact_Form_GroupTag::GROUP ) { 
            $fName = 'group';
            if ($fieldName) {
                $fName = $fieldName; 
            }

            require_once 'CRM/Contact/BAO/GroupContact.php';
            $contactGroup =& CRM_Contact_BAO_GroupContact::getContactGroup( $id, 'Added', null, false, true );  
            if ( $contactGroup ) {  
                foreach ( $contactGroup as $group ) {  
                    $defaults[$fName ."[". $group['group_id'] ."]"] = 1;  
                } 
            }
        }

        if ( $type & CRM_Contact_Form_GroupTag::TAG ) {
            $fName = 'tag';
            if ($fieldName) {
                $fName = $fieldName; 
            }
            
            require_once 'CRM/Core/BAO/EntityTag.php';
            $contactTag =& CRM_Core_BAO_EntityTag::getTag($id);  
            if ( $contactTag ) {  
                foreach ( $contactTag as $tag ) {  
                    $defaults[$fName ."[" . $tag . "]" ] = 1;  
                }  
            }  
        }

    }

}



