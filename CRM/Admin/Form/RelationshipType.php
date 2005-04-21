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
 * This class generates form components for Relationship Type
 * 
 */
class CRM_Admin_Form_RelationshipType extends CRM_Form
{
    
    /**
     * The relationship type id, used when editing relationship type
     *
     * @var int
     */
    protected $_id;

    /**
     * class constructor
     *
     * @param string $name        Name of the form.
     * @param string $state       The state object associated with this form
     * @param int     $mode       The mode of the form
     *
     * @return CRM_Admin_Form_RelationshipType
     * @access public
     */
    function __construct($name, $state, $mode = self::MODE_NONE) {
        parent::__construct($name, $state, $mode);
    }

    function preProcess( ) {
        $this->_id    = $this->get( 'id' );
    }

    /**
     * This function sets the default values for the form. RelationshipType that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        $defaults = array( );
        $params   = array( );

        if ( isset( $this->_id ) ) {
            $params = array( 'id' => $this->_id );
            CRM_Contact_BAO_RelationshipType::retrieve( $params, $defaults );
        }
        
        return $defaults;
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        $this->add('text', 'name_a_b'       , 'Relationship Label for A to B'       ,
                   CRM_DAO::getAttribute( 'CRM_Contact_DAO_RelationshipType', 'name_a_b' ) );
        $this->addRule( 'name_a_b', 'Please enter a valid Relationship Label for A to B.', 'required' );

        $this->add('text', 'name_b_a'       , 'Relationship Label for B to A'       ,
                   CRM_DAO::getAttribute( 'CRM_Contact_DAO_RelationshipType', 'name_b_a' ) );
        $this->addRule( 'name_b_a', 'Please enter a valid Relationship Label for B to A.', 'required' );

        // add select for contact type
        $contactType = CRM_PseudoConstant::$contactType;
        $contactType = array(' ' => ' - any contact type - ') + $contactType;
        $this->add('select', 'contact_type_a', 'Contact Type A ', $contactType);
        $this->add('select', 'contact_type_b', 'Contact Type B ', $contactType);

        $this->add('text', 'description', 'Description', 
                   CRM_DAO::getAttribute( 'CRM_Contact_DAO_RelationshipType', 'description' ) );
        
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => 'Save',
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => 'Cancel' ),
                                 )
                           );
        
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
        $params = $this->exportValues();

        // action is taken depending upon the mode
        $relationshipType               = new CRM_Contact_DAO_RelationshipType( );
        
        $relationshipType->copyValues( $params );
        $relationshipType->domain_id    = 1;
                
        if ($this->_mode & self::MODE_UPDATE ) {
            $relationshipType->id = $this->_id;
        }else {
            $relationshipType->is_active    = 1;        
        }
        
        // print_r($relationshipType);
        $relationshipType->save( );

        CRM_Session::setStatus( 'The Relationship Type ' . $relationshipType->name . ' has been saved.' );
    }//end of function


}

?>
