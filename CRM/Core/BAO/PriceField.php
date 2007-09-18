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

require_once 'CRM/Core/DAO/PriceField.php';
//require_once 'CRM/Core/DAO/CustomGroup.php';
//require_once 'CRM/Core/DAO/CustomValue.php';
//require_once 'CRM/Core/DAO/CustomOption.php';
require_once 'CRM/Core/BAO/CustomOption.php';

/**
 * Business objects for managing price fields.
 *
 */
class CRM_Core_BAO_PriceField extends CRM_Core_DAO_PriceField 
{

    protected $_options;

    /**
     * takes an associative array and creates a price field object
     *
     * This function is invoked from within the web form layer and also from the api layer
     *
     * @param array $params (reference) an assoc array of name/value pairs
     *
     * @return object CRM_Core_DAO_PriceField object
     * @access public
     * @static
     */
    static function create(&$params)
    {
        $priceFieldBAO =& new CRM_Core_BAO_PriceField();
        $priceFieldBAO->copyValues($params);
        return $priceFieldBAO->save();
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
     * @return object CRM_Core_DAO_PriceField object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults )
    {
        return CRM_Core_DAO::commonRetrieve( 'CRM_Core_DAO_PriceField', $params, $defaults );
    }

    /**
     * update the is_active flag in the db
     *
     * @param int      $id         Id of the database record
     * @param boolean  $is_active  Value we want to set the is_active field
     *
     * @return   Object            DAO object on sucess, null otherwise
     * 
     * @access public
     * @static
     */
    static function setIsActive( $id, $is_active )
    {
        return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_PriceField', $id, 'is_active', $is_active );
    }
    
    /**
     * Get number of elements for a particular field.
     *
     * This method returns the number of entries in the crm_custom_value table for this particular field.
     *
     * @param int $fieldId - id of field.
     * @return int $numValue - number of custom data values for this field.
     *
     * @access public
     * @static
     *
     */
    /*
    public static function getNumValue($fieldId)
    {
        $cvTable = CRM_Core_DAO_CustomValue::getTableName();
        $query = "SELECT count(*) 
                  FROM   $cvTable 
                  WHERE  $cvTable.custom_field_id = %1";
        $p = array( 1 => array( $fieldId, 'Integer' ) );

        return CRM_Core_DAO::singleValueQuery( $query, $p );
    }
     */

    /**
     * Get the field title.
     *
     * @param int $id id of field.
     * @return string name 
     *
     * @access public
     * @static
     *
     */
    public static function getTitle( $id )
    {
        return CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_PriceField', $id, 'label' );
    }
    
    /**
     * Store and return an array of all active price fields.
     *
     * @param string      $contactType   Contact type
     * @param boolean     $showAll       If true returns all fields (includes disabled fields)
     *
     * @return array      $fields - an array of active price fields.
     *
     * @access public
     * @static
     */
    public static function &getFields( $showAll = false ) 
    {
        $priceFieldTable = self::getTableName();
        $priceSetTable = CRM_Core_DAO_PriceSet::getTableName();

        $query = "SELECT $priceFieldTable.id, $priceFieldTable.label,
                         $priceSetTable.title, $priceSetTable.html_type, 
                         $priceFieldTable.options_per_line
                  FROM $priceFieldTable
                  INNER JOIN $priceSetTable
                  ON $priceFieldTable.price_set_id = $priceFieldGroup.id";
        
        if (! $showAll) {
            $query .= " WHERE $priceFieldTable.is_active = 1 AND $priceSetTable.is_active = 1";
        }

        $query .= " ORDER BY $priceSetTable.title, $priceFieldTable.weight, $priceFieldTable.label";
     
        $crmDAO =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        $result = $crmDAO->getDatabaseResult();
    
        $fields = array( );
        while (($row = $result->fetchRow()) != null) {
            $id = array_shift($row);
            $fields[$id] = $row;
        }
    }

