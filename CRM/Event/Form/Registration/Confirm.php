<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org. If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Event/Form/ManageEvent.php';

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_Registration_Confirm extends CRM_Core_Form
{
    /**
     * the values for the contribution db object
     *
     * @var array
     * @protected
     */
    public $_values;

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) {
        $this->_id = $this->get('id');
    }

    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    { 
        $eventPage = array( );
        $params = array( 'event_id' => $this->_id );
        $confirm =  $this->get('registrationValue');
        $this->assign('confirm',$confirm);
                   
        $this->addButtons(array(
                                array ( 'type'      => 'back',
                                        'name'      => ts('<< Previous') ),
                                array ( 'type'      => 'next',
                                        'name'      => ts('Continue'),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );
    }
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $session =& CRM_Core_Session::singleton( );
        $contactID = $session->get( 'userID' );
        if ($contactID) {
            // updateContactRecord here;
        } else {
            // finding contact record based on duplicate match 
            $params = array();
            
            $firstName = $params['first_name'];// = 'deepak';
            $lastName = $params['last_name'];// = 'srivastava';
            $email = $params['email'];// = 'deepak@webaccess.co.in';
            
            require_once 'CRM/Contact/BAO/Contact.php';
            $tables = array('civicrm_email' => "civicrm_email.email='deepak@webaccess.co.in' AND civicrm_location.id = civicrm_email.location_id AND civicrm_email.is_primary = 1",
                            'civicrm_individual' => "civicrm_individual.first_name='deepak' AND civicrm_individual.last_name='srivastava' AND contact_a.id = civicrm_individual.contact_id",
                            );
            $query  = "SELECT DISTINCT contact_a.id as id";
            $query .= CRM_Contact_BAO_Query::fromClause( $tables, array('civicrm_email' => 1) );
            $dao =& CRM_Core_DAO::executeQuery( $query, $params );
            while ( $dao->fetch( ) ) {
                $contactID = $dao->id;
            }
        }
        
    }//end of function
    
    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) 
    {
        return ts('Event Confirmation');
    }
    
}
?>
