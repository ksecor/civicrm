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

require_once 'CRM/Form.php';
require_once 'CRM/SelectValues.php';
require_once 'CRM/ShowHideBlocks.php';

/**
 * Auxilary class to provide support to the Contact Form class. Does this by implementing
 * a small set of static methods
 *
 */
class CRM_Contact_Form_Organization extends CRM_Form 
{
    /**
     * This function provides the HTML form elements that are specific to this Contact Type
     *
     * @access public
     * @return None
     */
    public function buildQuickForm( $form ) {
        $attributes = CRM_DAO::getAttribute('CRM_Contact_DAO_Organization');
        
        // Organization_name
        $this->addElement('text', 'organization_name', 'Organization Name:', $attributes['organization_name']);
        
        // legal_name
        $this->addElement('text', 'legal_name', 'Legal Name:', $attributes['legal_name']);

        // nick_name
        $this->addElement('text', 'nick_name',"Nick Name:", $attributes['nick_name']);

        // sic_code
        $this->addElement('text', 'sic_code', 'SIC Code:', $attributes['sic_code']);
    }

    static function formRule( &$fields ) {
        $errors = array( );
        
        $primaryEmail = null;

        // make sure that at least one field is marked is_primary
        if ( array_key_exists( 'location', $fields ) && is_array( $fields['location'] ) ) {
            $locationKeys = array_keys( $fields['location']);
            $isPrimary = false;
            foreach ( $locationKeys as $locationId ) {
                if ( array_key_exists( 'is_primary', $fields['location'][$locationId] ) ) {
                    if ( $fields['location'][$locationId]['is_primary'] ) {
                        if ( $isPrimary ) {
                            $errors["location[$locationId][is_primary]"] = "Only one location can be marked as primary.";
                        }
                        $isPrimary = true;
                    }

                    // only harvest email from the primary locations
                    if ( array_key_exists( 'email', $fields['location'][$locationId] ) &&
                         is_array( $fields['location'][$locationId]['email'] )         &&
                         empty( $primaryEmail ) ) {
                        foreach ( $fields['location'][$locationId]['email'] as $idx => &$email ) {
                            if ( array_key_exists( 'email', $email ) ) {
                                $primaryEmail = $email['email'];
                                break;
                            }
                        }
                    }
                }
            }
            
            if ( ! $isPrimary ) {
                $errors["location[1][is_primary]"] = "One location needs to be marked as primary.";
            }

        }
        
        // make sure that Organization Name or a primary email is set
        if ( ! array_key_exists( 'organization_name', $fields ) ||
             empty( $primaryEmail ) ) {
            $errors['organization_name'] = "Organization Name OR an email in the Primary Location should be set.";
        }
        
        // add code to make sure that the uniqueness criteria is satisfied

        if ( ! empty( $errors ) ) {
            return $errors;
        }
        return true;
    }
}


    
?>