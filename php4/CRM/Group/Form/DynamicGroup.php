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



require_once 'CRM/Core/Form.php';
require_once 'CRM/Contact/BAO/SavedSearch.php';
require_once 'CRM/Contact/DAO/Group.php';
require_once 'CRM/Core/Form.php';

/**
 * This class for the second step in Group Wizard (for listing saved searches)
 */
class CRM_Group_Form_DynamicGroup extends CRM_Core_Form {

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
     function preProcess( ) {

    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
     function buildQuickForm( ) {

        $savedSearch = new CRM_Contact_BAO_SavedSearch ();
        
        $aSavedResults = array ();
        
        $aSavedResults = $savedSearch->getAll();
        
        $this->addElement('select', 'saved_search_id', ts('Saved Search: '), $aSavedResults);

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Done'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'back',
                                         'name'      => ts('Previous') ),
                                 array ( 'type'      => 'reset',
                                         'name'      => ts('Reset')),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    /**
     * Process the form
     *
     * @return void
     * @access public
     */
     function postProcess( ) {
        $params = array ();
        $params['title'] = $this->controller->exportValue('Group','title' );
        $params['description'] = $this->controller->exportValue('Group','description' );
        $params['group_type'] = $this->controller->exportValue('Group','group_type' );
        $params['saved_search_id'] = $this->controller->exportValue('DynamicGroup','saved_search_id' );
        
        $group = new CRM_Contact_DAO_Group();
        
        $group->copyValues( $params );
        $group->domain_id = 1;
        
        $group->save();

    }

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
     function getTitle( ) {
        return 'Dynamic Group';
    }

    
}

?>
