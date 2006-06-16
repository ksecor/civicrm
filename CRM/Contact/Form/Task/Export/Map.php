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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/DAO/MappingField.php';

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
    protected $_mappingId;

   
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
        
        $this->_mappingId =  $this->get('savedMapping');
    }
    
    public function buildQuickForm( ) {

        require_once "CRM/Core/BAO/Mapping.php";
        CRM_Core_BAO_Mapping::buildMappingForm($this, 'Export', $this->_mappingId, $this->_columnCount, $blockCnt = 2);

        $this->addButtons( array(
                                 array ( 'type'      => 'back',
                                         'name'      => ts('<< Previous') ),
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Export >>'),
                                         'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' ),
                                 array ( 'type'      => 'done',
                                         'name'      => ts('Done') ),
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
        //updated for CRM-965 
        if ( CRM_Utils_Array::value( 'saveMapping', $fields ) && ! $fields['_qf_Map_done']) {
            $nameField = CRM_Utils_Array::value( 'saveMappingName', $fields );
            if ( empty( $nameField ) ) {
                $errors['saveMappingName'] = "Name is required to save Export Mapping";
            } else {
                //check for Duplicate mappingName
               if(CRM_Core_BAO_Mapping::checkMapping($nameField,'Export')){
                     $errors['saveMappingName'] = ts('Duplicate Export Mapping Name');
                }
            }
        }

        if ( !empty($errors) ) {
            require_once 'CRM/Core/Page.php';
            $_flag = 1;
            require_once 'CRM/Core/Page.php';
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
        
        //To Refresh the Page 
        //updated for CRM-965
        
        //get the button name
        $buttonName = $this->controller->getButtonName('done');
        $buttonName1 = $this->controller->getButtonName('next');
        if ( $buttonName == '_qf_Map_done') {
            $this->set('columnCount',null);
            if (! $this->controller->exportValue( $this->_name, 'loadMapping' ) )  {
                CRM_Utils_Array::value( 'savedMapping', $params );
                $this->set('savedMapping', null);
            }
            $this->controller->resetPage( $this->_name );
            return CRM_Utils_System::redirect( CRM_Utils_System::url('civicrm/contact/search/basic', 'force=1') );
        }


        if ( $this->controller->exportValue( $this->_name, 'addMore' ) )  {
            $this->set( 'columnCount', $this->_columnCount );
            return;
        }

        //reload the mapfield if load mapping is pressed
        //if ( CRM_Utils_Array::value( 'savedMapping', $params ) ) {
        if ( $this->controller->exportValue( $this->_name, 'loadMapping' ) )  {
            CRM_Utils_Array::value( 'savedMapping', $params );
            $this->set('savedMapping', $params['savedMapping']);
            $this->controller->resetPage( $this->_name );
            return;
        }

        
        //$mapperKeys = $this->controller->exportValue( $this->_name,
        //'mapper1' );  
        $mapperKeys = $params['mapper'][1];  
       
        $checkEmpty = 0;
        foreach($mapperKeys as $value) {
            if ($value[0]) {
                $checkEmpty++;
            }
        }

        if (!$checkEmpty ) {
            $this->set('savedMapping', null);
            require_once 'CRM/Utils/System.php';            
            CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/contact/search/basic', '_qf_Map_display=true' ) );
        }
        

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
                    $updateMappingFields->name = $mapperKeys[$i][1];
                    $updateMappingFields->contact_type =  $mapperKeys[$i][0];
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
            $mappingParams = array('name'         => $params['saveMappingName'],
                                   'description'  => $params['saveMappingDesc'],
                                   'mapping_type' => 'Export');
            
            $temp = array();
            //when Export button is clicked then save the details 
            //changed for CRM-965
            if( $buttonName1 == '_qf_Map_next' ){
                $saveMapping = CRM_Core_BAO_Mapping::add($mappingParams, $temp) ;
            }

            for ( $i = 0; $i < $this->_columnCount; $i++ ) {
                if ( !empty($mapperKeys[$i][0]) ) {
                    $saveMappingFields =& new CRM_Core_DAO_MappingField();
                    $saveMappingFields->mapping_id = $saveMapping->id;
                    $saveMappingFields->name =  $mapperKeys[$i][1];
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
                                                $this->get( 'queryParams' ),
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
        return ts('Select Fields to Export');
    }

}

?>
