<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
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
            $this->addElement('select'  , 'group_id', ts('Add to a group'), $groupList );
            $this->addRule('group_id',ts('Please select the group.'), 'required');

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
        // store the submitted values in an array
//         commented for CRM - 721

//         $params = $this->exportValues();
//         $params['contact_id'] = $this->_contactId;
//         $params['status']     = 'Added';
//         $params['method']  = 'Admin';
//         $params['date']    = date('YmdHis');
//         $groupContact = CRM_Contact_BAO_GroupContact::add($params);
        
        $contactID = array($this->_contactId);
        $groupId = $this->controller->exportValue( 'GroupContact', 'group_id'  );
        $groupContact = CRM_Contact_BAO_GroupContact::addContactsToGroup($contactID, $groupId);
        
        if ($groupContact) {
            CRM_Core_Session::setStatus( ts('Contact has been added to the selected group.') );
        }
    }//end of function


    

}

?>
