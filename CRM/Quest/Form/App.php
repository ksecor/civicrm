<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/


/**
 * Application Form Base Class
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * Base class for the application form
 * 
 */
class CRM_Quest_Form_App extends CRM_Core_Form
{
    const
        TEST_ACT  = 1,
        TEST_PSAT = 2,
        TEST_SAT  = 4;

    protected $_contactID;
    protected $_studentID;

    function preProcess( ) {
        $this->_contactID = $this->get( 'contactID' );
        $this->_studentID = $this->get( 'studentID' );
    }

    /**
     * This function sets the default values for the form. For edit/view action
     * the default values are retrieved from the database
     * 
     * @access public
     * @return void
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        return $defaults;
    }
    

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->assign       ( 'displayRecent' , false );
        $this->assign       ( 'welcome_name'  , $this->get('welcome_name'));
        if ( $this->_name == 'Personal' ) {
            if ( $this->_action & CRM_Core_Action::VIEW ) {
                $this->addDefaultButtons(ts('Continue'), 'next', null);
            } else {
                $this->addDefaultButtons(ts('Save & Continue'), 'upload', null);
            }
        } else if ( $this->_name == 'Submit' ) {
            if ( $this->_action & CRM_Core_Action::VIEW ) {
                $this->addDefaultButtons( ts('Continue') );
            } else {
                $this->addDefaultButtons( ts('Submit Application') );
            }
        } else if ( $this->_name == "Essay-PersonalStat" ) {
            if ( $this->_action & CRM_Core_Action::VIEW ) {
                $this->addDefaultButtons(ts('Continue'), 'next');
            } else {
                $this->addDefaultButtons(ts('Save & Continue'), 'upload');
            }
        } else if ($this->_name == "Stanford-StfEssay") { 
            if ( $this->_action & CRM_Core_Action::VIEW ) {
                $this->addDefaultButtons(ts('Continue'), 'next');
            } else {
                $this->addDefaultButtons(ts('Save & Continue'), 'upload');
            }
        } else {
            if ( $this->_action & CRM_Core_Action::VIEW ) {
                $this->addDefaultButtons( ts('Continue') );
            } else {
                $this->addDefaultButtons( ts('Save & Continue') );
            }
        }

        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $this->freeze();
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
        require_once 'CRM/Project/BAO/TaskStatus.php';
        CRM_Project_BAO_TaskStatus::updateTaskStatus( $this );

        // also update the appStatus
        $taskStatus = $this->get( 'TaskStatus' );
        if ( $taskStatus == 'Completed' &&
             $this->controller->matchAppComplete( ) ) {
            CRM_Project_BAO_TaskStatus::updateTaskStatusWithValue( $this,
                                                                   $taskStatus,
                                                                   'appTaskStatus' );
        } else {
            $taskStatus = 'In Progress';
            CRM_Project_BAO_TaskStatus::updateTaskStatusWithValue( $this,
                                                                   $taskStatus,
                                                                   'appTaskStatus' );
        }

    }//end of function

}

?>
