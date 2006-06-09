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
class CRM_Quest_Form_App_SchoolOther extends CRM_Quest_Form_App
{
    protected $_orgIDsOther;
    protected $_relIDsOther;
    
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
        
        require_once 'CRM/Core/DAO/CustomValue.php';
        $customDAO = & new CRM_Core_DAO_CustomValue();
        $customDAO->char_data    = 'Other School';
        $customDAO->find();
        while ( $customDAO->fetch() ) {
            if(array_key_exists($customDAO->entity_id, $orgIds )) {
                $count = count( $this->_orgIDsOther)+1;
                $this->_orgIDsOther[$count] = $customDAO->entity_id;
            }
        }
      
        //get relationshipID
        require_once 'CRM/Contact/DAO/Relationship.php';
        if (is_array($this->_orgIDsOther)) {
            foreach ( $this->_orgIDsOther as $key => $value ) {
                $dao = & new CRM_Contact_DAO_Relationship();
                $dao->contact_id_b =$value;
                $dao->find(true);
                $this->_relIDsOther[$key] = $dao->id;
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
        if (is_array($this->_orgIDsOther) ) {
            foreach ($this->_orgIDsOther as $key => $value ) {
                if ( $value  ) {
                    $ids         = array( );
                    $orgDefaults = array( );
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
                    $relDAO->id = $this->_relIDsOther[$key]; 
                    if ( $relDAO->find(true) ) {
                        $orgDefaults['date_of_entry'] =  CRM_Utils_Date::unformat( $relDAO->start_date , '-' );
                        $orgDefaults['date_of_exit'] =  CRM_Utils_Date::unformat( $relDAO->end_date , '-' );
                    }
                    
                    //format note for contact
                    if (is_array ($orgDefaults['note'])) {
                        foreach( $orgDefaults['note'] as $k1 => $v1) {
                            $orgDefaults['note'] = $v1['note'];
                            break;
                        }
                    }
                }
               
                foreach ($orgDefaults as $k => $v ) {
                    $defaults[$k."_".$key] = $v;
                }
            }
        }

        // fix for note field 
        require_once 'CRM/Core/DAO.php';
          
        // Assign show and hide blocks lists to the template for optional test blocks (SATII and AP)
        if ( ! ( $this->_action & CRM_Core_Action::VIEW ) ) {
            $this->_showHide =& new CRM_Core_ShowHideBlocks( );
            for ( $i = 2; $i <= 5; $i++ ) {
                if ( CRM_Utils_Array::value( "organization_name_$i", $defaults )) {
                    $this->_showHide->addShow( "otherSchool_info_$i" );
                    $this->_showHide->addHide( 'otherSchool_info_' . $i . '[show]' );
                } else {
                    $this->_showHide->addHide( "otherSchool_info_$i" );
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
    public function buildQuickForm( ) 
    {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Organization' );
        
        require_once 'CRM/Core/ShowHideBlocks.php';

        $otherSchool_info = array( );
        for ( $i = 1; $i <= 5; $i++ ) {
            $this->addElement('text', 'organization_name_'.$i, ts( 'Name of Institution' ), $attributes['organization_name'] );
            $this->addElement('date', 'date_of_entry_'.$i, ts( 'Dates Attended' ), 
                              CRM_Core_SelectValues::date( 'custom', 7, 0, "M\001Y" ) );
            $this->addElement('date', 'date_of_exit_'.$i, ts( 'Dates Attended' ), 
                              CRM_Core_SelectValues::date( 'custom', 7, 2, "M\001Y" ) );
            $this->buildAddressBlock( 1, ts( 'Location' ), null, null, null, null, null, "location_$i" );
            $this->addElement('textarea', "note_{$i}", ts( 'School Description' ), array("rows"=>5,"cols"=>60));
            if ( ! ( $this->_action & CRM_Core_Action::VIEW ) ) {
                $otherSchool_info[$i] = CRM_Core_ShowHideBlocks::links( $this,"otherSchool_info_$i",
                                                                        ts('add another Special Program'),
                                                                        ts('hide this Program'),
                                                                        false );
            }
        }
        $maxOtherSchool = 5;
        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $defaults = $this->setDefaultValues( );
            $maxOtherSchool = 0;
            for ( $i = 1; $i <= 5; $i++ ) {
                if ( CRM_Utils_Array::value( "organization_name_$i", $defaults )) {
                    $maxOtherSchool++;
                }
            }
        }
        
        $this->assign( 'maxOtherSchool', $maxOtherSchool + 1 );
        $this->assign( 'otherSchool_info', $otherSchool_info );
        
        parent::buildQuickForm( );
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
            if ( ! empty( $this->_orgIDsOther ) ) {
                foreach( $this->_orgIDsOther as $orgID ) {
                    CRM_Contact_BAO_Contact::deleteContact( $orgID );
                }
            }
            $this->_orgIDsOther = null;
            $this->relIDsOther  = null;

            foreach( $params as $key => $value ) {
                $keyArray = explode( '_', $key );
                $orgnizationParams[$keyArray[count($keyArray)-1]][substr($key, 0, -2)] = $value ;
            }
            
            foreach( $orgnizationParams as $key => $orgParams) {
                if (! $orgParams['organization_name']) {
                    continue;
                }
                $orgParams['location'][1]['location_type_id'] = 1;
                $orgParams['location'][1]['is_primary'] = 1;
                
                $orgParams['contact_type'] = 'Organization';
                $orgParams['custom_4']     = 'Other School';
                
                $ids = array();
                if ( $this->_orgIDsOther[$key] ) {
                    $idParams = array( 'id' => $this->_orgIDsOther[$key], 'contact_id' => $this->_orgIDsOther[$key] );
                    CRM_Contact_BAO_Contact::retrieve( $idParams, $defaults, $ids );
                }
                
                $org = CRM_Contact_BAO_Contact::create($orgParams, $ids, 2);
                $this->_orgIDsOther[$key] = $org->id; 
                $this->set('orgIDsOther' , $this->_orgIDsOther );
                
                // add data for custom fields 
                require_once 'CRM/Core/BAO/CustomGroup.php';
                $this->_groupTree = & CRM_Core_BAO_CustomGroup::getTree('Organization',$org->id, 0 );
                
                CRM_Core_BAO_CustomGroup::postProcess( $this->_groupTree, $orgParams );
                
                CRM_Core_BAO_CustomGroup::updateCustomData($this->_groupTree,'Organization',$org->id); 
                
                //create a realtionship
                require_once 'CRM/Utils/Date.php';
                $relationshipParams = array();
                
                require_once 'CRM/Contact/DAO/RelationshipType.php';
                $dao = & new CRM_Contact_DAO_RelationshipType();
                $dao->name_a_b = 'Student of';
                $dao->find(true);
                $relID  = $dao->id ;
                
                $relationshipParams['relationship_type_id'] = $relID.'_a_b';
                $relationshipParams['start_date']           = $orgParams['date_of_entry'];
                $relationshipParams['end_date']            =  $orgParams['date_of_exit'];
                $relationshipParams['contact_check']        = array("$org->id" => 1 ); 
                
                if ( $this->relIDsOther[$key] ) {
                    $ids = array('contact' =>$this->_contactID,'relationship' => $this->relIDsOther[$key] ,'contactTarget' =>$organizationID);
                } else {
                    $ids = array('contact' =>$this->_contactID);
                }
                
                $organizationID = $org->id;
                
                require_once 'CRM/Contact/BAO/Relationship.php';
                $relationship= CRM_Contact_BAO_Relationship::add($relationshipParams,$ids,$organizationID);
                $this->relIDsOther[$key] = $relationship->id;
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
        return ts('Other School Information');
    }
}

?>
