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
class CRM_Contact_Form_Edit_IM
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
    static function buildQuickForm( &$form ) {
        
        //FIXME : &$location, $locationId, $count 
        
        require_once 'CRM/Core/BAO/Preferences.php';
        $count = 2;
        if ( CRM_Utils_Array::value( 'im', CRM_Core_BAO_Preferences::valueOptions( 'address_options', true, null, true ) ) ) {
            $form->assign('showIM', true);
            for ($i = 1; $i <= $count; $i++) {
                
                //IM provider select
                $form->addElement('select', "im[$i][provider_id]", '',
                                  array('' => ts('- select service -')) + CRM_Core_PseudoConstant::IMProvider() );

                //Block type select
                $form->addElement('select',"im[$i][location_id]", '' , CRM_Core_PseudoConstant::locationType());

                //IM box
                $form->addElement('text', "im[$i][name]", ts('Instant Messenger'),
                                  CRM_Core_DAO::getAttribute('CRM_Core_DAO_IM', 'name') );

                //Primary radio
                $options = array( HTML_QuickForm::createElement('radio', null, '') );
                $form->addGroup($options, "im[$i][is_primary]", ''); 
            }
        }
        $form->assign( 'imCount', $count );
    }
}



