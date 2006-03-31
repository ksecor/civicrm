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
    static $_orgIDOther;
    static $_relIDOther;
    
    
    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $contactID = $this->get( 'contact_id' );
        //to get relationship id
        require_once 'CRM/Contact/DAO/Relationship.php';
        $relDAO = & new CRM_Contact_DAO_Relationship();
        $relDAO->contact_id_a            = $contactID;
        $relDAO->relationship_type_id    = 1;//need to fix  	  
        $relDAO->find(); 
        while ( $relDAO->fetch()) {
           $orgID = $this->get('orgID');
           if ( $relDAO->contact_id_b != $orgID  ) {
               $this->_orgIDOther = $relDAO->contact_id_b;
               $this->_relIDOther = $relDAO->id;
               $this->set('orgIDOther' , $relDAO->contact_id_b);
               $this->set('relIDOther' , $relDAO->id);
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
        if ( $this->_orgIDOther ) {
            $ids = array();
            $params  = array('contact_id' => $this->_orgIDOther ,'contact_type' => 'Organization'); 
            require_once 'CRM/Contact/BAO/Contact.php';
            $contact =& CRM_Contact_BAO_Contact::retrieve( &$params, &$defaults, &$ids );
            
            //set custom data defaults
            require_once 'CRM/Core/BAO/CustomGroup.php';
            $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree('Organization',$this->_orgIDOther, 0);
            $viewMode = false;
            $inactiveNeeded = false;
            if( isset($this->_groupTree) ) {
                CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, $viewMode, $inactiveNeeded );
            }
            
            // set relationship defaults
            require_once 'CRM/Utils/Date.php';
            require_once 'CRM/Contact/DAO/Relationship.php';
            $relDAO = & new CRM_Contact_DAO_Relationship();
            $relDAO->id = $this->_relIDOther; 
            if ( $relDAO->find(true) ) {
                $defaults['date_of_entry'] = CRM_Utils_Date::unformat( $relDAO->start_date,'-' );
                $defaults['date_of_exit']  = CRM_Utils_Date::unformat( $relDAO->start_date,'-' );
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Organization' );
        
        // name of school
        $this->addElement('text', 'organization_name',
                          ts( 'Name of Institution' ),
                          $attributes['organization_name'] );
        
        $this->addElement('date', 'date_of_entry',
                          ts( 'Dates Attended' ),
                          CRM_Core_SelectValues::date( 'custom', 7, 0, "M\001Y" ) );
        
        $this->addElement('date', 'date_of_exit',
                          ts( 'Dates Attended' ),
                          CRM_Core_SelectValues::date( 'custom', 7, 0, "M\001Y" ) );
        
        $this->buildAddressBlock( 1,
                                  ts( 'Location' ),null );

        $this->addElement('textarea',
                          'note',
                          ts( 'School Description' ),
                          CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Note', 'note' ) );

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
        $params = $this->controller->exportValues( $this->_name );
        $params['location'][1]['location_type_id'] = 1;
        $params['location'][1]['is_primary'] = 1 ;

        $contactID = $this->get('contact_id');
        $params['contact_type'] = 'Organization';
        
        $ids = array();
        $this->_orgIDOther = $this->get('orgIDOther');
        
        if ( $this->_orgIDOther ) {
            $idParams = array( 'id' => $this->_orgIDOther, 'contact_id' => $this->_orgIDOther );
            CRM_Contact_BAO_Contact::retrieve( $idParams, $defaults, $ids );
        }
        
        $org = CRM_Contact_BAO_Contact::create($params, $ids, 2);
        $this->set('orgIDOther' , $org->id );
        
        // add data for custom fields 
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $this->_groupTree = & CRM_Core_BAO_CustomGroup::getTree('Organization',$this->_relationshipId,0);
        CRM_Core_BAO_CustomGroup::updateCustomData($this->_groupTree,'Organization',$org->id); 
        CRM_Core_BAO_CustomGroup::postProcess( $this->_groupTree, $params );
        
        //create a realtionship
        require_once 'CRM/Utils/Date.php';
        $relationshipParams = array();
        
        $relationshipParams['relationship_type_id'] = '1_a_b';
        $relationshipParams['start_date']           = CRM_Utils_Date::format($params['date_of_entry']);
        $relationshipParams['end_date']            =  CRM_Utils_Date::format($params['date_of_exit'] );
        $relationshipParams['contact_check']        = array("$org->id" => 1 ); 
        
        $this->relIDOther = $this->get('relIDOther');
        
        if ( $this->relIDOther ) {
            $ids = array('contact' =>$contactID,'relationship' => $this->relIDOther ,'contactTarget' =>$organizationID);
        } else {
            $ids = array('contact' =>$contactID);
        }
        
        $organizationID = $org->id;
        
        require_once 'CRM/Contact/BAO/Relationship.php';
        $relationship= CRM_Contact_BAO_Relationship::add($relationshipParams,$ids,$organizationID);
        $this->set('relIDOther' , $relationship->id );
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