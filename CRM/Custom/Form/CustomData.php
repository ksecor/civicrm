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

require_once 'CRM/Core/BAO/CustomGroup.php';

/**
 * this class builds custom data
 */
class CRM_Custom_Form_CustomData 
{
    static function preProcess( &$form, $subName = null, $subType = null, $groupCount = null, $type = null, $entityID = null )
    {
        if ( $type ) {
        	$form->_type = $type;
	    } else {
			$form->_type     = CRM_Utils_Request::retrieve( 'type', 'String', $form );
		}
		
		if ( $subType ) {
			if ( $subType == 'null' ) {
				$form->_subType = null;
			} else {
				$form->_subType = $subType;
			}
		} else {
			$form->_subType = CRM_Utils_Request::retrieve( 'subType', 'String', $form );	
		}
		
        if ( $subName ) {
			if ( $subName == 'null' ) {
				$form->_subName = null;
			} else {
				$form->_subName = $subName;
			}
		} else {
			$form->_subName = CRM_Utils_Request::retrieve( 'subName', 'String', $form );	
		}
        
		if ( $groupCount ) {
			$form->_groupCount = $groupCount;
		} else {
			$form->_groupCount = CRM_Utils_Request::retrieve( 'cgcount', 'Positive', $form );	
		}
		
		$form->assign('cgCount', $form->_groupCount);
		
		if ( $entityID ) {
			$form->_entityId = $entityID;
		} else {
			$form->_entityId = CRM_Utils_Request::retrieve( 'entityID', 'Positive', $form );	
		}

        $form->_groupID  = CRM_Utils_Request::retrieve( 'groupID', 'Positive', $form );

		$groupTree =& CRM_Core_BAO_CustomGroup::getTree( $form->_type,
														 $form,
														 $form->_entityId,
														 $form->_groupID,
														 $form->_subType,
														 $form->_subName );

        // we should use simplified formatted groupTree
        $form->_groupTree = CRM_Core_BAO_CustomGroup::formatGroupTree( $groupTree, $form->_groupCount );
    }

    static function setDefaultValues( &$form ) 
    {
        $defaults = array( );
        CRM_Core_BAO_CustomGroup::setDefaults( $form->_groupTree, $defaults);
        return $defaults;
    }
    
    static function buildQuickForm( &$form )
    {
        $form->addElement( 'hidden', 'hidden_custom', 1 );
		$form->addElement( 'hidden', "hidden_custom_group_count[{$form->_groupID}]", $form->_groupCount );
        CRM_Core_BAO_CustomGroup::buildQuickForm( $form, $form->_groupTree, false, $form->_groupCount );
    }


}

