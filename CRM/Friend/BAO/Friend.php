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
 * $Id$
 *
 */

require_once 'CRM/Friend/DAO/Friend.php';

/**
 * This class contains the funtions for Friend
 *
 */
class CRM_Friend_BAO_Friend extends CRM_Friend_DAO_Friend
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * takes an associative array and creates a friend object
     *
     * the function extract all the params it needs to initialize the create a
     * friend object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Friend_BAO_Friend object
     * @access public
     * @static
     */
    static function add( &$params ) 
    {
        require_once 'CRM/Contact/BAO/Contact.php';        
        $friend = CRM_Contact_BAO_Contact::createProfileContact( $params, CRM_Core_DAO::$_nullArray );
        return $friend;
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array  $params input parameters to find object
     * @param array  $values output values of the object
     *
     * @return array $values values
     * @access public
     * @static
     */
    static function retrieve( &$params, &$values ) 
    {
        $friend =& new CRM_Friend_DAO_Friend( );

        $friend->copyValues( $params );

        $friend->find(true);
       
        CRM_Core_DAO::storeValues( $friend, $values );
        
        return $values;
    }

    /**
     * takes an associative array and creates a friend object
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Contact_BAO_Contact object 
     * @access public
     * @static
     */
    static function create( &$params ) 
    {
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );

        $mailParams = array( );
        //create contact corresponding to each friend
        foreach ( $params['friend'] as $key => $details ) {
            if ( $details["first_name"] ) {
                $contactParams[$key] = array( 'first_name'     => $details["first_name"],
                                              'last_name'      => $details["last_name"], 
                                              'contact_source' => ts( 'Tell a Friend' ) . ": {$params['title']}",
                                              'email-Primary'  => $details["email"] );
                
                $displayName = $details["first_name"] ." ". $details["last_name"];
                $mailParams['email'][$displayName] = $details["email"];
            }
        }

        $frndParams = array( );
        $frndParams['entity_id']    = $params['entity_id'];
        $frndParams['entity_table'] = $params['entity_table'];  
        self::getValues($frndParams);  
        
        require_once 'CRM/Activity/BAO/Activity.php';

        $activityTypeId = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', 'Tell a Friend', 'value', 'name' );

        //create activity 
        $activityParams = array ( 'source_contact_id'  => $params['source_contact_id'],
                                  'source_record_id'   => NULL,
                                  'activity_type_id'   => $activityTypeId,
                                  'title'              => $params['title'],
                                  'activity_date_time' => date("YmdHis"), 
                                  'subject'            => ts( 'Tell a Friend' ) . ": {$params['title']}",
                                  'details'            => $params['suggested_message'],
                                  'status_id'          => 2,
                                  'is_test'            => $params['is_test'] );
        
        //activity creation
        $activity = CRM_Activity_BAO_Activity::create( $activityParams );

        //friend contacts creation
        require_once 'CRM/Activity/BAO/ActivityTarget.php';
        require_once 'CRM/Core/BAO/UFGroup.php';
        foreach ( $contactParams as $key => $value ) {
            
            //create contact only if it does not exits in db
            $value['email'] = $value['email-Primary'];
            $contact = CRM_Core_BAO_UFGroup::findContact( $value, null, 'Individual' );

            if ( !$contact ) {
                $contact = self::add( $value );
            }
            
            // attempt to save activity targets                       
            $targetParams = array( 'activity_id'       => $activity->id,
                                   'target_contact_id' => $contact );            
            
            $resultTarget = CRM_Activity_BAO_ActivityTarget::create( $targetParams );           
        }
        
        $transaction->commit( );

        //process sending of mails
        $mailParams['title']        = $params['title'];       
        $mailParams['general_link'] = $frndParams['general_link'];
        $mailParams['message']      = $params['suggested_message'];
        
        if ( $params['entity_table'] == 'civicrm_contribution_page' ) {
            $mailParams['email_from'] = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage',
                                                                     $params['entity_id'],
                                                                     'receipt_from_email',
                                                                     'id' );
            $urlPath = 'civicrm/contribute/transact';
            $mailParams['module'] = 'contribute';
        } elseif ( $params['entity_table'] == 'civicrm_event_page' ) {
            $mailParams['email_from'] = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_EventPage',
                                                                     $params['entity_id'],
                                                                     'confirm_from_email' );
            $urlPath = 'civicrm/event/info';
            $mailParams['module'] = 'event';
        } 

        $mailParams['page_url'] = CRM_Utils_System::url($urlPath, "reset=1&id={$params['entity_id']}", true, null, false);
        list( $username, $mailParams['domain'] ) = split( '@', $mailParams['email_from'] );
       
        //send mail
        self::sendMail( $params['source_contact_id'], $mailParams ); 
        
    }

    /**
     * Function to build the form
     *
     * @param object $form form object
     *
     * @return None
     * @access public
     */
    function buildFriendForm( $form )
    {
        $form->addElement('checkbox', 'is_active', ts( 'Tell A Friend enabled?' ),null,array('onclick' =>"friendBlock(this)") );
        // name
        $form->add('text', 'title', ts('Title'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'title'), true);
        
        // intro-text and thank-you text
        $form->addWysiwyg('intro', ts('Introduction'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'intro'), true);
        
        $form->add('textarea', 'suggested_message', ts('Suggested Message'), 
                   CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'suggested_message'), false);

        $form->add('text','general_link',ts('Info Page Link'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'general_link'));
        
        $form->add('text', 'thankyou_title', ts('Thank-you Title'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'thankyou_title'), true );

        $form->addWysiwyg('thankyou_text', ts('Thank-you Message'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'thankyou_text') , true);
    }
    
    /**
     * The function sets the deafult values of the form.
     *
     * @param array   $defaults (reference) the default values.
     *
     * @return void
     * @access public
     * @static
     */
    static function getValues( &$defaults )
    {
        $friend =& new CRM_Friend_BAO_Friend( );
        $friend->copyValues( $defaults );        
        $friend->find(true) ;           
        CRM_Core_DAO::storeValues( $friend, $defaults );
    }  

    /**
     * Process that send tell a friend e-mails
     *
     * @params int     $contactId      contact id
     * @params array   $values         associative array of name/value pair
     * @return void
     * @access public
     */
    static function sendMail( $contactID, &$values )
    {   
        $template =& CRM_Core_Smarty::singleton( );
        
        require_once 'CRM/Contact/BAO/Contact.php';
        list( $fromName, $email ) = CRM_Contact_BAO_Contact::getContactDetails( $contactID );
        // if no $fromName (only email collected from originating contact) - list returns single space
        if ( trim($fromName) == '') {
            $fromName = $email;
        }

        // set details in the template here
        $template->assign( $values['module']  , $values['module'] );
        $template->assign( 'senderContactName', $fromName ); 
        $template->assign( 'title',             $values['title'] );
        $template->assign( 'generalLink',       $values['general_link'] );
        $template->assign( 'pageURL',           $values['page_url'] );
        $template->assign( 'senderMessage',     $values['message'] );
                
        $subject = trim( $template->fetch( 'CRM/Friend/Form/SubjectTemplate.tpl' ) );
        $message = $template->fetch( 'CRM/Friend/Form/MessageTemplate.tpl' ); 

        $emailFrom = '"' . $fromName. ' (via '.$values['domain']. ')'. '" <' . $values['email_from'] . '>';
        
        require_once 'CRM/Utils/Mail.php';        
        foreach ( $values['email'] as $displayName => $emailTo ) {
            if ( $emailTo ) {
                CRM_Utils_Mail::send( $emailFrom,
                                      $displayName,
                                      $emailTo,
                                      $subject,
                                      $message,
                                      null,
                                      null,
                                      $email
                                      );
            }
        }            
    }

    /**
     * takes an associative array and creates a tell a friend object
     *
     * the function extract all the params it needs to initialize the create/edit a
     * friend object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Friend_BAO_Friend object
     * @access public
     * @static
     */
    static function addTellAFriend( &$params ) 
    {
        $friendDAO =& new CRM_Friend_DAO_Friend();
       
        $friendDAO->copyValues($params);
        $friendDAO->is_active  = CRM_Utils_Array::value( 'is_active', $params, false );
      
        $friendDAO->save();
        
        return $friendDAO;
    }
}


