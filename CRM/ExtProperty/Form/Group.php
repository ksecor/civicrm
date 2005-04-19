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

require_once 'CRM/Form.php';

/**
 * form to process actions on the group aspect of ExtProperty
 */
class CRM_ExtProperty_Form_Group extends CRM_Form {

    /**
     * the group id saved to the session for an update
     *
     * @var int
     */
    protected $_groupId;

    /**
     * class constructor
     */
    function __construct($name, $state, $mode = self::MODE_NONE) {
        parent::__construct($name, $state, $mode);
    }

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess( ) {
        $this->_groupId = $this->get( 'groupId' );
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        $this->add( 'text'  , 'title'      , 'Group Name',
                    CRM_DAO::getAttribute( 'CRM_DAO_ExtPropertyGroup', 'title'       ), true );
        $this->addRule( 'title', 'Please enter a valid name.', 'title' );

        $this->add( 'text'  , 'description', 'Group Description',
                    CRM_DAO::getAttribute( 'CRM_DAO_ExtPropertyGroup', 'description' ), true );
        $this->add( 'select', 'extends', 'Used For', CRM_SelectValues::$extPropertyGroupExtends );
        $this->addElement( 'checkbox', 'is_active', 'Is this Extended Property Group active?' );
        
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => 'Continue',
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'reset',
                                         'name'      => 'Reset'),
                                 array ( 'type'      => 'cancel',
                                         'name'      => 'Cancel' ),
                                 )
                           );
    }

    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        $params = $this->controller->exportValues( 'Group' );

        $group = new CRM_DAO_ExtPropertyGroup( );
        $group->title       = $params['title'];
        $group->name        = CRM_String::titleToVar( $params['title'] );
        $group->description = $params['description'];
        $group->extends     = $params['extends'];
        $group->is_active   = CRM_Array::value( 'is_active', $params, false );
        $group->domain_id   = 1;

        if ( $this->_mode & self::MODE_UPDATE ) {
            $group->id = $this->_groupId;
        }
        $group->save( );

        CRM_Session::setStatus( 'Your Group ' . $group->title . ' has been saved' );
    }

}

?>