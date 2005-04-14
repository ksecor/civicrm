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
        $this->_rtype    = $this->get('rtype');
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

            $session = CRM_Session::singleton( );

            $relationship = new CRM_Contact_DAO_Relationship( );

            $relationship->id = $this->_relationshipId;

            if ($relationship->find(true)) {
                $defaults['relationship_type_id'] = $relationship->relationship_type_id."_".$this->_rtype;

                $defaults['start_date'] = $relationship->start_date;
                $defaults['end_date'] = $relationship->end_date;

                $a_temp = explode('_', $this->_rtype);

                $str_contact = 'contact_id_'.$a_temp[0];

                $cId = $relationship->$str_contact;

                $contact = new CRM_Contact_DAO_Contact( );
                $contact->id = $cId;
                if ($contact->find(true)) {
                    $this->assign('sort_name', $contact->sort_name);                
                }
            }
         }
        //  print_r($defaults);
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
        $this->addElement('select', "relationship_type_id", '', CRM_Contact_BAO_Relationship::getContactRelationshipType($this->_contactId, $this->_rtype));
        
        $this->addElement('select', "contact_type", '', CRM_SelectValues::$contactType);
        
        $this->addElement('text', 'name' );

        $this->addElement('hidden', 'csearch','0' );

        $this->addElement('date', 'start_date', 'Starting:', CRM_SelectValues::$date);
        
        $this->addElement('date', 'end_date', 'Ending:', CRM_SelectValues::$date);

        $arraySearch = array();
        $params = array();
        $arraySearch = $this->exportValues();
 
        if ($this->_mode != self::MODE_UPDATE ) {
            if (strlen(trim($arraySearch['name']))) {
                $params['name'] = $arraySearch['name'];
                $params['contact_type'] = $arraySearch['contact_type'];
            
                $this->getContactList($this, $params);
            }
        }

        $this->addButtons( array(
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

        // action is taken depending upon the mode
        $ids = array( );
        $ids['contact'] = $this->_contactId;
        if ($this->_mode & self::MODE_UPDATE ) {
            $ids['relationship'] = $this->_relationshipId;
        }    

        $relationship = CRM_Contact_BAO_Relationship::create( $params, $ids );

        $session = CRM_Session::singleton( );
        if ($relationship->id) {
            $session->setStatus( 'Your relationship record has been saved' );
        }

    }//end of function


    /**
     * This function is to get the result of the search for contact in relationship form
     *
     * param  array $params  This contains elements for search criteria
     *
     * @access public
     * @return None
     *
     */
    function getContactList($this, &$params) {
        
        $contact = new CRM_Contact_BAO_Contact( );
        
        $lngResultCount = 0;
        
        //max records that will be listed
        $maxResultCount = 50;
      
        $contact->whereAdd( " LOWER(crm_contact.sort_name) like '%".addslashes(strtolower($params['name']))."%'");
        if (strlen($params['contact_type'])) {
            $contact->contact_type = $params['contact_type'];
        }
        $lngResultCount = $contact->count();

        
        if ($lngResultCount > $maxResultCount) {
            $this->assign('noResult', 'Please enter appropriate search criteria.');
        } else {

            $config = CRM_Config::singleton( );
            $contact->find();
            while($contact->fetch()) {

                $values[$contact->id]['id'] = $contact->id;
                $values[$contact->id]['name'] = $contact->sort_name;

                $contact_type = '<img src="' . $config->resourceBase . 'i/contact_';
                switch ($contact->contact_type ) {
                case 'Individual' :
                    $contact_type .= 'ind.png" alt="Individual">';
                    break;
                case 'Household' :
                    $contact_type .= 'house.png" alt="Household" height="16" width="16">';
                    break;
                case 'Organization' :
                    $contact_type .= 'org.gif" alt="Organization" height="16" width="18">';
                    break;
                    
                }
                $values[$contact->id]['type'] = $contact_type;
                
                $contact_chk[$contact->id] = $this->createElement('checkbox', $contact->id, null,'');                
            }
            
            $this->addGroup($contact_chk, 'contact_check');
            if ($lngResultCount == 0) $this->assign('noContacts',' No results were found.');

            $this->assign('contacts', $values);
        }
        
    }


}

?>
