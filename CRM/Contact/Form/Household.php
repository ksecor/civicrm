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
            require_once 'CRM/Dedupe/Finder.php';
            $dedupeParams = CRM_Dedupe_Finder::formatParams($fields, 'Household');
            $dupeIDs = CRM_Dedupe_Finder::dupesByParams($dedupeParams, 'Household', 'Fuzzy', array($options));
            $viewUrls = array( );
            $urls     = array( );
            foreach ( $dupeIDs as $id ) {
                $displayName = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'display_name' );
                $viewUrls[] = '<a href="' . CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $id ) .
                '" target="_blank">' . $displayName . '</a>';
                $urls[] = '<a href="' . CRM_Utils_System::url( 'civicrm/contact/add', 'reset=1&action=update&cid=' . $id ) .
                    '">' . $displayName . '</a>';
            }
            if (!empty($dupeIDs)) {
                $viewUrl = implode( ', ',  $viewUrls );
                $url = implode( ', ',  $urls );
                $errors['_qf_default']  = ts('One matching contact was found.', array('count' => count($urls), 'plural' => '%count matching contacts were found.'));
                $errors['_qf_default'] .= '<br />';
                $errors['_qf_default'] .= ts('If you need to verify if this is the same contact, click here - %1 - to VIEW the existing contact in a new tab.', array(1 => $viewUrl, 'count' => count($urls), 'plural' => 'If you need to verify whether one of these is the same household, click here - %1 - to VIEW the existing contact in a new tab.'));
                $errors['_qf_default'] .= '<br />';
                $errors['_qf_default'] .= ts('If you know the record you are creating is a duplicate, click here - %1 - to EDIT the original record instead.', array(1 => $url));
                $errors['_qf_default'] .= '<br />';
                $errors['_qf_default'] .= ts('If you are sure this is not a duplicate, click the Save Matching Contact button below.');
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
     * This function synchronizes (updates) the address of individuals,
     * sharing the address of the passed household-contact-ID.
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
        $values    =& _civicrm_location_get( $locParams, $location_types );
        $query     =  "SELECT id from civicrm_contact where mail_to_household_id=$householdContactID";
        $dao       = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch( ) ) {    
            $params = array();
            $idParams = array( 'contact_id' => $dao->id );
            $ids =& _civicrm_location_get( $idParams, $dnc );
            $params['contact_id']                      = $dao->id;
            $params['location'][1]['address']          = $values[1]['address'];
            $params['location'][1]['location_type_id'] = $values[1]['location_type_id'];
            $params['location'][1]['is_primary']       = 1;
            
            // removing unwanted ids from the params array
            $unsetFields = array( 'id', 'timezone' );
            foreach ( $unsetFields as $fld ) {
                unset( $params['location'][1]['address'][$fld] );
            }
            
            CRM_Core_BAO_Location::create( $params );
            unset($params, $ids);
        }
    }
}
    

