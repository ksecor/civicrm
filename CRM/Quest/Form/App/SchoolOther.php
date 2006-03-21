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
                          CRM_Core_SelectValues::date( 'custom', 7, 0, "Y\001M" ) );
        
        $this->addElement('date', 'date_of_exit',
                          ts( 'Dates Attended' ),
                          CRM_Core_SelectValues::date( 'custom', 7, 0, "Y\001M" ) );
        
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
        $contactID = $this->get('contact_id');
        $params['contact_type'] = 'Organization';
        
        $ids = array();
        $org_2_id = $this->get('org_2_id');
        
        if ( $org_2_id ) {
            $idParams = array( 'id' => $org_2_id, 'contact_id' => $org_2_id );
            CRM_Contact_BAO_Contact::retrieve( $idParams, $defaults, $ids );
        }
        
        $org = CRM_Contact_BAO_Contact::create($params, $ids, 2);
        $this->set('org_2_id' , $org->id );
        
        // add data for custom fields 
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $this->_groupTree = & CRM_Core_BAO_CustomGroup::getTree('Organization',$this->_relationshipId,0);
        CRM_Core_BAO_CustomGroup::updateCustomData($this->_groupTree,'Organization',$org->id); 
        CRM_Core_BAO_CustomGroup::postProcess( $this->_groupTree, $params );
        
        //create a realtionship
        $relationshipParams = array();
        
        $relationshipParams['relationship_type_id'] = '1_a_b';
        $relationshipParams['start_date']           = $params['date_of_entry'];
        $relationshipParams['end_date']           = $params['date_of_exit'];
        $relationshipParams['contact_check']        = array("$org->id" => 1 ); 
        
        $rel_2_id = $this->get('rel_2_id');
        
        if ( $rel_2_id ) {
            $ids = array('contact' =>$contactID,'relationship' => $rel_2_id ,'contactTarget' =>$organizationID);
        } else {
            $ids = array('contact' =>$contactID);
        }
        
        $organizationID = $org->id;
        
        require_once 'CRM/Contact/BAO/Relationship.php';
        $relationship= CRM_Contact_BAO_Relationship::add($relationshipParams,$ids,$organizationID);
        $this->set('rel_2_id' , $relationship->id );
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