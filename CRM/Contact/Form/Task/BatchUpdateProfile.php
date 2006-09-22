<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | copyright CiviCRM LLC (c) 2004-2006                                  |
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
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */


require_once 'CRM/Profile/Form.php';

/**
 * This class provides the functionality for batch profile update
 */
class CRM_Contact_Form_Task_BatchUpdateProfile extends CRM_Contact_Form_Task {

    /**
     * the title of the group
     *
     * @var string
     */
    protected $_title;

    /**
     * maximum contacts that should be allowed to update
     *
     */
    protected $_maxContacts = 2;

    /**
     * maximum profile fields that will be displayed
     *
     */
    protected $_maxFields = 8;


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
    function preProcess( ) {
        /*
         * initialize the task and row fields
         */
        parent::preProcess( );
    
        $session =& CRM_Core_Session::singleton();
        $this->_userContext = $session->readUserContext( );
    
        $validate = false;
        //validations
        if ( count($this->_contactIds) > $this->_maxContacts) {
            CRM_Core_Session::setStatus("The maximum number of contacts you can select for Batch Update is {$this->_maxContacts}. You have selected ". count($this->_contactIds). ". Please select fewer contacts from your search results and try again." );
            $validate = true;
        }
        
        if (CRM_Contact_BAO_Contact::checkContactType($this->_contactIds)) {
            CRM_Core_Session::setStatus("Batch update requires that all selected contacts be the same type (e.g. all Individuals OR all Organizations...). Please modify your selected contacts and try again.");
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
    function buildQuickForm( ) {

        CRM_Utils_System::setTitle( ts('Batch Profile Update') );
        
        // add select for groups
        $ufGroup = array( '' => ts('- select profile -')) + CRM_Core_PseudoConstant::ufgroup( );
        $ufGroupElement = $this->add('select', 'uf_group_id', ts('Select Profile'), $ufGroup, true);
        
        $ufGroupId = $this->get('ufGroupId');

        $bName = ts('Continue');
        
        if ( $ufGroupId ) {
            $this->addDefaultButtons( ts('Save') );
            $this->_fields  = array( );
            $this->_fields  = CRM_Core_BAO_UFGroup::getFields( $ufGroupId, false, CRM_Core_Action::VIEW );
            $this->_fields  = array_slice($this->_fields, 0, $this->_maxFields);
            
            $this->addButtons( array(
                                     array ( 'type'      => 'submit',
                                             'name'      => ts('Update Contact(s)'),
                                             'isDefault' => true   ),
                                     array ( 'type'      => 'cancel',
                                             'name'      => ts('Cancel') ),
                                     )
                               );

            $this->assign( 'fields', $this->_fields     );
            $this->assign( 'contactIds', $this->_contactIds );
            
            foreach ($this->_contactIds as $contactId) {
                foreach ($this->_fields as $name => $field ) {
                    CRM_Core_BAO_UFGroup::buildProfile($this, $field, null, $contactId );
                }
            }
            
            $bName = ts('Select Another Profile');
        }

        $this->addElement( 'submit', $this->getButtonName('refresh'), $bName, array( 'class' => 'form-submit' ) );
        $this->addElement( 'submit', $this->getButtonName('cancel' ), ts('Cancel'), array( 'class' => 'form-submit' ) );
    }

    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) {
        $this->addFormRule( array( 'CRM_Contact_Form_Task_BatchUpdateProfile', 'formRule' ) );
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
    static function formRule( &$fields ) {
        require_once "CRM/Core/BAO/UFField.php";
        if ( CRM_Core_BAO_UFField::checkProfileType($fields['uf_group_id'], true) ) {
            $errorMsg['uf_group_id'] = "You cannot select mix profile for batch update.";
        }

        if ( !empty($errorMsg) ) {
            return $errorMsg;
        }
        
        return true;
    }    

    /**
     * This function sets the default values for the form.
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        if (empty($this->_fields)) {
            return;
        }
        
        foreach ($this->_contactIds as $contactId) {
            $details[$contactId] = array( );

            //build sortname
            $sortName[$contactId] = CRM_Contact_BAO_Contact::sortName($contactId);
            
            CRM_Core_BAO_UFGroup::setProfileDefaults( $contactId, $this->_fields, $defaults, false );
        }
        
        $this->assign('sortName', $sortName);

        return $defaults;
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
  
        if ( CRM_Utils_Array::value( '_qf_BatchUpdateProfile_refresh', $params ) ) {
            $this->set( 'ufGroupId', $params['uf_group_id'] );
            return;
        }
        
        $ufGroupId = $this->get( 'ufGroupId' );

        foreach($params['field'] as $key => $value) {
            CRM_Contact_BAO_Contact::createProfileContact($value, $this->_fields, $key, null, $ufGroupId );
        }
        
        CRM_Core_Session::setStatus("Your updates have been saved.");
        CRM_Utils_System::redirect( $this->_userContext );
        
    }//end of function
}
?>
