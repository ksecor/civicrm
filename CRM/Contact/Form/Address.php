<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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

require_once 'CRM/Contact/Form/StateCountry.php';

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
    static function buildAddressBlock(&$form, &$location, $locationId)
    {
        require_once 'CRM/Core/BAO/Preferences.php';
        $addressOptions = CRM_Core_BAO_Preferences::valueOptions( 'address_options' );

        $config =& CRM_Core_Config::singleton( );
        $attributes = CRM_Core_DAO::getAttribute('CRM_Core_DAO_Address');

        $elements = array( 
                          'street_address'         => array( ts('Street Address')   , null, null ),
                          'supplemental_address_1' => array( ts('Addt\'l Address 1'), null, null ),
                          'supplemental_address_2' => array( ts('Addt\'l Address 2'), null, null ),
                          'city'                   => array( ts('City')             , null, null ),
                          'postal_code'            => array( ts('Zip / Postal Code'), null, null ),
                          'postal_code_suffix'     => array(ts('Postal Code Suffix')       ,
                                                            array( 'size' => 4, 'maxlength' => 12 ), null ),
                          'county'                 => array( ts('County')           , null, county        ),
                          'state_province'         => array( ts('State / Province') , null, stateProvince ),
                          'country'                => array( ts('Country')          , null, country       ), 
                          'geo_code_1'             => array( ts('Latitude')         ,
                                                             array( 'size' => 4, 'maxlength' => 8 ), null ),
                          'geo_code_2'             => array( ts('Longitude')         ,
                                                             array( 'size' => 4, 'maxlength' => 8 ), null ),
                          );

        foreach ( $elements as $name => $v ) {
            list( $title, $attributes, $select ) = $v;

            if ( ! $addressOptions[$title] ) {
                continue;
            }

            if ( ! $attributes ) {
                $attributes = $attributes[$name];
            }
             
            if ( ! $select ) {
                $location[$locationId]['address'][$name] =
                    $form->addElement( 'text',
                                       "location[$locationId][address][$name]",
                                       $title,
                                       $attributes );
            } else {
                $attributes = null;
                if ( $name == 'country' ) {
                    $attributes = array( 'dojoType'       => 'ComboBox',
                                         'mode'           => 'remote',
                                         'style'          => 'width: 300px;',
                                         'dataUrl'        => CRM_Utils_System::url( "civicrm/ajax/country",
                                                                                    "s=%{searchString}&node=root",
                                                                                    true, null, false ),
                                         'onValueChanged' => 'checkParamChildren',
                                         'id'             => 'country'.$locationId );

                    $stateURL = CRM_Utils_System::url( "civicrm/ajax/state","s=%{searchString}",
                                                       true, null, false );
                    $form->assign( 'stateURL', $stateURL);
                }

                $location[$locationId]['address'][$name] =
                    $form->add( 'text',
                                "location[$locationId][address][$name]",
                                $title,
                                $attributes );
            }
        }
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

            $stateProvinces  = array( );
            $stateProvinces  = CRM_Core_PseudoConstant::stateProvince( false, false );

            $stateProvinceId = array_search ( CRM_Utils_Array::value( 'state_province',
                                                                      $fields['location'][$i]['address'] ) , $stateProvinces );
            $countyId        = CRM_Utils_Array::value( 'county_id', $fields['location'][$i]['address'] );
            
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
}

?>
