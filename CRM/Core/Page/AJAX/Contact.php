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
 *
 */

/**
 * This class contains all contact related functions that are called using AJAX (jQuery)
 */
class CRM_Core_Page_AJAX_Contact
{
    static function getContactList( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $name = CRM_Utils_Array::value( 'text', $_GET );
        
        $query = "
SELECT sort_name, id
FROM civicrm_contact
WHERE sort_name LIKE '%$name%'
AND contact_type = 'Individual'
ORDER BY sort_name ";            

        $dao = CRM_Core_DAO::executeQuery( $query );
        $contactList = null;
        while ( $dao->fetch( ) ) {
            echo $contactList = "$dao->sort_name|$dao->id\n";
        }
    }

    static function relationship( &$config ) 
    {
        CRM_Core_Error::debug_var( 'GET' , $_GET , true, true );
        CRM_Core_Error::debug_var( 'POST', $_POST, true, true );
        
        require_once 'CRM/Utils/Type.php';
        $relType         = CRM_Utils_Array::value( 'rel_type', $_POST );
        $relContactID    = CRM_Utils_Array::value( 'rel_contact', $_POST );
        $sourceContactID = CRM_Utils_Array::value( 'contact_id', $_POST );
        $relationshipID  = CRM_Utils_Array::value( 'rel_id', $_POST );
        $caseID          = CRM_Utils_Array::value( 'case_id', $_POST );


        $relationParams = array('relationship_type_id' => $relType .'_a_b', 
                                'contact_check'        => array( $relContactID => 1),
                                'is_active'            => 1,
                                'case_id'              => $caseID
                                );
        
        if ( $relationshipID == 'null' ) {
            $relationIds = array( 'contact'      => $sourceContactID);
        } else {
            $relationIds = array( 'contact'      => $sourceContactID, 
                                  'relationship' => $relationshipID,
                                  'contactTarget'=>  $relContactID );
        }

        require_once "CRM/Contact/BAO/Relationship.php";
        CRM_Contact_BAO_Relationship::create( $relationParams, $relationIds );
        
    }
}
