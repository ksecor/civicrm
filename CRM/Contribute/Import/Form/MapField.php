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

require_once 'CRM/Core/DAO/Mapping.php';
require_once 'CRM/Core/DAO/MappingField.php';

require_once 'CRM/Contribute/Import/Parser/Contribution.php';

/**
 * This class gets the name of the file to upload
 */
class CRM_Contribute_Import_Form_MapField extends CRM_Core_Form {

    /**
     * cache of preview data values
     *
     * @var array
     * @access protected
     */
    protected $_dataValues;

    /**
     * mapper fields
     *
     * @var array
     * @access protected
     */
    protected $_mapperFields;

    /**
     * loaded mapping ID
     *
     * @var int
     * @access protected
     */
    protected $_loadedMappingId;

    /**
     * number of columns in import file
     *
     * @var int
     * @access protected
     */
    protected $_columnCount;


    /**
     * column headers, if we have them
     *
     * @var array
     * @access protected
     */
    protected $_columnHeaders;

    /**
     * an array of booleans to keep track of whether a field has been used in
     * form building already.
     *
     * @var array
     * @access protected
     */
    protected $_fieldUsed;
    

    
    /**
     * Attempt to resolve the header with our mapper fields
     *
     * @param header
     * @param mapperFields
     * @return string
     * @access public
     */
    public function defaultFromHeader($header, &$patterns) {
        foreach ($patterns as $key => $re) {
            /* Skip the first (empty) key/pattern */
            if (empty($re)) continue;
            
            /* if we've already used this field, move on */
//             if ($this->_fieldUsed[$key])
//                 continue;
            /* Scan through the headerPatterns defined in the schema for a
             * match */
            if (preg_match($re, $header)) {
                $this->_fieldUsed[$key] = true;
                return $key;
            }
        }
        return '';
    }

    /**
     * Guess at the field names given the data and patterns from the schema
     *
     * @param patterns
     * @param index
     * @return string
     * @access public
     */
    public function defaultFromData(&$patterns, $index) {
        $best = '';
        $bestHits = 0;
        $n = count($this->_dataValues);
        
        foreach ($patterns as $key => $re) {
            if (empty($re)) continue;

//             if ($this->_fieldUsed[$key])
//                 continue;

            /* Take a vote over the preview data set */
            $hits = 0;
            for ($i = 0; $i < $n; $i++) {
                if (preg_match($re, $this->_dataValues[$i][$index])) {
                    $hits++;
                }
            }

            if ($hits > $bestHits) {
                $bestHits = $hits;
                $best = $key;
            }
        }
    
        if ($best != '') {
            $this->_fieldUsed[$best] = true;
        }
        return $best;
    }

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $this->_mapperFields = $this->get( 'fields' );
        
        $this->_columnCount = $this->get( 'columnCount' );
        $this->assign( 'columnCount' , $this->_columnCount );
        $this->_dataValues = $this->get( 'dataValues' );
        $this->assign( 'dataValues'  , $this->_dataValues );
        
        $skipColumnHeader = $this->controller->exportValue( 'UploadFile', 'skipColumnHeader' );

