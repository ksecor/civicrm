<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/SelectValues.php';

/**
 * This class generates form components generic to all the contact types.
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
 */
class CRM_Contact_Form_RelatedContact extends CRM_Core_Form
{
    /**
     * The contact type of the form
     *
     * @var string
     */
    protected $_contactType;

    /**
     * The contact type of the form
     *
     * @var string
     */
    protected $_contactSubType;

    /**
     * The contact id, used when editing the form
     *
     * @var int
     */
    public $_contactId;
    
    protected $_maxLocationBlocks = 0;

    protected $_editOptions = array( );

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) 
    {
        // reset action from the session
        $this->_action              = CRM_Utils_Request::retrieve('action', 'String', 
                                                                  $this, false, 'update' );
        
        // find the system config related location blocks
        require_once 'CRM/Core/BAO/Preferences.php';
        //since we are only editing the primary location
        $this->_maxLocationBlocks = 1;

        $this->_editOptions  = CRM_Core_BAO_Preferences::valueOptions( 'contact_edit_options' );

        // this is update mode, first get the id from the session
        // else get it from the REQUEST
        
        if ( ! $this->_contactId ) {
            $this->_contactId   = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, true );
        }
        
        if ( $this->_contactId ) {
            require_once 'CRM/Contact/BAO/Contact.php';
            $contact =& new CRM_Contact_DAO_Contact( );
            $contact->id = $this->_contactId;
            if ( ! $contact->find( true ) ) {
                CRM_Core_Error::statusBounce( ts('contact does not exist: %1', array(1 => $this->_contactId)) );
            }
            $this->_contactType    = $contact->contact_type;
            $this->_contactSubType = $contact->contact_sub_type;
           
            // check for permissions
            require_once 'CRM/Contact/BAO/Contact/Permission.php';
            if ( ! CRM_Contact_BAO_Contact_Permission::allow( $this->_contactId, CRM_Core_Permission::EDIT ) ) {
                CRM_Core_Error::statusBounce( ts('You do not have the necessary permission to edit this contact.') );
            }
            
            list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $this->_contactId );
            CRM_Utils_System::setTitle( $contactImage . ' ' . $displayName ); 
            
            //get the no of locations for the contact
            $this->_maxLocationBlocks = CRM_Contact_BAO_Contact::getContactLocations( $this->_contactId );
            return;
        }
        CRM_Core_Error::statusBounce( ts('Could not get a contact_id and/or contact_type') );
    }
    

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        $params   = array( );

        $config =& CRM_Core_Config::singleton( );

        // this is update mode
        // get values from contact table
        $params['id'] = $params['contact_id'] = $this->_contactId;
        $contact = CRM_Contact_BAO_Contact::retrieve( $params, $defaults );
        
        $locationExists = array( );
        
        foreach( $contact->location as $index => $loc) {
            $locationExists[] = $loc['location_type_id'];
            //to get the billing location
            $defaults['location'][$index]['is_billing'] = CRM_Utils_Array::value( 'is_billing' ,
                                                                                  $defaults['location'][$index]['address'] );
        }
        $this->assign( 'locationExists' , $locationExists );
        
        $this->assign( 'contactId' , $this->_contactId );
        // also set contact_type, since this is used in showHide routines 
        // to decide whether to display certain blocks (demographics)
        $this->_contactType = CRM_Utils_Array::value( 'contact_type', $defaults );
        
        //check primary for first location
        $defaults['location'][1]['is_primary'] = true;
      
        //set defaults for country-state dojo widget
        if ( ! empty ( $defaults['location'] ) ) {
            $countries      =& CRM_Core_PseudoConstant::country( );
            $stateProvinces =& CRM_Core_PseudoConstant::stateProvince( false, false );
            
            foreach ( $defaults['location'] as $key => $value ) {
                if ( isset( $value['address'] ) ) {

                    // hack, check if we have created a country element
                    if ( isset( $this->_elementIndex[ "location[$key][address][country_id]" ] ) ) {
                        $countryValue = $this->getElementValue( "location[$key][address][country_id]" );
                        
                        if ( !$countryValue && isset($value['address']['country_id']) ) {
                            $countryValue = $value['address']['country_id'];
                            
                            //retrive country by using country code for assigning country name to template
                            $country = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Country', 
                                                                    $countryValue, 
                                                                    'name', 
                                                                    'id' );
                            $this->assign( "country" , $country );
                        }
                        
                        $this->assign( "country_{$key}_value"   ,  $countryValue );
                    }
                    
                    if ( isset( $this->_elementIndex[ "location[$key][address][state_province_id]" ] ) ) {
                        $stateValue = $this->getElementValue( "location[$key][address][state_province_id]" );
                        
                        if ( !$stateValue && isset($value['address']['state_province_id']) ) {
                            $stateValue = $value['address']['state_province_id'];
                            
                            //retrive country by using country code for assigning country name to template
                            $state = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_StateProvince', 
                                                                  $stateValue, 
                                                                  'name', 
                                                                  'id' );
                            $this->assign( "state" , $state );
                        }

                        $this->assign( "state_province_{$key}_value", $stateValue );
                    }
                    
                    if ( isset( $value['address']['display']) ) {
                        $this->assign( "location_{$key}_address_display", 
                                       str_replace("\n", "<br/>", $value['address']['display']) );
                    }
                }
            }
        }
      
        return $defaults;
     
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        CRM_Contact_BAO_Relationship::buildOnBehalfForm( $this, $this->_contactType );

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Save'),
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ) ) );
    }
    
    
    /**
     * Form submission of new/edit contact is processed.
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );
        
	    $params['contact_type']   = $this->_contactType;
        
        if ( $this->_contactId ) {
            $params['contact_id'] = $this->_contactId;
        }
        
        require_once 'CRM/Contact/BAO/Contact.php';
        $contact =& CRM_Contact_BAO_Contact::create($params, true, false );
        
        if ( $this->_contactType == 'Household' && ( $this->_action & CRM_Core_Action::UPDATE ) ) {
            CRM_Contact_Form_Household::synchronizeIndividualAddresses( $contact->id );
        }

        // set status message.
        CRM_Core_Session::setStatus(ts('Your %1 contact record has been saved.', 
                                       array(1 => $contact->contact_type_display)));
    }
 
}


