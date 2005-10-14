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

require_once 'CRM/Core/Form.php';

/**
 * This class gets the name of the file to upload
 */
class CRM_Contact_Form_Task_Export_Map extends CRM_Core_Form {
    
    /**
     * mapper fields
     *
     * @var array
     * @access protected
     */
    protected $_mapperFields;

    /**
     * number of columns in import file
     *
     * @var int
     * @access protected
     */
    protected $_columnCount;

    /**
     * loaded mapping ID
     *
     * @var int
     * @access protected
     */
    protected $_loadedMappingId;

   
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function preProcess() {

        $this->_columnCount = $this->get('columnCount');
        if (! $this->_columnCount ) {
            $this->_columnCount = 10;
        } else {
            $this->_columnCount = $this->_columnCount + 10;
        }
        
        $this->_loadedMappingId =  $this->get('savedMapping');
    }
    
    public function buildQuickForm( ) {

        //get the saved mapping details
        Require_once 'CRM/Core/DAO/Mapping.php';
        require_once 'CRM/Contact/BAO/Contact.php';
        require_once 'CRM/Core/BAO/LocationType.php';
        $mappingDAO =&  new CRM_Core_DAO_Mapping();
        $mappingDAO->domain_id = CRM_Core_Config::domainID( );
        $mappingDAO->mapping_type = 'Export';
        $mappingDAO->find();
        
        $mappingArray = array();
        while ($mappingDAO->fetch()) {
            $mappingArray[$mappingDAO->id] = $mappingDAO->name;
        }

        if ( !empty($mappingArray) ) {
            $this->assign('savedMapping',$mappingArray);
            $this->add('select','savedMapping', ts('Mapping Option'), array('' => '-select-')+$mappingArray);
            $this->addElement( 'submit', 'loadMapping', ts('Load Mapping'), array( 'class' => 'form-submit' ) ); 
        }

        //to save the current mappings
        if ( !isset($this->_loadedMappingId) ) {
            $saveDetailsName = ts('Save this field mapping');
            $this->add('text','saveMappingName',ts('Name'));
            $this->add('text','saveMappingDesc',ts('Description'));
        } else {
            //mapping is to be loaded from database
            $mapping =& new CRM_Core_DAO_MappingField();
            $mapping->mapping_id = $this->_loadedMappingId;
            $mapping->orderBy('column_number');
            $mapping->find();

            $mappingName = array();
            $mappingLocation = array();
            $mappingContactType = array();
            $mappingPhoneType = array();
            while($mapping->fetch()) {
                $mappingName[] = $mapping->name;
                $mappingContactType[] = $mapping->contact_type;                
                if ( !empty($mapping->location_type_id ) ) {
                    $mappingLocation[$mapping->column_number] = $mapping->location_type_id;
                }
                if ( !empty( $mapping->phone_type ) ) {
                    $mappingPhoneType[$mapping->column_number] = $mapping->phone_type;
                }
            }

            $this->assign('loadedMapping', $this->_loadedMappingId);

            $getMappingName =&  new CRM_Core_DAO_Mapping();
            $getMappingName->id = $savedMapping;
            $getMappingName->mapping_type = 'Export';
            $getMappingName->find();
            while($getMappingName->fetch()) {
                $mapperName = $getMappingName->name;
            }

            $this->assign('savedName', $mapperName);

            $this->add('hidden','mappingId',$this->_loadedMappingId);

            $this->addElement('checkbox','updateMapping',ts('Update this field mapping'), null);
            $saveDetailsName = ts('Save as a new field mapping');
            $this->add('text','saveMappingName',ts('Name'));
            $this->add('text','saveMappingDesc',ts('Description'));}
        
        
        $this->addElement('checkbox','saveMapping',$saveDetailsName, null, array('onclick' =>"showSaveDetails(this)"));
        
        $this->addFormRule( array( 'CRM_Contact_Form_Task_Export_Map', 'formRule' ) );

        //-------- end of saved mapping stuff ---------
        
        $this->_defaults = array( );
        $hasLocationTypes= array();
        
        $fields = array();
        $fields['Individual']   =& CRM_Contact_BAO_Contact::exportableFields('Individual');
        $fields['Household']    =& CRM_Contact_BAO_Contact::exportableFields('Household');
        $fields['Organization'] =& CRM_Contact_BAO_Contact::exportableFields('Organization');

        foreach ($fields as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $this->_mapperFields[$key][$key1] = $value1['title'];
                $hasLocationTypes[$key][$key1]    = $value1['hasLocationType'];
            }
        }
        
        $mapperKeys      = array_keys( $this->_mapperFields );

        $this->_location_types  =& CRM_Core_PseudoConstant::locationType();
        
