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

require_once 'CRM/Core/DAO/CustomOption.php';


/**
 * Business objects for managing custom data options.
 *
 */
class CRM_Core_BAO_CustomOption extends CRM_Core_DAO_CustomOption {

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
        $customOption =& new CRM_Core_DAO_CustomOption( );
        $customOption->copyValues( $params );
        if ( $customOption->find( true ) ) {
            CRM_Core_DAO::storeValues( $customOption, $defaults );
            return $customOption;
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
        return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_CustomOption', $id, 'is_active', $is_active );
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
    static function getCustomOption($fieldId, $inactiveNeeded=false)
    {
        $customOptionDAO =& new CRM_Core_DAO_CustomOption();
        $customOptionDAO->custom_field_id = $fieldId;
        if (!$inactiveNeeded) {
            $customOptionDAO->is_active = 1;
        }
        $customOptionDAO->orderBy('weight ASC');
        $customOptionDAO->find();
        
        $customOption = array();
        while ($customOptionDAO->fetch()) {
            $customOption[$customOptionDAO->id] = array();
            $customOption[$customOptionDAO->id]['id']    = $customOptionDAO->id;
            $customOption[$customOptionDAO->id]['label'] = $customOptionDAO->label;
            $customOption[$customOptionDAO->id]['value'] = $customOptionDAO->value;
        }
        return $customOption;
    }

    /**
     * Function to get the values of the checkboxes
     *
     * param $fieldId integer field id
     *
     * @static
     * @access public
     */
    static function getCustomValues($fieldId)
    {
        $customValueDAO =& new CRM_Core_DAO_CustomValue();
        $customValueDAO->custom_field_id = $fieldId;
        $customValueDAO->find(true);
        $values = $customValueDAO->char_data;
        $customValue = array();
        $customValue = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR ,$values);
        return $customValue;
    }
}
?>
