<?php
/**
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Core/ShowHideBlocks.php';

/**
 * Auxilary class to provide support to the Contact Form class. Does this by implementing
 * a small set of static methods
 *
 */
class CRM_Contact_Form_Household {
    /**
     * This function provides the HTML form elements that are specific to the Individual Contact Type
     *
     * @access public
     * @return None
     */
    public function buildQuickForm( $form ) {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Household');
        
        $this->applyFilter('__ALL__','trim');  
      
        // household_name
        $this->add('text', 'household_name', 'Household Name', $attributes['household_name']);
        
        // nick_name
        $this->addElement('text', 'nick_name', 'Nick Name', $attributes['nick_name']);
    }

    static function formRule( &$fields ) {
        $errors = array( );

        $primaryEmail = CRM_Contact_Form_Edit::formRule( $fields, $errors );

        // make sure that household name is set
        if (! CRM_Utils_Array::value( 'household_name', $fields ) ) {
            $errors['household_name'] = 'Household Name should be set.';
        }

        return empty( $errors ) ? true : $errors;
    }

}


    
?>