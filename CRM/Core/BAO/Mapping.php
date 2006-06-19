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
     * @params onject $form        form object
     * @params string $mappingType mapping type (Export/Import/Search Builder)
     * @params int    $mappingId   mapping id
     * @params mixed  $columnCount column count is int for and array for search builder
     * 
     * @return none
     * @access public
     * @static
     */
    static function buildMappingForm($form, $mappingType = 'Export', $mappingId = null, $columnNo, $blockCnt = 3 ) 
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
                //mapping is to be loaded from database
                $colCnt = 0;
                $mapping = $mappingId;
                
                list ($mappingName, $mappingContactType, $mappingLocation, $mappingPhoneType, $mappingRelation  ) = CRM_Core_BAO_Mapping::getMappingFields($mapping);
                $colCnt=count($mappingName);
                
                if ( $colCnt > $columnCount ) {
                    $columnCount  = $colCnt;
                }
                
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
        } 

        
        $defaults = array( );
        $hasLocationTypes= array();
        
        $contactId = array();
        $fields    = array();
        
        $fields['Individual'  ] =& CRM_Contact_BAO_Contact::exportableFields('Individual', false, true);
        $fields['Household'   ] =& CRM_Contact_BAO_Contact::exportableFields('Household', false, true);
        $fields['Organization'] =& CRM_Contact_BAO_Contact::exportableFields('Organization', false, true);
        
        // add component fields
        $compArray = array();
        require_once 'CRM/Quest/BAO/Student.php';
        require_once 'CRM/Contribute/BAO/Contribution.php';
        $config = CRM_Core_Config::singleton();
        $enabledComponent = $config->enableComponents;
        
        if (is_array( $enabledComponent )) {
            foreach( $enabledComponent as $component ) {
                if ($component == 'Quest') {
                    $fields['Student'] =& CRM_Quest_BAO_Student::exportableFields();
                    $compArray['Student'] = 'Student';
                } else if ( $component == 'CiviContribute') {
                    $fields['Contribution'] =& CRM_Contribute_BAO_Contribution::exportableFields();
                    $compArray['Contribution'] = 'Contribution';
                }
            }
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
        
        // print_r($sel3);
        
        $defaults = array();
        $js = "<script type='text/javascript'>\n";
        $formName = "document.{$name}";
        
        //used to warn for mismatch column count or mismatch mapping 
        $warning = 0;
        for ( $x = 1; $x < $blockCnt; $x++ ) {
            for ( $i = 0; $i < $columnCount[$x]; $i++ ) {
                 
                $sel =& $form->addElement('hierselect', "mapper[$x][$i]", ts('Mapper for Field %1', array(1 => $i)), null);
                $jsSet = false;
                
                if( isset($mappingId) && $mappingType == 'Export' ) {
                    $locationId = isset($mappingLocation[$i])? $mappingLocation[$i] : 0;                
                    if ( isset($mappingName[$i]) ) {
                        if (is_array($mapperFields[$mappingContactType[$i]])) {
                            $phoneType = isset($mappingPhoneType[$i]) ? $mappingPhoneType[$i] : null;
                            $defaults["mapper[$x][$i]"] = array( $mappingContactType[$i], $mappingName[$i],
                                                             $locationId, $phoneType
                                                             );
                        
                            if ( ! $mappingName[$i] ) {
                                $js .= "{$formName}['mapper[$x][$i][1]'].style.display = 'none';\n";
                            }
                            if ( ! $locationId ) {
                                $js .= "{$formName}['mapper[$x][$i][2]'].style.display = 'none';\n";
                            }
                            if ( ! $phoneType ) {
                                $js .= "{$formName}['mapper[$x][$i][3]'].style.display = 'none';\n";
                            }
                            $jsSet = true;
                        }
                    } 
                } 
                
                $formValues = $form->controller->exportValues( $name );
                
                if ( ! $jsSet ) {
                    if ( empty( $formValues ) ) {
                        for ( $k = 1; $k < 4; $k++ ) {
                            $js .= "{$formName}['mapper[$x][$i][$k]'].style.display = 'none';\n"; 
                        }
                    } else {

                        foreach ( $formValues['mapper'][$x] as $value) {
                            for ( $k = 1; $k < 4; $k++ ) {
                                if (!$formValues['mapper'][$x][$i][$k]) {
                                    $js .= "{$formName}['mapper[$x][$i][$k]'].style.display = 'none';\n"; 
                                } else {
                                    $js .= "{$formName}['mapper[$x][$i][$k]'].style.display = '';\n"; 
                                    
                                }
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
        
        $form->setDefaults($defaults);
        
        $form->setDefaultAction( 'refresh' );
        
    }
    

    /**
     * Function returns associated array of elements, that will be passed for search
     *
     * @params array $params associated array of submitted values
     *
     * @return $returnFields  formatted associated array of elements
     *
     * @static
     * @public
     */
    static function &formattedFields( &$params ) {
        $fields = array( );

        if ( empty( $params ) ) {
            return $fields;
        }
        
        foreach ($params['mapper'] as $key => $value) {
            foreach ($value as $k => $v) {
                if ($v[1]) {
                    $fldName = $v[1];
                    if ( $v[2] ) {
                        $fldName .= "-{$v[2]}";
                    }
                    
                    if ( $v[3] ) {
                        $fldName .= "-{$v[3]}";
                    }
                    
                    $fields[] = array( $fldName,
                                             $params['operator'][$key][$k],
                                             $params['value'   ][$key][$k],
                                             $key,
                                             0 );
                }
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
                    if ( $v[2] ) {
                        if ( ! array_key_exists( 'location', $fields ) ) {
                            $fields['location'] = array( );
                        }

                        // make sure that we have a location fields and a location type for this
                        $locationName = $locationTypes[$v[2]];
                        if ( ! array_key_exists( $locationName, $fields['location'] ) ) {
                            $fields['location'][$locationName] = array( );
                            $fields['location'][$locationName]['location_type'] = $v[2];
                        }

                        if ( $v[3] ) {
                            // DOES NOT WORK, fix
                        }
                        $fields['location'][$locationName][$v[1] . "-1"] = 1;
                    } else {
                        $fields[$v[1]] = 1;
                    }
                }
            }
        }
        return $fields;

    }

    /**
     * save the mapping info for search builder give the formvalues
     *
     * @param array $params asscociated array of formvalues
     *
     * @return null
     * @static
     * @access public
     */
    static function saveSearchBuilderMapping($params) 
    {
        //save record in mapping table
        $mappingParams = array('mapping_type' => 'Search Builder');
        $temp = array();
        $mapping = CRM_Core_BAO_Mapping::add($mappingParams, $temp) ;
        
        //save record in mapping field table
        require_once "CRM/Core/DAO/MappingField.php";
        
        foreach ($params['mapper'] as $key => $value) {
            foreach ($value as $k => $v) {
                if ($v[1]) {
                    $saveMappingFields =& new CRM_Core_DAO_MappingField();
                    $saveMappingFields->mapping_id   = $mapping->id;
                    $saveMappingFields->name         =  $v[1];
                    $saveMappingFields->contact_type =  $v[0];
                    
                    $locationId = $v[2];
                    $saveMappingFields->location_type_id = isset($locationId) ? $locationId : null;
                    
                    $saveMappingFields->phone_type = $v[3];
                    $saveMappingFields->operator   = $params['operator'][$key][$k];
                    $saveMappingFields->value      = $params['value'   ][$key][$k];
                    $saveMappingFields->grouping   = $key;
                    $saveMappingFields->save();
                }
            }
        }

        return $mapping;
    }
    
}
?>