        if ( $skipColumnHeader ) {
            $this->assign( 'skipColumnHeader' , $skipColumnHeader );
            $this->assign( 'rowDisplayCount', 3 );
            /* if we had a column header to skip, stash it for later */
            $this->_columnHeaders = $this->_dataValues[0];
        } else {
            $this->assign( 'rowDisplayCount', 2 );
        }
        
    }

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        //get the saved mapping details
        $mappingDAO =&  new CRM_Core_DAO_Mapping();
        $mappingDAO->domain_id = CRM_Core_Config::domainID( );
        $mappingDAO->mapping_type = 'Import Contributions';
        $mappingDAO->find();
        
        $mappingArray = array();
        while ($mappingDAO->fetch()) {
            $mappingArray[$mappingDAO->id] = $mappingDAO->name;
        }

        $this->assign('savedMapping',$mappingArray);
        $this->add('select','savedMapping', ts('Mapping Option'), array('' => ts('- select -'))+$mappingArray);
        $this->addElement('submit','loadMapping',ts('Load Mapping'), null, array('onclick'=>'checkSelect()'));

        //to save the current mappings
        if ( !$this->get('savedMapping') ) {
            $saveDetailsName = ts('Save this field mapping');
            $this->add('text','saveMappingName',ts('Name'));
            $this->add('text','saveMappingDesc',ts('Description'));
        } else {
            $savedMapping = $this->get('savedMapping');
            //mapping is to be loaded from database
            $mapping =& new CRM_Core_DAO_MappingField();
            $mapping->mapping_id = $savedMapping;
            $mapping->orderBy('column_number');
            $mapping->find();

            $mappingName = array();
            while($mapping->fetch()) {
                $mappingName[$mapping->column_number] = $mapping->name;
            }

            $this->assign('loadedMapping', $savedMapping);

            $getMappingName =&  new CRM_Core_DAO_Mapping();
            $getMappingName->id = $savedMapping;
            $getMappingName->mapping_type = 'Import Contributions';
            $getMappingName->find();
            while($getMappingName->fetch()) {
                $mapperName = $getMappingName->name;
            }

            $this->assign('savedName', $mapperName);

            $this->add('hidden','mappingId',$savedMapping);

            $this->addElement('checkbox','updateMapping',ts('Update this field mapping'), null);
            $saveDetailsName = ts('Save as a new field mapping');
            $this->add('text','saveMappingName',ts('Name'));
            $this->add('text','saveMappingDesc',ts('Description'));
        }
        
        $this->addElement('checkbox','saveMapping',$saveDetailsName, null, array('onclick' =>"showSaveDetails(this)"));
        
        $this->addFormRule( array( 'CRM_Contribute_Import_Form_MapField', 'formRule' ) );

        //-------- end of saved mapping stuff ---------

        $defaults = array( );
        $mapperKeys      = array_keys( $this->_mapperFields );
        $hasHeaders      = !empty($this->_columnHeaders);
        $headerPatterns  = $this->get( 'headerPatterns' );
        $dataPatterns    = $this->get( 'dataPatterns' );

        /* Initialize all field usages to false */
        foreach ($mapperKeys as $key) {
            $this->_fieldUsed[$key] = false;
        }

        $sel1 = $this->_mapperFields;

        $sel2[''] = null;

        $js = "<script type='text/javascript'>\n";
        $formName = 'document.forms.' . $this->_name;
        
        //used to warn for mismatch column count or mismatch mapping      
        $warning = 0;

        
        for ( $i = 0; $i < $this->_columnCount; $i++ ) {
            $sel =& $this->addElement('hierselect', "mapper[$i]", ts('Mapper for Field %1', array(1 => $i)), null);
            $jsSet = false;
            if( $this->get('savedMapping') ) {                                              
                if ( isset($mappingName[$i]) ) {
                    if ( $mappingName[$i] != ts('- do not import -')) {                                
                        
                        $mappingHeader = array_keys($this->_mapperFields, $mappingName[$i]);
                        
                        if ( ! $locationId ) {
                            $js .= "{$formName}['mapper[$i][1]'].style.display = 'none';\n";
                        }

                        if ( ! $phoneType ) {
                            $js .= "{$formName}['mapper[$i][2]'].style.display = 'none';\n";
                        }
                        
                        $js .= "{$formName}['mapper[$i][3]'].style.display = 'none';\n";
                        
                        $defaults["mapper[$i]"] = array( $mappingHeader[0], $locationId, $phoneType );
                        $jsSet = true;
                    } else {
                        $defaults["mapper[$i]"] = array();
                    }                          
                    if ( ! $jsSet ) {
                        for ( $k = 1; $k < 4; $k++ ) {
                            $js .= "{$formName}['mapper[$i][$k]'].style.display = 'none';\n"; 
                        }
                    }
                } else {
                    // this load section to help mapping if we ran out of saved columns when doing Load Mapping
                    $js .= "swapOptions($formName, 'mapper[$i]', 0, 3, 'hs_mapper_".$i."_');\n";
                    
                    if ($hasHeaders) {
                        $defaults["mapper[$i]"] = array( $this->defaultFromHeader($this->_columnHeaders[$i],$headerPatterns) );
                    } else {
                        $defaults["mapper[$i]"] = array( $this->defaultFromData($dataPatterns, $i) );
                    }                    
                } //end of load mapping
            } else {
                $js .= "swapOptions($formName, 'mapper[$i]', 0, 3, 'hs_mapper_".$i."_');\n";
                if ($hasHeaders) {
                    // Infer the default from the skipped headers if we have them
                    $defaults["mapper[$i]"] = array(
                                                           $this->defaultFromHeader($this->_columnHeaders[$i], 
                                                                                    $headerPatterns),
                                                           //                     $defaultLocationType->id
                                                           0
                                                           );
                    
                } else {
                    // Otherwise guess the default from the form of the data
                    $defaults["mapper[$i]"] = array(
                                                           $this->defaultFromData($dataPatterns, $i),
                                                           //                     $defaultLocationType->id
                                                           0
                                                           );
                }
            }
            
            $sel->setOptions(array($sel1, $sel2, $sel3, $sel4));
        }
        $js .= "</script>\n";
        $this->assign('initHideBoxes', $js);

        //set warning if mismatch in more than 
        if ( ($this->_columnCount != count($mappingName)) ) {
            $warning++;            
        }

        if ( $warning != 0 && $this->get('savedMapping') ) {
            $session =& CRM_Core_Session::singleton( );
            $session->setStatus( ts( 'The data columns in this import file appear to be different from the saved mapping. Please verify that you have selected the correct saved mapping before continuing.' ) );
        } else {
            $session =& CRM_Core_Session::singleton( );
            $session->setStatus( null ); 
        }

        $this->setDefaults( $defaults );       

        $this->addButtons( array(
                                 array ( 'type'      => 'back',
                                         'name'      => ts('<< Previous') ),
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Continue >>'),
                                         'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                         'isDefault' => true   ),
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

        if (!array_key_exists('savedMapping', $fields)) {
            $importKeys = array();
            foreach ($fields['mapper'] as $mapperPart) {
                $importKeys[] = $mapperPart[0];
            }
            // FIXME: should use the schema titles, not redeclare them
            $requiredFields = array(
                'contact_id'        => ts('Contact ID'),
                'total_amount'      => ts('Total Amount'),
                'receive_date'      => ts('Receive Date'),
                'contribution_type' => ts('Contribution Type')
            );
            // FIXME: appears with packages/HTML @r3847, doesn't appear with updated QuickForm
            foreach ($requiredFields as $field => $title) {
                if (!in_array($field, $importKeys)) {
                    $errors['_qf_default'] .= ts('Missing required field: %1', array(1 => $title)) . '<br />';
                }
            }
        }


        if ( CRM_Utils_Array::value( 'saveMapping', $fields ) ) {
            $nameField = CRM_Utils_Array::value( 'saveMappingName', $fields );
            if ( empty( $nameField ) ) {
                $errors['saveMappingName'] = ts('Name is required to save Import Mapping');
            } else {
                $mappingName =& new CRM_Core_DAO_Mapping();
                $mappingName->name = $nameField;
                $mappingName->domain_id = CRM_Core_Config::domainID( );
                $mappingName->mapping_type = 'Import Contributions';
                if ( $mappingName->find( true ) ) {
                    $errors['saveMappingName'] = ts('Duplicate Import Mapping Name');
                }
            }
        }

        if ( !empty($errors) ) {
            if (!empty($errors['saveMappingName'])) {
                $_flag = 1;
                require_once 'CRM/Core/Page.php';
                $assignError =& new CRM_Core_Page(); 
                $assignError->assign('mappingDetailsError', $_flag);
            }
            return $errors;
        }

        return true;
    }

    /**
     * Process the mapped fields and map it into the uploaded file
     * preview the file and extract some summary statistics
     *
     * @return void
     * @access public
     */
    public function postProcess()
    {
        $params = $this->controller->exportValues( 'MapField' );

        //reload the mapfield if load mapping is pressed
        if( !empty($params['savedMapping']) ) {            
            $this->set('savedMapping', $params['savedMapping']);
            $this->controller->resetPage( $this->_name );
            return;
        }
        
        
        $fileName         = $this->controller->exportValue( 'UploadFile', 'uploadFile' );
        $skipColumnHeader = $this->controller->exportValue( 'UploadFile', 'skipColumnHeader' );

        $seperator = ',';

        $mapperKeys = array( );
        $mapper     = array( );
        $mapperKeys = $this->controller->exportValue( $this->_name, 'mapper' );
        $mapperKeysMain     = array();
        
        for ( $i = 0; $i < $this->_columnCount; $i++ ) {
            $mapper[$i]     = $this->_mapperFields[$mapperKeys[$i][0]];
            $mapperKeysMain[$i] = $mapperKeys[$i][0];
        }

        $this->set( 'mapper'    , $mapper     );
               
        // store mapping Id to display it in the preview page 
        $this->set('loadMappingId', $params['mappingId']);
        
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

                $updateMappingFields =& new CRM_Core_DAO_MappingField();
                $updateMappingFields->id = $mappingFieldsId[$i];
                $updateMappingFields->mapping_id = $params['mappingId'];
                $updateMappingFields->column_number = $i;

                list($id, $first, $second) = explode('_', $mapperKeys[$i][0]);
                $updateMappingFields->name = $mapper[$i];
                $updateMappingFields->save();                
            }
        }
        
        //Saving Mapping Details and Records
        if ( CRM_Utils_Array::value('saveMapping', $params)) {
            $saveMapping =& new CRM_Core_DAO_Mapping();
            $saveMapping->domain_id = CRM_Core_Config::domainID( );
            $saveMapping->name = $params['saveMappingName'];
            $saveMapping->description = $params['saveMappingDesc'];
            $saveMapping->mapping_type = 'Import Contributions';
            $saveMapping->save();
            

            for ( $i = 0; $i < $this->_columnCount; $i++ ) {                  
                
                $saveMappingFields =& new CRM_Core_DAO_MappingField();
                $saveMappingFields->mapping_id = $saveMapping->id;
                $saveMappingFields->column_number = $i;                             
                
                list($id, $first, $second) = explode('_', $mapperKeys[$i][0]);
                $saveMappingFields->name = $mapper[$i];
                $saveMappingFields->save();
            }
        }

        $parser =& new CRM_Contribute_Import_Parser_Contribution( $mapperKeysMain );
        $parser->run( $fileName, $seperator, $mapper, $skipColumnHeader,
                      CRM_Contribute_Import_Parser::MODE_PREVIEW );
        
        // add all the necessary variables to the form
        $parser->set( $this );        
    }

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('Match Fields');
    }

    
}

?>