    /**
     * This function for building custom fields
     * 
     * @param object  $qf             form object (reference)
     * @param string  $elementName    name of the custom field
     * @param boolean $inactiveNeeded 
     * @param boolean $userRequired   true if required else false
     * @param boolean $search         true if used for search else false
     * @param string  $label          label for custom field        
     *
     * @access public
     * @static
     */
    public static function addQuickFormElement( &$qf,
                                                $elementName,
                                                $fieldId,
                                                $inactiveNeeded,
                                                $useRequired = true,
                                                $label = null ) 
    {
        require_once 'CRM/Utils/Money.php';
        $field =& new CRM_Core_DAO_PriceField();
        $field->id = $fieldId;
        if (! $field->find(true)) {
            /* FIXME: failure! */
            return null;
        }
        
        if (!isset($label)) {
            $label = $field->label;
        }

        switch($field->html_type) {
        case 'Text':
            if ($field->is_display_amounts) {
                $customOption = CRM_Core_BAO_PriceField::getOptions( $field->id, $inactiveNeeded );
                // text fields only have one option
                $optionKey = key($customOption);
                $label .= '&nbsp;-&nbsp;';
                $label .= CRM_Utils_Money::format( CRM_Utils_Array::value('value', $customOption[$optionKey]) );
            }
            $qf->add(
                'text', $elementName, $label, 'size="4"',
                ( $useRequired || ( $useRequired && $field->is_required ) )
            );

            // integers will have numeric rule applied to them.
            $qf->addRule($elementName, ts('%1 must be an integer (whole number).', array(1 => $label)), 'integer');
            break;

        case 'Radio':
            $choice = array();
            $customOption = CRM_Core_BAO_PriceField::getOptions($field->id, $inactiveNeeded);
            
            if ( !$field->is_required ) {
                // add "none" option
                $choice[] = $qf->createElement('radio', null, '', '-none-', '0', $field->attributes);
            }

            foreach ($customOption as $opt) {
                if ($field->is_display_amounts) {
                    $opt['label'] .= '&nbsp;-&nbsp;';
                    $opt['label'] .= CRM_Utils_Money::format( $opt['value'] );
                }
                $choice[] = $qf->createElement('radio', null, '', $opt['label'], $opt['id'], $field->attributes);
            }
            $qf->addGroup($choice, $elementName, $label);

            if ( ( $useRequired || ( $useRequired && $field->is_required) ) ) {
                $qf->addRule($elementName, ts('%1 is a required field.', array(1 => $label)) , 'required');
            }
            break;
            
        case 'Select':
            $customOption = CRM_Core_BAO_PriceField::getOptions($field->id, $inactiveNeeded);
            $selectOption = array();
            foreach ($customOption as $opt) {
                if ($field->is_display_amounts) {
                    $opt['label'] .= '&nbsp;-&nbsp;';
                    $opt['label'] .= CRM_Utils_Money::format( $opt['value'] );
                }
                $selectOption[$opt['id']] = $opt['label'];
            }
            $qf->add('select', $elementName, $label,
                     array( '' => ts('- select -')) + $selectOption,
                     ( ( $useRequired || ($useRequired && $field->is_required) ) ) );
            break;

        case 'CheckBox':
            $customOption = CRM_Core_BAO_PriceField::getOptions($field->id, $inactiveNeeded);
            $check = array();
            foreach ($customOption as $opt) {
                if ($field->is_display_amounts) {
                    $opt['label'] .= '&nbsp;-&nbsp;';
                    $opt['label'] .= CRM_Utils_Money::format( $opt['value'] );
                }
                $check[] =& $qf->createElement('checkbox', $opt['id'], null, $opt['label']); 
            }
            $qf->addGroup($check, $elementName, $label);
            if ( ( $useRequired ||( $useRequired && $field->is_required) ) ) {
                $qf->addRule($elementName, ts('%1 is a required field.', array(1 => $label)) , 'required');
            }
            break;
            
        }
    }

    /**
     * Retrieve a list of options for the specified field
     *
     * @param int $fieldId price field ID
     * @param bool $inactiveNeeded include inactive options
     * @param bool $reset ignore stored values\
     *
     * @return array array of options
     */
    public static function getOptions( $fieldId, $inactiveNeeded = false, $reset = false ) {
        static $options = array();
        if ( $reset || empty( $options[$fieldId] ) ) {
            $options[$fieldId] = CRM_Core_BAO_CustomOption::getCustomOption($fieldId, $inactiveNeeded, 'civicrm_price_field');
        }
        return $options[$fieldId];
    }
            
    /**
     * Delete the Custom Field.
     *
     * @param   int   $id    Field Id 
     * 
     * @return  boolean
     *
     * @access public
     * @static
     *
     */
    public static function deleteField($id) 
    {
        require_once 'CRM/Utils/Weight.php';

        // delete options
        require_once( 'CRM/Core/DAO/CustomOption.php' );
        $customOption =& new CRM_Core_DAO_CustomOption();
        $customOption->entity_table = 'civicrm_price_field';
        $customOption->entity_id = $id;
        $customOption->delete();
        
        //delete field
        $field = & new CRM_Core_DAO_PriceField();
        $field->id = $id; 
        if ( $field->find( ) ) {
            $field->fetch( );
            $price_set_id = $field->price_set_id;
            $fieldValues = array( 'price_set_id' => $price_set_id );
            CRM_Utils_Weight::delWeight( 'CRM_Core_DAO_PriceField', $field->id, $fieldValues );
            return $field->delete( );
        }
        
        return null;
    }

}
?>
