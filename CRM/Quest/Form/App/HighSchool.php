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
class CRM_Quest_Form_App_HighSchool extends CRM_Quest_Form_App
{
    static $_orgIDs;
    static $_relIDs;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess( );

        require_once 'CRM/Contact/DAO/RelationshipType.php';
        $dao = & new CRM_Contact_DAO_RelationshipType();
        $dao->name_a_b = 'Student of';
        $dao->find(true);
        $relID  = $dao->id ;

        // to get  OrganizationId and Relationship ID's

        require_once 'CRM/Contact/DAO/Relationship.php';
        $dao = & new CRM_Contact_DAO_Relationship();
        $dao->relationship_type_id = $relID;
        $dao->contact_id_a   	   = $this->_contactID;
        $dao->find();
        $orgIds = array();
        while( $dao->fetch() ) {
            $orgIds[$dao->contact_id_b] = $dao->contact_id_b;
        }
        
        //get Orgnization Ids
        require_once 'CRM/Core/DAO/CustomValue.php';
        $customDAO = & new CRM_Core_DAO_CustomValue();
        $customDAO->char_data    = 'Highschool';
        $customDAO->find();
        while ( $customDAO->fetch() ) {
            if(array_key_exists($customDAO->entity_id,$orgIds)) {
            $count = count( $this->_orgIDs)+1;
            $this->_orgIDs[$count] = $customDAO->entity_id;
            }
        }
        //get relationshipID
        
        
        if (is_array($this->_orgIDs)) {
            foreach ( $this->_orgIDs as $key => $value ) {
                $dao = & new CRM_Contact_DAO_Relationship();
                $dao->contact_id_b =$value;
                $dao->find(true);
                $this->_relIDs[$key] = $dao->id;
            }
        }
        
        $this->set('relIDs' , $this->_relIDs);
        $this->set('orgIDs' , $this->_orgIDs);
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
                    $contact =& CRM_Contact_BAO_Contact::retrieve( &$params, &$orgDefaults, &$ids );
                    
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
                    $relDAO->id = $this->_relIDs[$key]; 
                    if ( $relDAO->find(true) ) {
                        $orgDefaults['date_of_entry'] =  CRM_Utils_Date::unformat( $relDAO->start_date , '-' );;
                    }
                    
                }
                foreach ($orgDefaults as $k => $value ) {
                    $defaults[$k."_".$key] = $value;
                }
            }

        }
        // Assign show and hide blocks lists to the template for optional test blocks (SATII and AP)
        require_once 'CRM/Core/ShowHideBlocks.php';
        $this->_showHide =& new CRM_Core_ShowHideBlocks( );
        for ( $i = 2; $i <= 2; $i++ ) {
            if ( CRM_Utils_Array::value( "organization_name_$i", $defaults )) {
                $this->_showHide->addShow( "HighSchool_$i" );
                $this->_showHide->addHide( 'HighSchool_' . $i . '[show]' );
            } else {
                $this->_showHide->addHide( "HighSchool_$i" );
            }
        }
        
        $this->_showHide->addToTemplate( );
        

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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Organization' );
        $highschool = array( );
        for ( $i = 1; $i < 3; $i++ ) {
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
                              CRM_Core_SelectValues::date( 'custom', 7, 0, "M\001Y" ) );
            $this->addRule('date_of_exit_'.$i, ts('Select a valid date.'), 'qfDate');

            $this->addRadio( 'custom_2_'.$i,
                             ts( 'Your School Is' ),
                             CRM_Core_OptionGroup::values( 'school_type' ) );
            
            $this->addElement('text', 'custom_3_'.$i,
                              ts( 'Number of students in your enter school (all classes)' ),
                              $attributes['organization_name'] );
            //$this->addRule('custom_3',ts('Please enter Number of Students'),'required');
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
            $highschool[$i] = CRM_Core_ShowHideBlocks::links( $this,"HighSchool_$i",
                                                           ts('add another High School '),
                                                           ts('hide this High School'),
                                                           false );
        }
        $this->assign( 'highschool',$highschool );

        $this->addFormRule(array('CRM_Quest_Form_App_HighSchool', 'formRule'));
       
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
        
        if ( (!$params['date_of_entry_1']['M']) && (!$params['date_of_entry_1']['Y']) 
             && (!$params['date_of_exit_1']['M']) && (!$params['date_of_exit_1']['Y'])) {
            $errors["date_of_exit_1"] = "Please enter the date";
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
        if ($this->_action !=  CRM_Core_Action::VIEW ) {
            $params = $this->controller->exportValues( $this->_name );
            
            //delete all contact entries
            require_once 'CRM/Contact/BAO/Contact.php';
           
            if ( is_array( $this->_orgIDs ) ) {
                foreach( $this->_orgIDs as $orgID ) {
                    CRM_Contact_BAO_Contact::deleteContact( $orgID );
                }
            }
            $this->_orgIDs      = null;
            $this->_relIDs      = null;
            
            //format parameters
            foreach( $params as $key => $value ) {
                $keyArray = explode( '_', $key );
                $orgnizationParams[$keyArray[count($keyArray)-1]][substr($key, 0, -2)] = $value ;// need to fix
            }
        
            foreach ( $orgnizationParams as $key => $orgParams) {
                if ( ! $orgParams['organization_name']) {
                    continue;
                }
                
                $orgParams['location'][1]['location_type_id'] = 1;
                $orgParams['location'][1]['is_primary'] = 1 ;
                $orgParams['contact_type'] = 'Organization';
                $orgParams['custom_4'] = 'Highschool';
                
                $ids = array();
                
                if ( $this->_orgIDs[$key] ) {
                    $idParams = array( 'id' => $this->_orgIDs[$key], 'contact_id' => $this->_orgIDs[$key] );
                    CRM_Contact_BAO_Contact::retrieve( $idParams, $defaults, $ids );
                }
                
                $org = CRM_Contact_BAO_Contact::create($orgParams, $ids, 2);
                $this->_orgIDs[$key] = $org->id;
                $this->set('orgIDs' , $this->_orgIDs);
                
                // add data for custom fields 
                require_once 'CRM/Core/BAO/CustomGroup.php';
                $this->_groupTree = & CRM_Core_BAO_CustomGroup::getTree('Organization',$org->id,0);
                
                CRM_Core_BAO_CustomGroup::postProcess( $this->_groupTree, $orgParams );
                
                CRM_Core_BAO_CustomGroup::updateCustomData($this->_groupTree,'Organization',$org->id); 
                
                //create a realtionship
                
                $relationshipParams = array();
                
                require_once 'CRM/Contact/DAO/RelationshipType.php';
                $dao = & new CRM_Contact_DAO_RelationshipType();
                $dao->name_a_b = 'Student of';
                $dao->find(true);
                $relID  = $dao->id ;
                
                $relationshipParams['relationship_type_id'] = $relID.'_a_b';
                $relationshipParams['start_date']           = $orgParams['date_of_entry'];
                $relationshipParams['contact_check']        = array("$org->id" => 1 ); 
                
                $organizationID = $org->id;
               
                
                if ( $this->_relIDs[$key] ) {
                    $ids = array('contact' =>$this->_contactID,'relationship' => $this->_relIDs[$key] ,'contactTarget' =>$organizationID);
                } else {
                    $ids = array('contact' =>$this->_contactID);
                }
                
                
                
                require_once 'CRM/Contact/BAO/Relationship.php';
                
                $relationship= CRM_Contact_BAO_Relationship::add($relationshipParams,$ids,$organizationID);
                $this->_relIDs[$key] = $relationship->id;
                
                $this->set('relIDs' , $this->_relIDs);
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
