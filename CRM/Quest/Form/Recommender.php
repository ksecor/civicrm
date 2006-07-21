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
 * Recommender Form Base Class
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * Base class for the recommender (teacher/counselor) form
 * 
 */
class CRM_Quest_Form_Recommender extends CRM_Core_Form
{
    protected $_recommenderID;
    protected $_studentContactID;

    function preProcess( ) {
        //$this->_recommenderID    = $this->get( 'recommenderID' );
        $this->_recommenderID    = $this->get('contactID' );
        $this->_studentContactID = $this->get( 'scid'          );
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
        $this->assign       ( 'displayRecent'       , false                            );
        $this->assign       ( 'welcome_name'        , $this->get('welcome_name'        ) );
        $this->assign       ( 'student_welcome_name', $this->get('student_welcome_name') );

        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $this->addDefaultButtons( ts('Continue') );
            $this->freeze();
        } else {
            $this->addDefaultButtons( ts('Save & Continue') );
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
        // update the task record
        require_once 'CRM/Project/DAO/TaskStatus.php';
        $dao =& new CRM_Project_DAO_TaskStatus( );
        $dao->responsible_entity_table = 'civicrm_contact';
        $dao->responsible_entity_id    = $this->_recommenderID;
        $dao->target_entity_table      = 'civicrm_contact';
        $dao->target_entity_id         = $this->_studentContactID;
        if ( ! $dao->find( true ) ) {
            CRM_Core_Error::fatal( "The task status table is inconsistent" );
        }
        
        $status =& CRM_Core_OptionGroup::values( 'task_status', true );
        if ( $this->_name != 'Evaluation' && $dao->status_id != $status['Completed'] ) {
            $dao->status_id = $status['In Progress'];
        } else {
            $dao->status_id = $status['Completed'];
        }

        $dao->create_date   = CRM_Utils_Date::isoToMysql( $dao->create_date );
        $dao->modified_date = date( 'YmdHis' );
        
        // now save all the valid values to fool QFC
        $data =& $this->controller->container( );
        $dao->status_detail = serialize( $data['valid'] );

        $dao->save( );
    }//end of function

}

?>
