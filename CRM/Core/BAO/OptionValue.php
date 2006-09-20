<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/DAO/OptionValue.php';

class CRM_Core_BAO_OptionValue extends CRM_Core_DAO_OptionValue 
{

    

    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }
    
    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Core_BAO_OptionValue object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $optionValue =& new CRM_Core_DAO_OptionValue( );
        $optionValue->copyValues( $params );
        if ( $optionValue->find( true ) ) {
            CRM_Core_DAO::storeValues( $optionValue, $defaults );
            return $optionValue;
        }
        return null;
    }

    /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * @static
     */
    static function setIsActive( $id, $is_active ) 
    {
        return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_OptionValue', $id, 'is_active', $is_active );
    }

    /**
     * function to add the Option Group
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add(&$params, &$ids) 
    {
        
        $params['is_active']  =  CRM_Utils_Array::value( 'is_active', $params, false );
        $params['is_default'] =  CRM_Utils_Array::value( 'is_default', $params, false );
        $params['is_optgroup'] =  CRM_Utils_Array::value( 'is_optgroup', $params, false );
        
        // action is taken depending upon the mode
        $optionValue               =& new CRM_Core_DAO_OptionValue( );
        $optionValue->domain_id    = CRM_Core_Config::domainID( );
        
        $optionValue->copyValues( $params );;
        
        if ($params['is_default']) {
            $query = 'UPDATE civicrm_option_value SET is_default = 0 WHERE  option_group_id = %1';
            $p = array( 1 => array( $params['option_group_id'], 'Integer' ) );
            CRM_Core_DAO::executeQuery( $query, $p );
        }
        
        $optionValue->id = CRM_Utils_Array::value( 'optionValue', $ids );
        $optionValue->save( );
        return $optionValue;
    }
    
    /**
     * Function to delete Option Value 
     * 
     * @param  int  $optionGroupId     Id of the Option Group to be deleted.
     * 
     * @return void
     * 
     * @access public
     * @static
     */
    static function del($optionValueId) 
    {
        //echo $optionValueId;
        $optionValue =& new CRM_Core_DAO_OptionValue( );
        $optionValue->id = $optionValueId;
        $optionValue->find(true);
        
        $optionGroup =& new CRM_Core_DAO_OptionGroup( );
        $optionGroup->id = $optionValue->option_group_id;
        $optionGroup->find(true);
        
        $ids = array('id'    => $optionValueId,
                     'gName' => $optionGroup->name,
                     'value' => $optionValue->value);
        require_once 'CRM/Contact/BAO/Individual.php';
        CRM_Contact_BAO_Individual::updateDisplayNames($ids, CRM_Core_Action::DELETE);
        
        //$optionValue->delete();
        
        // Returning the count of deleted otion value
        // as it is used in if condition in CRM/Admin/Form/Gender
        
        return $optionValue->delete();
    }



    /**
     * retrieve the id and decription
     *
     * @param NULL
     * 
     * @return Array            id and decription
     * @static
     * @access public
     */
    static function &getActivityDescription() 
    {
        
        $query =
            "SELECT civicrm_option_value.value, civicrm_option_value.description
FROM civicrm_option_value
LEFT JOIN civicrm_option_group ON ( civicrm_option_value.option_group_id = civicrm_option_group.id )
WHERE civicrm_option_value.is_active =1
AND civicrm_option_value.value >4
AND civicrm_option_group.name = 'activity_type'  ORDER BY civicrm_option_value.name";
        $dao   =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        $description =array();
        while($dao->fetch()) {
            $description[ $dao->value] = $dao->description;
            
        }
        return $description;
    }
    

    
}
?>