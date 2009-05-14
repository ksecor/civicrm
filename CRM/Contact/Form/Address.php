<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

/**
 * This class is used to build address block
 */
class CRM_Contact_Form_Address
{
    /**
     * build form for address input fields 
     *
     * @param object $form - CRM_Core_Form (or subclass)
     * @param array reference $location - location array
     * @param int $locationId - location id whose block needs to be built.
     * @return none
     *
     * @access public
     * @static
     */
    static function buildAddressBlock(&$form, &$location, $locationId, $countryDefault = null)
    {
        require_once 'CRM/Core/BAO/Preferences.php';
        $addressOptions = CRM_Core_BAO_Preferences::valueOptions( 'address_options', true, null, true );

        $config =& CRM_Core_Config::singleton( );
        if ( $countryDefault == null ) {
            $countryDefault = $config->defaultContactCountry;
        }
        $attributes = CRM_Core_DAO::getAttribute('CRM_Core_DAO_Address');
              
        $elements = array( 
                          'address_name'           => array( ts('Address Name')      ,  $attributes['address_name'], null ),
                          'street_address'         => array( ts('Street Address')    ,  $attributes['street_address'], null ),
                          'supplemental_address_1' => array( ts('Addt\'l Address 1') ,  $attributes['supplemental_address_1'], null ),
                          'supplemental_address_2' => array( ts('Addt\'l Address 2') ,  $attributes['supplemental_address_2'], null ),
                          'city'                   => array( ts('City')              ,  $attributes['city'] , null ),
                          'postal_code'            => array( ts('Zip / Postal Code') ,  $attributes['postal_code'], null ),
                          'postal_code_suffix'     => array( ts('Postal Code Suffix'),  array( 'size' => 4, 'maxlength' => 12 ), null ),
                          'county_id'              => array( ts('County')            ,  $attributes['county_id'], 'county' ),
                          'state_province_id'      => array( ts('State / Province')  ,  $attributes['state_province_id'],null ),
                          'country_id'             => array( ts('Country')           ,  $attributes['country_id'], null ), 
                          'geo_code_1'             => array( ts('Latitude') ,  array( 'size' => 9, 'maxlength' => 10 ), null ),
                          'geo_code_2'             => array( ts('Longitude'),  array( 'size' => 9, 'maxlength' => 10 ), null )
                          );

        $stateCountryMap = array( );
        foreach ( $elements as $name => $v ) {
            list( $title, $attributes, $select ) = $v;

            $nameWithoutID = strpos( $name, '_id' ) !== false ? substr( $name, 0, -3 ) : $name;
            if ( ! CRM_Utils_Array::value( $nameWithoutID, $addressOptions ) ) {
                continue;
            }
            
            if ( ! $attributes ) {
                $attributes = $attributes[$name];
            }
            
            //build normal select if country is not present in address block
            if ( $name == 'state_province_id' && ! $addressOptions['country'] ) {
                $select = 'stateProvince';
            }
            
            if ( ! $select ) {
                if ( $name == 'country_id' || $name == 'state_province_id' ) {
                    if ( $name == 'country_id' ) {
                        $stateCountryMap[$locationId]['country'] = "location_{$locationId}_address_{$name}";
                        $selectOptions = array('' => ts('- select -')) + 
                            CRM_Core_PseudoConstant::country( );
                    } else {
                        $stateCountryMap[$locationId]['state_province'] = "location_{$locationId}_address_{$name}";
                        if ( $countryDefault ) {
                            $selectOptions = array('' => ts('- select -')) +
                                CRM_Core_PseudoConstant::stateProvinceForCountry( $countryDefault );
                        } else {
                            $selectOptions = array( '' => ts( '- select a country -' ) );
                        }
                    }
                    $location[$locationId]['address'][$name] =
                        $form->addElement( 'select',
                                           "location[$locationId][address][$name]",
                                           $title,
                                           $selectOptions );
                } else {
                    if ( $name == 'address_name' ) {
                        $name = "name";
                    }
                    
                    $location[$locationId]['address'][$name] =
                        $form->addElement( 'text',
                                           "location[$locationId][address][$name]",
                                           $title,
                                           $attributes );
                }
            } else {
                $location[$locationId]['address'][$name] =
                    $form->addElement( 'select',
                                       "location[$locationId][address][$name]",
                                       $title,
                                       array('' => ts('- select -')) + CRM_Core_PseudoConstant::$select( ) );
            }
        }

        require_once 'CRM/Core/BAO/Address.php';
        CRM_Core_BAO_Address::addStateCountryMap( $stateCountryMap );

    }
    
