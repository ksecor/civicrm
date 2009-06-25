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
 * form helper class for a phone object 
 */
class CRM_Contact_Form_Edit_Phone 
{
    /**
     * build the form elements for a phone object
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
        
        //FIXME : &$location, $locationId, $count, $phoneType = null
        $count = 2;
        require_once 'CRM/Core/ShowHideBlocks.php';
        for ($i = 1; $i <= $count; $i++) {
            if ( ! $phoneType ) {
                $phoneType = CRM_Core_PseudoConstant::phoneType( );
            }

            //phone type select
            $form->addElement('select', "phone[$i][phone_type_id]", ts('Phone'), $phoneType, null );
            
            //Block type select
            $form->addElement('select',"phone[$i][location_id]", '' , CRM_Core_PseudoConstant::locationType());
            
            //phone box
            $form->addElement('text', "phone[$i][phone]", ts('Phone'), 
                              CRM_Core_DAO::getAttribute('CRM_Core_DAO_Phone', 'phone'));
            
            
            //Primary radio
            $options = array( HTML_QuickForm::createElement('radio', null, '') );
            $form->addGroup($options, "phone[$i][is_primary]", ''); 
            

            // TODO: set this up as a group, we need a valid phone_type_id if we have a  phone number
            // $form->addRule( "location[$locationId][phone][$i][phone]", ts('Phone number is not valid.'), 'phone' );
        }
        $form->assign( 'phoneCount', $count );
    }

}



