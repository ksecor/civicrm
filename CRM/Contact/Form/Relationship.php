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
     * max number of contacts we will display for a relationship
     */
    const MAX_RELATIONSHIPS = 50;
          

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
        $this->_rtype          = CRM_Utils_Request::retrieve( 'rtype', $this );
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

        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            $relationship = new CRM_Contact_DAO_Relationship( );
            $relationship->id = $this->_relationshipId;
            if ($relationship->find(true)) {
                $defaults['relationship_type_id'] = $relationship->relationship_type_id . '_' . $this->_rtype;
                $defaults['start_date']           = CRM_Utils_Date::unformat( $relationship->start_date );
                $defaults['end_date'  ]           = CRM_Utils_Date::unformat( $relationship->end_date   );
                
                $contact = new CRM_Contact_DAO_Contact( );
                if ($this->_rtype == 'a_b' && $relationship->contact_id_a == $this->_contactId ) {
                    $contact->id = $relationship->contact_id_b;
                } else {
                    $contact->id = $relationship->contact_id_a;
                }

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
        $this->addRule('relationship_type_id', 'Please select a relationship type. ', 'required' );
        $this->addRule('start_date'          , 'Start date is not valid.'           , 'qfDate' );
        $this->addRule('end_date'            , 'End date is not valid.'             , 'qfDate' );

        $this->addFormRule( array( 'CRM_Contact_Form_Relationship', 'formRule' ) );
    }


    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->addElement('select',
                          'relationship_type_id',
                          'Relationship Type',
                          array('' => '- select -') +
                          CRM_Contact_BAO_Relationship::getContactRelationshipType( $this->_contactId,
                                                                                    $this->_rtype,
                                                                                    $this->_relationshipId ) );
        
        $this->addElement('text', 'name'      , 'Find Target Contact' );
        $this->addElement('date', 'start_date', 'Start Date', CRM_Core_SelectValues::date( 'relative' ) );
        $this->addElement('date', 'end_date'  , 'End Date'  , CRM_Core_SelectValues::date( 'relative' ) );

        $searchRows    = $this->get( 'searchRows'    );
        $searchCount   = $this->get( 'searchCount'   );
        if ( $searchRows ) {
            $checkBoxes = array( );
            foreach ( $searchRows as $id => $row ) {
                $checkBoxes[$id] = $this->createElement('checkbox', $id, null, '' );
            }
            $this->addGroup($checkBoxes, 'contact_check');
            $this->assign('searchRows', $searchRows );

        }

        $this->assign( 'searchCount', $searchCount );
        $this->assign( 'searchDone'  , $this->get( 'searchDone'   ) );
        $this->assign( 'contact_type', $this->get( 'contact_type' ) );

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

        $this->set( 'searchDone', 0 );
        if ( CRM_Utils_Array::value( '_qf_Relationship_refresh', $_POST ) ) {
            $this->search( $params );
            $this->set( 'searchDone', 1 );
            return;
        }

        // action is taken depending upon the mode
        $ids = array( );
        $ids['contact'] = $this->_contactId;
        if ($this->_action & CRM_Core_Action::UPDATE ) {
            $ids['relationship'] = $this->_relationshipId;
            
            $relation = CRM_Contact_BAO_Relationship::getContactIds( $this->_relationshipId );
            $ids['contactTarget'] = ( $relation->contact_id_a == $this->_contactId ) ?
                $relation->contact_id_b : $relation->contact_id_a;
        }    
        
        list( $valid, $invalid, $duplicate ) = CRM_Contact_BAO_Relationship::create( $params, $ids );
        $status = '';
        if ( $valid ) {
            $status .= " $valid new relationship record(s) created.";
        }
        if ( $invalid ) {
            $status .= " $invalid relationship record(s) not created due to invalid target contact type.";
        }
        if ( $duplicate ) {
            $status .= " $duplicate relationship record(s) not created - duplicate of existing relationship.";
        }
        CRM_Core_Session::setStatus( $status );
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
    function search(&$params) {
        //max records that will be listed
        $searchValues = array();
        $searchValues['sort_name'] = $params['name']; 
        $excludedContactIds = array( $this->_contactId );

        if ( $params['relationship_type_id'] ) {
            $relationshipType = new CRM_Contact_DAO_RelationshipType( );
            list( $rid, $direction ) = explode( '_', $params['relationship_type_id'], 2 );
            $relationshipType->id = $rid;
            if ( $relationshipType->find( true ) ) {
                // find all the contacts which have the same relationship
                $relationship = new CRM_Contact_DAO_Relationship( );
                $relationship->relationship_type_id = $rid;
                if ( $direction == 'a_b' ) {
                    $type = $relationshipType->contact_type_b;
                    $relationship->contact_id_a = $this->_contactId;
                } else {
                    $type = $relationshipType->contact_type_a;
                    $relationship->contact_id_b = $this->_contactId;
                }

                $this->set( 'contact_type', $type );
                if ( $type == 'Individual' ) {
                    $searchValues['cb_contact_type'] = array( $type => 1 ); 
                } else if ( $type == 'Household' ) {
                    $searchValues['cb_contact_type'] = array( $type => 2 );
                }  else if ( $type == 'Organization' ) {
                    $searchValues['cb_contact_type'] = array( $type => 3 );
                }

                $relationship->find( );
                while ( $relationship->fetch( ) ) {
                    $excludedContactIds[] = ( $direction == 'a_b' ) ? $relationship->contact_id_b : $relationship->contact_id_a;
                }

                // also do the reverse if a_b == b_a
                if ( $relationshipType->name_a_b === $relationshipType->name_b_a ) {
                    $relationship = new CRM_Contact_DAO_Relationship( );
                    $relationship->relationship_type_id = $rid;
                    $relationship->contact_id_b = $this->_contactId;
                    $relationship->find( );
                    while ( $relationship->fetch( ) ) {
                        $excludedContactIds[] = $relationship->contact_id_a;
                    }
                }
            }
        }

        // get the count of contact
        $contactBAO  = new CRM_Contact_BAO_Contact( );
        $searchCount = $contactBAO->searchQuery($searchValues, 0, 0, null, true );
        $this->set( 'searchCount', $searchCount );
        if ( $searchCount <= self::MAX_RELATIONSHIPS ) {
            // get the result of the search
            $result = $contactBAO->searchQuery($searchValues, 0, 50, null, false );

            $config = CRM_Core_Config::singleton( );
            $searchRows = array( );

            while($result->fetch()) {
                $contactID = $result->contact_id;
                if ( in_array( $contactID, $excludedContactIds ) ) {
                    continue;
                }

                $searchRows[$contactID]['id'] = $contactID;
                $searchRows[$contactID]['name'] = $result->sort_name;
                $searchRows[$contactID]['city'] = $result->city;
                $searchRows[$contactID]['state'] = $result->state;
                $searchRows[$contactID]['email'] = $result->email;
                $searchRows[$contactID]['phone'] = $result->phone;

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
                $searchRows[$contactID]['type'] = $contact_type;
            }

            $this->set( 'searchRows' , $searchRows );
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
        // hack, no error check for refresh
        if ( CRM_Utils_Array::value( '_qf_Relationship_refresh', $_POST ) ) {
            return true;
        }

        $ids = array( );
        $session = CRM_Core_Session::singleton( );
        $ids['contact'     ] = $session->get( 'contactId'     , 'CRM_Core_Controller_Simple' );
        $ids['relationship'] = $session->get( 'relationshipId', 'CRM_Core_Controller_Simple' );

        $errors        = array( );
        if ( CRM_Utils_Array::value( 'contact_check', $params ) && is_array( $params['contact_check'] ) ) {
            foreach ( $params['contact_check'] as $cid => $dontCare ) {
                $message = CRM_Contact_BAO_Relationship::checkValidRelationship( $params, $ids, $cid);
                if ( $message ) {
                    $errors['relationship_type_id'] = $message;
                    break;
                }
            }
        } else {
            $errors['contact_check'] = 'Please select at least one contact.';
        }
        return empty($errors) ? true : $errors;
    }

}

?>
