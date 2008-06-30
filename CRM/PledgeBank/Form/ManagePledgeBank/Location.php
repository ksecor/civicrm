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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/PledgeBank/Form/ManagePledgeBank.php';
require_once 'CRM/Core/SelectValues.php';

/**
 * This class generates form components for processing Pledge Location 
 * civicrm_pledge. 
 */
class CRM_PledgeBank_Form_ManagePledgeBank_Location extends CRM_PledgeBank_Form_ManagePledgeBank
{

    /**
     * how many locationBlocks should we display?
     *
     * @var int
     * @const
     */
    const LOCATION_BLOCKS = 1;
    
    /**
     * the variable, for storing the location array
     *
     * @var array
     */
    protected $_locationIds = array();

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) 
    {
        parent::preProcess( );
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
        $pledgeId = $this->_id;

        $defaults = array( );
        $params   = array( );
        if ( isset( $pledgeId ) ) {
            $params = array( 'entity_id' => $pledgeId ,'entity_table' => 'civicrm_pb_pledge');
            require_once 'CRM/Core/BAO/Location.php';
            $location = CRM_Core_BAO_Location::getValues($params, $defaults);
            
            $hasLocation = CRM_Core_DAO::getFieldValue( 'CRM_PledgeBank_BAO_Pledge',
                                                        $pledgeId,
                                                        'loc_block_id'
                                                        );
            
        }
        
        $defaults['has_location'] = $hasLocation;
       
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
                        }
                        
                        $this->assign( "country_{$key}_value"   ,  $countryValue );
                    }
                    
                    if ( isset( $this->_elementIndex[ "location[$key][address][state_province_id]" ] ) ) {
                        $stateValue = $this->getElementValue( "location[$key][address][state_province_id]" );
                        
                        if ( !$stateValue && isset($value['address']['state_province_id']) ) {
                            $stateValue = $value['address']['state_province_id'];
                        }

                        $this->assign( "state_province_{$key}_value", $stateValue );
                    }
                }
            }
        }
       
        return $defaults;
    }       

    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) 
    {
        $this->addFormRule( array( 'CRM_PledgeBank_Form_ManagePledgeBank_Location', 'formRule' ) );
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
     *  function to build location block 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    { 
        $this->addElement('checkbox', 'has_location', ts('Does this pledge have location?'), null,
                          array('onclick' =>"return showHideByValue('has_location','','location_show','block','radio',false);"));
        $this->assign( 'locationCount', self::LOCATION_BLOCKS + 1);
        
        //hack the address sequence so that state province always comes after country
        $config =& CRM_Core_Config::singleton( );
        $addressSequence = $config->addressSequence();
      
        $key = array_search( 'country', $addressSequence);
        unset($addressSequence[$key]);

        $key = array_search( 'state_province', $addressSequence);
        unset($addressSequence[$key]);

        $key = array_search( 'street_address', $addressSequence);
        unset($addressSequence[$key]);
        
        $key = array_search( 'supplemental_address_1', $addressSequence);
        unset($addressSequence[$key]);

        $key = array_search( 'supplemental_address_2', $addressSequence);
        unset($addressSequence[$key]);
       
        $addressSequence = array_merge( $addressSequence, array ( 'country', 'state_province' ) );
        $this->assign( 'addressSequence', $addressSequence );
        require_once 'CRM/Contact/Form/Location.php';

        //blocks to be displayed
        $locationCompoments = array();
        CRM_Contact_Form_Location::buildLocationBlock( $this, self::LOCATION_BLOCKS + 1, true, $locationCompoments);
       
        $this->assign( 'index' , 1 );
        $this->assign( 'blockCount'   , CRM_Contact_Form_Location::BLOCKS + 1);
    
        parent::buildQuickForm();
    }
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess( ) 
    {
        $params = $ids = array( );
        $params = $this->exportValues( );
        $params['entity_table'] = 'civicrm_pb_pledge';
        $ids = $this->_locationIds;
        $pledgeId = $this->_id;
        unset($params['location'][1]['email']);
        $params['entity_id'] = $pledgeId; 
        
        //set the location type to default location type
        require_once 'CRM/Core/BAO/LocationType.php';
        $defaultLocationType =& CRM_Core_BAO_LocationType::getDefault();
        $params['location'][1]['location_type_id'] = $defaultLocationType->id;
        $params['location'][1]['is_primary'] = 1;
        require_once 'CRM/Core/BAO/Location.php';
        $location = CRM_Core_BAO_Location::create($params, true, 'pb_pledge');
        $params['loc_block_id'] = $location['id'];
        
        $params['id'] = $pledgeId;
        
        require_once 'CRM/PledgeBank/BAO/Pledge.php';
        CRM_PledgeBank_BAO_Pledge::add($params ,$ids);
                
    }//end of function

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) 
    {
        return ts('Pledge Location');
    }
}

