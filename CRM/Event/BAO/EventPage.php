<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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

require_once 'CRM/Event/DAO/EventPage.php';

class CRM_Event_BAO_EventPage extends CRM_Event_DAO_EventPage 
{

    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }
    
    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Event_BAO_ManageEvent object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $eventPage  = new CRM_Event_DAO_EventPage( );
        $eventPage->copyValues( $params );
        if ( $eventPage->find( true ) ) {
            CRM_Core_DAO::storeValues( $eventPage, $defaults );
            return $eventPage;
        }
        return null;
    }

    /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * @static
     */
    static function setIsActive( $id, $is_active ) 
    {
        return CRM_Core_DAO::setFieldValue( 'CRM_Event_DAO_EventPage', $id, 'is_active', $is_active );
    }
    
    /**
     * function to add the eventship types
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add( &$params ) 
    {
        $eventPage            =& new CRM_Event_DAO_EventPage( );
        $eventPage->event_id  =  CRM_Utils_Array::value( 'event_id', $params );
        $eventPage->find(true);
        $eventPage->copyValues( $params );
        $eventPage->save( );
        return $eventPage;
    }

    /**
     * Process that send e-mails
     *
     * @return void
     * @access public
     */
    static function sendMail( $contactID, &$values, $participantId ) 
    {
        require_once 'CRM/Core/BAO/UFGroup.php';
        //this condition is added, since same contact can have
        //multiple event registrations..       
        $params = array( array( 'participant_id', '=', $participantId, 0, 0 ) );
        $gIds = array(
                    'custom_pre_id' => $values['custom_pre_id'],
                    'custom_post_id'=> $values['custom_post_id']
                    );
        
        //send notification email if field values are set (CRM-1941)
        foreach ($gIds as $gId) {
            $val = CRM_Core_BAO_UFGroup::checkFieldsEmptyValues($gId,$contactID,$params);         
            CRM_Core_BAO_UFGroup::commonSendMail($contactID, $val);
        }        
            
        if ( $values['event_page']['is_email_confirm'] ) {
            $template =& CRM_Core_Smarty::singleton( );
            require_once 'CRM/Contact/BAO/Contact.php';

            // get the billing location type
            $locationTypes =& CRM_Core_PseudoConstant::locationType( );
            $bltID = array_search( 'Billing',  $locationTypes );

            list( $displayName, $email ) = CRM_Contact_BAO_Contact::getEmailDetails( $contactID, $bltID );
            self::buildCustomDisplay( $values['custom_pre_id'] , 'customPre' , $contactID, $template, $participantId );
            self::buildCustomDisplay( $values['custom_post_id'], 'customPost', $contactID, $template, $participantId );

            // set confirm_text and contact email address for display in the template here
            $template->assign( 'email', $email );
            $template->assign( 'confirm_email_text', $values['event_page']['confirm_email_text'] );
           
            $isShowLocation = CRM_Utils_Array::value('is_show_location',$values['event']);
            $template->assign( 'isShowLocation', $isShowLocation );

            $subject = trim( $template->fetch( 'CRM/Event/Form/Registration/ReceiptSubject.tpl' ) );
            $message = $template->fetch( 'CRM/Event/Form/Registration/ReceiptMessage.tpl' );
            $receiptFrom = '"' . $values['event_page']['confirm_from_name'] . '" <' . $values['event_page']['confirm_from_email'] . '>';
            
            require_once 'CRM/Utils/Mail.php';
            CRM_Utils_Mail::send( $receiptFrom,
                                  $displayName,
                                  $email,
                                  $subject,
                                  $message,
                                  $values['event_page']['cc_confirm'],
                                  $values['event_page']['bcc_confirm']
                                  );
        }
    }

    /**  
     * Function to add the custom fields
     *  
     * @return None  
     * @access public  
     */ 
    function buildCustomDisplay( $gid, $name, $cid, &$template, $participantId ) 
    {
        if ( $gid ) {
            require_once 'CRM/Core/BAO/UFGroup.php';
            if ( CRM_Core_BAO_UFGroup::filterUFGroups($gid, $cid) ){
                $values = array( );
                $groupTitle = null;
                $fields = CRM_Core_BAO_UFGroup::getFields( $gid, false, CRM_Core_Action::VIEW );

                //this condition is added, since same contact can have multiple event registrations..
                $params = array( array( 'participant_id', '=', $participantId, 0, 0 ) );

                CRM_Core_BAO_UFGroup::getValues( $cid, $fields, $values , false, $params );
                foreach( $fields as $v  ) {
                    $groupTitle = $v["groupTitle"];
                }
                if ( $groupTitle ) {
                    $template->assign( $name."_grouptitle", $groupTitle );
                }
                if ( count( $values ) ) {
                    $template->assign( $name, $values );
                }
            }
        }
    }    
}

?>
