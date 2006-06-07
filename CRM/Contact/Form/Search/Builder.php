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
 * This class if for search builder processing
 */
class CRM_Contact_Form_Search_Builder extends CRM_Core_Form 
{
    
    /**
     * mapper fields
     *
     * @var array
     * @access protected
     */
    protected $_mapperFields;

    /**
     * number of columns in where
     *
     * @var int
     * @access protected
     */
    protected $_columnCount1;

    /**
     * number of columns in also where
     *
     * @var int
     * @access protected
     */
    protected $_columnCount2;

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function preProcess() {
        $this->_columnCount1 = $this->get('columnCount1');
        $this->_columnCount2 = $this->get('columnCount2');
        if (! $this->_columnCount1 ) {
            $this->_columnCount1 = 1;
        } else {
            $this->_columnCount1 = $this->_columnCount1 + 1;
        }
        
        if (! $this->_columnCount2 ) {
            $this->_columnCount2 = 1;
        } else {
            $this->_columnCount2 = $this->_columnCount2 + 1;
        }

        $this->_loadedMappingId =  $this->get('savedMapping');
    }
    
    public function buildQuickForm( ) {

        require_once 'CRM/Contact/BAO/Contact.php';
        require_once 'CRM/Core/BAO/LocationType.php';
        require_once 'CRM/Contact/DAO/Group.php';
        
        //add name
        $this->add("text", "name", ts('Name'), CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Group', 'title' ));
        $this->addRule( 'name', ts('Please enter a valid name.'), 'required' );
        
        $this->_defaults = array( );
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
        
        $sel1 = array('' => '-select-') + CRM_Core_SelectValues::contactType() + $compArray;
        
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
        
        
        $this->_defaults = array();
        $js = "<script type='text/javascript'>\n";
        $formName = "document.{$this->_name}";
        
    
        //used to warn for mismatch column count or mismatch mapping 
        $warning = 0;
        for ( $x = 1; $x < 3; $x++ ) {
            if ( $x == 1 ) {
                $cnt = $this->_columnCount1;
            } else {
                $cnt = $this->_columnCount2;
            }
            
            for ( $i = 0; $i < $cnt; $i++ ) {
                
                $sel =& $this->addElement('hierselect', "mapper{$x}[$i]", ts('Mapper for Field %1', array(1 => $i)), null);
                $jsSet = false;
                
                $formValues = $this->controller->exportValues( $this->_name );
                if (empty($formValues)) {
                    $formValues = $_POST; // using $_POST since export values don't give values on first submit
                } 
                //print_r($formValues);
                
                /*
                if ( ! $jsSet && empty( $formValues ) ) {
                    for ( $k = 1; $k < 4; $k++ ) {
                        $js .= "{$formName}['mapper{$x}[$i][$k]'].style.display = 'none';\n"; 
                    }
                }
                */
                
                if ( ! $jsSet ) {
                    if ( empty( $formValues ) ) {
                        for ( $k = 1; $k < 4; $k++ ) {
                            $js .= "{$formName}['mapper" .$x. "[$i][$k]'].style.display = 'none';\n"; 
                        }
                    } else {
                        foreach ( $formValues['mapper' . $x] as $value) {
                            //print_r($value);
                            if ($value[0])  {
                                for ( $k = 1; $k < 4; $k++ ) {
                                    if (!trim($value[$k])) {
                                        $js .= "{$formName}['mapper" .$x. "[$i][$k]'].style.display = 'none';\n"; 
                                    }
                                }
                            } else {
                                $js .= "{$formName}['mapper" .$x. "[$i][1]'].style.display = 'none';\n"; 
                            }
                        }
                    }
                }
                
                $sel->setOptions(array($sel1,$sel2,$sel3, $sel4));
                
                $operatorArray = array ('=' => '=', '!=' => '!=', '>' => '>', '<' => '<', '>=' => '>=', '<=' => '<=', 'IN' => 'IN', 'NOT IN' => 'NOT IN', 'LIKE' => 'LIKE', 'NOT LIKE' => 'NOT LIKE');
        
                $this->add('select',"operator{$x}[$i]",'', $operatorArray);
                $this->add('text',"value{$x}[$i]",'');
            }
            
            $this->addElement( 'submit', 'addMore'.$x, ts('another search field'), array( 'class' => 'form-submit' ) );
            
        }
        
        $js .= "</script>\n";
        
        $this->assign('initHideBoxes', $js);
        $this->assign('columnCount1', $this->_columnCount1);
        $this->assign('columnCount2', $this->_columnCount2);
        
        
        $this->setDefaults($defaults);
        
        $this->setDefaultAction( 'refresh' );
        
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Search')
                                         ))
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
        
    }    
    
    
    /**
     * Process the uploaded file
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        $params = $this->controller->exportValues( $this->_name );
        
        if ( $this->controller->exportValue( $this->_name, 'addMore1' ) )  {
            $this->set( 'columnCount1', $this->_columnCount1 );
            return;
        }
        if ( $this->controller->exportValue( $this->_name, 'addMore2' ) )  {
            $this->set( 'columnCount2', $this->_columnCount2 );
            return;
        }
        
        $checkEmpty = 0;
        foreach ($params['mapper1'] as $value) {
            if ($value[0]) {
                $checkEmpty++;
            }
        }
        
        if (!$checkEmpty) {
            foreach ($params['mapper2'] as $value) {
                if ($value[0]) {
                    $checkEmpty++;
                }
            }
        }
        
        if (!$checkEmpty ) {
            require_once 'CRM/Utils/System.php';            
            CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/contact/search/builder', '_qf_Builder_display=true' ) );
        }
        
        //save the record in mapping table
        $mappingParams = array('mapping_type' => 'Search Builder');
        
        $temp = array();
        require_once "CRM/Core/BAO/Mapping.php";
        $saveMapping = CRM_Core_BAO_Mapping::add($mappingParams, $temp) ;
        
        //save mapping fields
        for ( $i = 0; $i < $this->_columnCount1; $i++ ) {
            if ( !empty($params['mapper1'][$i][0]) ) {
                $saveMappingFields =& new CRM_Core_DAO_MappingField();
                $saveMappingFields->mapping_id = $saveMapping->id;
                //$saveMappingFields->name =  $this->_mapperFields[$params['mapper1'][$i][0]][$params['mapper1'][$i][1]];
                $saveMappingFields->name =  $params['mapper1'][$i][1];
                $saveMappingFields->contact_type =  $params['mapper1'][$i][0];
                $saveMappingFields->operator = $params['operator1'][$i];
                $saveMappingFields->value    = $params['value1'][$i];
	    
                $locationId = $params['mapper1'][$i][2];
                $saveMappingFields->location_type_id = isset($locationId) ? $locationId : null;
	    
                $saveMappingFields->phone_type = $params['mapper1'][$i][3];
	    
                $relation = $params['mapper1'][$i][1];
                list($id, $first, $second) = explode('_', $relation);
                if ( ($first == 'a' && $second == 'b') || ($first == 'b' && $second == 'a') ) {
                    $saveMappingFields->relationship_type_id = $id;
                } else {
                    $saveMappingFields->relationship_type_id = null;
                }
	    
                $saveMappingFields->save();
            }
        }

        for ( $i = 0; $i < $this->_columnCount2; $i++ ) {
            if ( !empty($params['mapper2'][$i][0]) ) {
                $saveMappingFields =& new CRM_Core_DAO_MappingField();
                $saveMappingFields->mapping_id = $saveMapping->id;
                //$saveMappingFields->name =  $this->_mapperFields[$params['mapper2'][$i][0]][$params['mapper2'][$i][1]];
                $saveMappingFields->name =  $params['mapper2'][$i][1];
                $saveMappingFields->contact_type =  $params['mapper2'][$i][0];
                $saveMappingFields->grouping = 1;
                $saveMappingFields->operator = $params['operator2'][$i];
                $saveMappingFields->value    = $params['value2'][$i];
	    
                $locationId = $params['mapper2'][$i][2];
                $saveMappingFields->location_type_id = isset($locationId) ? $locationId : null;
	    
                $saveMappingFields->phone_type = $params['mapper2'][$i][3];
	    
                $relation = $params['mapper2'][$i][1];
                list($id, $first, $second) = explode('_', $relation);
                if ( ($first == 'a' && $second == 'b') || ($first == 'b' && $second == 'a') ) {
                    $saveMappingFields->relationship_type_id = $id;
                } else {
                    $saveMappingFields->relationship_type_id = null;
                }
	    
                $saveMappingFields->save();
            }
        }
        
        
        // save the search
        require_once "CRM/Contact/BAO/SavedSearch.php";
        $savedSearch =& new CRM_Contact_BAO_SavedSearch();
        $savedSearch->mapping_id   = $saveMapping->id;
        $savedSearch->save();
        
        // also create a group that is associated with this saved search only if new saved search
        $groupParams = array( );
        $groupParams['domain_id'  ]     = CRM_Core_Config::domainID( );
        $groupParams['title'      ]     = $params['name'];
        $groupParams['visibility' ]     = 'User and User Admin Only';
        $groupParams['saved_search_id'] = $savedSearch->id;
        $groupParams['is_active']       = 1;
        
        $group =& CRM_Contact_BAO_Group::create( $groupParams );

    }
    
}

?>
