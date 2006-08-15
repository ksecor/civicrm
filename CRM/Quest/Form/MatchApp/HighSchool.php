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
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/


/**
 * High School Form Page
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
 * This class generates form components for high school
 * 
 */
class CRM_Quest_Form_MatchApp_HighSchool extends CRM_Quest_Form_App
{
    protected $_orgIDs    = null;
    protected $_ceebCodes = null;
    protected $_relIDs    = null;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess( );

        $relTypeID  = 8;

        // to get  OrganizationId and Relationship ID's
        require_once 'CRM/Contact/DAO/Relationship.php';
        $dao = & new CRM_Contact_DAO_Relationship();
        $dao->relationship_type_id = $relTypeID;
        $dao->contact_id_a   	   = $this->_contactID;
        $dao->find();

        $this->_orgIDs    = array( );
        $this->_relIDs    = array( );
        $this->_ceebCodes = null;

        $orgIDs = array();
        while( $dao->fetch() ) {
            $orgIDs[$dao->contact_id_b] = $dao->id;
        }
        
        $orgString = implode( ',', array_keys( $orgIDs ) );
        if ( $orgString ) {
            
            // now get all the Highschool organizations that have an entity_id in here
            $query = "
SELECT o.contact_id as id
FROM   civicrm_organization o,
       civicrm_custom_value v
WHERE  o.contact_id IN ( $orgString )
  AND  v.custom_field_id = 4
  AND  v.entity_id       = o.contact_id
  AND  v.entity_table    = 'civicrm_contact'
  AND  v.char_data       = 'Highschool'
";
            $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            $count = 1;
            while ( $dao->fetch( ) ) {
                $this->_orgIDs[$count] = $dao->id;
                $this->_relIDs[$dao->id] = $orgIDs[$dao->id];
                $count++;
            }
        }
    }


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
        if (is_array($this->_orgIDs)) {
            foreach ($this->_orgIDs as $key => $value ) {
                if ( $value  ) {
                    $ids = array();
                    $params  = array('contact_id' => $value ,'contact_type' => 'Organization'); 

                    require_once 'CRM/Contact/BAO/Contact.php';
                    $contact =& CRM_Contact_BAO_Contact::retrieve( $params, $orgDefaults, $ids );
                    
                    //set custom data defaults
                    require_once 'CRM/Core/BAO/CustomGroup.php';
                    $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree('Organization',$value , 0);
                    $viewMode = false;
                    $inactiveNeeded = false;
                    if( isset($this->_groupTree) ) {
                        CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree,$orgDefaults, $viewMode, $inactiveNeeded );
                    }
                    
                    // set relationship defaults
                    require_once 'CRM/Utils/Date.php';
                    require_once 'CRM/Contact/DAO/Relationship.php';
                    $relDAO = & new CRM_Contact_DAO_Relationship();
                    $relDAO->id = $this->_relIDs[$value]; 
                    if ( $relDAO->find(true) ) {
                        $orgDefaults['date_of_entry'] = CRM_Utils_Date::unformat( $relDAO->start_date , '-' );;
                        $orgDefaults['date_of_exit']  = CRM_Utils_Date::unformat( $relDAO->end_date , '-' );;
                    }
                    
                    $this->_ceebCodes[$value] = $orgDefaults['custom_1'];
                }

                foreach ($orgDefaults as $k => $value ) {
                    $defaults["{$k}_{$key}"] = $value;
                }
            }
        }

        // Assign show and hide blocks lists to the template for optional test blocks (SATII and AP)
        if ( ! ( $this->_action & CRM_Core_Action::VIEW ) ) {
            require_once 'CRM/Core/ShowHideBlocks.php';
            $this->_showHide =& new CRM_Core_ShowHideBlocks( );
            for ( $i = 2; $i <= 3; $i++ ) {
                if ( CRM_Utils_Array::value( "organization_name_$i", $defaults )) {
                    $this->_showHide->addShow( "id_HighSchool_$i" );
                    $this->_showHide->addHide( 'id_HighSchool_' . $i . '_show' );
                } else {
                    $this->_showHide->addHide( "id_HighSchool_$i" );
                }
            }
            
            $this->_showHide->addToTemplate( );
        }

        return $defaults;
    }
            
    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) {
        
        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Organization' );
        $highschool = array( );
        for ( $i = 1; $i < 4; $i++ ) {
            // name of school
            if ( $i == 1) {
                $title = ts("Current High School");
            	$this->addElement('text', 'organization_name_'. $i ,
                                  $title,
                                  $attributes['organization_name'] );
            	$this->addRule('organization_name_'.$i,ts('Please enter School Name'),'required');
            } else {
                $title = ts("Previous High School");
            	$this->addElement('text', 'organization_name_'. $i ,
                                  $title,
                                  $attributes['organization_name'] );
            }
            
            $this->addElement('text', 'organization_name_'. $i ,
                              $title,
                              $attributes['organization_name'] );
            if ( $i == 1 ) {
                $this->addRule('organization_name_'. $i ,ts('Please enter School Name'),'required');
            }
            
            $this->addElement('text', 'custom_1_'.$i,
                              ts( 'School Search Code' ),
                              $attributes['organization_name'] );
            
            $this->addElement('date', 'date_of_entry_'.$i,
                              ts( 'Dates Attended (month/year)' ),
                              CRM_Core_SelectValues::date( 'custom', 7, 0, "M\001Y" ) );
            $this->addRule('date_of_entry_'.$i, ts('Select a valid date.'), 'qfDate');

            $this->addElement('date', 'date_of_exit_'.$i, ts( 'Dates attended (month/year)' ),
                              CRM_Core_SelectValues::date( 'custom', 7, 2, "M\001Y" ) );
            $this->addRule('date_of_exit_'.$i, ts('Select a valid date.'), 'qfDate');

            $schoolTypes = array( '' => '- select -', 'A' => 'Public', 'B' => 'Independent, Not Religious', 'C' => 'Independent, Catholic', 'D' => 'Other Independent, Religious', 'E' => 'Home School Association', 'F' => 'Charter', 'G' => 'Correspondence', 'H' => 'Other', 'I' => 'Education Provider' );
            $this->addElement( 'select', 'custom_2_'. $i,
                               ts( 'Your School Is' ),
                               $schoolTypes );
            
            $this->addElement('text', 'custom_3_'.$i,
                              ts( 'Number of students in your entire school (all classes)' ),
                              $attributes['organization_name'] );
            $this->addRule('custom_3_'.$i , ts('number of students is not valid value'),'integer');
            
            if ( $i == 1 ) {
                $addressRequired = true;
            } else {
                $addressRequired = null;
            }
            $this->buildAddressBlock( 1,
                                      ts( 'School Address' ),
                                      ts( 'School Phone' ) ,
                                      null,$addressRequired,null,null,'location_'.$i);
            
            require_once 'CRM/Core/ShowHideBlocks.php';
            if ( ! ( $this->_action & CRM_Core_Action::VIEW ) ) {
                $highschool[$i] = CRM_Core_ShowHideBlocks::links( $this,"HighSchool_$i",
                                                                  ts('add another High School '),
                                                                  ts('hide this High School'),
                                                                  false );
            }
        }
        $maxHighschool = 3;
        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $defaults = $this->setDefaultValues( );
            $maxHighschool = 0;
            for ( $i = 1; $i < 3; $i++ ) {
                if ( CRM_Utils_Array::value( "organization_name_$i", $defaults )) {
                    $maxHighschool++;
                }
            }
        }
       
        $this->assign( 'highschool',$highschool );
        $this->assign( 'max', $maxHighschool + 1);
        $this->addFormRule(array('CRM_Quest_Form_MatchApp_HighSchool', 'formRule'));
         
        $this->addElement('text', 'custom_1_'.$i,
                          ts( 'School Search Code' ),
                          $attributes['organization_name'] );
        parent::buildQuickForm( );
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

          // make sure that the user has not messed with school details etc
          if ( $params['custom_1_1'] ) {
              $name = CRM_Core_DAO::getFieldValue( 'CRM_Quest_DAO_CEEB', $params['custom_1_1'], 'school_name', 'code' );
              if ( $name != trim( $params['organization_name_1'] ) ) {
                  $errors['organization_name_1'] = ts( 'You cannot change school details if you have found your school' );
              }
          }

          if ( $params['custom_1_2'] ) {
              $name = CRM_Core_DAO::getFieldValue( 'CRM_Quest_DAO_CEEB', $params['custom_1_2'], 'school_name', 'code' );
              if ( $name != trim( $params['organization_name_2'] ) ) {
                  $errors['organization_name_2'] = ts( 'You cannot change school details if you have found your school' );
              }
          }

          if ( $params['custom_1_3'] ) {
              $name = CRM_Core_DAO::getFieldValue( 'CRM_Quest_DAO_CEEB', $params['custom_1_3'], 'school_name', 'code' );
              if ( $name != trim( $params['organization_name_3'] ) ) {
                  $errors['organization_name_3'] = ts( 'You cannot change school details if you have found your school' );
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

        if ( ! ( $this->_action &  CRM_Core_Action::VIEW ) ) {
            $params = $this->controller->exportValues( $this->_name );
          
            //delete all contact entries
            require_once 'CRM/Contact/BAO/Contact.php';
           
            if ( is_array( $this->_orgIDs ) ) {
                foreach( $this->_orgIDs as $orgID ) {
                    // only delete the relationship
                    $dao = & new CRM_Contact_DAO_Relationship();
                    $dao->id = $this->_relIDs[$orgID];
                    $dao->delete( );
                }
            }

            $this->_orgIDs      = null;
            $this->_relIDs      = null;
            
            //format parameters
            foreach( $params as $key => $value ) {
                $keyArray = explode( '_', $key );
                $organizationParams[$keyArray[count($keyArray)-1]][substr($key, 0, -2)] = $value ;// need to fix
            }

            foreach ( $organizationParams as $key => $orgParams) {
                if ( ! $orgParams['organization_name']) {
                    continue;
                }
                
                if ( $orgParams['custom_1'] ) {
                    require_once 'CRM/Quest/BAO/CEEB.php';
                    $org =& CRM_Quest_BAO_CEEB::createOrganization( $orgParams['custom_1'],
                                                                    $orgParams['location'][1]['phone'][1]['phone'] );
                } else {
                    $orgParams['location'][1]['location_type_id'] = 1;
                    $orgParams['location'][1]['is_primary']       = 1 ;
                    $orgParams['contact_type']                    = 'Organization';
                
                    $ids = array();
                    $org =& CRM_Contact_BAO_Contact::create($orgParams, $ids, 1);
                }

                $this->_orgIDs[$key] = $org->id;
                
                $orgParams['custom_4']                        = 'Highschool';

                // add data for custom fields 
                require_once 'CRM/Core/BAO/CustomGroup.php';
                $this->_groupTree = & CRM_Core_BAO_CustomGroup::getTree('Organization',$org->id,0);
                
                CRM_Core_BAO_CustomGroup::postProcess( $this->_groupTree, $orgParams );
                
                CRM_Core_BAO_CustomGroup::updateCustomData($this->_groupTree,'Organization',$org->id); 
                
                //create a realtionship
                $relationshipParams = array();
                
                $relTypeID = 8;
                
                $relationshipParams['relationship_type_id'] = $relTypeID.'_a_b';
                $relationshipParams['start_date']           = $orgParams['date_of_entry'];
                $relationshipParams['end_date']             = $orgParams['date_of_exit'];
                $relationshipParams['contact_check']        = array("$org->id" => 1 ); 
                
                $organizationID = $org->id;
               
                
                $ids = array('contact' => $this->_contactID );
                
                require_once 'CRM/Contact/BAO/Relationship.php';
                $relationship= CRM_Contact_BAO_Relationship::add($relationshipParams,$ids,$organizationID);
            }
        }
        parent::postProcess( );
          
    }//end of function

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('High School Information');
    }
}
?>
