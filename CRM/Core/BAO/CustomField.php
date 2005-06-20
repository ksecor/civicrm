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
		        'Multi-Select'  => ts('Multiple Options')
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
        $queryString = "SELECT count(*) 
                        FROM   crm_custom_value 
                        WHERE  crm_custom_value.custom_field_id = $fieldId";

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
}
?>
