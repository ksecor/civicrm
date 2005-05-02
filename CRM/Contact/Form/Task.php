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

require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Contact_Form_Task extends CRM_Form
{
    /**
     * the task being performed
     *
     * @var int
     */
    protected $_task;

    /**
     * The rows that hold display data
     *
     * @var array
     */
    protected $_rows;

    /**
     * class constructor
     *
     * @param string $name        Name of the form.
     * @param string $state       The state object associated with this form
     * @param int     $mode       The mode of the form
     *
     * @return CRM_Contact_Form_Task
     * @access public
     */
    function __construct($name, $state, $mode = self::MODE_NONE) 
    {
        parent::__construct($name, $state, $mode);
    }
    
    /**
     * build all the data structures needed to build the form
     *
     * @param none
     * @return void
     * @access public
     */
    function preProcess( ) 
    {
        $session = CRM_Session::singleton( );        

        //CRM_Error::debug_var('session', $session);
        
        // get the submitted values of the search form
        // we'll need to get fv from either search or adv search in the future
        $values = $this->controller->exportValues( 'Search' );

        $this->_task = $values['task'];
        $this->assign( 'taskName', CRM_Contact_Task::$tasks[$this->_task] );

        $this->_rows = array( );

        // radio_ts = radio button for task selection - could be either selected or All search results
        if($values['radio_ts'] == 'ts_sel') {
            // need to perform action on only selected contacts
            foreach ( $values as $name => $value ) {
                if ( substr( $name, 0, self::CB_PREFIX_LEN ) == self::CB_PREFIX ) {
                    $id = substr( $name, self::CB_PREFIX_LEN );
                    $this->_rows[$id] = array( );
                    $this->_rows[$id]['displayName'] = CRM_Contact_BAO_Contact::displayName( $id );
                }
            }
        } else {
            // need to perform action on all contacts
            // fire the query again and get the contact id's + display name
            //$session = CRM_Session::singleton( );        
            $taskQuery = $session->get('tq', CRM_Contact_Form_Search::SESSION_SCOPE_SEARCH);
            $dao = new CRM_DAO();
            $dao->query($taskQuery);
            while($dao->fetch()) {
                $this->_rows[$dao->contact_id] = array( );
                $this->_rows[$dao->contact_id]['displayName'] = $dao->sort_name;
            }
        }
        $this->assign_by_ref( 'rows', $this->_rows );
        $session->set('selectedContacts', $this->_rows);
        
        $this->assign_by_ref( 'totalSelectedContact', count($this->_rows) );
        
    }

    /**
     * This function sets the default values for the form. Relationship that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
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
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => 'Confirm Action!',
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'back',
                                         'name'      => 'Previous' ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => 'Cancel' ),
                                 )
                           );
        
    }

       
    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
    }//end of function

    /**
     * simple shell that derived classes can call to add buttons to
     * the form with a customized title for the main Submit
     *
     * @param string $title title of the main button
     *
     * @return void
     * @access public
     */
    function addDefaultButtons( $title ) {
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => $title,
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'back',
                                         'name'      => 'Previous' ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => 'Cancel' ),
                                 )
                           );
    }

}

?>