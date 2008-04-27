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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/BAO/CustomGroup.php';

/**
 * this class builds custom data
 */
class CRM_Custom_Form_CustomData extends CRM_Core_Form 
{
    function preProcess( )
    {
        //Custom Group Inline Edit form
        $this->_type     = CRM_Utils_Array::value( 'type', $_GET );
        $this->_subType  = CRM_Utils_Array::value( 'subType', $_GET );
        $this->_entityId = CRM_Utils_Array::value( 'entityId', $_GET );

        $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree( $this->_type, $this->_entityId, 0, $this->_subType );
        $this->setDefaultValues();
    }

    function setDefaultValues( ) 
    {
        $defaults = array( );
        CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults);
        return $defaults;
    }
    
    function buildQuickForm( )
    {
        CRM_Core_BAO_CustomGroup::buildQuickForm( $this, $this->_groupTree );
    }


}

