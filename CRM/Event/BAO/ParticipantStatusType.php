<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.3                                                |
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


require_once 'CRM/Event/DAO/ParticipantStatusType.php';

class CRM_Event_BAO_ParticipantStatusType extends CRM_Event_DAO_ParticipantStatusType
{
#   /**
#    * Return the DAO object containing to the default row of
#    * civicrm_mail_settings and cache it for further calls
#    *
#    * @return object  DAO with the default mail settings set
#    */
#   static function &defaultDAO()
#   {
#       static $dao = null;
#       if (!$dao) {
#           $dao = new self;
#           $dao->is_default = 1;
#           $dao->find(true);
#       }
#       return $dao;
#   }

#   /**
#    * Return the domain from the default set of settings
#    *
#    * @return string  default domain
#    */
#   static function defaultDomain()
#   {
#       return self::defaultDAO()->domain;
#   }

#   /**
#    * Return the localpart from the default set of settings
#    *
#    * @return string  default localpart
#    */
#   static function defaultLocalpart()
#   {
#       return self::defaultDAO()->localpart;
#   }

    static function retrieve(&$params, &$defaults)
    {
        $result = null;

        $dao = new CRM_Event_DAO_ParticipantStatusType;
        $dao->copyValues($params);
        if ($dao->find(true)) {
            CRM_Core_DAO::storeValues($dao, $defaults);
            $result = $dao;
        }

        return $result;
    }

#   /**
#    * function to add new mail Settings.
#    *
#    * @param array $params reference array contains the values submitted by the form
#    *
#    * @access public
#    * @static
#    * @return object
#    */
#   static function add( &$params )
#   {
#       $result = null;
#       if ( empty($params) ) {
#           return $result;
#       }

#       $params['is_ssl'  ]    = CRM_Utils_Array::value( 'is_ssl', $params, false );
#       $params['is_default' ] = CRM_Utils_Array::value( 'is_default', $params, false);

#       //handle is_default.
#       if ( $params['is_default'] ) {
#           $query = 'UPDATE civicrm_mail_settings SET is_default = 0';
#           CRM_Core_DAO::executeQuery( $query );
#       }

#       $mailSettings =& new CRM_Core_DAO_MailSettings( );
#       $mailSettings->copyValues( $params );
#       $result = $mailSettings->save( );

#       return $result;
#   }

#   /**
#    * Given the list of params in the params array, fetch the object
#    * and store the values in the values array
#    *
#    * @param array $params input parameters to find object
#    * @param array $values output values of the object
#    * @param array $returnProperties  if you want to return specific fields
#    *
#    * @return array associated array of field values
#    * @access public
#    * @static
#    */
#   static function &getValues( &$params, &$values, $returnProperties = null )
#   {
#       CRM_Core_DAO::commonRetrieve('CRM_Core_DAO_MailSettings', $params, $values, $returnProperties );
#       return $values;
#   }

#   /**
#    * takes an associative array and creates a mail settings object
#    *
#    * @param array $params (reference ) an assoc array of name/value pairs
#    *
#    * @return object CRM_Core_BAO_MailSettings object
#    * @access public
#    * @static
#    */
#   static function &create( &$params )
#   {
#       require_once 'CRM/Core/Transaction.php';
#       $transaction = new CRM_Core_Transaction( );

#       $mailSettings = self::add( $params );
#       if ( is_a( $mailSettings, 'CRM_Core_Error') ) {
#           $mailSettings->rollback( );
#           return $mailSettings;
#       }

#       $transaction->commit( );

#       return $mailSettings;
#   }

#   /**
#    * Function to delete the mail settings.
#    *
#    * @param int $id mail settings id
#    *
#    * @access public
#    * @static
#    *
#    */
#   static function deleteMailSettings( $id )
#   {
#       $results = null;
#       require_once 'CRM/Core/Transaction.php';
#       $transaction = new CRM_Core_Transaction( );

#       $mailSettings = new CRM_Core_DAO_MailSettings( );
#       $mailSettings->id = $id;
#       $results = $mailSettings->delete( );

#       $transaction->commit( );

#       return $results;
#   }
}
