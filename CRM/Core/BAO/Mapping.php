<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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

require_once 'CRM/Core/DAO/Mapping.php';

class CRM_Core_BAO_Mapping extends CRM_Core_DAO_Mapping 
{

    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     * 
     * @param array $params      (reference ) an assoc array of name/value pairs
     * @param array $defaults    (reference ) an assoc array to hold the flattened values
     * 
     * @return object     CRM_Core_DAO_Mapping object on success, otherwise null
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $mapping =& new CRM_Core_DAO_Mapping( );
        $mapping->copyValues( $params );
        if ( $mapping->find( true ) ) {
            CRM_Core_DAO::storeValues( $mapping, $defaults );
            return $mapping;
        }
        return null;
    }
    
    /**
     * Function to delete the mapping 
     *
     * @param int $id   mapping id
     *
     * @return boolean
     * @access public
     * @static
     *
     */
    static function del ( $id ) 
    {
        // delete from mapping_field table
        require_once "CRM/Core/DAO/MappingField.php";
        $mappingField =& new CRM_Core_DAO_MappingField( );
        $mappingField->mapping_id = $id;
        $mappingField->find();
        while ( $mappingField->fetch() ) {
            $mappingField->delete();
        }
        
        // delete from mapping table
        $mapping =& new CRM_Core_DAO_Mapping( );
        $mapping->id = $id;
        $mapping->delete();
        CRM_Core_Session::setStatus( ts('Selected Mapping has been Deleted Successfuly.') );
        
        return true;
    }
    
    /**
     * takes an associative array and creates a contact object
     * 
     * The function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     * 
     * @param array  $params         (reference) an assoc array of name/value pairs
     * @param array  $ids            (reference) the array that holds all the db ids
     * 
     * @return object    CRM_Core_DAO_Mapper object on success, otherwise null
     * @access public
     * @static
     */
    static function add( &$params, &$ids ) 
    {
        if ( ! self::dataExists( $params ) ) {
	  //return null;
        }
        
        $mapping               =& new CRM_Core_DAO_Mapping( );
        $mapping->domain_id    = CRM_Core_Config::domainID( );
        $mapping->copyValues( $params );
        $mapping->id = CRM_Utils_Array::value( 'mapping', $ids );
        $mapping->save( );

        //CRM_Core_Session::setStatus( ts('The mapping "%1" has been saved.', array(1 => $mapping->name)) );
        
        return $mapping;
    }
    
    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params ) 
    {
        if ( !empty( $params['name'] ) ) {
	   return true;
        }
        
        return false;
    }

    /**
     * function to get the list of mappings
     * 
     * @params string  $mappingType  mapping type 
     *
     * @return array $mapping array of mapping name 
     * @access public
     * @static
     */
    static function getMappings($mappingType)
    {
        $mappingArray = array();
        $mappingDAO =&  new CRM_Core_DAO_Mapping();
        $mappingDAO->domain_id = CRM_Core_Config::domainID( );
        $mappingDAO->mapping_type = $mappingType;
        $mappingDAO->find();
        
        while ($mappingDAO->fetch()) {
            $mappingArray[$mappingDAO->id] = $mappingDAO->name;
        }
        
        return $mappingArray;
    }

    /**
     * function to get the mapping fields
     *
     * @params int $mappingId  mapping id
     *
     * @return array $mappingFields array of mapping fields
     * @access public
     * @static
     *
     */
    static function getMappingFields( $mappingId )
    {
        //mapping is to be loaded from database
        $mapping =& new CRM_Core_DAO_MappingField();
        $mapping->mapping_id = $mappingId;
        $mapping->orderBy('column_number');
        $mapping->find();
        
        $mappingName = array();
        $mappingLocation = array();
        $mappingContactType = array();
        $mappingPhoneType = array();
        $mappingRelation = array();
        while($mapping->fetch()) {
            $mappingName[$mapping->column_number] = $mapping->name;
            $mappingContactType[] = $mapping->contact_type;                
            
            if ( !empty($mapping->location_type_id ) ) {
                $mappingLocation[$mapping->column_number] = $mapping->location_type_id;
            }
            
            if ( !empty( $mapping->phone_type ) ) {
                $mappingPhoneType[$mapping->column_number] = $mapping->phone_type;
            }
            
            if ( !empty($mapping->relationship_type_id) ) {
                $mappingRelation[$mapping->column_number] = $mapping->relationship_type_id;
            }
        }
        
        return array ($mappingName, $mappingContactType, $mappingLocation, $mappingPhoneType, $mappingRelation);   
    }

    /**
     *function to check Duplicate Mapping Name
     *
     * @params $nameField  string mapping Name
     *
     * @params $mapType string mapping Type
     *
     * @return boolean
     * 
     */
    static function checkMapping($nameField,$mapType)
    {
         $mappingName =& new CRM_Core_DAO_Mapping();
         $mappingName->name = $nameField;
         $mappingName->mapping_type = $mapType;
         if($mappingName->find(true)){
             return true;
         }else{
             return false;
         }
    }


    /**
     * Function returns associated array of elements, that will be passed for search
     *
     * @params int $smartGroupId smart group id 
     *
     * @return $returnFields  associated array of elements
     *
     * @static
     * @public
     */
    static function getFormatedFields($smartGroupId) 
    {
        $returnFields = array();

        //get the fields from mapping table
        $dao =& new CRM_Core_DAO_MappingField( );
        $dao->mapping_id = $smartGroupId;
        $dao->find();
        while ( $dao->fetch( ) ) {
            $fldName = $dao->name;
            if ($dao->location_type_id) {
                $fldName .= "-{$dao->location_type_id}";
            }
            if ($dao->phone_type) {
                $fldName .= "-{$dao->phone_type}" ;
            }
            $returnFields[$fldName]['value'   ] = $dao->value;
            $returnFields[$fldName]['op'      ] = $dao->operator;
            $returnFields[$fldName]['grouping'] = $dao->grouping;

        }

        //print_r($returnFields);
        return $returnFields;
    }

}
?>