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


require_once 'CRM/Profile/Form.php';

/**
 * This class provides the functionality for batch profile update
 */
class CRM_Contact_Form_Task_BatchUpdateProfile extends CRM_Contact_Form_Task {
    /**
     * The context that we are working on
     *
     * @var string
     */
    protected $_context;
    protected $_fields;

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
        
        $params = $this->exportValues();
        if ( $params['_qf_BatchUpdateProfile_refresh'] ) {
            $this->addDefaultButtons( ts('Save') );
            $this->_fields  = CRM_Core_BAO_UFGroup::getFields( $params['uf_group_id']);
            //CRM_Core_Error::debug('f', $this->_fields);
            
            $this->addButtons( array(
                                     array ( 'type'      => 'save',
                                             'name'      => ts('Save'),
                                             'isDefault' => true   ),
                                     array ( 'type'      => 'cancel',
                                             'name'      => ts('Cancel') ),
                                     )
                               );
            $this->_fields  = CRM_Core_BAO_UFGroup::getFields( $params['uf_group_id']);
            //   CRM_Core_Error::debug('f', $this->_fields);   
            
        }
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
        ///CRM_Core_Error::debug('q', $params);
 
        
    }//end of function


}

?>
