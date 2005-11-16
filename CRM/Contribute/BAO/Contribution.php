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
        
        return $contribution->save();
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
