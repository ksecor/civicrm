<?php
/*
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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Page/Basic.php';

/**
 * Page for displaying list of Calls
 */
class CRM_Activity_Page extends CRM_Core_Page_Basic 
{

    /**
     * name of the BAO to perform various DB manipulations
     *
     * @return string
     * @access public
     */

    function getBAOName( ) 
    {
    }

    /**
     * an array of action links
     *
     * @return array (reference)
     * @access public
     */
    function &links( )
    {
    }

    /**
     * name of the edit form class
     *
     * @return string
     * @access public
     */
    function editForm( ) 
    {

    }

    /**
     * name of the form
     *
     * @return string
     * @access public
     */
    function editName( ) 
    {

    }

    /**
     * userContext to pop back to
     *
     * @param int $mode mode that we are in
     *
     * @return string
     * @access public
     */
    function userContext( $mode = null ) 
    {
    }


    /**
     * function to get the contact id
     *
     *
     */
    function getContactId () {
        $page =& new CRM_Contact_Page_View();
        return CRM_Utils_Request::retrieve( 'cid', $page);
    }
   
    /**
     * function to get userContext params
     *
     * @param int $mode mode that we are in
     *
     * @return string
     * @access public
     */
    function userContextParams( $mode = null ) {
        return 'action=browse';
    }
  
    /**
     * browse all entities.
     *
     * @param int $action
     */
    function browse($action = null) {
        $links =& $this->links();
        if ($action == null) {
            $action = array_sum(array_keys($links));
        }
        if ( $action & CRM_Core_Action::DISABLE ) {
            $action -= CRM_Core_Action::DISABLE;
        }
        if ( $action & CRM_Core_Action::ENABLE ) {
            $action -= CRM_Core_Action::ENABLE;
        }

        eval( '$object =& new ' . $this->getBAOName( ) . '( );' );

        $values = array();
        
        /*
         * lets make sure we get the stuff sorted by name if it exists
         */
        $fields =& $object->fields( );
        $key = '';
        if ( CRM_Utils_Array::value( 'title', $fields ) ) {
            $key = 'title';
        }  else if ( CRM_Utils_Array::value( 'label', $fields ) ) {
            $key = 'label';
        } else if ( CRM_Utils_Array::value( 'name', $fields ) ) {
            $key = 'name';
        }

        if ( $key ) {
            $object->orderBy ( $key . ' asc' );
        }

        $object->contact_id = self::getContactId();

        //echo "++++++++++============++++++++++";

        // find all objects
        $object->find();
        while ($object->fetch()) {
            $permission = $this->checkPermission( $object->id, $object->$key );
            if ( $permission ) {
                $values[$object->id] = array( );
                CRM_Core_DAO::storeValues( $object, $values[$object->id]);
                // populate action links
                self::action( $object, $action, $values[$object->id], $links, $permission );
            }
        }

        $this->assign( 'rows', $values );
    }
    
}
?>
