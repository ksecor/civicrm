<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/


/**
 * Personal Information Form Page
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_App_Household extends CRM_Quest_Form_App
{
    /**
     * This function sets the default values for the form. Relationship that in edit/view action
     * the default values are retrieved from the database
     * 
     * @access public
     * @return void
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        
        $session =& CRM_Core_Session::singleton( );
        $this->_contactId = $session->get( 'userID' );
        if ( $this->_contactId ) {
            for ( $i = 1; $i <= 2; $i++ ) {
                require_once 'CRM/Quest/DAO/Household.php';
                $dao = & new CRM_Quest_DAO_Household();
                $dao->contact_id     = $this->_contactId ;
                if ($i == 1) {
                    $dao->household_type = 'Current';
                } else {
                    $dao->household_type = 'Previous';
                }
                if ( $dao->find(true) ) {
                    $defaults['member_count_'.$i]   = $dao->member_count;
                    $defaults['years_lived_id_'.$i] = $dao->years_lived_id;
                    $defaults['description']     = $dao->description;
                    for ( $j = 1; $j <= 2; $j++ ) {
                        require_once 'CRM/Quest/DAO/Person.php';
                        $personDAO = & new CRM_Quest_DAO_Person();
                        $string = "person_".$j."_id"; 
                        $personDAO->id = $dao->$string;
                        if ( $dao->$string && $personDAO->find(true) ) {
                            $defaults['relationship_id_'.$i.'_'.$j] = $personDAO->relationship_id;
                            $defaults['first_name_'.$i.'_'.$j]      = $personDAO->first_name;
                            $defaults['last_name_'.$i.'_'.$j]       = $personDAO->last_name;
                        }
                    }
                }
            }
        }
        
        return $defaults;
    }
    

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Household');
        for ( $i = 1; $i <= 2; $i++ ) {
            if ( $i == 1 ) {
                $title = ts( 'How many people live with you in your current household?' );
            } else {
                $title = ts( 'How many people lived with you in your previous household?' );
            }
            $this->addElement( 'text',
                               'member_count_' . $i,
                               $title,
                               $attributes['member_count'] );
            if ( $i == 1 ) {
                $this->addRule( "member_count_$i",ts('Please enter the number of people who live with you.'),'required');
            }
            $this->addRule('member_count_'.$i,ts('Not a valid number.'),'integer');

            for ( $j = 1; $j <= 2; $j++ ) {
                $this->addSelect( "relationship",
                                   ts( 'Relationship' ),
                                  "_".$i."_".$j );
                $this->addElement( 'text', "first_name_".$i."_".$j,
                                   ts('First Name'),
                                   $attributes['first_name'] );
                
                $this->addElement( 'text', "last_name_".$i."_".$j,
                                   ts('Last Name'),
                                   $attributes['last_name'] );
                
                if ( $i == 2 ) {
                    $this->addElement( 'checkbox', "same_".$i."_".$j, null, null, array('onClick' =>"copyNames()") );
                }
            }

            $this->addSelect( "years_lived",
                              ts( 'How long have you lived in this household?' ),
                              "_".$i );
        }

        $this->addElement('textarea',
                          'description',
                          ts( 'If this section above does not adequately capture your primary caregiver situation (e,g, perhaps your older sibling was your guardian), or if you have any other unique circumstances regarding your household situation, please describe it here:' ),
                          CRM_Core_DAO::getAttribute( 'CRM_Quest_DAO_Household', 'description' ) );

        $this->addFormRule(array('CRM_Quest_Form_App_Household', 'formRule'));

        parent::buildQuickForm( );

    }//end of function

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('Household Information');
    }

    /**
     * Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    public function formRule(&$params) {
        $errors = array( );
        $numBlocks = 2;

        for ( $i = 1; $i <= $numBlocks; $i++ ) {
            for ( $j = 1; $j <= $numBlocks; $j++ ) {
                if ($params["relationship_id_".$i."_".$j]) {
                    if (! $params["first_name_".$i."_".$j]) {
                        $errors["first_name_".$i."_".$j] = "Please enter the family member First Name.";
                    }
                    if (! $params["last_name_".$i."_".$j]) {
                        $errors["last_name_".$i."_".$j] = "Please enter the family member Last Name.";
                    }
                    if ( $i != 1 && ! is_numeric( $params["member_count_$i"] ) && $params["member_count_$i"] <= 0 ) {
                        $errors["member_count_".$i] = "Please enter the number of people who lived with you";                        
                    }
                } else {
                    if ($params["first_name_".$i."_".$j] || $params["last_name_".$i."_".$j]) {
                        $errors["relationship_id_".$i."_".$j] = "Please select the type of Family Member.";
                    }
                }
            }
            if ($params["relationship_id_".$i."_1"] || $params["relationship_id_".$i."_2"]) {
                if (! $params["years_lived_id_".$i]) {
                    $errors["years_lived_id_".$i] = "Please specify the number of years you lived in the household.";
                }
            }
        }

        return empty($errors) ? true : $errors;
    }

    /** 
     * process the form after the input has been submitted and validated 
     * 
     * @access public 
     * @return void 
     */ 
    public function postProcess()  
    { 
        // get all the relevant details so we can decide the detailed information we need
        $params  = $this->controller->exportValues( 'Household' );
        $relationship = CRM_Core_OptionGroup::values( 'relationship' );

        $details = array( );
        for ( $i = 1; $i <= 2; $i++ ) {
            $householdParams = array( );
            $householdParams['contact_id']      = $this->get('contact_id'); 
            $householdParams['household_type'] = ( $i == 1 ) ? 'Current' : 'Previous';
            $householdParams['member_count']   = $params["member_count_$i"];
            $householdParams['years_lived_id'] = $params["years_lived_id_$i"];
            
            if ( $i == 1 ) {
                $householdParams['description'] = $params["description"];
            }

            $needed = false;
            for ( $j = 1; $j <= 2; $j++ ) {
                $personID = $this->getRelationshipDetail( $details, $relationship, $params, $i, $j );
                if ( $personID ) {
                    $needed = true;
                    $householdParams["person_{$j}_id"] = $personID;
                }
            }

            if ( $needed ) {
                // now create the household
                require_once 'CRM/Quest/BAO/Household.php';
                $dao                 =& new CRM_Quest_DAO_Household();
                $dao->contact_id     =  $householdParams['contact_id'];
                $dao->household_type =  $householdParams['household_type'];
                $id = null;
                if ( $dao->find(true) ) {
                    $id = $dao->id;
                }
                $ids = array( 'id' => $id );
                CRM_Quest_BAO_Household::create( $householdParams , $ids );
            }
        }

        // make sure we have a mother and father in there
        if ( ! CRM_Utils_Array::value( 'Mother', $details ) ) {
            $details['Mother'] = array( 'className' => 'CRM_Quest_Form_App_Guardian',
                                        'title' => 'Mother Details',
                                        'options' => null );
        }

        if ( ! CRM_Utils_Array::value( 'Father', $details ) ) {
            $details['Father'] = array( 'className' => 'CRM_Quest_Form_App_Guardian',
                                        'title' => 'Father Details',
                                        'options' => null );
        }
        
        $this->set( 'householdDetails', $details );
    }//end of function 

    public function getRelationshipDetail( &$details, &$relationship, &$params, $i, $j ) {
        $first = CRM_Utils_Array::value( "first_name_{$i}_{$j}", $params );
        $last  = CRM_Utils_Array::value( "last_name_{$i}_{$j}" , $params );
        $relationshipID = CRM_Utils_Array::value( "relationship_id_{$i}_{$j}", $params );
        
        $name = trim( $first . ' ' . $last );
        if ( ! $name ) {
            return;
        }

        $relationshipName = trim( CRM_Utils_Array::value( $relationshipID,
                                                          $relationship ) );

        if ( ! $relationshipName ) {
            return;
        }

        if ( CRM_Utils_Array::value( "same_{$i}_{$j}", $params ) ) {
            if ( ! CRM_Utils_Array::value( $relationshipName, $details ) ) {
                CRM_Core_Error::fatal( ts( "This should have been trapped in a form rule" ) );
            }
            return $details[$relationshipName]['options']['personID'];
        }

        // we also need to create the person record here
        $params['first_name']         = $first;
        $params['last_name' ]         = $last;
        $params['relationship_id']    = $relationshipID;
        $params['contact_id']         = $this->get('contact_id');
        $params['is_parent_guardian'] = true;

        $ids = array( );

        require_once 'CRM/Quest/BAO/Person.php'; 

        $dao = new CRM_Quest_DAO_Person(); 
        $dao->contact_id      = $this->get('contact_id'); 
        $dao->relationship_id = $relationshipID; 
        $personID = null;
        if ( $dao->find(true) ) { 
            $personID = $dao->id; 
        }
        $ids = array( );
        $ids['id'] = $personID;
        $person = CRM_Quest_BAO_Person::create( $params , $ids );
        if ( ! $personID ) {
            $personID = $person->id;
        }

        $details[$relationshipName] = array( 'className' => 'CRM_Quest_Form_App_Guardian',
                                             'title' => "$name Details",
                                             'options' => array( 'personID'       => $personID,
                                                                 'relationshipID' => $relationshipID ) );
        return $personID;
    }

    static function &getPages( &$controller ) {
        $details       = $controller->get( 'householdDetails' );

        if ( ! $details ) {
            $cid = $controller->get( 'contact_id' ); 
            require_once 'CRM/Quest/DAO/Person.php';
            $dao =& new CRM_Quest_DAO_Person( );
            $dao->contact_id = $cid;
            $dao->is_parent_guardian = true;
            $dao->find( );
            $details = array( );
            $relationship = CRM_Core_OptionGroup::values( 'relationship' );
            while ( $dao->fetch( ) ) {
                $relationshipName = trim( CRM_Utils_Array::value( $dao->relationship_id,
                                                                  $relationship ) );
                $name = trim( "{$dao->first_name} {$dao->last_name}" );
                $details[$relationshipName] = array( 'className' => 'CRM_Quest_Form_App_Guardian', 
                                                     'title' => "$name Details",
                                                     'options' => array( 'personID'       => $dao->id,
                                                                         'relationshipID' => $dao->relationshipID ) );
            }
            if ( empty( $details ) ) {
                $details = array( 'Mother' => array( 'className' => 'CRM_Quest_Form_App_Guardian',
                                                     'title' => 'Mother Details',
                                                     'options' => null ),
                                  'Father' => array( 'className' => 'CRM_Quest_Form_App_Guardian',
                                                     'title' => 'Father Details',
                                                     'options' => null ) );
            }
            $controller->set( 'householdDetails', $details );
        }
        return $details;
    }

}

?>