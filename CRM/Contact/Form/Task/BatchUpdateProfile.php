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
 * @author Donald A. Lobo <lobo@yahoo.com>
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
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) {
        // add select for groups
        $ufGroup = array( '' => ts('- select profile -')) + CRM_Core_PseudoConstant::ufgroup( );
        $ufGroupElement = $this->add('select', 'uf_group_id', ts('Select Profile'), $ufGroup, true);
        
        $this->addElement( 'submit', $this->getButtonName('refresh'), ts('Go'), array( 'class' => 'form-submit' ) );
         
        CRM_Utils_System::setTitle( ts('Batch Profile Update') );
        
        $this->_fields  = array();
        $params = $this->exportValues();
        if ( $params['_qf_BatchUpdateProfile_refresh'] ) {
            $this->addDefaultButtons( ts('Save') );
            $this->_fields  = CRM_Core_BAO_UFGroup::getFields( $params['uf_group_id']);
            
            $this->addButtons( array(
                                     array ( 'type'      => 'submit',
                                             'name'      => ts('Save'),
                                             'isDefault' => true   ),
                                     array ( 'type'      => 'cancel',
                                             'name'      => ts('Cancel') ),
                                     )
                               );

            $this->assign( 'fields', $this->_fields     );
            $this->assign( 'contactIds', $this->_contactIds );
            
            foreach ($this->_contactIds as $contactId) {
                //$field['is_required'] currently ignoring required condition
                foreach ($this->_fields as $name => $field ) {
                    CRM_Core_BAO_UFGroup::buildProfile($this, $field['name'], $field['title'], false, $field['attributes'], $search, $contactId );
                }
            }
        }
    }

    /**
     * This function sets the default values for the form.
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        foreach ($this->_contactIds as $contactId) {
            $details[$contactId] = array( );
            
            // get the contact details (hier)
            $params = array('sort_name' => 1) + $this->_fields;
            list($contactDetails, $options) = CRM_Contact_BAO_Contact::getHierContactDetails( $contactId, $params );
            $details[$contactId] = $contactDetails[$contactId];
            foreach ($contactDetails as $key => $value) {
                foreach ($value as $k => $v) { 
                    if ( array_key_exists($k, $params) ) {
                        if ($k == 'sort_name') {
                            $sortName[$contactId] = $v;
                        } else {
                            $defaults["field[$contactId][$k]"] = $v; 
                        }
                    }
                }
            }
            
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
        //CRM_Core_Error::debug('q', $params);
        
    }//end of function


}

?>
