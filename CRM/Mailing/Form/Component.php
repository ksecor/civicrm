<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for Location Type
 * 
 */
class CRM_Mailing_Form_Component extends CRM_Core_Form
{
    /**
     * The id of the object being edited / created
     *
     * @var int
     */
    protected $_id;

    /**
     * The name of the BAO object for this form
     *
     * @var string
     */
    protected $_BAOName;

    function preProcess( ) {
        $this->_id      = $this->get( 'id'      );
        $this->_BAOName = $this->get( 'BAOName' );
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->applyFilter('__ALL__', 'trim');

        $this->add('text', 'name'       , ts('Name')       ,
                   CRM_Core_DAO::getAttribute( 'CRM_Mailing_DAO_Component', 'name' ), true );
        $this->addRule( 'name', ts('Name already exists in Database.'), 'objectExists', array( 'CRM_Mailing_DAO_Component', $this->_id ) );

        $this->add('select', 'component_type', ts( 'Component Type' ), CRM_Core_SelectValues::mailingComponents( ) );

        $this->add('text', 'subject', ts('Subject'),
                   CRM_Core_DAO::getAttribute( 'CRM_Mailing_DAO_Component', 'subject' ),
                   true );
        $this->add('textarea', 'body_text', ts('Body - TEXT Format'),
                   CRM_Core_DAO::getAttribute( 'CRM_Mailing_DAO_Component', 'body_text' ),
                   true );
        $this->add('textarea', 'body_html', ts('Body - HTML Format'),
                   CRM_Core_DAO::getAttribute( 'CRM_Mailing_DAO_Component', 'body_html' ),
                   true );
        
        $this->add('checkbox', 'is_default', ts('Default?'));
        $this->add('checkbox', 'is_active' , ts('Enabled?'));
        
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Save'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
        
    }

    /**
     * This function sets the default values for the form.
     *
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        $defaults = array( );
        $params   = array( );

        if ( isset( $this->_id ) ) {
            $params = array( 'id' => $this->_id );
            require_once(str_replace('_', DIRECTORY_SEPARATOR, $this->_BAOName) . ".php");
            eval( $this->_BAOName . '::retrieve( $params, $defaults );' );
        }
        $defaults['is_active'] = 1;

        return $defaults;
    }
       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess( ) 
    {
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );
        
        $ids = array( );
        
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            $ids['id'] = $this->_id;
        }
        
        require_once 'CRM/Mailing/BAO/Component.php';
        CRM_Mailing_BAO_Component::add( $params, $ids );
        
    }//end of function

}

?>