    /**
     * check for correct state / country mapping.
     *
     * @param array reference $fields - submitted form values.
     * @param array reference $errors - if any errors found add to this array. please.
     * @return true if no errors
     *         array of errors if any present.
     *
     * @access public
     * @static
     */
    static function formRule(&$fields, &$errors)
    {
        // check for state/county match if not report error to user.
        for ($i=1; $i<=CRM_Contact_Form_Location::BLOCKS; $i++) {
            if ( ! CRM_Utils_Array::value( $i, $fields['location'] ) &&
                 ! CRM_Utils_Array::value( 'address', $fields['location'][$i] ) ) {
                continue;
            }

            //state country validation
            $countryId = $stateProvinceId = null;
            if ( CRM_Utils_Array::value( 'country_id', $fields['location'][$i]['address'] ) ) {
                $countries = CRM_Core_PseudoConstant::country( );
                
                $countryExists = null;
                $countryExists = array_key_exists( CRM_Utils_Array::value( 'country_id',
                                                                           $fields['location'][$i]['address'] ), $countries );
                if ( $countryExists ) {
                    $countryId =  CRM_Utils_Array::value( 'country_id', $fields['location'][$i]['address'] );
                } else {
                    $errors["location[$i][address][country_id]"] = ts('Enter a valid country name.');
                }
            }

            if ( CRM_Utils_Array::value( 'state_province_id', $fields['location'][$i]['address'] ) ) {
                $stateProvinceValue = CRM_Utils_Array::value( 'state_province_id',
                                                              $fields['location'][$i]['address'] );
                    
                // hack to skip  - type first letter(s) - for state_province
                // CRM-2649
                $selectOption = ts('- type first letter(s) -');
                if ( $stateProvinceValue != $selectOption ) {
                    $stateProvinces  = CRM_Core_PseudoConstant::stateProvince( false, false );
                    
                    $stateProvinceExists = null;
                    $stateProvinceExists = array_key_exists( $stateProvinceValue, $stateProvinces );
                    if ( $stateProvinceExists ) {
                        $stateProvinceId = CRM_Utils_Array::value( 'state_province_id', $fields['location'][$i]['address'] );
                    } else {
                        $errors["location[$i][address][state_province_id]"] = "Please select a valid State/Province name.";
                    }
                }
            }

            $countyId = CRM_Utils_Array::value( 'county_id', $fields['location'][$i]['address'] );
            
            if ( $stateProvinceId && $countryId ) {
                $stateProvinceDAO =& new CRM_Core_DAO_StateProvince();
                $stateProvinceDAO->id = $stateProvinceId;
                $stateProvinceDAO->find(true);
                if ($stateProvinceDAO->country_id != $countryId) {
                    // countries mismatch hence display error
                    $stateProvinces = CRM_Core_PseudoConstant::stateProvince();
                    $countries =& CRM_Core_PseudoConstant::country();
                    $errors["location[$i][address][state_province_id]"] = "State/Province " . $stateProvinces[$stateProvinceId] . " is not part of ". $countries[$countryId] . ". It belongs to " . $countries[$stateProvinceDAO->country_id] . "." ;
                }
            }

            //state county validation
            if ( $stateProvinceId && $countyId ) {
                $countyDAO =& new CRM_Core_DAO_County();
                $countyDAO->id = $countyId;
                $countyDAO->find(true);
                
                if ($countyDAO->state_province_id != $stateProvinceId) {
                    $counties =& CRM_Core_PseudoConstant::county();
                    $errors["location[$i][address][county_id]"] = "County " . $counties[$countyId] . " is not part of ". $stateProvinces[$stateProvinceId] . ". It belongs to " . $stateProvinces[$countyDAO->state_province_id] . "." ;
                }
            }
        }             
    }

    static function fixStateSelect( &$form,
                                    $countryElementName,
                                    $stateElementName,
                                    $countryDefaultValue ) {
        $countryID = null;
        if ( isset( $form->_elementIndex[$countryElementName] ) ) {
            $countryValue = $form->getElementValue( $countryElementName );
            if ( $countryValue ) {
                $countryID = $countryValue[0];
            } else {
                $countryID = $countryDefaultValue;
            }
        }
        
        $stateTitle = ts( 'State/Province' );
        if ( isset( $form->_fields[$stateElementName]['title'] ) ) {
            $stateTitle = $form->_fields[$stateElementName]['title'];
        }
            
        if ( $countryID &&
             isset( $form->_elementIndex[$stateElementName] ) ) {
            $form->addElement( 'select',
                               $stateElementName,
                               $stateTitle,
                               array( '' => ts( '- select -' ) ) +
                               CRM_Core_PseudoConstant::stateProvinceForCountry( $countryID ) );
        }
    }

}


