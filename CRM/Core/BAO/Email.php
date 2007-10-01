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
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

require_once 'CRM/Core/DAO/Email.php';

/**
 * This class contains functions for email handling
 */
class CRM_Core_BAO_Email extends CRM_Core_DAO_Email 
{

    /**
     * takes an associative array and adds email
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object       CRM_Core_BAO_Email object on success, null otherwise
     * @access public
     * @static
     */
    static function add( &$params ) 
    {
        $email =& new CRM_Core_DAO_Email( );
        
        $email->copyValues($params);

        if ( $email->is_bulkmail ) {
            $sql = "
UPDATE civicrm_email 
SET is_bulkmail = 0
WHERE 
contact_id = {$params['contact_id']}";
            CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
        }
        
        // handle if email is on hold TO DO
//         if ( array_key_exists( 'on_hold', $params['location'][$locationId]['email'][$emailId]) ) {
//             $values = array(
//                       'location' => array( $locationId => $params['location'][$locationId]['id'] ),
//                       'email'    => $ids['location'][$locationId]['email']
//                       );
            
//             self::holdEmail( $email, $values, $locationId, $emailId,
//                              CRM_Utils_Array::value( 'on_hold', $params['location'][$locationId]['email'][$emailId], false));
            
//             return $email;
//         }


        return $email->save( );
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params        input parameters to find object
     * @param array $values        output values of the object
     * @param array $ids           the array that holds all the db ids
     * @param int   $blockCount    number of blocks to fetch
     *
     * @return boolean
     * @access public
     * @static
     */
    static function &getValues( $contactId ) 
    {
        $email =& new CRM_Core_BAO_Email( );
        return CRM_Core_BAO_Block::getValues( $email, 'email', $contactId );
    }

    /**
     * Get all the emails for a specified contact_id, with the primary email being first
     *
     * @param int $id the contact id
     *
     * @return array  the array of email id's
     * @access public
     * @static
     */
    static function allEmails( $id ) 
    {
        if ( ! $id ) {
            return null;
        }

        $query = "
SELECT email, civicrm_location_type.name as locationType, civicrm_email.is_primary as is_primary, civicrm_email.on_hold as on_hold,
civicrm_email.id as email_id, civicrm_email.location_type_id as locationTypeId
FROM      civicrm_contact
LEFT JOIN civicrm_email ON ( civicrm_email.contact_id = civicrm_contact.id )
LEFT JOIN civicrm_location_type ON ( civicrm_email.location_type_id = civicrm_location_type.id )
WHERE
  civicrm_contact.id = %1
ORDER BY
  civicrm_email.is_primary DESC, civicrm_email.location_type_id DESC, email_id ASC ";
        $params = array( 1 => array( $id, 'Integer' ) );

        $emails = array( );
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
        while ( $dao->fetch( ) ) {
            $emails[$dao->email_id] = array( 'locationType'   => $dao->locationType,
                                             'is_primary'     => $dao->is_primary,
                                             'on_hold'        => $dao->on_hold,
                                             'id'             => $dao->email_id,
                                             'email'          => $dao->email,
                                             'locationTypeId' => $dao->locationTypeId );
        }
        return $emails;
    }
    
    /**
     * Delete email address records from a location
     *
     * @param int $locationId       Location ID to delete for
     * 
     * @return void
     * 
     * @access public
     * @static
     */
    public static function deleteLocation( $locationId ) {
        $dao =& new CRM_Core_DAO_Email();
        $dao->location_id = $locationId;
        $dao->find();

        require_once 'CRM/Mailing/Event/BAO/Queue.php';
        while ($dao->fetch()) {
            CRM_Mailing_Event_BAO_Queue::deleteEventQueue( $dao->id );
        }
        
        $dao->reset();
        $dao->location_id = $locationId;
        $dao->delete();
    }
    
    /**
     * Method to hold or reset email(s)
     * 
     * This method is used to hold and reset the email(s) according to
     * the 'holodStatus' value provided.
     * 'Values' array contains values required to search for required
     * email record in update mode.
     * An example Values array looks like : 
     * 
     * Values
     *
     * Array
     * (
     * [location] => Array
     *      (
     *       [2] => 92
     *      )
     *
     * [email] => Array
     *      (
     *       [1] => 170
     *       [2] => 171
     *       [3] => 172
     *      )
     *
     * )
     * 
     * @param object  $emailDAO          (referance) email dao object
     * @param array   $values
     * @param int     $locationBlockId   Location Block Number
     * @param int     $emailBlockId      Email Block Number
     * @param boolean $holdStatus        flag to indicate whether hold
     *                                   an email or reset
     *
     */
    public static function holdEmail( &$emailDAO, $values, $locationBlockId = 1, $emailBlockId = 1, $holdStatus = false) 
    {
        if ( $holdStatus ) {
            $emailDAO->on_hold     = 1;
            $emailDAO->hold_date   = date( 'YmdHis' );
            $emailDAO->reset_date  = '';
        } else if ( !empty($values['email'][$emailBlockId])) {
            $emailDAO->save();
            $emailDAO->location_id = $values['location'][$locationBlockId];
            
            $emailDAO->whereAdd('id=' . $values['email'][$emailBlockId]);
            $emailDAO->whereAdd('hold_date IS NOT NULL');
            if ( $emailDAO->find(true) ) {
                $emailDAO->on_hold     = 0;
                $emailDAO->hold_date   = '';
                $emailDAO->reset_date  = date( 'YmdHis' );
            }
        }
        
        $emailDAO->save();
        return true;
    }
}
?>