        $defaultLocationType =& CRM_Core_BAO_LocationType::getDefault();
        
        /* FIXME: dirty hack to make the default option show up first.  This
         * avoids a mozilla browser bug with defaults on dynamically constructed
         * selector widgets. */
        
        if ($defaultLocationType) {
            $defaultLocation = $this->_location_types[$defaultLocationType->id];
            unset($this->_location_types[$defaultLocationType->id]);
            $this->_location_types = 
                array($defaultLocationType->id => $defaultLocation) + 
                $this->_location_types;
        }
        
        $sel1 = array('' => '-do not export-') + CRM_Core_SelectValues::contactType();
        
        foreach($sel1 as $key=>$sel ) {
            if($key) {
                $sel2[$key] = $this->_mapperFields[$key];
            }
        }
       
        $sel3[''] = null;
        $phoneTypes = CRM_Core_SelectValues::phoneType();

        foreach($sel1 as $k=>$sel ) {
            if($k) {
                foreach ($this->_location_types as $key => $value) {                        
                    $sel4[$k]['phone'][$key] =& $phoneTypes;
                }
            }
        }
        
        foreach($sel1 as $k=>$sel ) {
            if($k) {
                foreach ($this->_mapperFields[$k]  as $key=>$value) {
                   
                    if ($hasLocationTypes[$k][$key]) {
                       
                        $sel3[$k][$key] = $this->_location_types;
                    } else {
                        $sel3[$key] = null;
                    }
                }
            }
        }

        // print_r($sel3);

        $this->_defaults = array();
        $js = "<script type='text/javascript'>\n";
        $formName = "document.{$this->_name}";
        
        //used to warn for mismatch column count or mismatch mapping 
        $warning = 0;
        for ( $i = 0; $i < $this->_columnCount; $i++ ) {
            $sel =& $this->addElement('hierselect', "mapper[$i]", ts('Mapper for Field %1', array(1 => $i)), null);
            $jsSet = false;
             if( isset($this->_loadedMappingId) ) {
                $locationId = isset($mappingLocation[$i])? $mappingLocation[$i] : 0;                
                if ( isset($mappingName[$i]) ) {
                    $phoneType = isset($mappingPhoneType[$i]) ? $mappingPhoneType[$i] : null;
                    $mappingHeader = array_keys($this->_mapperFields[$mappingContactType[$i]], $mappingName[$i]);
                    $defaults["mapper[$i]"] = array( $mappingContactType[$i], $mappingHeader[0],
                                                     $locationId, $phoneType
                                                     );
                    if ( ! $mappingHeader[0] ) {
                        $js .= "{$formName}['mapper[$i][1]'].style.display = 'none';\n";
                    }
                    if ( ! $locationId ) {
                        $js .= "{$formName}['mapper[$i][2]'].style.display = 'none';\n";
                    }
                    if ( ! $phoneType ) {
                        $js .= "{$formName}['mapper[$i][3]'].style.display = 'none';\n";
                    }
                    $jsSet = true;
                }
             }
             $formValues = $this->controller->exportValues( $this->_name );
             if ( ! $jsSet && empty( $formValues ) ) {
                 for ( $k = 1; $k < 4; $k++ ) {
                     $js .= "{$formName}['mapper[$i][$k]'].style.display = 'none';\n"; 
                 }
             }
             $sel->setOptions(array($sel1,$sel2,$sel3, $sel4));

            //set the defaults on load mapping
                        
        }
        $js .= "</script>\n";
        $this->assign('initHideBoxes', $js);
        $this->assign('columnCount', $this->_columnCount);

        $this->setDefaults($defaults);

        $this->addElement( 'submit', $this->getButtonName('refresh'), ts('Select more fields'), array( 'class' => 'form-submit' ) );
        $this->setDefaultAction( 'refresh' );

