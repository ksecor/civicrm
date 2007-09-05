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
     * @param array  $ids    the array that holds all the db ids
     *
     * @return object CRM_Friend_BAO_Friend object
     * @access public
     * @static
     */
    static function add(&$params, &$ids) 
    {
        $friendDAO =& new CRM_Friend_DAO_Friend();
       
        $friendDAO->copyValues($params);
        $friendDAO->id = CRM_Utils_Array::value( 'friend', $ids );
        $result = $friendDAO->save();
        
        return $result;
    }

    /**
     * Given the list of params in the params array, returns the
     * friend id 
     *
     * @param array $id     id of entity
     * @param array $table  name of entity table 
     *
     * @return id|null the found id or null
     * @access public
     * @static
     */
    static function &getFriendId( $id, $table ) 
    {
        $friend =& new CRM_Friend_BAO_Friend( );  
        $friend->entity_id    = $id;  
        $friend->entity_table = $table;
        
        if ( $friend->find(true) ) {
            $ids['friend'] = $friend->id;
            return $ids;
        }      
        
        return null;
    }



    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params input parameters to find object
     * @param array $values output values of the object
     * @param array $ids    the array that holds all the db ids
     *
     * @return CRM_Friend_BAO_Friend|null the found object or null
     * @access public
     * @static
     */
    static function &retrieve( &$params, &$values, &$ids ) 
    {
        $friend =& new CRM_Friend_BAO_Friend( );

        $friend->copyValues( $params );
        
        if ( $friend->find(true) ) {
            $ids['friend']    = $friend->id;            

            CRM_Core_DAO::storeValues( $friend, $values );
            
            return $values;
        }
        return null;
    }

    /**
     * takes an associative array and creates a friend object
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Friend_BAO_Friend object 
     * @access public
     * @static
     */
    static function &create(&$params, &$ids) 
    {
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );
        
        $friend = self::add($params, $ids);
        
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
     * @return None
     * @access public
     */
    function buildFriendForm($this)
    {
       
        $this->addElement('checkbox', 'is_active', ts( 'Tell A Friend enabled?' ),null,array('onclick' =>"friendBlock(this)") );
        // name
        $this->add('text', 'title', ts('Title'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'title'), true);
        
        // intro_text and footer_text
        $this->add('textarea', 'intro', ts('Introductory'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'intro'), true);

        $this->add('textarea', 'suggested_message', ts('Suggested Message'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'suggested_message'), false);

        $this->add('text','general_link',ts('Info Page Link'));
        
        $this->add('text', 'thankyou_title', ts('Thank-you Title'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'thankyou_title'), true );

        $this->add('textarea', 'thankyou_text', ts('Thank-you Message'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'thankyou_text') , true);
        
        $this->addButtons(array( 
                                    array ( 'type'      => 'next',
                                            'name'      => ts('Save'), 
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                            'isDefault' => true   ), 
                                    array ( 'type'      => 'cancel', 
                                            'name'      => ts('Cancel') ), 
                                    ) 
                              );
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
    static function setDefaults(&$defaults)
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
        list( $displayName, $email ) = CRM_Contact_BAO_Contact::getEmailDetails( $contactID );
        $first_name = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $contactID, 'first_name');
        $last_name  = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $contactID, 'last_name');
               
        // set details in the template here
        $template->assign( $values['module'], $values['module'] );        
        $template->assign( 'senderContactFirstName', $first_name ); 
        $template->assign( 'senderContactLastName',  $last_name ); 
        $template->assign( 'title', $values['title'] );
        $template->assign( 'generalLink', $values['general_link'] );
        $template->assign( 'pageURL', $values['page_url'] );
                
        $subject = trim( $template->fetch( 'CRM/Friend/Form/SubjectTemplate.tpl' ) );
        $message = $template->fetch( 'CRM/Friend/Form/MessageTemplate.tpl' );             
        
        require_once 'CRM/Utils/Mail.php';        
        foreach ( $values['email'] as $emailTo ) {
            if ( $emailTo ) {
                CRM_Utils_Mail::send( $values['email_from'],
                                      "",
                                      $emailTo,
                                      $subject,
                                      $message,
                                      null,
                                      null
                                      );
            }
        }            
    }
}

?>
