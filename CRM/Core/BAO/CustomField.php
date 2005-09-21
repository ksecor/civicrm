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
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/DAO/CustomField.php';


/**
 * Business objects for managing custom data fields.
 *
 */
class CRM_Core_BAO_CustomField extends CRM_Core_DAO_CustomField {

    /**
     * Array for valid combinations of data_type & descriptions
     *
     * @var array
     * @static
     */
    public static $_dataType = null;


    /**
     * Array to hold (formatted) fields for import
     *
     * @var array
     * @static
     */
    public static $_importFields = null;

    /**
     * Build and retrieve the list of data types and descriptions
     *
     * @param none
     * @return array        Data type => Description
     * @access public
     * @static
     */
    static function &dataType()
    {
        if (!(self::$_dataType)) {
            self::$_dataType = array(
                'String'        => ts('Alphanumeric'),
                'Int'           => ts('Integer'),
                'Float'         => ts('Number'),
                'Money'         => ts('Money'),
                'Memo'          => ts('Note'),
                'Date'          => ts('Date'),
                'Boolean'       => ts('Yes or No'),
                'StateProvince' => ts('State/Province'),
                'Country'       => ts('Country'),
            );
        }
        return self::$_dataType;
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
     * @return object CRM_Core_BAO_CustomField object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults )
    {
        $customField =& new CRM_Core_DAO_CustomField( );
        $customField->copyValues( $params );
        if ( $customField->find( true ) ) {
            CRM_Core_DAO::storeValues( $customField, $defaults );
            return $customField;
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
        return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_CustomField', $id, 'is_active', $is_active );
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
    public static function getNumValue($fieldId)
    {
        $cvTable = CRM_Core_DAO_CustomValue::getTableName();
        $query = "SELECT count(*) 
                  FROM   $cvTable 
                  WHERE  $cvTable.custom_field_id = " .
                  CRM_Utils_Type::escape($fieldId, 'Integer');

        return CRM_Core_DAO::singleValueQuery( $query );
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
        return CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField', $id, 'label' );
    }
    
    /**
     * Store and return an array of all active custom fields.
     *
     * @return array $fields - 
     *
     * @access public
     * @static
     */
    public static function &getFields($contactType = 'Individual' ) {
        
        // if (!(self::$_importFields)) {
           
            $cfTable = self::getTableName();
            $cgTable = CRM_Core_DAO_CustomGroup::getTableName();
            $query ="SELECT $cfTable.id, $cfTable.label,
                            $cgTable.title, $cfTable.data_type, $cfTable.options_per_line,
                            $cgTable.extends
                     FROM $cfTable
                     INNER JOIN $cgTable
                     ON $cfTable.custom_group_id = $cgTable.id
                     WHERE $cfTable.is_active = 1
                     AND   $cgTable.is_active = 1
                     AND   $cgTable.extends IN 
                            ('".$contactType."', 'Contact')
                     ORDER BY $cgTable.weight, $cgTable.id,
                              $cfTable.weight, $cfTable.id";
                 
            $crmDAO =& new CRM_Core_DAO();
            $crmDAO->query($query);
            $result = $crmDAO->getDatabaseResult();
            self::$_importFields = array();
        
            while (($row = $result->fetchRow()) != null) {
                $id = array_shift($row);
                self::$_importFields[$id] = $row;
            }
            // }
        
        // CRM_Core_Error::debug( 's', self::$_importFields );
        return self::$_importFields;
    }

    /**
     * Return the field ids and names (with groups) for import purposes.
     *
     * @param int $contactType contact type
     *
     * @return array $fields - 
     *
     * @access public
     * @static
     */
    public static function &getFieldsForImport($contactType = 'Individual') {
        
        $fields = self::getFields($contactType);
        
        $importableFields = array();
        foreach ($fields as $id => $values) {
            /* generate the key for the fields array */
            $key = "custom_$id";
            $regexp = preg_replace('/[.,;:!?]/', '', $values[0]);
            $importableFields[$key] = array(
                'title' => "$values[1]: $values[0]",
                'headerPattern' => '/' . preg_quote($regexp, '/') . '/',
                'import' => 1,
                'custom_field_id' => $id,
                'options_per_line' => $values[3]
            );
        }
         
        return $importableFields;
    }

    /**
     * Get the field id from an import key
     *
     * @param string $key       The key to parse
     * @return int|null         The id (if exists)
     * @access public
     * @static
     */
    public static function getKeyID($key) {
        if (preg_match('/^custom_(\d+)$/', $key, $match)) {
            return $match[1];
        } 
        return null;
    }
    

    /* static wrapper for _addQuickFormElement */
    public static function addQuickFormElement(&$qf, $elementName, $fieldId, $inactiveNeeded, $useRequired) {
        $field =& new CRM_Core_BAO_CustomField();
        $field->id = $fieldId;
        if (! $field->find(true)) {
            /* FIXME: failure! */
            return null;
        }
        
        /**
         * THis was split into a different function before. however thanx to php4's bug with references,
         * it was not working, so i munged it back into one big function - lobo
         */
        switch($field->html_type) {
            case 'Text':
            case 'TextArea':
                $element = $qf->add(strtolower($field->html_type), $elementName, $field->label,
                                        $field->attributes, ($useRequired && $field->is_required));
                break;

            case 'Select Date':
                $qf->add('date', $elementName, $field->label, CRM_Core_SelectValues::date( 'custom' ), ($useRequired && $field->is_required));
                break;

            case 'Radio':
                $choice = array();
                if($field->data_type != 'Boolean') {
                    $customOption = CRM_Core_BAO_CustomOption::getCustomOption($field->id, $inactiveNeeded);
                    
                    foreach ($customOption as $v) {
                        $choice[] = $qf->createElement('radio', null, '', $v['label'], $v['value'], $field->attributes);
                    }
                    
                    $qf->addGroup($choice, $elementName, $field->label);
                    
                } else {
                    $choice[] = $qf->createElement('radio', null, '', ts('Yes'), '1', $field->attributes);
                    $choice[] = $qf->createElement('radio', null, '', ts('No') , '0' , $field->attributes);
                    $qf->addGroup($choice, $elementName, $field->label);
                }
                if ($useRequired && $field->is_required) {
                    $qf->addRule($elementName, ts('%1 is a required field.', array(1 => $field->label)) , 'required');
                }
                break;

            case 'Select':
                $customOption = CRM_Core_BAO_CustomOption::getCustomOption($field->id, $inactiveNeeded);
                $selectOption = array();
                $selectOption[] = '(select)';
                foreach ($customOption as $v) {
                    $selectOption[$v['value']] = $v['label'];
                }
                $qf->add('select', $elementName, $field->label, $selectOption, ($useRequired && $field->is_required));
                break;

            case 'CheckBox':
                $customOption = CRM_Core_BAO_CustomOption::getCustomOption($field->id, $inactiveNeeded);
                $check = array();
                foreach ($customOption as $v) {
                    $checked = array();
                    $check[] = $qf->createElement('checkbox', $v['value'], null, $v['label']);
                }
                $qf->addGroup($check, $elementName, $field->label);
                if ($useRequired && $field->is_required) {
                    $qf->addRule($elementName, ts('%1 is a required field.', array(1 => $field->label)) , 'required');
                }
                break;

            case 'Select State/Province':
                //Add State
                if ($qf->getAction() & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
                    $stateOption = array('' => ts('')) + CRM_Core_PseudoConstant::stateProvince();
                } else { 
                    $stateOption = array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince();
                }
                $qf->add('select', $elementName, $field->label, $stateOption, ($useRequired && $field->is_required));
                break;

            case 'Select Country':
                //Add Country
                if ($qf->getAction() & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
                    $countryOption = array('' => ts('')) + CRM_Core_PseudoConstant::country();
                } else {
                    $countryOption = array('' => ts('- select -')) + CRM_Core_PseudoConstant::country();
                }
                $qf->add('select', $elementName, $field->label, $countryOption, ($useRequired && $field->is_required));
                break;
            }
                
        switch ( $field->data_type ) {
            case 'Int':
                // integers will have numeric rule applied to them.
                $qf->addRule($elementName, ts('%1 must be an integer (whole number).', array(1 => $field->label)), 'integer');
                break;

            case 'Date':
                $qf->addRule($elementName, ts('%1 is not a valid date.', array(1 => $field->label)), 'qfDate');
                break;

            case 'Float':
            case 'Money':
                $qf->addRule($elementName, ts('%1 must be a number (with or without decimal point).', array(1 => $field->label)), 'numeric');
                break;
        }
    }

}
?>
