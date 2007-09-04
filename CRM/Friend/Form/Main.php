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

require_once 'CRM/Friend/Form/Friend.php';
require_once 'CRM/Friend/BAO/Friend.php';


/**
 * This class generates form components for Tell A Frienf Form For End User
 * 
 */
class CRM_Friend_Form_Main extends CRM_Core_Form
{

     /** 
     * Constants for number of options for data types of multiple option. 
     */ 
    const NUM_OPTION = 3;
    public function preProcess()  
    {  
        
        $this->_action    = CRM_Utils_Request::retrieve( 'action', 'String',
                                                         $this, false, 'add' );
        $this->_id        = CRM_Utils_Request::retrieve( 'id', 'Positive',
                                                         $this );       
        $this->_context   = CRM_Utils_Request::retrieve( 'context', 'String',
                                                         $this );

        if ( $this->_context == 'Contribute' ) {
            $this->_title  = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage', $this->_id, 'title');
        } else {
            $id           = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_EventPage', $this->_id, 'event_id' );
            $this->_title = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event', $id, 'title' );
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
        
        $defaults['entity_id'] = $this->_id;
        if ( $this->_context == 'Contribute' ) {
            $defaults['entity_table'] = 'civicrm_contribution_page';
        } else {
            $defaults['entity_table'] = 'civicrm_event';
        }
         
        CRM_Friend_BAO_Friend::setDefaults($defaults);
        $this->_defaults = $defaults;
        $this->assign( 'title', $defaults['title'] );//crm_core_error::debug('$defaults',$defaults);
        $this->assign( 'intro', $defaults['intro'] );
        $this->assign( 'message', $defaults['suggested_message'] );
        
        // $this->_Ftitle = $defaults['title'];
//         $this->_fid    = $defaults['id'];
        $this->_general_link = $defaults['general_link'];
        
        // foreach ( array('first_name','last_name') as  $v ) {
//             $defaults[$v] = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $this->_contactID, $v);
//         }

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
        $this->add('text', 'first_name', ts('Your First Name'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'first_name'), true);
        $this->add('text', 'last_name',  ts('Your Last Name'),  CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'last_name'),  true);   
        $this->add('text', 'email',      ts('Your Email'),      CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email',       'email'),     true);

        $this->add('textarea', 'suggested_message', ts('Message'), CRM_Core_DAO::getAttribute('CRM_Friend_DAO_Friend', 'suggested_message'), false);            
        
        for ( $i = 1; $i <= self::NUM_OPTION; $i++ ) {           
            $this->add('text', "first_name[$i]", ts("Friend's First Name"));           
            $this->add('text', "last_name[$i]", ts("Friend's Last Name")); 
            $this->add('text', "email[$i]", ts("Friend's Email"));
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
        //$this->addFormRule(array('CRM_Friend_Form_Main', 'formRule'));
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
    public function formRule( &$params ) {
        $errors = array( );$flag = 0;
        for ( $i=1; $i<=self::NUM_OPTION; $i++ ) {
            if ( !$params['first_name'][$i] && !$params['last_name'][$i] && !$params['email'][$i] ) {
                $flag++ ;
            }
            if ($flag == 3) {
                $errors['first_name'] = "Please enter first name,last name,email.";
            }
        } 
            
        
              
        return empty($errors) ? true : $errors;
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
        //crm_core_error::debug('$formValues',$formValues);

        //create params for friend's contact
        $temp = array();
        for ( $i=1; $i<=self::NUM_OPTION; $i++ ) {
            foreach ( $formValues as $k => $v ) {
                if( $k == 'first_name') {
                    $temp[$i][$k] = $v[$i] ;
                } elseif ( $k == 'last_name' ) {
                    $temp[$i][$k] = $v[$i] ;
                }  elseif ( $k == 'email' ) {
                    $temp[$i]['contact_type']   = 'Individual';
                    $temp[$i]['contact_source'] = 'Tell a Friend:'.$this->_title;
                    $temp[$i]['location'][1]['is_primary'] = 1;
                    $temp[$i]['location'][1]['email'][1]['email'] = $v[$i];  
                    $temp[$i]['location'][1]['email'][1]['is_primary'] = 1;
                }
            }
            
        } 
         

        //create params for activity
        $params['source_contact_id'] = $this->_contactID;
        $params['source_record_id']  = NULL;
        $params['activity_type_id']  = $defaults['id']; 
        $params['activity_date']     = date("Ymd"); 
        $params['subject']           = 'Tell a Friend:'.$this->_title;
        $params['details']           = $formValues['suggested_message'];
        //$params['is_test'] = ;

        require_once 'CRM/Contact/BAO/Contact.php';
        require_once 'CRM/Activity/BAO/Activity.php';
        require_once 'CRM/Activity/DAO/ActivityTarget.php';
        $ids = array(); 
        $activity = CRM_Activity_BAO_Activity::createActivity($params,$ids);//crm_core_error::debug('temp',$temp);      
        

        //create friend contacts and entry in activity target table
        $ids = array(); 
        foreach ( $temp as $k => $v ) {
            $contact[$k]            =& CRM_Contact_BAO_Contact::create( $v, $ids, 1, true, false );
            $dao                    = new CRM_Activity_DAO_ActivityTarget;
            $dao->activity_id       = $activity->id;
            $dao->target_contact_id = $contact[$k]->id;
            $dao->save();
        }  
        
        
        //create params for sending mails
        $values['title']        = $this->_title;
        $values['email']        = $formValues['email'];
        $values['general_link'] = $this->_general_link;
        $this->assign( 'registerText', $registerText );
                $url = CRM_Utils_System::url("civicrm/friend/sendmail", "id={$this->_id}&reset=1&action=preview&context=event" );                    
                $this->assign( 'registerURL', $url );

        if( $this->_context = 'contribute' ) {
            $values['email_from'] = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage', $this->_id, 'receipt_from_email', 'id' );
            $$values['page_url'] = CRM_Utils_System::url('civicrm/contribute/transact', "reset=1&action=preview&id=$this->_id");
        } else {
            $values['email_from'] = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_EventPage', $this->_id, 'confirm_from_email' );
            $values['page_url'] = CRM_Utils_System::url('civicrm/event/register', "reset=1&action=preview&id=$this->_id");
        }
        crm_core_error::debug('$values',$values);
        //CRM_Friend_BAO_Friend::sendMail( $this->_contactID, $values, $this->_context );
       
       
    }
}
?>