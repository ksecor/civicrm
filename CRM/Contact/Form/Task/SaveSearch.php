<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contact/Form/Task.php';

/**
 * This class provides the functionality to save a search
 * Saved Searches are used for saving frequently used queries
 */
class CRM_Contact_Form_Task_SaveSearch extends CRM_Contact_Form_Task {
    /**
     * saved search id if any
     *
     * @var int
     */
    protected $_id;

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess()
    {
        $this->_id   = null;

        // get the submitted values of the search form
        // we'll need to get fv from either search or adv search in the future
        if ( $this->_action == CRM_Core_Action::ADVANCED ) {
            $values = $this->controller->exportValues( 'Advanced' );
        } else {
            $values = $this->controller->exportValues( 'Search' );
        }

        $this->_task = $values['task'];
        $crmContactTaskTasks = CRM_Contact_Task::tasks();
        $this->assign('taskName', $crmContactTaskTasks[$this->_task]);
    }

    /**
     * Build the form - it consists of
     *    - displaying the QILL (query in local language)
     *    - displaying elements for saving the search
     *
     * @access public
     * @return void
     */
    function buildQuickForm()
    {
        // get the qill 
        $query =& new CRM_Contact_BAO_Query( $this->get( 'formValues' ) );
        $qill = $query->qill( );

        // need to save qill for the smarty template
        $this->assign('qill', $qill);
        
        // the name and description are actually stored with the group and not the saved search
        $this->add('text', 'title', ts('Name'),
                   CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Group', 'title'), true);
        $this->addRule( 'title', ts('Name already exists in Database.'),
                        'objectExists',
                        array( 'CRM_Contact_DAO_Group', $this->_id, 'title' ) );

        $this->addElement('text', 'description', ts('Description'),
                          CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Group', 'description'));

        // get the group id for the saved search
        if ( isset( $this->_id ) ) { 
            $params = array( 'saved_search_id' => $this->_id );
            CRM_Contact_BAO_Group::retrieve( $params, $values );
            $groupId = $values['id'];

            $this->addRule( 'title', ts('Name already exists in Database.'),
                            'objectExists', array( 'CRM_Contact_DAO_Group', $groupId, 'title' ) );
        }
        
        if ( isset( $this->_id ) ) {
            $this->addDefaultButtons( ts('Update Smart Group') );
        } else {
            $this->addDefaultButtons( ts('Save Smart Group') );
        }
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return void
     */
    public function postProcess()
    {
        // saved search form values
        $formValues = $this->controller->exportValues($this->_name);

        // save the search
        $savedSearch =& new CRM_Contact_BAO_SavedSearch();
        $savedSearch->id          = $this->_id;
        $savedSearch->domain_id   = CRM_Core_Config::domainID( );
        $savedSearch->form_values = serialize($this->get( 'formValues' ));
        $savedSearch->is_active = 1;
        $savedSearch->save();
        $this->set('ssID',$savedSearch->id);
        CRM_Core_Session::setStatus( ts('Your smart group has been saved as "%1".', array(1 => $formValues['title'])) );

        // also create a group that is associated with this saved search only if new saved search
        $params = array( );
        $params['domain_id'  ]     = CRM_Core_Config::domainID( );
        $params['title'      ]     = $formValues['title'];
        $params['description']     =  $formValues['description'];
        $params['visibility' ]     = 'User and User Admin Only';
        $params['saved_search_id'] = $savedSearch->id;
        $params['is_active']       = 1;
        
        if ( $this->_id ) {
            $params['id'] = CRM_Contact_BAO_SavedSearch::getName( $this->_id, 'id' );
        }

        $group =& CRM_Contact_BAO_Group::create( $params );
    }
}

?>
