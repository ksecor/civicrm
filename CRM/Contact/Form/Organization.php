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
class CRM_Contact_Form_Organization extends CRM_Core_Form 
{
    /**
     * This function provides the HTML form elements that are specific to this Contact Type
     *
     * @access public
     * @return None
     */
    public function buildQuickForm( &$form ) {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact');

        $form->applyFilter('__ALL__','trim');
        
        // Organization_name
        $form->add('text', 'organization_name', ts('Organization Name'), $attributes['organization_name']);
        
        // legal_name
        $form->addElement('text', 'legal_name', ts('Legal Name'), $attributes['legal_name']);

        // nick_name
        $form->addElement('text', 'nick_name', ts('Nick Name'),
                          CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'nick_name') );

        // sic_code
        $form->addElement('text', 'sic_code', ts('SIC Code'), $attributes['sic_code']);

        // home_URL
        $form->addElement('text', 'home_URL', ts('Website'),
                          array_merge( CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'home_URL'),
                                       array('onfocus' => "if (!this.value) this.value='http://'; else return false",
                                             'onblur' => "if ( this.value == 'http://') this.value=''; else return false")
                                       ));
        $form->addRule('home_URL', ts('Enter a valid Website.'), 'url');
        
        $form->addElement('text', 'contact_source', ts('Source'));
        $form->add('text', 'external_identifier', ts('External Id'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'external_identifier'), false);
        $form->addRule( 'external_identifier',
			ts('External ID already exists in Database.'), 
                        'objectExists', 
			array( 'CRM_Contact_DAO_Contact', $form->_contactId, 'external_identifier' ) );
    }

    static function formRule( &$fields ,&$files, $options) {
       
        $errors = array( );
        
        $primaryEmail = CRM_Contact_Form_Edit::formRule( $fields, $errors );
        
        // make sure that organization name is set
        if (! CRM_Utils_Array::value( 'organization_name', $fields ) ) {
            $errors['organization_name'] = 'Organization Name should be set.';
        }
        
        //code for dupe match
        if ( ! CRM_Utils_Array::value( '_qf_Edit_next_duplicate', $fields )) {
            require_once 'CRM/Dedupe/Finder.php';
            $dedupeParams = CRM_Dedupe_Finder::formatParams($fields, 'Organization');
            $dupeIDs = CRM_Dedupe_Finder::dupesByParams($dedupeParams, 'Organization', 'Fuzzy', array($options));
            $viewUrls = array( );
            $urls     = array( );
            foreach( $dupeIDs as $id ) {
                $displayName = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'display_name' );
                $viewUrls[] = '<a href="' . CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $id ) .
                '" target="_blank">' . $displayName . '</a>';
                $urls[] = '<a href="' . CRM_Utils_System::url( 'civicrm/contact/add', 'reset=1&action=update&cid=' . $id ) .
                    '">' . $displayName . '</a>';
            }
            if (!empty($dupeIDs)) {
                $url = implode( ', ',  $urls );
                $viewUrl = implode( ', ',  $viewUrls );
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
        // add code to make sure that the uniqueness criteria is satisfied
        return empty( $errors ) ? true : $errors;
    }
}


    

