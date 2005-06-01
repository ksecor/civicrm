<?php
/**
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */


require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/PseudoConstant.php';
require_once 'CRM/Contact/BAO/GroupContact.php';
require_once 'CRM/Core/Session.php';
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
    var $_groupContactId;
    
    /**
     * The contact id, used when add/edit groupContact
     *
     * @var int
     */
    var $_contactId;
    
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
     function buildQuickForm( ) 
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

        $groupList[0] = "- select group -" ;
        asort($groupList);

        if ( count( $groupList ) > 1 ) {
            $this->addElement('select'  , 'group_id', 'Add to a group', $groupList );
            
            $this->addButtons( array(
                                     array ( 'type'      => 'next',
                                             'name'      => 'Add',
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
     function postProcess() 
    {
        // store the submitted values in an array
        $params = $this->exportValues();
        
        $params['contact_id'] = $this->_contactId;
        $params['status']     = 'In';
        $params['in_method']  = 'Admin';
        $params['in_date']    = date('Ymd');
        
        CRM_Contact_BAO_GroupContact::add($params);
       
        CRM_Core_Session::setStatus( 'Contact has been added to the selected group.' );
    }//end of function


    

}

?>
