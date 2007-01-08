<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */


require_once 'CRM/Profile/Form.php';
require_once 'CRM/Event/Form/Task.php';

 /**
  * This class provides the functionality for batch profile update for event participations
  */
class CRM_Event_Form_Task_PickProfile extends CRM_Event_Form_Task 
{
    /**
     * the title of the group
     *
     * @var string
     */
    protected $_title;

    /**
     * maximum event participations that should be allowed to update
     *
     */
    protected $_maxParticipations = 100;

    /**
     * variable to store redirect path
     *
     */
    protected $_userContext;


    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) 
    {
        /*
         * initialize the task and row fields
         */
        parent::preProcess( );

        $session =& CRM_Core_Session::singleton();
        $this->_userContext = $session->readUserContext( );
        
        $validate = false;
        //validations
        if ( count( $this->_participantIds ) > $this->_maxParticipations ) {
            CRM_Core_Session::setStatus("The maximum number of event participations you can select for Batch Update is {$this->_maxParticipations}. You have selected ". count($this->_participantIds). ". Please select fewer participantions from your search results and try again." );
            $validate = true;
        }
        
        if ($validate) { // than redirect
            CRM_Utils_System::redirect( $this->_userContext );
        }
    }
  
    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {
        CRM_Utils_System::setTitle( ts('Batch Profile Update for Event') );
        // add select for groups
        require_once "CRM/Core/BAO/UFGroup.php";
        $profiles = array( '' => ts('- select profile -')) + CRM_Core_BAO_UFGroup::getProfiles(array('Participant'));
        $ufGroupElement = $this->add('select', 'uf_group_id', ts('Select Profile'), $profiles, true);
        $this->addDefaultButtons( ts( 'Continue >>' ) );
    }

    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) 
    {
        $this->addFormRule( array( 'CRM_Event_Form_Task_PickProfile', 'formRule' ) );
    }
    
    /**
     * global validation rules for the form
     *
     * @param array $fields posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @static
     * @access public
     */
    static function formRule( &$fields ) 
    {
        return true;
    }    

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $params = $this->exportValues( );
        $this->set( 'ufGroupId', $params['uf_group_id'] );
    }//end of function
}
?>
