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
class CRM_Contact_Form_Task extends CRM_Core_Form
{
    /**
     * the task being performed
     *
     * @var int
     */
    protected $_task;

    /**
     * The array that holds all the contact ids
     *
     * @var array
     */
    protected $_contactIds;

    /**
     * build all the data structures needed to build the form
     *
     * @param none
     * @return void
     * @access public
     */
    function preProcess( ) 
    {
        $this->_contactIds = array( );

        // get the submitted values of the search form
        // we'll need to get fv from either search or adv search in the future
        if ( $this->_mode == CRM_Core_Form::MODE_ADVANCED ) {
            $values = $this->controller->exportValues( 'Advanced' );
        } else {
            $values = $this->controller->exportValues( 'Search' );
        }
        
        $this->_task = $values['task'];
        $this->assign( 'taskName', CRM_Contact_Task::$tasks[$this->_task] );

        // all contacts or action = save a search
        if (($values['radio_ts'] == 'ts_all') || ($this->_task == CRM_Contact_Task::SAVE_SEARCH)) {
            // need to perform action on all contacts
            // fire the query again and get the contact id's + display name
            $contact = new CRM_Contact_BAO_Contact();
            $fv = $this->get( 'formValues' );
            $result = $contact->searchQuery( $fv, 0, 0, null );
            while ( $result->fetch( ) ) {
                $this->_contactIds[] = $result->contact_id;
            }
        } else if($values['radio_ts'] == 'ts_sel') {
            // selected contacts only
            // need to perform action on only selected contacts
            foreach ( $values as $name => $value ) {
                if ( substr( $name, 0, self::CB_PREFIX_LEN ) == self::CB_PREFIX ) {
                    $this->_contactIds[] = substr( $name, self::CB_PREFIX_LEN );
                }
            }
        }

        $this->assign( 'totalSelectedContacts', count( $this->_contactIds ) );
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
        $this->addDefaultButtons('Confirm Action');        
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
     * @param string $type  button type for the form after processing
     * @return void
     * @access public
     */
    function addDefaultButtons( $title, $type = 'next' ) {
        $this->addButtons( array(
                                 array ( 'type'      => $type,
                                         'name'      => $title,
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'back',
                                         'name'      => 'Cancel' ),
                                 )
                           );
    }

}

?>