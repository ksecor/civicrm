<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

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
    public function preProcess( ) {

    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {

        $savedSearch =& new CRM_Contact_BAO_SavedSearch ();
        
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
    public function postProcess( ) {
        $params = array ();
        $params['title'] = $this->controller->exportValue('Group','title' );
        $params['description'] = $this->controller->exportValue('Group','description' );
        $params['saved_search_id'] = $this->controller->exportValue('DynamicGroup','saved_search_id' );
        
        $group =& new CRM_Contact_DAO_Group();
        
        $group->copyValues( $params );
        $group->domain_id = CRM_Core_Config::domainID( );
        
        $group->save();

    }

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) {
        return 'Dynamic Group';
    }

    
}

?>
