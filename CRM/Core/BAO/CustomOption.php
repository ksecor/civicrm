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


/**
 * Business objects for managing custom data options.
 *
 */
class CRM_Core_BAO_CustomOption {

    const VALUE_SEPERATOR = "";

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
     * @return object CRM_Core_BAO_CustomOption object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults )
    {
        require_once 'CRM/Core/DAO/OptionValue.php';
        $customOption =& new CRM_Core_DAO_OptionValue( );
        $customOption->copyValues( $params );
        if ( $customOption->find( true ) ) {
            CRM_Core_DAO::storeValues( $customOption, $defaults );
            return $customOption;
        }
        return null;
    }

     /**
     * takes an associative array and creates a custom option object
     *
     * This function is invoked from within the web form layer and also from the api layer
     *
     * @param array $params (reference) an assoc array of name/value pairs
     *
     * @return object CRM_Core_DAO_CustomField object
     * @access public
     * @static
     */
    static function create(&$params)
    {
        $customOptionBAO =& new CRM_Core_BAO_CustomOption();
        $customOptionBAO->copyValues($params);
        return $customOptionBAO->save();
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
        CRM_Core_Error::fatal( 'This function has been obsoleted' );
    }


    /**
     * returns all active options ordered by weight for 
     *
     * @param  int      $fieldId         field whose options are needed
     * @param  boolean  $inactiveNeeded  do we need inactive options ?
     *
     * @return array $customOption all active options for fieldId
     * @static
     */
    static function getCustomOption( $fieldID,
                                     $inactiveNeeded = false )
    {       
        $options = array();
        if ( ! $fieldID ) {
            return $options;
        }

        // get the option group id
        $optionGroupID = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField',
                                                      $fieldID,
                                                      'option_group_id' );
        if ( ! $optionGroupID ) {
            return $options;
        }

        $dao =& new CRM_Core_DAO_OptionValue();
        $dao->option_group_id = $optionGroupID;
        if ( ! $inactiveNeeded ) {
            $dao->is_active = 1;
        }
        $dao->orderBy('weight ASC, label ASC');
        $dao->find( );
        
        while ( $dao->fetch( ) ) {
            $options[$dao->id] = array();
            $options[$dao->id]['id']    = $dao->id;
            $options[$dao->id]['label'] = $dao->label;
            $options[$dao->id]['value'] = $dao->value;
        }
        return $options;
    }

    static function getOptionLabel($fieldId, $value, $fieldType = null, $dataType = null, $entityTable = 'civicrm_custom_field')
    {
        switch ($fieldType) {

        case null:
        case 'CheckBox':
        case 'Multi-Select':
        case 'Radio':
        case 'Select':
            $query = "
SELECT v.label
FROM   civicrm_option_value v,
       civicrm_option_group g,
       civicrm_custom_field f
WHERE  f.id    = %1
AND    v.value = %2
AND    g.id    = f.option_group_id
AND    g.id    = v.option_group_id";
            $params = array( 1 => array( $fieldId, 'Integer' ),
                             2 => array( $value  , 'String'  ) );
            $dao   = CRM_Core_DAO::executeQuery( $query, $params );
            $label = $dao->fetch( ) ? $dao->label : $value;
            $dao->free();
            break;

        case 'Select Country':
            $label =& CRM_Core_PseudoConstant::country($value);
            break;

        case 'Select Date':
            $label = CRM_Utils_Date::customFormat($value);
            break;

        case 'Select State/Province':
            $label = CRM_Core_PseudoConstant::stateProvince($value);
            break;

        default:
            $label = $value;
            break;

        }

        if ( $dataType == 'Boolean' ) {
            $label = $value ? ts('Yes') : ts('No');
        }

        return $label;
    }


    /**
     * Function to delete Option
     *
     * param $optionId integer option id
     *
     * @static
     * @access public
     */
    static function del( $optionId ) 
    {
        // get the customFieldID
        $query = "
SELECT f.id as id, f.data_type as dataType
FROM   civicrm_option_value v,
       civicrm_option_group g,
       civicrm_custom_field f
WHERE  v.id    = %1
AND    g.id    = f.option_group_id
AND    g.id    = v.option_group_id";
        $params = array( 1 => array( $optionId, 'Integer' ) );
        $dao    = CRM_Core_DAO::executeQuery( $query, $params );
        if ( $dao->fetch( ) ) {
            if ( in_array( $dao->dataType,
                           array( 'Int', 'Float', 'Money', 'Boolean' ) ) ) {
                $value = 0;
            } else {
                $value = '';
            }
            $params = array( 'optionId' => $optionId,
                             'fieldId'  => $dao->id,
                             'value'    => $value );
            // delete this value from the tables
            self::updateCustomValues( $params );

            // also delete this option value
            $query = "
DELETE
FROM   civicrm_option_value
WHERE  id = %1";
            $params = array( 1 => array( $optionId, 'Integer' ) );
            CRM_Core_DAO::executeQuery( $query, $params );
        }
    }

    static function updateCustomValues($params) 
    {
        $optionDAO =& new CRM_Core_DAO_OptionValue();
        $optionDAO->id = $params['optionId'];
        $optionDAO->find( true );
        $oldValue = $optionDAO->value;

        // get the table, column, html_type and data type for this field
        $query = "
SELECT g.table_name  as tableName ,
       f.column_name as columnName,
       f.data_type   as dataType,
       f.html_type   as htmlType
FROM   civicrm_custom_group g,
       civicrm_custom_field f
WHERE  f.custom_group_id = g.id
  AND  f.id = %1";
        $queryParams = array( 1 => array( $params['fieldId'], 'Integer' ) );
        $dao = CRM_Core_DAO::executeQuery( $query, $queryParams );
        if ( $dao->fetch( ) ) {
            switch ( $dao->htmlType ) {
            case 'Select':
            case 'Radio':
                $query = "
UPDATE {$dao->tableName}
SET    {$dao->columnName} = %1
WHERE  {$dao->columnName} = %2";
                $queryParams = array( 1 => array( $params['value'],
                                                  $dao->dataType ),
                                      2 => array( $oldValue,
                                                  $dao->dataType ) );
                break;

            case 'Multi-Select':
            case 'CheckBox':
                $oldString =
                    CRM_Core_DAO::VALUE_SEPARATOR . $oldValue . CRM_Core_DAO::VALUE_SEPARATOR;
                $newString = 
                    CRM_Core_DAO::VALUE_SEPARATOR . $params['value'] . CRM_Core_DAO::VALUE_SEPARATOR;
                $query = "
UPDATE {$dao->tableName}
SET    {$dao->columnName} = REPLACE( {$dao->columnName}, %1, %2 )";
                $queryParams = array( 1 => array( $oldString, 'String' ),
                                      2 => array( $newString, 'String' ) );
                break;

            default:
                CRM_Core_Error::fatal( );
            }
            $dao = CRM_Core_DAO::executeQuery( $query, $queryParams );
        }
    }

    /**
     * return the custom options associated with a specific entity id/table
     * as a name/value pair
     *
     * @param string $entity_table name of the table
     * @param string $entity_id   
     * @param array  $values       array tos tore the options in
     *
     * @return void
     * @static
     */
    static function getAssoc( $entity_table, $entity_id, &$values ) {
        CRM_Core_Error::fatal( 'This function has been obsoleted' );

        // check CRM_Core_OptionGroup::getAssoc for the same function in 2.0
    }

}
?>
