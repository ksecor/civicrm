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
        
        $mapping            =& new CRM_Core_DAO_Mapping( );
        $mapping->domain_id = CRM_Core_Config::domainID( );        
        $mapping->id        = CRM_Utils_Array::value( 'mapping', $ids );
        $mapping->copyValues( $params );
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
        require_once "CRM/Core/DAO/MappingField.php";
        $mapping =& new CRM_Core_DAO_MappingField();
        $mapping->mapping_id = $mappingId;
        $mapping->orderBy('column_number');
        $mapping->find();
        
        $mappingName = $mappingLocation = $mappingContactType = $mappingPhoneType = array( );
        $mappingRelation = $mappingOperator = $mappingValue = array( );
        while($mapping->fetch()) {
            $mappingName[$mapping->grouping][$mapping->column_number] = $mapping->name;
            $mappingContactType[$mapping->grouping][$mapping->column_number] = $mapping->contact_type;                
            
            if ( !empty($mapping->location_type_id ) ) {
                $mappingLocation[$mapping->grouping][$mapping->column_number] = $mapping->location_type_id;
            }
            
            if ( !empty( $mapping->phone_type ) ) {
                $mappingPhoneType[$mapping->grouping][$mapping->column_number] = $mapping->phone_type;
            }
            
            if ( !empty($mapping->relationship_type_id) ) {
                $mappingRelation[$mapping->grouping][$mapping->column_number] = $mapping->relationship_type_id;
            }
            
            if ( !empty($mapping->operator) ) {
                $mappingOperator[$mapping->grouping][$mapping->column_number] = $mapping->operator;
            }

            if ( !empty($mapping->value) ) {
                $mappingValue[$mapping->grouping][$mapping->column_number] = $mapping->value;
            }
        }
        
        return array ($mappingName, $mappingContactType, $mappingLocation, $mappingPhoneType,
                      $mappingRelation, $mappingOperator, $mappingValue);   
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
    static function getFormattedFields($smartGroupId) 
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
        return $returnFields;
    }

    /**
     * Function to build the mapping form
     *
     * @params object $form        form object
     * @params string $mappingType mapping type (Export/Import/Search Builder)
     * @params int    $mappingId   mapping id
     * @params mixed  $columnCount column count is int for and array for search builder
     * @params int    $blockCount  block count (no of blocks shown) 
     *
     * @return none
     * @access public
     * @static
     */
    static function buildMappingForm($form, $mappingType = 'Export', $mappingId = null, $columnNo, $blockCount = 3 ) 
    {
        if ($mappingType == 'Export') {
            $name = "Map";
            $columnCount = array ('1' => $columnNo);
        } else if ($mappingType == 'Search Builder') {
            $name = "Builder";
            $columnCount = $columnNo;
        }

        //get the saved mapping details
        require_once 'CRM/Core/DAO/Mapping.php';
        require_once 'CRM/Contact/BAO/Contact.php';
        require_once 'CRM/Core/BAO/LocationType.php';

        if ( $mappingType == 'Export' ) {
            $mappingArray =array();
            
            require_once "CRM/Core/BAO/Mapping.php";
            $mappingArray = CRM_Core_BAO_Mapping::getMappings($mappingType);
            
            if ( !empty($mappingArray) ) {
                $form->assign('savedMapping',$mappingArray);
                $form->add('select','savedMapping', ts('Mapping Option'), array('' => '-select-')+$mappingArray);
                $form->addElement( 'submit', 'loadMapping', ts('Load Mapping'), array( 'class' => 'form-submit' ) ); 
            }
            
            //to save the current mappings
            if ( !isset($mappingId) ) {
                $saveDetailsName = ts('Save this field mapping');
                $form->add('text','saveMappingName',ts('Name'));
                $form->add('text','saveMappingDesc',ts('Description'));
            } else {
                $form->assign('loadedMapping', $mappingId);
                
                $params = array('id' =>  $mappingId);
                $temp   = array ();
                $mappingDetails = CRM_Core_BAO_Mapping::retrieve($params, $temp);
                
                $form->assign('savedName',$mappingDetails->name);
                
                $form->add('hidden','mappingId',$mappingId);

                $form->addElement('checkbox','updateMapping',ts('Update this field mapping'), null);
                $saveDetailsName = ts('Save as a new field mapping');
                $form->add('text','saveMappingName',ts('Name'));
                $form->add('text','saveMappingDesc',ts('Description'));
            }
            
            $form->addElement('checkbox','saveMapping',$saveDetailsName, null, array('onclick' =>"showSaveDetails(this)"));
            $form->addFormRule( array( 'CRM_Contact_Form_Task_Export_Map', 'formRule' ) );
        } else  if ($mappingType == 'Search Builder') { 
            $form->addElement('submit', "addBlock", 'Also include contacts where', array( 'class' => 'form-submit' ) );
        }
        
        $defaults        = array( );
        $hasLocationTypes= array();
        $fields          = array();
        
        $fields['Individual'  ] =& CRM_Contact_BAO_Contact::exportableFields('Individual', false, true);
        $fields['Household'   ] =& CRM_Contact_BAO_Contact::exportableFields('Household', false, true);
        $fields['Organization'] =& CRM_Contact_BAO_Contact::exportableFields('Organization', false, true);
        
        // add component fields
        $compArray = array();

        if ( CRM_Core_Permission::access( 'Quest' ) ) {
            require_once 'CRM/Quest/BAO/Student.php';
            $fields['Student'] =& CRM_Quest_BAO_Student::exportableFields();
            $compArray['Student'] = 'Student';
        }

        if ( CRM_Core_Permission::access( 'CiviContribute' ) ) {
            require_once 'CRM/Contribute/BAO/Contribution.php';
            $fields['Contribution'] =& CRM_Contribute_BAO_Contribution::exportableFields();
            $compArray['Contribution'] = 'Contribution';
        }

        foreach ($fields as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $mapperFields[$key][$key1] = $value1['title'];
                $hasLocationTypes[$key][$key1]    = $value1['hasLocationType'];
            }
        }
        
        $mapperKeys      = array_keys( $mapperFields );
        
        $locationTypes  =& CRM_Core_PseudoConstant::locationType();
                
        $defaultLocationType =& CRM_Core_BAO_LocationType::getDefault();
            
        /* FIXME: dirty hack to make the default option show up first.  This
         * avoids a mozilla browser bug with defaults on dynamically constructed
         * selector widgets. */
        
        if ($defaultLocationType) {
            $defaultLocation = $locationTypes[$defaultLocationType->id];
            unset($locationTypes[$defaultLocationType->id]);
            $locationTypes = 
                array($defaultLocationType->id => $defaultLocation) + 
                $locationTypes;
        }
        
        $locationTypes = array (' ' => ts('Primary')) + $locationTypes;


        $sel1 = array('' => '-select-') + CRM_Core_SelectValues::contactType() + $compArray;
        
        foreach($sel1 as $key=>$sel ) {
            if($key) {
                $sel2[$key] = $mapperFields[$key];
            }
        }
        
        $sel3[''] = null;
        $phoneTypes = CRM_Core_SelectValues::phoneType();
        
        foreach($sel1 as $k=>$sel ) {
            if($k) {
                foreach ($locationTypes as $key => $value) {                        
                    $sel4[$k]['phone'][$key] =& $phoneTypes;
                }
            }
        }
        
        foreach($sel1 as $k=>$sel ) {
            if($k) {
                foreach ($mapperFields[$k]  as $key=>$value) {
                    
                    if ($hasLocationTypes[$k][$key]) {
                        
                        $sel3[$k][$key] = $locationTypes;
                    } else {
                        $sel3[$key] = null;
                    }
                }
            }
        }
        
        //special fields that have location, hack for primary location
        $specialFields = array ('street_address','supplemental_address_1', 'supplemental_address_2', 'city', 'postal_code', 'postal_code_suffix', 'geo_code_1', 'geo_code_2', 'state_province', 'country', 'phone', 'email', 'im' );
        
        if ( isset($mappingId) ) {
            $colCnt = 0;
            
            list ($mappingName, $mappingContactType, $mappingLocation, $mappingPhoneType, $mappingRelation, $mappingOperator, $mappingValue ) = CRM_Core_BAO_Mapping::getMappingFields($mappingId);
            
            $blkCnt = count($mappingName);
            if ( $blkCnt >= $blockCount ) {
                $blockCount  = $blkCnt + 1;
            }
            for ( $x = 1; $x < $blockCount; $x++ ) { 
                $colCnt = count($mappingName[$x]);
                if ( $colCnt >= $columnCount[$x] ) {
                    $columnCount[$x]  = $colCnt;
                }
            }
        }
        
        $defaults = array();
        $js = "<script type='text/javascript'>\n";
        $formName = "document.{$name}";
  
        //used to warn for mismatch column count or mismatch mapping 
        $warning = 0;
        for ( $x = 1; $x < $blockCount; $x++ ) {

            for ( $i = 0; $i < $columnCount[$x]; $i++ ) {
                 
                $sel =& $form->addElement('hierselect', "mapper[$x][$i]", ts('Mapper for Field %1', array(1 => $i)), null);
                $jsSet = false;
                
                if ( isset($mappingId) ) {
                    $locationId = isset($mappingLocation[$x][$i])? $mappingLocation[$x][$i] : 0;                
                    if ( isset($mappingName[$x][$i]) ) {
                        if (is_array($mapperFields[$mappingContactType[$x][$i]])) {
                            $phoneType = isset($mappingPhoneType[$x][$i]) ? $mappingPhoneType[$x][$i] : null;
                            
                            if ( !$locationId && in_array($mappingName[$x][$i], $specialFields) ) {
                                $locationId = " ";
                            }

                            $defaults["mapper[$x][$i]"] = array( $mappingContactType[$x][$i], $mappingName[$x][$i],
                                                             $locationId, $phoneType
                                                             );

                            if ( ! $mappingName[$x][$i] ) {
                                $js .= "{$formName}['mapper[$x][$i][1]'].style.display = 'none';\n";
                            }
                            if ( ! $locationId ) {
                                $js .= "{$formName}['mapper[$x][$i][2]'].style.display = 'none';\n";
                            }
                            if ( ! $phoneType ) {
                                $js .= "{$formName}['mapper[$x][$i][3]'].style.display = 'none';\n";
                            }
                            $jsSet = true;

                            if ($mappingOperator[$x][$i]) {
                                $defaults["operator[$x][$i]"] = $mappingOperator[$x][$i];
                            }
                            
                            if ($mappingValue[$x][$i]) {
                                $defaults["value[$x][$i]"] = $mappingValue[$x][$i];
                            }
                        }
                    } 
                } 
                
                $formValues = $form->exportValues( );
                
                if ( ! $jsSet ) {
                    if ( empty( $formValues ) ) {
                        for ( $k = 1; $k < 4; $k++ ) {
                            $js .= "{$formName}['mapper[$x][$i][$k]'].style.display = 'none';\n"; 
                        }
                    } else {
                        if ( !empty($formValues['mapper'][$x]) ) {
                            foreach ( $formValues['mapper'][$x] as $value) {
                                for ( $k = 1; $k < 4; $k++ ) {
                                    if (!$formValues['mapper'][$x][$i][$k]) {
                                        $js .= "{$formName}['mapper[$x][$i][$k]'].style.display = 'none';\n"; 
                                    } else {
                                        $js .= "{$formName}['mapper[$x][$i][$k]'].style.display = '';\n"; 
                                    }
                                }
                            }
                        } else {
                            for ( $k = 1; $k < 4; $k++ ) {
                                $js .= "{$formName}['mapper[$x][$i][$k]'].style.display = 'none';\n"; 
                            }
                        }
                    }
                }
                
                //$js .= "{$formName}['mapper[1][0][1]'].style.display = 'none';\n"; 
                $sel->setOptions(array($sel1,$sel2,$sel3, $sel4));
                
                if ($mappingType == 'Search Builder') {
                    $operatorArray = array ('=' => '=', '!=' => '!=', '>' => '>', '<' => '<', 
                                            '>=' => '>=', '<=' => '<=', 'IN' => 'IN',
                                            'NOT IN' => 'NOT IN', 'LIKE' => 'LIKE', 'NOT LIKE' => 'NOT LIKE');
                    
                    $form->add('select',"operator[$x][$i]",'', $operatorArray);
                    $form->add('text',"value[$x][$i]",'');
                }
                
            } //end of columnCnt for 
            if ($mappingType == 'Search Builder') {
                $title = ts('Another search field');
            } else {
                $title = ts('Select more fields');
            }
            $form->addElement('submit', "addMore[$x]", $title, array( 'class' => 'form-submit' ) );
        } //end of block for

        $js .= "</script>\n";

        $form->assign('initHideBoxes', $js);
        $form->assign('columnCount', $columnCount);
        $form->assign('blockCount', $blockCount);
        
        $form->setDefaults($defaults);
        
        $form->setDefaultAction( 'refresh' );
        
    }
    

    /**
     * Function returns associated array of elements, that will be passed for search
     *
     * @params array   $params associated array of submitted values
     * @params boolean $row    row no of the fields
     *
     * @return $returnFields  formatted associated array of elements
     *
     * @static
     * @public
     */
    static function &formattedFields( &$params , $row = false ) {
        $fields = array( );

        if ( empty( $params ) ) {
            return $fields;
        }
        
        $types = array( 'Individual', 'Organization', 'Household' );
        foreach ($params['mapper'] as $key => $value) {
            $contactType = null;
            foreach ($value as $k => $v) {
                if ( in_array( $v[0], $types ) ) {
                    if ( $contactType && $contactType != $v[0] ) {
                        CRM_Core_Error::fatal( ts( "Cannot have two clauses with different types: %1, %2",
                                                   array( 1 => $contactType, 2 => $v[0] ) ) );
                    }
                    $contactType = $v[0];
                }
                if ( $v[1] ) {
                    $fldName = $v[1];
                    if ( $v[2] ) {
                        $fldName .= "-{$v[2]}";
                    }
                    
                    if ( $v[3] ) {
                        $fldName .= "-{$v[3]}";
                    }
                    
                    $value = $params['value'   ][$key][$k];
                    if ( $fldName == 'groups' || $fldName == 'tags' ) {
                        $fldName = substr( $fldName, 0, -1 );
                        
                        $value = str_replace( '(', '', $value);
                        $value = str_replace( ')', '', $value);
                    
                        $v = explode( ',', $value );
                        $value = array( );
                        foreach ( $v as $i ) {
                            $value[$i] = 1;
                        }
                    }

                    if ( $row ) {
                        $fields[] = array( $fldName,
                                           $params['operator'][$key][$k],
                                           $value,
                                           $key,
                                           $k );
                    } else {
                        $fields[] = array( $fldName,
                                           $params['operator'][$key][$k],
                                           $value,
                                           $key,
                                           0 );

                    }
                }
            }
            if ( $contactType ) {
                $fields[] = array( 'contact_type',
                                   '=',
                                   $contactType,
                                   $key,
                                   0 );
            }
        }
        
        return $fields;
    }

    static function &returnProperties( &$params ) {
        $fields = array( 'contact_type'     => 1,
                         'contact_sub_type' => 1,
                         'sort_name'        => 1 );
        
        if ( empty( $params ) ) {
            return $fields;
        }
        
        $locationTypes  =& CRM_Core_PseudoConstant::locationType();
        foreach ($params['mapper'] as $key => $value) {
            foreach ($value as $k => $v) {
                if ( $v[1] ) {
                    if ( $v[1] == 'groups' || $v[1] == 'tags' ) {
                        continue;
                    }

                    if ( is_numeric($v[2]) ) {
                        if ( ! array_key_exists( 'location', $fields ) ) {
                            $fields['location'] = array( );
                        }
                        
                        // make sure that we have a location fields and a location type for this
                        $locationName = $locationTypes[$v[2]];
                        if ( ! array_key_exists( $locationName, $fields['location'] ) ) {
                            $fields['location'][$locationName] = array( );
                            $fields['location'][$locationName]['location_type'] = $v[2];
                        }
                        
                        if ( $v[1] == 'phone' || $v[1] == 'email' || $v[1] == 'im' ) {
                            if ( $v[3] ) { // phone type handling
                                $fields['location'][$locationName][$v[1] . "-" . $v[3]] = 1;
                            } else {
                                $fields['location'][$locationName][$v[1] . "-1"] = 1;
                            }
                        } else {
                            $fields['location'][$locationName][$v[1]] = 1;
                        }
                    } else {
                        $fields[$v[1]] = 1;
                    }
                }
            }
        }

        return $fields;

    }

    /**
     * save the mapping field info for search builder / export given the formvalues
     *
     * @param array $params       asscociated array of formvalues
     * @param int   $mappingId    mapping id
     *
     * @return null
     * @static
     * @access public
     */
    static function saveMappingFields(&$params, $mappingId ) 
    {
        //delete mapping fields records for exixting mapping
        require_once "CRM/Core/DAO/MappingField.php";
        $mappingFields =& new CRM_Core_DAO_MappingField();
        $mappingFields->mapping_id = $mappingId;
        $mappingFields->delete( );
        

        //save record in mapping field table
        foreach ($params['mapper'] as $key => $value) {
            $colCnt = 0;
            foreach ($value as $k => $v) {
                if ($v[1]) {
                    $saveMappingFields =& new CRM_Core_DAO_MappingField();
                    $saveMappingFields->mapping_id   = $mappingId;
                    $saveMappingFields->name         = $v[1];
                    $saveMappingFields->contact_type = $v[0];
                    
                    $locationId = $v[2];
                    $saveMappingFields->location_type_id = is_numeric($locationId) ? $locationId : null;
                    
                    $saveMappingFields->phone_type    = $v[3];
                    $saveMappingFields->operator      = $params['operator'][$key][$k];
                    $saveMappingFields->value         = $params['value'   ][$key][$k];
                    $saveMappingFields->grouping      = $key;
                    $saveMappingFields->column_number = $colCnt;

                    $saveMappingFields->save();
                    $colCnt ++;
                }
            }
        }
    }
    
}
?>