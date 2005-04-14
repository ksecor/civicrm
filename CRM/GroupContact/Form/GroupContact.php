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

require_once 'CRM/SelectValues.php';
require_once 'CRM/Form.php';

/**
 * This class generates form components for groupContact
 * 
 */
class CRM_GroupContact_Form_GroupContact extends CRM_Form
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
    

    /**
     * class constructor
     *
     * @param string $name        Name of the form.
     * @param string $state       The state object associated with this form
     * @param int    $mode        The mode of the form
     *
     * @return CRM_GroupContact_Form_GroupContact
     * @access public
     */
    function __construct($name, $state, $mode = self::MODE_NONE) 
    {
        parent::__construct($name, $state, $mode);
    }
    
    function preProcess( ) 
    {

        $this->_contactId   = $this->get('contactId');
        $this->_groupContactId    = $this->get('groupContactId');
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
        $aGroup = $aGroupContact = array ();
        // get the list of all the groups
        $aGroup = $this->getGroupList();

        // get the list of groups for the contact
        $aGroupContact = $this->getGroupList(true);
        
        if (is_array($aGroupContact)) {
            $aGrouplist = array_diff ($aGroup,$aGroupContact);
        } else {
            $aGrouplist = $aGroup;
        }

        $aGrouplist[0] = "- select group -" ;

        asort($aGrouplist);

        if (count($aGrouplist) > 1) {
            $this->addElement('select', 'allgroups', 'Add to another group:', $aGrouplist );
            $this->addElement('checkbox', 'antichk', 'Anti-spam \'disclaimer\' (tbd)');
            
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
    public function postProcess() 
    {

        // store the submitted values in an array
        $params = $this->exportValues();
        //        print_r($params);

        /*
        // action is taken depending upon the mode
        $ids = array( );
        $ids['contact'] = $this->_contactId;
        if ($this->_mode & self::MODE_UPDATE ) {
            $ids['groupContact'] = $this->_groupContactId;
        }    

        $groupContact = CRM_Contact_BAO_GroupContact::create( $params, $ids );

        $session = CRM_Session::singleton( );
        if ($groupContact->id) {
            $session->setStatus( 'Your Group(s) record has been saved.' );
        }
        */
    }//end of function


    /**
     * This function is to get list of all the groups
     *
     * param  bolean $lngStatus true give the list of groups for contact. false gives all the groups
     *
     * @access public
     * @return None
     *
     */
    function getGroupList($lngStatus = false) {
        
        $group = new CRM_Contact_DAO_Group( );

        $str_select = $str_from = $str_where = '';
        
        $str_select = "SELECT crm_group.id, crm_group.name ";
        $str_from = " FROM crm_group, crm_group_contact ";
        $str_where = " WHERE crm_group.group_type='static'";
        if ($lngStatus) {
            $str_where .= " AND crm_group.id = crm_group_contact.group_id 
                       AND crm_group_contact.contact_id = ".$this->_contactId;
        }

        $str_orderby = " ORDER BY crm_group.name";
        $str_sql = $str_select.$str_from.$str_where.$str_orderby;

        $group->query($str_sql);

        while($group->fetch()) {
            $values[$group->id] = $group->name;
        }
        
        return $values;
    }
    

}

?>
