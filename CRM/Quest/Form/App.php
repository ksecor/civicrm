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

        if ( $this->_action & CRM_Core_Action::VIEW ) {
            if ( $this->_name == 'Personal' ) {
                $this->addDefaultButtons(ts('Continue'), 'next', null);
            } else {
                $this->addDefaultButtons( ts('Continue') );
            }
            $this->freeze();
        } else {
            switch ( $this->_name ) {
            case 'Personal':
                $this->addDefaultButtons(ts('Save & Continue'), 'upload', null);
                break;

            case 'Submit':
                $this->addDefaultButtons( ts('Submit Application') );
                break;
        
            case 'Partner-Submit':
                $this->addDefaultButtons( ts('Submit Partner Supplement') );
                break;

            case 'Essay-PersonalStat':
                $this->addDefaultButtons(ts('Save & Continue'), 'upload');
                break;

            case 'Stanford-StfEssay':
                $this->addDefaultButtons(ts('Save & Continue'), 'upload');
                break;

            default:
                $this->addDefaultButtons( ts('Save & Continue') );
            }
            $this->add('hidden','is_save_draft', '0');

            // also make sure we set this as a constant
            $this->setConstants( array( 'is_save_draft' => 0 ) );
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
        $taskStatus = $this->get( 'taskStatus' );
        $valid = ( $taskStatus == 'Completed' ) ? 1 : 0;

        $changes = array( $this->controller->_subType => array( 'valid' => $valid ) );

        if ( $taskStatus == 'Completed' &&
             $this->controller->matchAppComplete( $this->_contactID ) ) {
            $url = CRM_Utils_System::url( 'civicrm/quest/matchapp/submit',
                                          'reset=1' );
            $changes['Submit'] = array( 'link' => $url );
        } else {
            CRM_Project_BAO_TaskStatus::updateTaskStatusWithValue( $this,
                                                                   'In Progress',
                                                                   'appTaskStatus' );
            $changes['Submit'] = array( 'link' => null );
        }

        $this->controller->changeCategoryValues( $changes );

        // if save draft is set, redirect to locker
        if ( CRM_Utils_Array::value( 'is_save_draft', $_POST ) ) {
            CRM_Utils_System::redirect( CRM_Utils_System::url( 'locker', 'reset=1' ) );
        }

    }//end of function

}

?>
