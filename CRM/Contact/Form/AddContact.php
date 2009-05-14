<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */


/**
 * Class to build contact combobox via ajax
 */
class CRM_Contact_Form_AddContact
{
    function buildQuickForm( $form, $fieldName = 'contact' ) 
    {
        $form->assign( 'dojoIncludes', "dojo.require('dojox.data.QueryReadStore'); dojo.require('dojo.parser');");
        
        $dataUrl = CRM_Utils_System::url( "civicrm/ajax/search",
                                          "reset=1",
                                          true, null, false );
        $this->assign('dataUrl',$dataUrl );


        $attributes = array( 'dojoType'     => 'civicrm.FilteringSelect',
                             'store'        => "contactStore",
                             'style'        => 'border: 1px solid #cfcfcf;',
                             'class'        => 'tundra',
                             'pageSize'     => 10,
                             'id'           => "{$fieldName}"
                             );
        
        $dataURL =  CRM_Utils_System::url( 'civicrm/ajax/search',
                                           "reset=1",
                                           false, null, false );
        
        $form->assign('dataURL',$dataURL );
        $form->addElement('text', "{$fieldName}", ts('Select Contact'), $attributes );
    }
}
