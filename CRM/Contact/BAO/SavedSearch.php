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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Contact/DAO/SavedSearch.php';

class CRM_Contact_BAO_SavedSearch extends CRM_Contact_DAO_SavedSearch 
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * query the db for all saved searches.
     *
     * @param boolean $count is this query used for counting rows only ?
     *
     * @return none
     *
     * @access public
     */
    function getAll($count=FALSE)
    {
        CRM_Error::le_method();
        CRM_Error::ll_method();
    }


    /**
     * convert form values to basic english language
     *
     * it checks for the following variables to build a basic english sentence
     *
     *     cb_contact_type, cb_group, cb_category
     *     sort_name, street_name, city, state_province, country
     *     postal_code, postal_code_low, postal_code_high
     *     cb_location_type, cb_primary_location
     *
     * @param array() reference of the submitted form values
     *
     * @return string english represetation of the form values.
     *
     * @access public
     */
    function convertToEnglish(&$formValues)
    {
        CRM_Error::le_method();

        $andArray = array();

        $englishString = "Get me all ";

        // check for contact type restriction
        if ($formValues['cb_contact_type']) {
            foreach ($formValues['cb_contact_type']  as $k => $v) {
                $englishString .= "{$k}s, "; 
            }            
            // replace the last comma with the parentheses.
            $englishString = rtrim($englishString);
            $englishString = rtrim($englishString, ",");
            $englishString .= " ";
        } else {
            $englishString .= "contacts ";
        }


        // check for group membership
        if ($formValues['cb_group']) {
            $englishString .= " who are members of group_id ";
            foreach ($formValues['cb_group']  as $k => $v) {
                $englishString .= "{$k}, "; 
            }
            // replace the last comma with the parentheses.
            $englishString = rtrim($englishString);
            $englishString = rtrim($englishString, ",");
            $englishString .= " ";
        }


        // check for group membership
        if ($formValues['cb_category']) {
            $englishString .= " who are categorized as ";
            foreach ($formValues['cb_category']  as $k => $v) {
                $englishString .= "{$k}, "; 
            }
            // replace the last comma with the parentheses.
            $englishString = rtrim($englishString);
            $englishString = rtrim($englishString, ",");
            $englishString .= " ";
        }
        
        return $englishString;

        CRM_Error::ll_method();
    }
}
?>