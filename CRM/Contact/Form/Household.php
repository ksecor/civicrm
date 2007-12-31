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
require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Core/ShowHideBlocks.php';

/**
 * Auxilary class to provide support to the Contact Form class. Does this by implementing
 * a small set of static methods
 *
 */
class CRM_Contact_Form_Household 
{
    /**
     * This function provides the HTML form elements that are specific to the Individual Contact Type
     *
     * @access public
     * @return None
     */
    public function buildQuickForm( &$form ) 
    {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact');
        
        $form->applyFilter('__ALL__','trim');  
      
        // household_name
        $form->add('text', 'household_name', ts('Household Name'), $attributes['household_name']);
        
        // nick_name
        $form->addElement('text', 'nick_name', ts('Nick Name'), $attributes['nick_name'] );
        $form->addElement('text', 'contact_source', ts('Source'), CRM_Utils_Array::value( 'source', $attributes ) );
        $form->add('text', 'external_identifier', ts('External Id'), $attributes['external_identifier'], false);
        $form->addRule( 'external_identifier',
                        ts('External ID already exists in Database.'), 
                        'objectExists', 
                        array( 'CRM_Contact_DAO_Contact', $form->_contactId, 'external_identifier' ) );
    }
    
    /**
     * add rule for household
     *
     * @params array $fields array of form values
     *
     * @return $error 
     * @static
     * @public
     */
    static function formRule( &$fields ,&$files, $options) 
    {
        $errors = array( );

        $primaryEmail = CRM_Contact_Form_Edit::formRule( $fields, $errors );

        // make sure that household name is set
        if (! CRM_Utils_Array::value( 'household_name', $fields ) ) {
            $errors['household_name'] = 'Household Name should be set.';
        }
        
        //code for dupe match
        if ( ! CRM_Utils_Array::value( '_qf_Edit_next_duplicate', $fields )) {
            $dupeIDs = array();
            require_once "CRM/Contact/DAO/Contact.php";
            $contact = & new CRM_Contact_DAO_Contact();
            $contact->household_name = $fields['household_name'];
            $contact->find();
            while ($contact->fetch(true)) {
                if ( $contact->id != $options) {
                    $dupeIDs[] = $contact->id;
                }
            }

            foreach ( $dupeIDs as $id ) {
                $displayName = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'display_name' );
                $urls[] = '<a href="' . CRM_Utils_System::url( 'civicrm/contact/add', 'reset=1&action=update&cid=' . $id ) .
                    '">' . $displayName . '</a>';
            }
            if (!empty($dupeIDs)) {
                $url = implode( ', ',  $urls );
                $errors['_qf_default'] = ts( 'One matching contact was found. You can edit it here: %1, or click Save Matching Contact button below.', array( 1 => $url, 'count' => count( $urls ), 'plural' => '%count matching contacts were found. You can edit them here: %1, or click Save Matching Contact button below.' ) );
                $template =& CRM_Core_Smarty::singleton( );
                $template->assign( 'isDuplicate', 1 );
            } else if ( CRM_Utils_Array::value( '_qf_Edit_refresh_dedupe', $fields ) ) {
                // add a session message for no matching contacts
                CRM_Core_Session::setStatus( 'No matching contact found.' );
            }
        }

        return empty( $errors ) ? true : $errors;
    }

    /**
     * This function synchronizes (updates) the address of individuals, sharing the address of the passed in household-contact-ID.
     *
     * @param integer $householdContactID  the household contact id.
     *
     * @return void
     * @access public
     * @static
     */
    static function synchronizeIndividualAddresses( $householdContactID ) 
    {
        require_once 'api/v2/Location.php';
        $locParams = array( 'contact_id' => $householdContactID );
        $values =& _civicrm_location_get( $locParams, $location_types );

        $query =  "SELECT contact_id from civicrm_individual where mail_to_household_id=$householdContactID";
        $nullArray = array( );
        $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );
        
        while ( $dao->fetch( ) ) {
            $params = array();
            
            $idParams = array( 'id' => $dao->contact_id, 'contact_id' => $dao->contact_id );
            $ids =& _civicrm_location_get( $idParams, $dnc );
            
            $params['contact_id']                      = $dao->contact_id;
            $params['location'][1]['address']          = $values[1]['address'];
            $params['location'][1]['location_type_id'] = $values[1]['location_type_id'];
            $params['location'][1]['is_primary']       = 1;
            
            // removing unwanted ids from the params array
            $unsetFields = array( 'id', 'location_id', 'timezone', 'note' );
            foreach ( $unsetFields as $fld ) {
                unset( $params['location'][1]['address'][$fld] );
            }
            
            // building new ids array of only those ids which needs to
            // be updated
            $releventIDs                  = array();
            $releventIDs['contact']       = $dao->contact_id;
            $releventIDs['location']      = array();
            $releventIDs['location']['1'] = array();
            
            $releventIDs['location']['1']['id']      = $ids['1']['id'];
            $releventIDs['location']['1']['address'] = $ids['1']['address']['id'];
            
            CRM_Core_BAO_Location::add( $params, $releventIDs, 1 );
            unset($params, $ids);
        }
    }
}
    
?>
