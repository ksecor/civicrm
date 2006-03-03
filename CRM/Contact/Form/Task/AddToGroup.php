<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contact/Form/Task.php';

/**
 * This class provides the functionality to group 
 * contacts. This class provides functionality for the actual
 * addition of contacts to groups.
 */
class CRM_Contact_Form_Task_AddToGroup extends CRM_Contact_Form_Task {
    /**
     * The context that we are working on
     *
     * @var string
     */
    protected $_context;

    /**
     * the groupId retrieved from the GET vars
     *
     * @var int
     */
    protected $_id;

    /**
     * the title of the group
     *
     * @var string
     */
    protected $_title;

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) {
        /*
         * initialize the task and row fields
         */
        parent::preProcess( );

        $this->_context = $this->get( 'context' );
        $this->_id      = $this->get( 'amtgID'  );
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) {
        // add select for groups
        $group = array( '' => ts('- select group -')) + CRM_Core_PseudoConstant::group( );
        $groupElement = $this->add('select', 'group_id', ts('Select Group'), $group, true);
        $this->_title  = $group[$this->_id];

        if ( $this->_context === 'amtg' ) {
            $groupElement->freeze( );

            // also set the group title
            $groupValues = array( 'id' => $this->_id, 'title' => $this->_title );
            $this->assign_by_ref( 'group', $groupValues );
        }
         
        // Set dynamic page title for 'Add Members Group (confirm)'
        if ( $this->_id ) {
            CRM_Utils_System::setTitle( ts('Add Members: %1', array(1 => $this->_title)) );
        }
        else {
            CRM_Utils_System::setTitle( ts('Add Members to A Group') );
        }

        $this->addDefaultButtons( ts('Add To Group') );
    }

    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues() {
        $defaults = array();

        if ( $this->_context === 'amtg' ) {
            $defaults['group_id'] = $this->_id;
        }
        return $defaults;
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        $groupId = $this->controller->exportValue( 'AddToGroup', 'group_id'  );
        $group   =& CRM_Core_PseudoConstant::group( );

        list( $total, $added, $notAdded ) = CRM_Contact_BAO_GroupContact::addContactsToGroup( $this->_contactIds, $groupId );
        $status = array(
                        ts('Added Contact(s) to %1', array(1 => $group[$groupId])),
                        ts('Total Selected Contact(s): %1', array(1 => $total))
                        );
        if ( $added ) {
            $status[] = ts('Total Contact(s) added to group: %1', array(1 => $added));
        }
        if ( $notAdded ) {
            $status[] = ts('Total Contact(s) already in group: %1', array(1 => $notAdded));
        }
        CRM_Core_Session::setStatus( $status );
        
    }//end of function


}

?>
