<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

/**
 * This class is to build the form for adding Group
 */
class CRM_Contact_Form_Domain extends CRM_Core_Form {

    /**
     * the group id, used when editing a group
     *
     * @var int
     */
    protected $_id;

    /**
     * the variable, for storing the location array
     *
     * @var array
     */
    protected $_ids;

    /**
     * how many locationBlocks should we display?
     *
     * @var int
     * @const
     */
    const LOCATION_BLOCKS = 1;

    function preProcess( ) {
        
        CRM_Utils_System::setTitle(ts('CiviMail Domain Information'));
        $breadCrumbPath = CRM_Utils_System::url( 'civicrm/admin', 'reset=1' );
        CRM_Utils_System::appendBreadCrumb( ts('Administer CiviCRM'), $breadCrumbPath );

        $this->_id = CRM_Core_Config::domainID();
        $this->_action = CRM_Utils_Request::retrieve( 'action', 'String',
                                                      $this, false, 'view' );
        
    }
    
    /*
     * This function sets the default values for the form.
     * the default values are retrieved from the database
     *
     * @access public
     * @return None
     */
    
    function setDefaultValues( ) {
        
        require_once 'CRM/Core/BAO/Domain.php';

        $defaults = array( );
        $params   = array( );
        $locParams = array();
        
        if ( isset( $this->_id ) ) {
            $params['id'] = $this->_id ;
            CRM_Core_BAO_Domain::retrieve( $params, $defaults );
            unset($params['id']);
            $locParams = $params + array('entity_id' => $this->_id, 'entity_table' => 'civicrm_domain');
            require_once 'CRM/Core/BAO/Location.php';
            CRM_Core_BAO_Location::getValues( $locParams, $defaults, $ids, self::LOCATION_BLOCKS);
            $this->_ids = $ids;

            //set defaults for country-state dojo widget
            if ( ! empty ( $defaults['location'] ) ) {
                $countries      =& CRM_Core_PseudoConstant::country( );
                $stateProvinces =& CRM_Core_PseudoConstant::stateProvince( false, false );
                
                foreach ( $defaults['location'] as $key => $value ) {
                    // hack, check if we have created a country element
                    if ( isset( $this->_elementIndex[ "location[$key][address][country_id]" ] ) ) {
                        $countryValue = $this->getElementValue( "location[$key][address][country_id]" );
                    
                        if ( $countryValue ) {
                            if ( ! is_numeric( $countryValue ) ) {
                                $this->assign( "country_{$key}_value", 
                                               $this->getElementValue( "location[$key][address][country_id]" ) );
                                $this->assign( "country_{$key}_id", 
                                               $this->getElementValue( "location[$key][address][country_id]" ) );
                            } else {
                                $this->assign( "country_{$key}_value",  $countries[$countryValue] );
                                $this->assign( "country_{$key}_id"   ,  $countryValue );
                            }
                        } else if ( isset($value['address']['country_id']) ) {
                            $countryId = $value['address']['country_id'];
                            if ( $countryId ) {
                                $this->assign( "country_{$key}_value",  $countries[$countryId] );
                                $this->assign( "country_{$key}_id"   ,  $countryId );
                            }
                        }
                    }
                    
                    if ( isset( $this->_elementIndex[ "location[$key][address][state_province_id]" ] ) ) {
                        $stateValue = $this->getElementValue( "location[$key][address][state_province_id]" );
                    
                        if ( $stateValue ) {
                            if ( ! is_numeric( $stateValue ) ) {
                                $this->assign( "state_province_{$key}_value", 
                                               $this->getElementValue( "location[$key][address][state_province_id]" ) );
                                $this->assign( "state_province_{$key}_id", 
                                               $this->getElementValue( "location[$key][address][state_province_id]" ) );
                            } else {
                                $this->assign( "state_province_{$key}_value",  $stateProvinces[$stateValue] );
                                $this->assign( "state_province_{$key}_id"   ,  $stateValue );
                            }
                        } else  if ( isset($value['address']['state_province_id']) ) {
                            $stateProvinceId = $value['address']['state_province_id'];
                            if ( $stateProvinceId ) {
                                $this->assign( "state_province_{$key}_value",  $stateProvinces[$stateProvinceId] );
                                $this->assign( "state_province_{$key}_id"   ,  $stateProvinceId );
                            }
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
     * @return None
     * @access public
     */

    public function buildQuickForm( ) {
        
        $this->add('text', 'name' , ts('Domain Name') , array('size' => 25));
        $this->add('text', 'description', ts('Description'), array('size' => 25) );
        $this->add('text', 'contact_name', ts('Contact Name'), array('size' => 25) );

        $this->add('text', 'email_name', ts('Default Email Name'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email','email'));

        $this->add('text', 'email_address', ts('Default Email'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email','email'));
        $this->addRule( "email_address", ts('Email is not valid.'), 'email' );

        $this->add('text', 'email_domain', ts('Email Domain'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email','email'));
        $this->addRule( "email_domain", ts('Email is not valid.'), 'domain' );

        $this->add('text', 'email_return_path', ts('Return-Path'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email','email'));
        $this->addRule( "email_return_path", ts('Return-Path must be a valid email address format.'), 'email' );
        
        //blocks to be displayed
        $this->assign( 'locationCount', self::LOCATION_BLOCKS + 1);    
   
        require_once 'CRM/Contact/Form/Location.php';
        $locationCompoments = array('Phone', 'Email');
        CRM_Contact_Form_Location::buildLocationBlock( $this, self::LOCATION_BLOCKS ,$locationCompoments);
        $this->assign( 'index' , 1 );
        $this->assign( 'blockCount'   , 1 );

        //hack the address sequence so that state province always comes after country
        $config =& CRM_Core_Config::singleton( );
        $addressSequence = $config->addressSequence();
        $key = array_search( 'country', $addressSequence);
        unset($addressSequence[$key]);

        $key = array_search( 'state_province', $addressSequence);
        unset($addressSequence[$key]);

        $addressSequence = array_merge( $addressSequence, array ( 'country', 'state_province' ) );
        $this->assign( 'addressSequence', $addressSequence );

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Save'),
                                         'subName'   => 'view',
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ) ) );
        
        if ($this->_action & CRM_Core_Action::VIEW ) { 
            $this->freeze();
        }        
        $this->assign('emailDomain',true);
    }

    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) 
    {
        $this->addFormRule( array( 'CRM_Contact_Form_Domain', 'formRule' ) );
    }
    
    /**
     * global validation rules for the form
     *
     * @param array $fields posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @static
     * @access public
     */
    static function formRule( &$fields ) 
    {
        $errors = array( );
        // check for state/country mapping
        CRM_Contact_Form_Address::formRule($fields, $errors);

        return empty($errors) ? true : $errors;
    }    

    /**
     * Process the form when submitted
     *
     * @return void
     * @access public
     */

    public function postProcess( ) {

        require_once 'CRM/Core/BAO/Domain.php';

        $params = array( );
        
        $params = $this->exportValues();
        $params['entity_id'] = $this->_id;
        $params['entity_table'] = CRM_Core_BAO_Domain::getTableName();
        $domain = CRM_Core_BAO_Domain::edit($params, $this->_id);

        require_once 'CRM/Core/BAO/LocationType.php';
        $defaultLocationType =& CRM_Core_BAO_LocationType::getDefault();

        $location = array();
        for ($locationId = 1; $locationId <= self::LOCATION_BLOCKS ; $locationId++) { // start of for loop for location
            $params['location'][$locationId]['location_type_id'] = $defaultLocationType->id;
            $location[$locationId] = CRM_Core_BAO_Location::add($params, $this->_ids, $locationId);
        }
        
        CRM_Core_Session::setStatus( ts('Domain information for "%1" has been saved.', array( 1 => $domain->name )) );
        $session =& CRM_Core_Session::singleton( );
        $session->replaceUserContext(CRM_Utils_System::url('civicrm/admin', 'reset=1' ) );

    }
    
}

?>
