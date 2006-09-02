<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */
require_once 'CRM/Core/BAO/OptionValue.php';
require_once 'CRM/Core/BAO/OptionGroup.php';

class CRM_Core_OptionValue {
    
    /**
     * Function to return option-values of a particular group
     *
     * @param  array     $groupParams   Array containing group fields whose option-values is to retrieved.
     * @param  string    $orderBy       for orderBy clause
     * @param  array     $links         has links like edit, delete, disable ..etc
     *
     * @return array of option-values     
     * 
     * @access public
     * @static
     */

    static function getRows( $groupParams, $links, $orderBy = 'weight' ) {
        $optionValue = array();
        
        if (! $groupParams['id'] ) {
            if ( $groupParams['name'] ) {
                $config =& CRM_Core_Config::singleton( );
                $groupParams['domain_id'] = $config->domainID( );
                
                $optionGroup = CRM_Core_BAO_OptionGroup::retrieve($groupParams, $dnc);
                $optionGroupID = $optionGroup->id;
            }
        } else {
            $optionGroupID = $groupParams['id'];
        }
        
        $dao =& new CRM_Core_DAO_OptionValue();
        
        if ($optionGroupID) {
            $dao->option_group_id = $optionGroupID;
            $dao->orderBy($orderBy);
            $dao->find();
        }
        
        while ($dao->fetch()) {
            $optionValue[$dao->id] = array();
            CRM_Core_DAO::storeValues( $dao, $optionValue[$dao->id]);
            // form all action links
            $action = array_sum(array_keys($links));
            if( $dao->is_default ) {
                $optionValue[$dao->id]['default_value'] = '[x]';
            }

            // update enable/disable links depending on if it is is_reserved or is_active
            if ($dao->is_reserved) {
                continue;
            } else {
                if ($dao->is_active) {
                    $action -= CRM_Core_Action::ENABLE;
                } else {
                    $action -= CRM_Core_Action::DISABLE;
                }
            }
            $optionValue[$dao->id]['action'] = CRM_Core_Action::formLink($links, $action, 
                                                                         array('id' => $dao->id,'gid' => $optionGroupID ));
        }
        return $optionValue;
    }

    /**
     * Function to add/edit option-value of a particular group
     *
     * @param  array     $params           Array containing exported values from the invoking form.
     * @param  array     $groupParams      Array containing group fields whose option-values is to retrieved/saved.
     * @param  string    $orderBy          for orderBy clause
     * @param  integer   $optionValueID    has the id of the optionValue being edited, disabled ..etc
     *
     * @return array of option-values     
     * 
     * @access public
     * @static
     */
    static function addOptionValue( &$params, &$groupParams, &$action, &$optionValueID ) {
        $params['is_active'] =  CRM_Utils_Array::value( 'is_active', $params, false );
        // checking if the group name with the given id or name (in $groupParams) exists
        if (! empty($groupParams)) {
            $config =& CRM_Core_Config::singleton( );
            $groupParams['domain_id'] = $config->domainID( );
            $groupParams['is_active']   = 1;
            $optionGroup = CRM_Core_BAO_OptionGroup::retrieve($groupParams, $defaults);
        }
        // if the corresponding group doesn't exist, create one, provided $groupParams has 'name' in it.
        if (! $optionGroup->id) {
            if ( $groupParams['name'] ) {
                $newOptionGroup = CRM_Core_BAO_OptionGroup::add($groupParams, $defaults);
                $params['weight'] = 1;
                $optionGroupID = $newOptionGroup->id;
            }
        } else {
            $optionGroupID = $optionGroup->id;
            if ( !$params['weight'] && !$optionValueID ) {
                $query = "SELECT max( `weight` ) as weight FROM `civicrm_option_value` where option_group_id=" . $optionGroupID;
                $dao =& new CRM_Core_DAO( );
                $dao->query( $query );
                $dao->fetch();
                $params['weight'] = ($dao->weight + 1);
            }
        }
        $params['option_group_id'] = $optionGroupID;

        if ( !$params['value'] ) {
            $params['value'] = $params['weight'];
        }
        if ( !$params['label'] ) {
            $params['label'] = $params['name'];
        }
        if ( $action & CRM_Core_Action::UPDATE ) {
            $ids['optionValue'] = $optionValueID;
        }
        $optionValue = CRM_Core_BAO_OptionValue::add($params, $ids);
        return $optionValue;
    }
}

?>