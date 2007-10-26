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
        
        $frndParams = array( );
        $frndParams['entity_id']    = $params['entity_id'];
        $frndParams['entity_table'] = $params['entity_table'];  
        self::getValues($frndParams);  
              
        //create params for activity
        $activityParams['source_contact_id']  = $params['source_contact_id'];
        $activityParams['source_record_id']   = NULL;        
        $activityParams['activity_type_id']   = $frndParams['id'];
        $activityParams['title']              = $params['title'];
        $activityParams['activity_date_time'] = date("Ymd"); 
        $activityParams['subject']            = 'Tell a Friend:'.$params['title'];
        $activityParams['details']            = $params['suggested_message'];
        $activityParams['is_test']            = $params['is_test'] ;

        require_once 'CRM/Contact/BAO/Contact.php';
        require_once 'CRM/Activity/BAO/Activity.php';
        require_once 'CRM/Activity/DAO/ActivityTarget.php';
        
        //activity creation
        $bao = new CRM_Activity_BAO_Activity;     
        $activity = $bao->createActivity($activityParams, CRM_Core_DAO::$_nullArray);

        //create params for friend contacts
        foreach ( $params['friend'] as $key => $details ) {
            foreach ( $details as $dontCare ) {
                if( $details["first_name"] ) {
                    $contactParams[$key] = array( 'first_name'     => $details["first_name"],
                                                  'last_name'      => $details["last_name"], 
                                                  'contact_source' => ts( 'Tell a Friend:' ) . $params['title'],
                                                  'email-Primary'  => $details["email"] );  
                  
                    $values['email'][$key] = $details["email"];
                }
            }
        }
        
        //friend contacts creation
        foreach ( $contactParams as $key => $value ) {
            $friend[$key] =  self::add( $value, CRM_Core_DAO::$_nullArray );
             
            //create activity for friends
            $dao                    =& new CRM_Activity_DAO_ActivityTarget;
            $dao->activity_id       = $activity->id;
            $dao->target_contact_id = $friend[$key];
            $dao->save();
        }
        
        //create params for sending mails
        $values['title']        = $params['title'];       
        $values['general_link'] = $frndParams['general_link'];
        $values['message']      = $params['suggested_message'];
        
        if ( $params['entity_table'] == 'civicrm_contribution_page' ) {
            $values['email_from'] = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage', $params['entity_id'], 'receipt_from_email', 'id' );            
            $values['page_url'] = CRM_Utils_System::url('civicrm/contribute/transact', "reset=1&id={$params['entity_id']}");
            $values['module']   = 'contribute';
        } elseif ( $params['entity_table'] == 'civicrm_event' ) {
            $values['email_from'] = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_EventPage', $params['entity_id'], 'confirm_from_email' );
            $values['page_url'] = CRM_Utils_System::url('civicrm/event/info', "reset=1&id={$params['entity_id']}");
            $values['module']   = 'event';
        } 
        list( $username, $values['domain'] ) = split( '@', $values['email_from'] );
        
        //send mail
        self::sendMail( $params['source_contact_id'], $values ); 

        $session =& CRM_Core_Session::singleton( );
        CRM_Utils_System::redirect( $session->popUserContext() );

        if ( is_a( $friend, 'CRM_Core_Error') ) {
            $transaction->rollback( );
            return $friend;
        }
        
        $transaction->commit( );
        
        return $friend;
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
        $form->add('textarea', 'intro', ts('Introductory'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'intro'), true);

        $form->add('textarea', 'suggested_message', ts('Suggested Message'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'suggested_message'), false);

        $form->add('text','general_link',ts('Info Page Link'));
        
        $form->add('text', 'thankyou_title', ts('Thank-you Title'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'thankyou_title'), true );

        $form->add('textarea', 'thankyou_text', ts('Thank-you Message'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'thankyou_text') , true);
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
     * @params string  $module         Contribution OR Event
     * @return void
     * @access public
     */
    static function sendMail( $contactID, &$values )
    {   
        $template =& CRM_Core_Smarty::singleton( );
        
        require_once 'CRM/Contact/BAO/Contact.php';
        
        $first_name = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $contactID, 'first_name');
        $last_name  = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $contactID, 'last_name');
        $email      = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Email',      $contactID, 'email', 'contact_id');
          
        // set details in the template here
        $template->assign( $values['module'], $values['module'] );        
        $template->assign( 'senderContactFirstName', $first_name ); 
        $template->assign( 'senderContactLastName',  $last_name ); 
        $template->assign( 'title', $values['title'] );
        $template->assign( 'generalLink', $values['general_link'] );
        $template->assign( 'pageURL', $values['page_url'] );
        $template->assign( 'senderMessage', $values['message'] );
                
        $subject = trim( $template->fetch( 'CRM/Friend/Form/SubjectTemplate.tpl' ) );
        $message = $template->fetch( 'CRM/Friend/Form/MessageTemplate.tpl' ); 

        $emailFrom = '"' . $first_name.' '. $last_name.' (via '.$values['domain']. ')'. '" <' . $values['email_from'] . '>';
        
        require_once 'CRM/Utils/Mail.php';        
        foreach ( $values['email'] as $emailTo ) {
            if ( $emailTo ) {
                CRM_Utils_Mail::send( $emailFrom,
                                      "",
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
    static function addTellAFriend(&$params) 
    {
        $friendDAO =& new CRM_Friend_DAO_Friend();
        $friendDAO->entity_id   = $params['entity_id'];
        $friendDAO->enity_table = $params['entity_table'];
        
        $friendDAO->find( true );
        
        $friendDAO->copyValues($params);
        $result = $friendDAO->save();
        
        return $result;
    }
}

?>
