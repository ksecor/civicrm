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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Quest/Form/App.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_SchoolSearch extends CRM_Quest_Form_App
{
    /**
     * max number of schools we will display
     */
    const MAX_SCHOOLS = 50;
          
    function preProcess( ) 
    {

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
        }

        return $defaults;
    }
    

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        
        $this->addElement('text', 'school_name'      , ts('School Name'    ) );
        $this->addElement('text', 'school_name'      , ts('Postal Code'    ) );
        $this->addElement('text', 'city'             , ts('City'           ) );
        $this->addElement('text', 'state_province'   , ts('State Province' ) );

        $searchRows            = $this->get( 'searchRows'    );
        $searchCount           = $this->get( 'searchCount'   );
        $duplicateRelationship = $this->get( 'duplicateRelationship' );
        $searchDone            = $this->get( 'searchDone' );

        if ( $searchRows ) {
            $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Organization' );

            $this->addElement('text', 'code', ts( 'CEEB Code' ) );
            $this->add('text', 'organization_name',
                       ts( 'School Name' ),
                       $attributes['organization_name'], true );
            $this->addElement('text', 'custom_1_'.$i,
                              ts( 'School Search Code' ),
                              $attributes['organization_name'] );
            
            $this->addElement('date', 'date_of_entry',
                              ts( 'Dates Attended (month/year)' ),
                              CRM_Core_SelectValues::date( 'custom', 7, 0, "M\001Y" ) );
            $this->addRule('date_of_entry', ts('Select a valid date.'), 'qfDate');

            $this->addElement('date', 'date_of_exit', ts( 'Dates attended (month/year)' ),
                              CRM_Core_SelectValues::date( 'custom', 7, 2, "M\001Y" ) );
            $this->addRule('date_of_exit', ts('Select a valid date.'), 'qfDate');

            $schoolTypes = array( 310 => 'Public', 311 => 'Private', 312 => 'Parochial' );
            $this->addRadio( 'custom_2',
                             ts( 'Your School Is' ),
                             $schoolTypes );
            
            $this->addElement('text', 'custom_3',
                              ts( 'Number of students in your entire school (all classes)' ),
                              $attributes['organization_name'] );
            $this->addRule('custom_3', ts('number of students is not valid value'),'integer');
            
            $this->buildAddressBlock( 1,
                                      ts( 'School Address' ),
                                      ts( 'School Phone' ) ,
                                      null, true, null, null, 'location');
            
        } 
        
        $this->assign('searchCount'          , $searchCount);
        $this->assign('searchDone'           , $searchDone);

        if ( $searchDone ) {
            $searchBtn = ts('Search Again');
        } else {
            $searchBtn = ts('Find My School');
        }
        $this->addElement( 'submit', $this->getButtonName('refresh'), $searchBtn, array( 'class' => 'form-submit' ) );
        $this->addElement( 'submit', $this->getButtonName('cancel' ), ts('Cancel'), array( 'class' => 'form-submit' ) );

        $this->addButtons( array(
                                 array ( 'type'      => $buttonType,
                                         'name'      => ts('Save Relationship'),
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ),
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
        
        if ($this->_action & CRM_Core_Action::DELETE ){
            CRM_Contact_BAO_Relationship::del($this->_relationshipId); 
            return;
        }
        
        if ($this->_action & CRM_Core_Action::UPDATE ) {
            $ids['relationship'] = $this->_relationshipId;
            
            $relation = CRM_Contact_BAO_Relationship::getContactIds( $this->_relationshipId );
            $ids['contactTarget'] = ( $relation->contact_id_a == $this->_contactId ) ?
                $relation->contact_id_b : $relation->contact_id_a;
        }    

        list( $valid, $invalid, $duplicate, $saved, $relationshipIds ) = CRM_Contact_BAO_Relationship::create( $params, $ids );
        $status = '';
        if ( $valid ) {
            $status .= ' ' . ts('%count new relationship record created.', array('count' => $valid, 'plural' => '%count new relationship records created.'));
        }
        if ( $invalid ) {
            $status .= ' ' . ts('%count relationship record not created due to invalid target contact type.', array('count' => $invalid, 'plural' => '%count relationship records not created due to invalid target contact type.'));
        }
        if ( $duplicate ) {
            $status .= ' ' . ts('%count relationship record not created - duplicate of existing relationship.', array('count' => $duplicate, 'plural' => '%count relationship records not created - duplicate of existing relationship.'));
        }
        if ( $saved ) {
            $status .= ts('Relationship record has been updated.');
        }
        
        CRM_Core_BAO_CustomGroup::postProcess( $this->_groupTree, $params );
        foreach($relationshipIds as $index => $id) {
            CRM_Core_BAO_CustomGroup::updateCustomData($this->_groupTree,'Relationship',$id); 
        }

        if ($this->_action & CRM_Core_Action::UPDATE ) {
            $note =& new CRM_Core_DAO_Note( );
            $note->entity_id = $relationshipIds[0];
            $note->entity_table = 'civicrm_relationship';
            if ($note->find(true)) {
                $id = $note->id;
                $noteParams = array(
                                    'entity_id'     => $relationshipIds[0],
                                    'entity_table'  => 'civicrm_relationship',
                                    'note'          => $params['note'],
                                    'id'            => $id
                                    );
                CRM_Core_BAO_Note::add($noteParams);
            }
        } else {
            if ( CRM_Utils_Array::value( 'note', $params ) ) {
                foreach($relationshipIds as $index => $id) {
                    $noteParams = array(
                                        'entity_id'     => $id,
                                        'entity_table'  => 'civicrm_relationship',
                                        'note'          => $params['note']
                                        );
                    CRM_Core_BAO_Note::add($noteParams);
                }
            }
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
        $searchValues[] = array( 'sort_name', 'LIKE', $params['name'], 0, 1 );
        $contactTypeAdded = false;
        
        $excludedContactIds = array( $this->_contactId );

        if ( $params['relationship_type_id'] ) {
            $relationshipType =& new CRM_Contact_DAO_RelationshipType( );
            list( $rid, $direction ) = explode( '_', $params['relationship_type_id'], 2 );
           
            $relationshipType->id = $rid;
            if ( $relationshipType->find( true ) ) {
                if ( $direction == 'a_b' ) {
                    $type = $relationshipType->contact_type_b;
                } else {
                    $type = $relationshipType->contact_type_a;
                }

                $this->set( 'contact_type', $type );
                if ( $type == 'Individual' ) {
                    $searchValues[] = array( 'contact_type', '=', array( $type => 1 ), 0, 0 );
                    $contactTypeAdded = true;
                } else if ( $type == 'Household' ) {
                    $searchValues[] = array( 'contact_type', '=', array( $type => 2 ), 0, 0 );
                    $contactTypeAdded = true;
                }  else if ( $type == 'Organization' ) {
                    $searchValues[] = array( 'contact_type', '=', array( $type => 3 ), 0, 0 );
                    $contactTypeAdded = true;
                }
            }
        }

        if ( ! $contactTypeAdded && CRM_Utils_Array::value( 'contact_type', $params ) ) {
            $searchValues[] = array( 'contact_type', '=', $params['contact_type'], 0, 0 );
        }

        // get the count of contact
        $contactBAO  =& new CRM_Contact_BAO_Contact( );
        $query =& new CRM_Contact_BAO_Query( $searchValues );
        $searchCount = $query->searchQuery(0, 0, null, true );
        $this->set( 'searchCount', $searchCount );
        if ( $searchCount <= self::MAX_RELATIONSHIPS ) {
            // get the result of the search
            $result = $query->searchQuery(0, 50, null);

            $config =& CRM_Core_Config::singleton( );
            $searchRows = array( );

            //variable is set if only one record is foun and that record already has relationship with the contact
            $duplicateRelationship = 0;
            
            while($result->fetch()) {
                $contactID = $result->contact_id;
                if ( in_array( $contactID, $excludedContactIds ) ) {
                    $duplicateRelationship++;
                    continue;
                }

                $duplicateRelationship = 0;                

                $searchRows[$contactID]['id'] = $contactID;
                $searchRows[$contactID]['name'] = $result->sort_name;
                $searchRows[$contactID]['city'] = $result->city;
                $searchRows[$contactID]['state'] = $result->state;
                $searchRows[$contactID]['email'] = $result->email;
                $searchRows[$contactID]['phone'] = $result->phone;

                $contact_type = '<img src="' . $config->resourceBase . 'i/contact_';
                switch ($result->contact_type ) {
                case 'Individual' :
                    $contact_type .= 'ind.gif" alt="' . ts('Individual') . '" />';
                    break;
                case 'Household' :
                    $contact_type .= 'house.png" alt="' . ts('Household') . '" height="16" width="16" />';
                    break;
                case 'Organization' :
                    $contact_type .= 'org.gif" alt="' . ts('Organization') . '" height="16" width="18" />';
                    break;
                }
                $searchRows[$contactID]['type'] = $contact_type;
            }

            $this->set( 'searchRows' , $searchRows );
            $this->set('duplicateRelationship', $duplicateRelationship);
        } else {
            // resetting the session variables if many records are found
            $this->set( 'searchRows' , null );
            $this->set('duplicateRelationship', null);
        }
    }
    

  /**
   * function for validation
   *
   * @param array $params (reference ) an assoc array of name/value pairs
   *
   * @return mixed true or array of errors
   * @access public
   * @static
   */
    static function formRule( &$params ) {
        // hack, no error check for refresh
        if ( CRM_Utils_Array::value( '_qf_Relationship_refresh', $_POST ) ) {
            return true;
        }

        $ids = array( );
        $session =& CRM_Core_Session::singleton( );
        $ids['contact'     ] = $session->get( 'contactId'     , 'CRM_Contact_Form_Relationship' );
        $ids['relationship'] = $session->get( 'relationshipId', 'CRM_Contact_Form_Relationship' );

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
            $errors['contact_check'] = ts( 'Please select at least one contact.' );
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * function for date validation
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    static function dateRule( &$params ) {
        $errors = array( );

        // check start and end date
        if ( CRM_Utils_Array::value( 'start_date', $params ) &&
             CRM_Utils_Array::value( 'end_date'  , $params ) ) {
            $start_date = CRM_Utils_Date::format( CRM_Utils_Array::value( 'start_date', $params ) );
            $end_date   = CRM_Utils_Date::format( CRM_Utils_Array::value( 'end_date'  , $params ) );
            if ( $start_date && $end_date && (int ) $end_date < (int ) $start_date ) {
                $errors['end_date'] = ts( 'The relationship end date cannot be prior to the start date.' );
            }
        }

        return empty($errors) ? true : $errors;

    }

}

?>
