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

require_once 'CRM/SelectValues.php';
require_once 'CRM/Form.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Relationship_Form_Relationship extends CRM_Form
{

    /**
     * The relationship id, used when editing the relationship
     *
     * @var int
     */
    protected $_relationshipId;
    
    /**
     * The contact id, used when add/edit relationship
     *
     * @var int
     */
    protected $_contactId;
    

    /**
     * class constructor
     *
     * @param string $name        Name of the form.
     * @param string $state       The state object associated with this form
     * @param int     $mode       The mode of the form
     *
     * @return CRM_Relationship_Form_Relationship
     * @access public
     */
    function __construct($name, $state, $mode = self::MODE_NONE) 
    {
        parent::__construct($name, $state, $mode);
    }
    
    function preProcess( ) 
    {
        $this->_contactId   = $this->get('contactId');
        $this->_relationshipId    = $this->get('relationshipId');
    }

    /**
     * This function sets the default values for the form. Relationship that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        $params   = array( );

        if ( $this->_mode & self::MODE_UPDATE ) {
            $relationship = new CRM_Contact_DAO_Relationship( );
            $relationship->id = $this->_relationshipId;
            if ($relationship->find(true)) {
                $defaults['relationship_type_id'] = $relationship->relationship_type_id;
                $defaults['start_date'] = $relationship->start_date;
                $defaults['end_date'] = $relationship->end_date;
            }
        }
      
        return $defaults;
    }


    /**
     * This function is used to add the rules for form.
     *
     * @return None
     * @access public
     */
    function addRules( )
    {
        $this->addRule('start_date', 'Select a valid start date.', 'qfDate' );
        $this->addRule('end_date', 'Select a valid end date.', 'qfDate' );
    }


    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {

        $this->addElement('select', "relationship_type_id", '', CRM_SelectValues::getRelationshipType());
        
        $this->addElement('select', "contact_type", '', CRM_SelectValues::$contactType);
        
        $this->addElement('text', "name" );
        
        $this->addElement('submit','search', 'Search');

        $this->addElement('date', 'start_date', 'Starting:', CRM_SelectValues::$date);
        
        $this->addElement('date', 'end_date', 'Ending:', CRM_SelectValues::$date);

        $this->addDefaultButtons( array(
                                        array ( 'type'      => 'next',
                                                'name'      => 'Save Relationship',
                                                'isDefault' => true   ),
                                        array ( 'type'       => 'cancel',
                                                'name'      => 'Cancel' ),
                                        )
                                  );
        
    }

       
    /**
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        
        // store the submitted values in an array
        $params = $this->exportValues();

        // create relationship object
        $relationship                = new CRM_Contact_DAO_Relationship( );
        $relationship->contact_id_a  = $this->_contactId;
        $relationship->contact_id_b  = 1;
        $relationship->relationship_type_id = $params['relationship_type_id'];

        $sdate = CRM_Array::value( 'start_date', $params );
        $relationship->start_date = null;
        if ( $sdate              &&
             !empty($sdate['M']) &&
             !empty($sdate['d']) &&
             !empty($sdate['Y']) ) {
            $sdate['M'] = ( $sdate['M'] < 10 ) ? '0' . $sdate['M'] : $sdate['M'];
            $sdate['d'] = ( $sdate['d'] < 10 ) ? '0' . $sdate['d'] : $sdate['d'];
            $relationship->start_date = $sdate['Y'] . $sdate['M'] . $sdate['d'];
        }

        $edate = CRM_Array::value( 'end_date', $params );
        $relationship->end_date = null;
        if ( $edate              &&
             !empty($edate['M']) &&
             !empty($edate['d']) &&
             !empty($edate['Y']) ) {
            $edate['M'] = ( $edate['M'] < 10 ) ? '0' . $edate['M'] : $edate['M'];
            $edate['d'] = ( $edate['d'] < 10 ) ? '0' . $edate['d'] : $edate['d'];
            $relationship->end_date = $edate['Y'] . $edate['M'] . $edate['d'];
        }

        if ($this->_mode & self::MODE_UPDATE ) {
            $relationship->id = $this->_relationshipId;
        } 

        $relationship->save( );

        $session = CRM_Session::singleton( );

        $session->setStatus( "Your Relationship has been saved." );

    }//end of function

}

?>
