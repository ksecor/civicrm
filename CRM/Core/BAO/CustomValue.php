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

require_once 'CRM/Core/DAO/CustomValue.php';


/**
 * Business objects for managing custom data values.
 *
 */
class CRM_Core_BAO_CustomValue extends CRM_Core_DAO_CustomValue {

    /**
     * Validate a value against a CustomField type
     *
     * @param string $type  The type of the data
     * @param string $value The data to be validated
     * 
     * @return boolean True if the value is of the specified type
     * @access public
     * @static
     */
    public static function typecheck($type, $value) {
        switch($type) {
            case 'Memo':
                return true;

            case 'String':
                return CRM_Utils_Rule::string($value);

            case 'Int':
                return CRM_Utils_Rule::integer($value);

            case 'Float':
            case 'Money':
                return CRM_Utils_Rule::numeric($value);

            case 'Date':
                return CRM_Utils_Rule::date($value);

            case 'Boolean':
                return CRM_Utils_Rule::boolean($value);

            case 'StateProvince':
                return
                    in_array($value,
                        CRM_Core_PseudoConstant::stateProvinceAbbreviation())
                    || in_array($value, CRM_Core_PseudoConstant::stateProvince());

            case 'Country':
                return
                    in_array($value, CRM_Core_PseudoConstant::countryIsoCode())
                    || in_array($value, CRM_Core_PseudoConstant::country());
        }
        return false;
    }



    /**
     * Create a new CustomValue record
     *
     * @param array $params  The values for the new record
     *
     * @return object  The new BAO
     * @access public
     * @static
     */
    public static function create(&$params) {
        $customValue =& new CRM_Core_BAO_CustomValue();

        $customValue->copyValues($params);
        
        switch($params['type']) {
            case 'String':
            case 'StateProvince':
            case 'Country':
                $customValue->char_data = $params['value'];
                break;
            case 'Boolean':
                $customValue->int_data = 
                CRM_Utils_String::strtobool($params['value']);
                break;
            case 'Int':
                $customValue->int_data = $params['value'];
                break;
            case 'Float':
            case 'Money':
                $customValue->float_data = $params['value'];
                break;
            case 'Memo':
                $customValue->memo_data = $params['value'];
                break;
            case 'Date':
                $customValue->date_data = $params['value'];
                break;
        }
        $customValue->save();
        
        return $customValue;
    }
}
?>
