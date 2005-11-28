<?php
  /*
   +--------------------------------------------------------------------+
   | CiviCRM version 1.1                                                |
   +--------------------------------------------------------------------+
   | Copyright (c) 2005 Social Source Foundation                        |
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
   *
   *
   * @package CRM
   * @author Donald A. Lobo <lobo@yahoo.com>
   * @copyright Social Source Foundation (c) 2005
   * $Id$
   *
   */

require_once 'CRM/Contribute/DAO/Contribution.php';

class CRM_Contribute_BAO_Contribution extends CRM_Contribute_DAO_Contribution
{
    /**
     * static field for all the contribution information that we can potentially import
     *
     * @var array
     * @static
     */
    static $_importableFields = null;

    function __construct()
    {
        parent::__construct();
    }
    

    /**
     * takes an associative array and creates a contribution object
     *
     * the function extract all the params it needs to initialize the create a
     * contribution object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Contribute_BAO_Contribution object
     * @access public
     * @static
     */
    static function add(&$params, &$ids) {
        $contribution =& new CRM_Contribute_BAO_Contribution();
        
        $contribution->copyValues($params);
        $contribution->domain_id = CRM_Utils_Array::value( 'domain' , $ids, CRM_Core_Config::domainID( ) );
        
        $contribution->id        = CRM_Utils_Array::value( 'contribution', $ids );

        require_once 'CRM/Utils/Rule.php';
        if (!CRM_Utils_Rule::currencyCode($contribution->currency)) {
            require_once 'CRM/Core/Config.php';
            $config =& CRM_Core_Config::singleton();
            $contribution->currency = $config->defaultCurrency;
        }
        
        return $contribution->save();
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params input parameters to find object
     * @param array $values output values of the object
     * @param array $ids    the array that holds all the db ids
     *
     * @return CRM_Contribute_BAO_Contribution|null the found object or null
     * @access public
     * @static
     */
    static function &getValues( &$params, &$values, &$ids ) {

        $contribution =& new CRM_Contribute_BAO_Contribution( );

        $contribution->copyValues( $params );

        if ( $contribution->find(true) ) {
            $ids['contribution'] = $contribution->id;
            $ids['domain' ] = $contribution->domain_id;

            CRM_Core_DAO::storeValues( $contribution, $values );

            return $contribution;
        }
        return null;
    }

    /**
     * takes an associative array and creates a contribution object
     *
     * This function is invoked from within the web form layer and also from the api layer
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Contribute_BAO_Contribution object 
     * @access public
     * @static
     */
    static function &create(&$params, &$ids) {
        require_once 'CRM/Utils/Hook.php';

        if ( CRM_Utils_Array::value( 'contribution', $ids ) ) {
            CRM_Utils_Hook::pre( 'edit', 'Contribution', $ids['contribution'], $params );
        } else {
            CRM_Utils_Hook::pre( 'create', 'Contribution', null, $params ); 
        }

        $contribution = self::add($params, $ids);

        $params['contribution_id'] = $contribution->id;

        // add custom field values
#       if ( CRM_Utils_Array::value( 'custom', $params ) ) {
#           foreach ($params['custom'] as $customValue) {
#               $cvParams = array(
#                                 'entity_table'    => 'civicrm_contact',
#                                 'entity_id'       => $contact->id,
#                                 'value'           => $customValue['value'],
#                                 'type'            => $customValue['type'],
#                                 'custom_field_id' => $customValue['custom_field_id'],
#                                 );
#               
#               if ($customValue['id']) {
#                   $cvParams['id'] = $customValue['id'];
#               }
#               CRM_Core_BAO_CustomValue::create($cvParams);
#           }
#       }
        
        if ( CRM_Utils_Array::value( 'contribution', $ids ) ) {
            CRM_Utils_Hook::post( 'edit', 'Contribution', $contribution->id, $contribution );
        } else {
            CRM_Utils_Hook::post( 'create', 'Contribution', $contribution->id, $contribution );
        }

        return $contribution;
    }

    /**
     * Get the values for pseudoconstants for name->value and reverse.
     *
     * @param array   $defaults (reference) the default values, some of which need to be resolved.
     * @param boolean $reverse  true if we want to resolve the values in the reverse direction (value -> name)
     *
     * @return void
     * @access public
     * @static
     */
    static function resolveDefaults(&$defaults, $reverse = false)
    {
        if (CRM_Utils_Array::value('contribution_type', $defaults)) {
            self::lookupValue($defaults, 'contribution_type', CRM_Contribute_PseudoConstant::contributionType(), $reverse);
        }
        if (CRM_Utils_Array::value('payment_instrument', $defaults)) {
            self::lookupValue($defaults, 'payment_instrument', CRM_Contribute_PseudoConstant::paymentInstrument(), $reverse);
        }
    }

    /**
     * This function is used to convert associative array names to values
     * and vice-versa.
     *
     * This function is used by both the web form layer and the api. Note that
     * the api needs the name => value conversion, also the view layer typically
     * requires value => name conversion
     */
    static function lookupValue(&$defaults, $property, &$lookup, $reverse)
    {
        $id = $property . '_id';

        $src = $reverse ? $property : $id;
        $dst = $reverse ? $id       : $property;

        if (!array_key_exists($src, $defaults)) {
            return false;
        }

        $look = $reverse ? array_flip($lookup) : $lookup;
        
        if(is_array($look)) {
            if (!array_key_exists($defaults[$src], $look)) {
                return false;
            }
        }
        $defaults[$dst] = $look[$defaults[$src]];
        return true;
    }

    /**
     * combine all the importable fields from the lower levels object
     *
     * The ordering is important, since currently we do not have a weight
     * scheme. Adding weight is super important and should be done in the
     * next week or so, before this can be called complete.
     *
     * @return array array of importable Fields
     * @access public
     */
    function &importableFields( ) {
        if ( ! self::$_importableFields ) {
            if ( ! self::$_importableFields ) {
                self::$_importableFields = array();
            }
            if (!$status) {
                $fields = array( '' => array( 'title' => ts('- do not import -') ) );
            } else {
                $fields = array( '' => array( 'title' => ts('- Contribution Fields -') ) );
            }
            //$fields = array();

            $tmpFields = CRM_Contribute_DAO_Contribution::import( );
            $fields = array_merge($fields, $tmpFields);

            self::$_importableFields = $fields;
        }
        return self::$_importableFields;
    }

}

?>
