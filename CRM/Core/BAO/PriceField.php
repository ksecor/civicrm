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

require_once 'CRM/Core/DAO/PriceField.php';


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
     * the function extract all the params it needs to initialize the create a
     * price field object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params    (reference ) an assoc array of name/value pairs
     * @param array  $ids       the array that holds all the db ids
     *
     * @return object CRM_Core_BAO_PriceField object
     * @access public
     * @static
     */
    static function &add( &$params, $ids ) 
    {
        $priceFieldBAO         =& new CRM_Core_BAO_PriceField( );
        
        $priceFieldBAO->copyValues( $params );
        
        if ( $id = CRM_Utils_Array::value( 'id', $ids ) ) {
            $priceFieldBAO->id = $id;
        }
        
        return $priceFieldBAO->save( );
    }
    
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
    static function create( &$params, $ids )
    {
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );
        
        $priceField =& self::add( $params, $ids );
        
        if ( is_a( $priceField, 'CRM_Core_Error') ) {
            $transaction->rollback( );
            return $priceField;
        }
        
        $options  = array( );
        require_once 'CRM/Price/Form/Field.php';
        $maxIndex = CRM_Price_Form_Field::NUM_OPTION;
        
        if ( $priceField->html_type == 'Text' ) {
            $maxIndex = 1;
        }
        $defaultArray = array();
        if ( $params['html_type'] == 'CheckBox' && isset($params['default_checkbox_option'] ) ) {
            $tempArray = array_keys( $params['default_checkbox_option'] );
            foreach ( $tempArray as $v ) {
                if ( $params['option_value'][$v] ) {
                    $defaultArray[$v] = 1;
                }
            }
        } else {
            if ( CRM_Utils_Array::value( 'default_option', $params ) 
                 && isset( $params['option_value'][$params['default_option']] ) ) {
                $defaultArray[$params['default_option']] = 1;
            }
        }  
        for ( $index = 1; $index <= $maxIndex; $index++ ) {
            if ( $maxIndex == 1 ) {
                $description = $params['label'];
            } else {
                $description = $params['label'] . " - " . trim($params['option_label'][$index]);
            }
            
            if ( ( ! empty( $params['option_label'][$index] ) ) &&
                 ( ! empty( $params['option_value'][$index] ) ) ) {
                $options[] = array( 'label'       => trim( $params['option_label'][$index] ),
                                    'name'        => CRM_Utils_Rule::cleanMoney( trim( $params['option_name'][$index] ) ),
                                    'value'       => trim( $params['option_value'][$index] ),
                                    'description' => $description,
                                    'weight'      => $params['option_weight'][$index],
                                    'is_active'   => 1,
                                    'is_default'  => CRM_Utils_Array::value( $index, $defaultArray)
                                    );
            }
        }
        
        if ( ! empty( $options ) ) {
            $params['default_amount_id'] = null;
            $groupName                   = "civicrm_price_field.amount.{$priceField->id}";
            require_once 'CRM/Core/OptionGroup.php';
            CRM_Core_OptionGroup::createAssoc( $groupName,
                                               $options,
                                               $params['default_amount_id'] );
        }
        
        $transaction->commit( );
        return $priceField;
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
     
        $crmDAO =& CRM_Core_DAO::executeQuery( $query );
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
     * @param boolean $useRequired    true if required else false
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
        $config =& CRM_Core_Config::singleton();
        $qf->assign('currencySymbol', CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Currency',$config->defaultCurrency,'symbol','name') );
        if (!isset($label)) {
            $label = $field->label;
        }
	if ( isset($qf->_online) ) {
	  $useRequired = false;
	}

        switch($field->html_type) {
        case 'Text':
            $customOption = CRM_Core_BAO_PriceField::getOptions( $field->id, $inactiveNeeded );
            
            // text fields only have one option
            $optionKey = key($customOption);
            
            if ($field->is_display_amounts) {
                $label .= '&nbsp;-&nbsp;';
                $label .= CRM_Utils_Money::format( CRM_Utils_Array::value('name', $customOption[$optionKey]) );
            }

            $element =& $qf->add(
                                 'text', $elementName, $label, array_merge( array('size' =>"4"), 
                                                                            array( 'price' => $optionKey."_".$customOption[$optionKey]['name'] )),
                                 $useRequired && $field->is_required
                                 );
            
            // integers will have numeric rule applied to them.
            $qf->addRule($elementName, ts('%1 must be an integer (whole number).', array(1 => $label)), 'positiveInteger');
            break;
            
        case 'Radio':
            $choice = array();
            $customOption = CRM_Core_BAO_PriceField::getOptions($field->id, $inactiveNeeded);
            if ( !$field->is_required ) {
                // add "none" option
                $choice[] = $qf->createElement('radio', null, '', '-none-', '0', array('price' => $elementName."-0" ));
            }
            
            foreach ($customOption as $opt) {
                if ($field->is_display_amounts) {
                    $opt['label'] .= '&nbsp;-&nbsp;';
                    $opt['label'] .= CRM_Utils_Money::format( $opt['name'] );
                }
                $choice[] = $qf->createElement('radio', null, '', $opt['label'], $opt['id'],
                                               array('price' => $elementName."-".$opt['name'] ) );
            }
            $element =& $qf->addGroup($choice, $elementName, $label);

            if ( $useRequired && $field->is_required ) {
                $qf->addRule($elementName, ts('%1 is a required field.', array(1 => $label)) , 'required');
            }
            break;
            
        case 'Select':
            $customOption = CRM_Core_BAO_PriceField::getOptions($field->id, $inactiveNeeded);
            $selectOption = array();
            $amount = '[{';
            foreach ($customOption as $opt) {
                $amount .= $opt['id'].':"'. $opt['name'] .'",';
                if ($field->is_display_amounts) {
                    $opt['label'] .= '&nbsp;-&nbsp;';
                    $opt['label'] .= CRM_Utils_Money::format( $opt['name'] );
                }
                $selectOption[$opt['id']] = $opt['label'];
            }
            $amount .= '}]';
            $element =& $qf->add('select', $elementName, $label,
                                 array( '' => ts('- select -')) + $selectOption,
                                 $useRequired && $field->is_required, 
                                 array( 'price' => $amount ) );
            break;
            
        case 'CheckBox':
            $customOption = CRM_Core_BAO_PriceField::getOptions($field->id, $inactiveNeeded);
            $check = array();
            foreach ($customOption as $opt) {
                if ($field->is_display_amounts) {
                    $opt['label'] .= '&nbsp;-&nbsp;';
                    $opt['label'] .= CRM_Utils_Money::format( $opt['name'] );
                }
                $check[] =& $qf->createElement('checkbox', $opt['id'], null, $opt['label'], 
                                               array('price' => $opt['id']."_".$opt['name'] ) );
            }
            $element =& $qf->addGroup($check, $elementName, $label);
            if ( $useRequired && $field->is_required ) {
                $qf->addRule($elementName, ts('%1 is a required field.', array(1 => $label)) , 'required');
            }
            break;
            
        }
        if ( isset($qf->_online) ) {
            $element->freeze();
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
            $groupParams = array( 'name' => "civicrm_price_field.amount.{$fieldId}");
            
            $values = array( );
            require_once 'CRM/Core/OptionValue.php';
            CRM_Core_OptionValue::getValues( $groupParams, $values, 'weight', ! $inactiveNeeded );
        }
        
        return $values;
    }
    
    public static function getOptionId( $optionLabel, $fid ) 
    {
        $optionGroupName = "civicrm_price_field.amount.{$fid}";
        
        $query = "
SELECT 
        option_value.id as id
FROM 
        civicrm_option_value option_value,
        civicrm_option_group option_group
WHERE 
        option_group.name  = '" . $optionGroupName . "'
    AND option_group.id    = option_value.option_group_id
    AND option_value.label = '" . $optionLabel . "'";
        
        $dao    =& CRM_Core_DAO::executeQuery( $query );
        
        while ( $dao->fetch( ) ) {
            return $dao->id;
        }
    }
    
    /**
     * Delete the price set field.
     *
     * @param   int   $id    Field Id 
     * 
     * @return  boolean
     *
     * @access public
     * @static
     *
     */
    public static function deleteField( $id ) 
    {
        $field     = & new CRM_Core_DAO_PriceField( );
        $field->id = $id;
        
        if ( $field->find( true ) ) {
            // delete the options for this field
            require_once 'CRM/Core/OptionGroup.php';
            CRM_Core_OptionGroup::deleteAssoc( "civicrm_price_field.amount.{$id}" );
            
            // reorder the weight before delete
            $fieldValues  = array( 'price_set_id' => $field->price_set_id );
            
            require_once 'CRM/Utils/Weight.php';
            CRM_Utils_Weight::delWeight( 'CRM_Core_DAO_PriceField', $field->id, $fieldValues );
            
            // now delete the field 
            return $field->delete( );
        }
        
        return null;
    }

    static function &htmlTypes( ) {
        static $htmlTypes = null;
        if ( ! $htmlTypes ) {
            $htmlTypes = array(
                               'Text'     => ts('Text / Numeric Quantity'),
                               'Select'   => ts('Select'),
                               'Radio'    => ts('Radio'),
                               'CheckBox' => ts('CheckBox'),
                               );
        }
        return $htmlTypes;
    }
}

