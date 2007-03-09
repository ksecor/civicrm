<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for groupContact
 * 
 */
class CRM_Contact_Form_GroupContact extends CRM_Core_Form
{

    /**
     * The groupContact id, used when editing the groupContact
     *
     * @var int
     */
    protected $_groupContactId;
    
    /**
     * The contact id, used when add/edit groupContact
     *
     * @var int
     */
    protected $_contactId;
    
    function preProcess( ) 
    {

        $this->_contactId      = $this->get('contactId');
        $this->_groupContactId = $this->get('groupContactId');
        $this->_context        = CRM_Utils_Request::retrieve( 'context', 'String', $this );
    }

    /**
     * This function sets the default values for the form. GroupContact that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        $params   = array( );

        return $defaults;
    }
    

    /**
     * This function is used to add the rules for form.
     *
     * @return None
     * @access public
     */
    function addRules( )
    {

    }


    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        // get the list of all the groups
        $allGroups = CRM_Core_PseudoConstant::group( );

        // get the list of groups for the contact
        $currentGroups = CRM_Contact_BAO_GroupContact::getGroupList($this->_contactId);
        
        if ( is_array( $currentGroups ) ) {
            $groupList = array_diff( $allGroups, $currentGroups );
        } else {
            $groupList = $allGroups;
        }

        $groupList[''] = ts('- select group -') ;
        asort($groupList);

        if ( count( $groupList ) > 1 ) {
            $session =& CRM_Core_Session::singleton();
            // user dashboard
            if ( strstr( $session->readUserContext( ) ,'user') ) {
                $msg = 'Join a Group';            
            } else {
                $msg = 'Add to a group';
            }
            
            $this->add('select'  , 'group_id', $msg, $groupList,true);
            
            $this->addButtons( array(
                                     array ( 'type'      => 'next',
                                             'name'      => ts('Add'),
                                             'isDefault' => true   ),
                                     )
                               );
        }
    }

    
    /**
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $contactID = array($this->_contactId);
        $groupId = $this->controller->exportValue( 'GroupContact', 'group_id'  );
        $groupContact = CRM_Contact_BAO_GroupContact::addContactsToGroup($contactID, $groupId);

        if ($groupContact &&  $this->_context != 'user') {
            CRM_Core_Session::setStatus( ts('Contact has been added to the selected group.') );
        }
    }//end of function


    

}

?>
