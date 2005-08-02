<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
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
        $queryString = "SELECT count(*) 
                        FROM   $cvTable 
                        WHERE  $cvTable.custom_field_id = $fieldId";

        // dummy dao needed
        $crmDAO =& new CRM_Core_DAO();
        $crmDAO->query($queryString);
        // does not work for php4
        //$row = $crmDAO->getDatabaseResult()->fetchRow();
        $result = $crmDAO->getDatabaseResult();
        $row    = $result->fetchRow();
        return $row[0];
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
    public static function &getFields( ) {
        if (!(self::$_importFields)) {
            $cfTable = self::getTableName();
            $cgTable = CRM_Core_DAO_CustomGroup::getTableName();
            $query ="SELECT $cfTable.id, $cfTable.label,
                            $cgTable.title, $cfTable.data_type,
                            $cgTable.extends
                     FROM $cfTable
                     INNER JOIN $cgTable
                     ON $cfTable.custom_group_id = $cgTable.id
                     WHERE $cfTable.is_active = 1
                     AND   $cgTable.is_active = 1
                     AND   $cgTable.extends IN 
                            ('Individual', 'Contact')
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
        }
        
        return self::$_importFields;
    }

    /**
     * Return the field ids and names (with groups) for import purposes.
     *
     * @return array $fields - 
     *
     * @access public
     * @static
     */
    public static function &getFieldsForImport( ) {
        $fields = self::getFields();
        
        $importableFields = array();
        foreach ($fields as $id => $values) {
            /* generate the key for the fields array */
            $key = "custom.$id";
            $regexp = preg_replace('/[.,;:!?]/', '', $values[0]);
            $importableFields[$key] = array(
                'title' => "$values[1]: $values[0]",
                'headerPattern' => '/' . preg_quote($regexp, '/') . '/',
                'import' => 1,
                'custom_field_id' => $id,
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
        if (preg_match('/^custom\.(\d+)$/', $key, $match)) {
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
        
        return $field->_addQuickFormElement($qf, $elementName, $inactiveNeeded, $useRequired);
    }

    public function _addQuickFormElement(&$qf, $elementName, $inactiveNeeded, $useRequired) {
        switch($this->html_type) {
            case 'Text':
            case 'TextArea':
                $element = $qf->add(strtolower($this->html_type), $elementName, $this->label,
                                        $this->attributes, ($useRequired && $this->is_required));
                break;

            case 'Select Date':
                $qf->add('date', $elementName, $this->label, CRM_Core_SelectValues::date( 'custom' ), ($useRequired && $this->is_required));
                break;

            case 'Radio':
                $choice = array();
                if($this->data_type != 'Boolean') {
                    $customOption = CRM_Core_BAO_CustomOption::getCustomOption($this->id, $inactiveNeeded);
                    foreach ($customOption as $v) {
                        $choice[] = $qf->createElement('radio', null, '', $v['label'], $v['value'], $this->attributes);
                    }
                    $qf->addGroup($choice, $elementName, $this->label);
                } else {
                    $choice[] = $qf->createElement('radio', null, '', ts('Yes'), 'yes', $this->attributes);
                    $choice[] = $qf->createElement('radio', null, '', ts('No') , 'no' , $this->attributes);
                    $qf->addGroup($choice, $elementName, $this->label);
                }
                if ($useRequired && $this->is_required) {
                    $qf->addRule($elementName, ts('%1 is a required field.', array(1 => $this->label)) , 'required');
                }
                break;

            case 'Select':
                $customOption = CRM_Core_BAO_CustomOption::getCustomOption($this->id, $inactiveNeeded);
                $selectOption = array();
                foreach ($customOption as $v) {
                    $selectOption[$v['value']] = $v['label'];
                }
                $qf->add('select', $elementName, $this->label, $selectOption, ($useRequired && $this->is_required));
                break;

            case 'CheckBox':
                $customOption = CRM_Core_BAO_CustomOption::getCustomOption($this->id, $inactiveNeeded);
                $check = array();
                foreach ($customOption as $v) {
                    $checked = array();
                    $check[] = $qf->createElement('checkbox', $v['value'], null, $v['label']);
                }
                $qf->addGroup($check, $elementName, $this->label);
                if ($useRequired && $this->is_required) {
                    $qf->addRule($elementName, ts('%1 is a required field.', array(1 => $this->label)) , 'required');
                }
                break;

            case 'Select State/Province':
                //Add State
                if ($qf->_action & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
                    $stateOption = array('' => ts('')) + CRM_Core_PseudoConstant::stateProvince();
                } else { 
                    $stateOption = array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince();
                }
                $qf->add('select', $elementName, $this->label, $stateOption, ($useRequired && $this->is_required));
                break;

            case 'Select Country':
                //Add Country
                if ($qf->_action & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
                    $countryOption = array('' => ts('')) + CRM_Core_PseudoConstant::country();
                } else {
                    $countryOption = array('' => ts('- select -')) + CRM_Core_PseudoConstant::country();
                }
                $qf->add('select', $elementName, $this->label, $countryOption, ($useRequired && $this->is_required));
                break;
            }
                
        switch ( $this->data_type ) {
            case 'Int':
                // integers will have numeric rule applied to them.
                $qf->addRule($elementName, ts('%1 must be an integer (whole number).', array(1 => $this->label)), 'integer');
                break;

            case 'Date':
                $qf->addRule($elementName, ts('%1 is not a valid date.', array(1 => $this->label)), 'qfDate');
                break;

            case 'Float':
            case 'Money':
                $qf->addRule($elementName, ts('%1 must be a number (with or without decimal point).', array(1 => $this->label)), 'numeric');
                break;
        }
    }

}
?>
