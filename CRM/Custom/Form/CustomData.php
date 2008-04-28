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
    static function preProcess( &$form )
    {
        //Custom Group Inline Edit form
        $form->_type     = CRM_Utils_Request::retrieve( 'type', 'String', $form );
        $form->_subType  = CRM_Utils_Request::retrieve( 'subType', 'String', $form );
        $form->_entityId = CRM_Utils_Request::retrieve( 'entityId', 'Positive', $form );

        $form->_groupTree =& CRM_Core_BAO_CustomGroup::getTree( $form->_type, $form->_entityId, 0, $form->_subType );
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
        CRM_Core_BAO_CustomGroup::buildQuickForm( $form, $form->_groupTree );
    }


}

