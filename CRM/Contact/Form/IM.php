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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

/**
 * form helper class for an IM object 
 */
class CRM_Contact_Form_IM
{
    /**
     * build the form elements for an IM object
     *
     * @param CRM_Core_Form $form       reference to the form object
     * @param array         $location   the location object to store all the form elements in
     * @param int           $locationId the locationId we are dealing with
     * @param int           $count      the number of blocks to create
     *
     * @return void
     * @access public
     * @static
     */
    static function buildIMBlock(&$form, &$location, $locationId, $count) {
        require_once 'CRM/Core/BAO/Preferences.php';
        
        if ( CRM_Utils_Array::value( 'im', CRM_Core_BAO_Preferences::valueOptions( 'address_options', true, null, true ) ) ) {
            $form->assign('showIM', true);
            for ($i = 1; $i <= $count; $i++) {
                
                $label = ($i == 1) ? ts('Instant Messenger (preferred)') : ts('Instant Messenger');
                
                CRM_Core_ShowHideBlocks::linksForArray( $form, $i, $count, "location[$locationId][im]", 
                                                        ts('another IM'), ts('hide this IM'));
                
                $location[$locationId]['im'][$i]['service_id'] = $form->addElement('select',
                                                                                   "location[$locationId][im][$i][provider_id]",
                                                                                   $label,
                                                                                   array('' => ts('- select service -')) +
                                                                                   CRM_Core_PseudoConstant::IMProvider()
                                                                                   );
                $location[$locationId]['im'][$i]['name'] = $form->addElement('text',
                                                                             "location[$locationId][im][$i][name]",
                                                                             null,
                                                                             CRM_Core_DAO::getAttribute('CRM_Core_DAO_IM',
                                                                                                        'name'));
            }
        }
        
    }    
}



