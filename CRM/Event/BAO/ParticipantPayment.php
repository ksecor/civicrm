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
   *
   * @package CRM
   * @copyright CiviCRM LLC (c) 2004-2007
   * $Id$
   *
   */

require_once 'CRM/Event/DAO/ParticipantPayment.php';

class CRM_Event_BAO_ParticipantPayment extends CRM_Event_DAO_ParticipantPayment
{
  
    static function &create(&$params, &$ids) 
    { 
        $paymentParticipant =& new CRM_Event_BAO_ParticipantPayment(); 
        $paymentParticipant->copyValues($params);
        $paymentParticipant->id = CRM_Utils_Array::value( 'id', $ids );
        $paymentParticipant->save();

        return $paymentParticipant;
    }

    
    /**                          
     * Delete the record that are associated with this Participation Payment
     * 
     * @param  array  $params   array in the format of $field => $value. 
     * 
     * @return boolean  true if deleted false otherwise
     * @access public 
     */ 
    static function deleteParticipantPayment( $params ) 
    {
        require_once 'CRM/Event/DAO/ParticipantPayment.php';
        $participantPayment = & new CRM_Event_DAO_ParticipantPayment( );

        foreach ( $params as $field => $value ) {
            $participantPayment->$field  = $value;
        }
        if ( ! $participantPayment->find( ) ) {
            return false;
        }
        
        while ( $participantPayment->fetch() ) {
            require_once 'CRM/Event/BAO/Participant.php';
            CRM_Event_BAO_Participant::deleteParticipantSubobjects( $participantPayment->contribution_id );
            $participantPayment->delete( ); 
        }
        
        return $participantPayment;
    }
}

