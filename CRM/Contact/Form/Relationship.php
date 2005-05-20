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

require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Contact_Form_Relationship extends CRM_Core_Form
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
     * This is a string which is either a_b or  b_a  used to determine the relationship between to contacts
     *
     */
    protected $_rtype;

    function preProcess( ) 
    {
        $this->_contactId      = $this->get('contactId');
        $this->_relationshipId = $this->get('relationshipId');
        $this->_rtype          = $this->get('rtype');
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
                $defaults['relationship_type_id'] = $relationship->relationship_type_id."_".$this->_rtype;

                $defaults['start_date'] = $relationship->start_date;
                $defaults['end_date'] = $relationship->end_date;

                $temp = explode('_', $this->_rtype);

                $str_contact = 'contact_id_'.$temp[1];

                $cId = $relationship->$str_contact;

                $contact = new CRM_Contact_DAO_Contact( );
                $contact->id = $cId;
                if ($contact->find(true)) {
                    $this->assign('sort_name', $contact->sort_name);                
                }
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
        $this->addRule('relationship_type_id', 'Please select the relationship. ', 'required' );
        $this->addRule('start_date', 'Select a valid start date.', 'qfDate' );
        $this->addRule('end_date', 'Select a valid end date.', 'qfDate' );
        $this->addFormRule(array('CRM_Contact_Form_Relationship','formRule'));
    }


    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $rtype = 'b_a';
        if (strlen(trim($this->_rtype))) {
            $rtype = $this->_rtype;
        }
        
        $this->addElement('select', "relationship_type_id", '', array('' => '- select relationship type -') + CRM_Contact_BAO_Relationship::getContactRelationshipType($this->_contactId, $rtype));
        
        $this->addElement('select', "contact_type", 'Contact Type', CRM_Core_SelectValues::$contactType);
        
        $this->addElement('text', 'name', 'Name' );

        $this->addElement('hidden', 'csearch','0' );

        $this->addElement('date', 'start_date', 'Starting:', CRM_Core_SelectValues::$date);
        
        $this->addElement('date', 'end_date', 'Ending:', CRM_Core_SelectValues::$date);

        $arraySearch = array();
        $params = array();
        $arraySearch = $this->controller->exportValues( $this->_name );
 
        if ($this->_mode != self::MODE_UPDATE ) {
            if (strlen(trim($arraySearch['name']))) {
                $params['name']         = $arraySearch['name'];
                $params['contact_type'] = $arraySearch['contact_type'];
            
                $this->getContactList($this, $params);
            }
        }

        $this->addElement( 'submit', $this->getButtonName('refresh'), 'Search', array( 'class' => 'form-submit' ) );
        $this->addElement( 'submit', $this->getButtonName('cancel' ), 'Cancel', array( 'class' => 'form-submit' ) );

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
     *  This function is called when the form is submitted 
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );

        // action is taken depending upon the mode
        $ids = array( );
        $ids['contact'] = $this->_contactId;
        if ($this->_mode & self::MODE_UPDATE ) {
            $ids['relationship'] = $this->_relationshipId;
        }    

        CRM_Contact_BAO_Relationship::create( $params, $ids );

    }//end of function


    /**
     * This function is to get the result of the search for contact in relationship form
     *
     * @param  array $params  This contains elements for search criteria
     *
     * @access public
     * @return None
     *
     */
    function getContactList($this, &$params) {
    
        //max records that will be listed
        $maxResultCount = 50;
        $resultCount = 0;
        $searchName = array();

        $contactBAO = new CRM_Contact_BAO_Contact( );
        $searchName['sort_name'] = $params['name']; 

        if (strlen($params['contact_type'])) { 
            $searchName['cb_contact_type'] = array($params['contact_type'] => 1); 
        }
        // get the count of contact
        $resultCount = $contactBAO->searchQuery($searchName, 0, 51, $sort, true );
                
        if ($resultCount > $maxResultCount) {
            $this->assign('noResult', 'Please enter appropriate search criteria.');
        } else {
            // get the result of the search
            $result = $contactBAO->searchQuery($searchName, 0, 50, $sort, false );

            $config = CRM_Core_Config::singleton( );

            while($result->fetch()) {

                $values[$result->contact_id]['id'] = $result->contact_id;
                $values[$result->contact_id]['name'] = $result->sort_name;
                $values[$result->contact_id]['city'] = $result->city;
                $values[$result->contact_id]['state'] = $result->state;
                $values[$result->contact_id]['email'] = $result->email;
                $values[$result->contact_id]['phone'] = $result->phone;

                $contact_type = '<img src="' . $config->resourceBase . 'i/contact_';
                switch ($result->contact_type ) {
                case 'Individual' :
                    $contact_type .= 'ind.gif" alt="Individual">';
                    break;
                case 'Household' :
                    $contact_type .= 'house.png" alt="Household" height="16" width="16">';
                    break;
                case 'Organization' :
                    $contact_type .= 'org.gif" alt="Organization" height="16" width="18">';
                    break;
                }
                $values[$result->contact_id]['type'] = $contact_type;
                
                $contact_chk[$result->contact_id] = $this->createElement('checkbox', $result->contact_id, null,'');                
            
            }

            $this->addGroup($contact_chk, 'contact_check');
            if ($resultCount == 0) $this->assign('noContacts',' No results were found.');

            $this->assign('contacts', $values);
        }
    }
    

  /**
   * function for validation
   *
   * @param array $params (reference ) an assoc array of name/value pairs
   *
   * @return object CRM_Contact_BAO_Relationship object 
   * @access public
   * @static
   */
    function formRule( &$params ) {
        $errors = array( );
        $errorsMessage = '';

        $ids = array( );
        $session = CRM_Core_Session::Singleton();
        $ids['contact'] = $session->get('contactId','CRM_Core_Controller_Simple');
        $ids['relationship'] = $session->get('relationshipId','CRM_Core_Controller_Simple');

        if (is_array($params['contact_check'])) {
            /*
            foreach ( $params['contact_check'] as $key => $value) {
                $errorsMessage .= CRM_Contact_BAO_Relationship::checkValidRelationship( $params, $ids, $key );
            }*/
        } else {
            $errorsMessage .= CRM_Contact_BAO_Relationship::checkValidRelationship( $params, $ids);
        }

        if (strlen(trim($errorsMessage))) {
            $errors['relationship_type_id'] = ' ';
            CRM_Core_Session::setStatus( $errorsMessage );
        }

        return empty($errors) ? true : $errors;

    }

}

?>
