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

require_once 'CRM/Utils/System.php';
require_once 'api/crm.php';
require_once 'CRM/Utils/Mail.php';

/**
 * BAO object for crm_email_history table
 */
class CRM_Core_BAO_EmailHistory  {
    
    static function &add( &$params ) {
        $email =& new CRM_Core_DAO_EmailHistory( );
                
        //Replace Tokens Before Saving to the DB
        $contactId  = array( 'contact_id' => $params['contact_id'] );
        $contact =& crm_fetch_contact( $contactId );
        require_once 'CRM/Utils/Token.php';
        $params['message'] = CRM_Utils_Token::replaceContactTokens( $params['message'],
                                                                    $contact, false );

        $params['subject'] = CRM_Utils_Token::replaceContactTokens( $params['subject'],
                                                                    $contact, false );

        $email->subject    = CRM_Utils_Array::value( 'subject'   , $params );
        $email->message    = CRM_Utils_Array::value( 'message'   , $params );
        $email->contact_id = CRM_Utils_Array::value( 'contact_id', $params );
        $email->sent_date  = CRM_Utils_Array::value( 'sent_date' , $params , date( 'Ymd' ) );

        $email->save( );

        return $email;
    }

    /**
     * compose the url to show details of this specific email
     * 
     * @param  int     $id
     * 
     * @return string            an HTML string containing a link to the given path.
     * 
     * @access public
     */
    public function showEmailDetails( $id )
    {
        return CRM_Utils_System::url('civicrm/contact/view/activity', "activity_id=3&details=1&action=view&id=$id&selectedChild=activity");
    }
    
    /**
     * delete all email history records for this contact id.
     *
     * @param int   $id   ID of the contact for which 
     *                    the email history need to be deleted.
     * 
     * @return void
     * 
     * @access public
     * @static
     */
    public static function deleteContact($id)
    {
        $dao =& new CRM_Core_DAO_EmailHistory();
        $dao->contact_id = $id;
        $dao->delete();
    }
}

?>
