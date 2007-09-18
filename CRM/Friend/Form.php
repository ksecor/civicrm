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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Friend/BAO/Friend.php';

/**
 * This class generates form components for Tell A Frienf Form For End User
 * 
 */
class CRM_Friend_Form extends CRM_Core_Form
{

     /** 
     * Constants for number of friend contacts
     */ 
    const NUM_OPTION = 3;
    /**
     * the id of the entity that we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_entityId;

    /**
     * the table name of the entity that we are proceessing
     *
     * @var string
     * @protected
     */
    protected $_entityTable;

    /**
     * the contact ID
     *
     * @var int
     * @protected
     */
    protected $_contactID;

    public function preProcess( )  
    {  
        $this->_entityId     = CRM_Utils_Request::retrieve( 'eid', 'Positive',
                                                            $this );       
        $this->_entityTable  = CRM_Utils_Request::retrieve( 'etable', 'String',
                                                            $this );
        $this->_action       = CRM_Utils_Request::retrieve( 'action', 'String', 
                                                            $this );
               
        if ( $this->_entityTable == 'civicrm_contribution_page' ) {
            $this->_title = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage', $this->_entityId, 'title');
        } elseif( $this->_entityTable == 'civicrm_event' ) {
            $this->_title = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event', $this->_entityId, 'title' );            
        }
       
        $session =& CRM_Core_Session::singleton( );
        $this->_contactID = $session->get( 'userID' );
        
    }

