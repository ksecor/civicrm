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

        switch($params['extends']) {
            case 'Contact':
                $customValue->entity_table = 'crm_contact';
                $customValue->entity_id = $params['contact_id'];
                break;
            case 'Individual':
                $customValue->entity_table = 'crm_individual';
                $customValue->entity_id = $params['individual_id'];
                break;
            case 'Household':
                $customValue->entity_table = 'crm_household';
                $customValue->entity_id = $params['household_id'];
                break;
            case 'Organization':
                $customValue->entity_table = 'crm_organization';
                $customValue->entity_id = $params['organization_id'];
                break;
        }
        $customValue->save();
        
    }
}
?>
