<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/BAO/CustomField.php';

/**
 * This class is to build the form for Deleting Group
 */
class CRM_Custom_Form_DeleteField extends CRM_Core_Form {

    /**
     * the group id
     *
     * @var int
     */
    protected $_id;

    /**
     * The title of the group being deleted
     *
     * @var string
     */
    protected $_title;

    /**
     * set up variables to build the form
     *
     * @param null
     * @return void
     * @acess protected
     */
    function preProcess( ) {
        $this->_id    = $this->get( 'id' );
        
       
        $defaults = array( );
        $params   = array( 'id' => $this->_id );
        CRM_Core_BAO_CustomField::retrieve( $params, $defaults );
        
        $this->_title = $defaults['label'];
        $this->assign( 'name' , $this->_title );
        
        CRM_Utils_System::setTitle( ts('Confirm Custom Field Delete') );
    }

    /**
     * Function to actually build the form
     *
     * @param null
     * 
     * @return void
     * @access public
     */
    public function buildQuickForm( ) {

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Delete Custom Field'),
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    /**
     * Process the form when submitted
     *
     * @param null
     * 
     * @return void
     * @access public
     */
    public function postProcess( ) {
        $field = & new CRM_Core_DAO_CustomField();
        $field->id = $this->_id;
        $field->find();
        $field->fetch();
        
        if (CRM_Core_BAO_CustomField::deleteGroup( $this->_id)) {
            require_once "CRM/Core/BAO/UFField.php";
            CRM_Core_BAO_UFField::delUFField($this->_id);
            CRM_Core_Session::setStatus( ts('The custom field "%1" has been deleted.', array(1 => $field->label)) );        
        }
    }
}
?>