        $this->addButtons( array(
                                 array ( 'type'      => 'back',
                                         'name'      => ts('<< Previous') ),
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Continue >>'),
                                         'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }


    /**
     * global validation rules for the form
     *
     * @param array $fields posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @static
     * @access public
     */

    static function formRule( &$fields ) {
        $errors  = array( );

        if ( CRM_Utils_Array::value( 'saveMapping', $fields ) ) {
            $nameField = CRM_Utils_Array::value( 'saveMappingName', $fields );
            if ( empty( $nameField ) ) {
                $errors['saveMappingName'] = "Name is required to save Export Mapping";
            } else {
                $importMappingName =& new CRM_Core_DAO_Mapping();
                $importMappingName->name = $nameField;
                $importMappingName->domain_id = CRM_Core_Config::domainID( );
                if ( $importMappingName->find( true ) ) {
                    $errors['saveMappingName'] = "Duplicate Export Mapping Name ";
                }
            }
        }

        if ( !empty($errors) ) {
            $_flag = 1;
            $assignError =& new CRM_Core_Page(); 
            $assignError->assign('mappingDetailsError', $_flag);
            return $errors;
        } else {
            return true;
        }
    }    


    /**
     * Process the uploaded file
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        $params = $this->controller->exportValues( $this->_name );

        if ( $this->controller->exportValue( $this->_name, '_qf_Map_refresh' ) )  {
            $this->set( 'columnCount', $this->_columnCount );
            return;
        }

        //reload the mapfield if load mapping is pressed
        if ( CRM_Utils_Array::value( 'savedMapping', $params ) ) {
            $this->set('savedMapping', $params['savedMapping']);
            $this->controller->resetPage( $this->_name );
            return;
        }

        $mapperKeys = $this->controller->exportValue( $this->_name, 'mapper' );        

        //Updating Mapping Records
        if ( CRM_Utils_Array::value('updateMapping', $params)) {
            
            $mappingFields =& new CRM_Core_DAO_MappingField();
            $mappingFields->mapping_id = $params['mappingId'];
            $mappingFields->find( );
            
            $mappingFieldsId = array();                
            while($mappingFields->fetch()) {
                if ( $mappingFields->id ) {
                    $mappingFieldsId[$mappingFields->column_number] = $mappingFields->id;
                }
            }

            for ( $i = 0; $i < $this->_columnCount; $i++ ) {
                if ( !empty($mapperKeys[$i][0]) ) {
                    $updateMappingFields =& new CRM_Core_DAO_MappingField();
                    $updateMappingFields->id = $mappingFieldsId[$i];
                    $updateMappingFields->mapping_id = $params['mappingId'];
                    $updateMappingFields->name = $this->_mapperFields[$mapperKeys[$i][0]][$mapperKeys[$i][1]];
                    $updateMappingFields->column_number = $i;
                    
                    $locationId = $mapperKeys[$i][2];
                    $updateMappingFields->location_type_id = isset($locationId) ? $locationId : null;
                    
                    $relation = $mapperKeys[$i][1];
                    list($id, $first, $second) = explode('_', $relation);
                    if ( ($first == 'a' && $second == 'b') || ($first == 'b' && $second == 'a') ) {
                        $updateMappingFields->relationship_type_id = $id;
                    } else {
                        $updateMappingFields->relationship_type_id = null;
                    }
                    
                    $phoneType = $mapperKeys[$i][3];
                    $updateMappingFields->phone_type = isset($phoneType) ? $phoneType : null;
                    
                    $updateMappingFields->save();                
                }
            }
        }
        
        //Saving Mapping Details and Records
        if ( CRM_Utils_Array::value('saveMapping', $params)) {
            $saveMapping =& new CRM_Core_DAO_Mapping();
            $saveMapping->domain_id = CRM_Core_Config::domainID( );
            $saveMapping->name = $params['saveMappingName'];
            $saveMapping->description = $params['saveMappingDesc'];
            $saveMapping->mapping_type = 'Export';
            $saveMapping->save();
            
            for ( $i = 0; $i < $this->_columnCount; $i++ ) {
                if ( !empty($mapperKeys[$i][0]) ) {
                    $saveMappingFields =& new CRM_Core_DAO_MappingField();
                    $saveMappingFields->mapping_id = $saveMapping->id;
                    $saveMappingFields->name =  $this->_mapperFields[$mapperKeys[$i][0]][$mapperKeys[$i][1]];
                    $saveMappingFields->contact_type =  $mapperKeys[$i][0];
                    $saveMappingFields->column_number = $i;
                    
                    $locationId = $mapperKeys[$i][2];
                    $saveMappingFields->location_type_id = isset($locationId) ? $locationId : null;
                    
                    $saveMappingFields->phone_type = $mapperKeys[$i][3];
                    
                    $relation = $mapperKeys[$i][1];
                    list($id, $first, $second) = explode('_', $relation);
                    if ( ($first == 'a' && $second == 'b') || ($first == 'b' && $second == 'a') ) {
                        $saveMappingFields->relationship_type_id = $id;
                    } else {
                        $saveMappingFields->relationship_type_id = null;
                    }
                    
                    $saveMappingFields->save();
                }
            }
        }
     
        //get the csv file
        require_once 'CRM/Contact/BAO/Export.php';
        CRM_Contact_BAO_Export::exportContacts( $this->get( 'selectAll' ),
                                                $this->get( 'contactIds' ),
                                                $this->get( 'formValues' ),
                                                $this->get( CRM_Utils_Sort::SORT_ORDER ),
                                                $mapperKeys);
    }
    
    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) {
        return ts('Map Fields');
    }

}

?>
