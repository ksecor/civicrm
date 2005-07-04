<?php
/**
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
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
        $this->add('textarea', 'body_text', ts('Body in text format'),
                   CRM_Core_DAO::getAttribute( 'CRM_Mailing_DAO_Component', 'body_text' ),
                   true );
        $this->add('textarea', 'body_html', ts('Body in html format'),
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
    public function postProcess() 
    {
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );

        // action is taken depending upon the mode
        $component                 =& new CRM_Mailing_DAO_Component( );
        $component->domain_id      =  CRM_Core_Config::domainID( );
        $component->name           =  $params['name'];
        $component->component_type =  $params['component_type'];
        $component->subject        =  $params['subject'];
        $component->body_text      =  $params['body_text'];
        $component->body_html      =  $params['body_html'];
        $component->is_active      =  CRM_Utils_Array::value( 'is_active' , $params, false );
        $component->is_default     =  CRM_Utils_Array::value( 'is_default', $params, false );

        if ($this->_action & CRM_Core_Action::UPDATE ) {
            $component->id = $this->_id;
        }

        $component->save( );

        CRM_Core_Session::setStatus( ts('The mailing component "%1" has been saved.',
                                        array( 1 => $component->name )) );
    }//end of function

}

?>
