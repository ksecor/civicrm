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
            
            $orgId = null;
            $excludeGroupIds = array( );
	        if ( $contactId > 0 ) {
	            require_once 'CRM/Contact/DAO/GroupOrganization.php';
	            require_once 'CRM/Contact/DAO/Group.php';
                
                //will revist this code once done with other fixes
	            $dao = new CRM_Contact_DAO_Contact( );
	            $query = "SELECT id FROM civicrm_contact WHERE id = $contactId";
	            $dao->query($query);
	    
	            if ( $dao->fetch() ) {
	                $orgId = $dao->id;
	            }
            }
	    
	        if ( $orgId != null ) {
	            $excludeGroupIds = array ( );
	            $dao = new CRM_Contact_DAO_GroupOrganization();
		        $query = "SELECT group_id FROM civicrm_group_organization WHERE organization_id = $orgId";
		        $dao->query($query);
		        while ( $dao->fetch() ) {
		            $excludeGroupIds[] = $dao->group_id;
		        }
	        }
	        /*
    	    if ( $groupFetchId != null ) {
    	        $dao = new CRM_Contact_DAO_Group();
    		$query = "SELECT title FROM civicrm_group WHERE id = $groupFetchId";
    		$dao->query($query);
    		if ( $dao->fetch() ) {
    		    $excludeGroupTitle = $dao->title;
    		}
    		else {
    		    $excludeGroupTitle = null;
    		}


    	    }
    	    */
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
		$disableBox = false;
		foreach ( $excludeGroupIds as $excludeGroupId ) {
		  if ( $excludeGroupId == $id ) {
		      $disableBox = true;
		  }
		}		
		if ( ! $disableBox ) {
                    $elements[] =& HTML_QuickForm::createElement('checkbox', $id, null, $name);
		}
		else {
		  $elements[] =& HTML_QuickForm::createElement('checkbox', $id, null, $name, array ('disabled' => 'disabled', 'checked' => 'checked'));
		}
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


?>