    /**
     * This function sets the default values for the form. 
     * 
     * @access public
     * @return None
     */
    public function setDefaultValues( ) {
        $defaults = array( );      
        
        $defaults['entity_id']    = $this->_entityId;
        $defaults['entity_table'] = $this->_entityTable;   
         
        CRM_Friend_BAO_Friend::setDefaults($defaults);
        CRM_Utils_System::setTitle(ts($defaults['title']));

        $this->assign( 'title', $defaults['title'] );
        $this->assign( 'intro', $defaults['intro'] );
        $this->assign( 'message', $defaults['suggested_message'] );

        $defaults['first_name_user'] = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $this->_contactID, 'first_name');
        $defaults['last_name_user']  = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $this->_contactID, 'last_name');
        $defaults['email_user']      = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Email',      $this->_contactID, 'email', 'contact_id');
       
        return $defaults;
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
       
        // Details of User        
        $this->add('text', 'first_name_user', ts('Your First Name'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'first_name'), true);
        $this->add('text', 'last_name_user',  ts('Your Last Name'),  CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'last_name'), true);   
        $this->add('text', 'email_user', ts('Your Email'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email','email'), true);
        $this->add('textarea', 'suggested_message', ts('Message'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'suggested_message'), false);            
        
        for ( $i = 1; $i <= self::NUM_OPTION; $i++ ) {           
            $this->add('text', "first_name[$i]", ts("Friend's First Name"));           
            $this->add('text', "last_name[$i]", ts("Friend's Last Name")); 
            $this->add('text', "email[$i]", ts("Friend's Email"));
            $this->addRule( "email[$i]", ts('Email is not valid.'), 'email' );
        }
       
        $this->addButtons(array( 
                                array ( 'type'      => 'next',
                                        'name'      => ts('Send Your Message'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );
        $this->addFormRule(array('CRM_Friend_Form', 'formRule'));
    }
    
    
    /**
     * Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    public function formRule( &$values ) {
        $errorMsg = array( );  

        $check = 0;
        foreach ( $values['first_name'] as $key => $val ) {
            if ( trim($val) && trim($values['last_name'][$key]) && trim($values['email'][$key]) ) {
                $check++;
                break;
            }
        }

        if ( !$check ) {
            if ( !$values['first_name'][1] ) {
                $errorMsg['first_name[1]'] = "Please enter first name for at least one contact.";
            }
            if ( !$values['last_name'][1] ) {
                $errorMsg['last_name[1]'] = "Please enter last name for at least one contact.";
            }
            
            if ( !$values['email'][1] ) {
                $errorMsg['email[1]'] = "Please enter email for at least one contact.";
            }
        }
        
        return empty($errorMsg) ? true : $errorMsg;
    }

       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );
        
        //create params for friend's contact
        $friends = array();
        for ( $i=1; $i<=self::NUM_OPTION; $i++ ) {
            foreach ( $formValues as $k => $v ) {
                if( $k == 'first_name' && $v[$i] ) {
                    $friends[$i][$k] = $v[$i] ;
                } elseif ( $k == 'last_name' && $v[$i] ) {
                    $friends[$i][$k] = $v[$i] ;
                }  elseif ( $k == 'email' && $v[$i] ) {                    
                    $friends[$i]['email'] = $v[$i];                      
                }
            }            
        }   
        
        $frndParams = array( );
        $frndParams['entity_id']    = $this->_entityId;
        $frndParams['entity_table'] = $this->_entityTable;  
        CRM_Friend_BAO_Friend::setDefaults($frndParams);  
              
        //create params for activity
        $params['source_contact_id']  = $this->_contactID;
        $params['source_record_id']   = NULL;
        $params['activity_type_id']   = $frndParams['id'];
        $params['activity_date_time'] = date("Ymd"); 
        $params['subject']            = 'Tell a Friend:'.$this->_title;
        $params['details']            = $formValues['suggested_message'];
        $params['is_test']            = $this->_action ? 1 : 0 ;

        require_once 'CRM/Contact/BAO/Contact.php';
        require_once 'CRM/Activity/BAO/Activity.php';
        require_once 'CRM/Activity/DAO/ActivityTarget.php';
        $ids = array(); 
        $activity = CRM_Activity_BAO_Activity::createActivity($params,$ids);     
        
        //create friend contacts and entry in activity target table        
        foreach ( $friends as $k => $v ) {
            if ( count($v) == 3 ) {
                //checking if first name, last name and email is present
                $v['contact_type']   = 'Individual';
                $v['contact_source'] = 'Tell a Friend:'.$this->_title;
                $v['location'][1]['is_primary'] = 1;
                $v['location'][1]['email'][1]['email'] = $v['email']; 
                $v['location'][1]['email'][1]['is_primary'] = 1;
                unset($v['email']);

                //create friend's contact
                $ids = array(); 
                $contact[$k]            =& CRM_Contact_BAO_Contact::create( $v, $ids, 1, true, false );

                //create activity for friends
                $dao                    =& new CRM_Activity_DAO_ActivityTarget;
                $dao->activity_id       = $activity->id;
                $dao->target_contact_id = $contact[$k]->id;
                $dao->save();
            }  
        }
        
        //create params for sending mails
        $values['title']        = $this->_title;
        $values['email']        = $formValues['email'];
        $values['general_link'] = $frndParams['general_link'];
        $values['message']      = $formValues['suggested_message'];
        
        if ( $this->_entityTable == 'civicrm_contribution_page' ) {
            $values['email_from'] = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage', $this->_entityId, 'receipt_from_email', 'id' );            
            $values['page_url'] = CRM_Utils_System::url('civicrm/contribute/transact', "reset=1&id=$this->_entityId");
            $values['module']   = 'contribute';
        } elseif ( $this->_entityTable == 'civicrm_event' ) {
            $values['email_from'] = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_EventPage', $this->_entityId, 'confirm_from_email' );
            $values['page_url'] = CRM_Utils_System::url('civicrm/event/info', "reset=1&id=$this->_entityId");
            $values['module']   = 'event';
        } 
        list( $username, $values['domain'] ) = split( '@', $values['email_from'] );
        
        CRM_Friend_BAO_Friend::sendMail( $this->_contactID, $values ); 
        $session =& CRM_Core_Session::singleton( );
        CRM_Utils_System::redirect( $session->popUserContext() );
    }
}
?>