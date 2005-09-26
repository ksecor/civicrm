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
class CRM_Export_Form_MapField extends CRM_Core_Form {
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
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    
    public function preProcess() {
        $this->_columnCount = 10;
        if (CRM_Utils_Request::retrieve( 'more', $form )) {
            $currentCount = $this->get('columnCount');
            $this->_columnCount =  $currentCount + 10;
        }
    }
    
    public function buildQuickForm( ) {

        $this->_defaults = array( );
        $hasLocationTypes= array();
        
        $fields = array();
        $fields['Individual']   =& CRM_Contact_BAO_Contact::exportableFields('Individual');
        $fields['Household']    =& CRM_Contact_BAO_Contact::exportableFields('Household');
        $fields['Organization'] =& CRM_Contact_BAO_Contact::exportableFields('Organization');
        //print_r($fields);
        //print_r(CRM_Contact_BAO_Contact::exportableFields('Household'));

        foreach ($fields as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $this->_mapperFields[$key][$key1] = $value1['title'];
                $hasLocationTypes[$key][$key1]    = $value1['hasLocationType'];
            }
        }
        
        // print_r($hasLocationTypes);
        /*
        foreach ($fields as $key => $value) {
            $this->_mapperFields[$key] = $value['title'];
            $hasLocationTypes[$key] = $value['hasLocationType'];
        }
        */

        // print_r($this->_mapperFields);

        // $this->_mapperFields = $fields;
              
        $mapperKeys      = array_keys( $this->_mapperFields );
        //$mapperKeys      = array_keys( $fields );
        

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
        
        $sel1 = array( '' => '-do not export-' , 'Individual' => 'Individual', 'Household' => 'Household', 'Organization' => 'Organization');
        
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

        $js = "<script type='text/javascript'>\n";
        $formName = 'document.forms.' . $this->_name;
        
        //used to warn for mismatch column count or mismatch mapping 
        $warning = 0;
        for ( $i = 0; $i < $this->_columnCount; $i++ ) {
            $js .= "swapOptions($formName, 'mapper[$i]', 0, 3, 'hs_mapper_".$i."_');\n";
            $sel =& $this->addElement('hierselect', "mapper[$i]", ts('Mapper for Field %1', array(1 => $i)), null);
            
            //$this->add( 'select', "mapper[$i]", ts('Mapper for Field %1', array(1 => $i)), $this->_mapperFields );
            //$this->_defaults["mapper[$i]"] = $mapperKeys[$i];

            $sel->setOptions(array($sel1,$sel2,$sel3, $sel4));
        }
        $js .= "</script>\n";
        $this->assign('initHideBoxes', $js);
        $this->assign('columnCount', $this->_columnCount);
        $this->set('columnCount', $this->_columnCount);

        $this->setDefaults( $this->_defaults );       

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
     * Process the uploaded file
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        
        $mapperKeys = $_POST['mapper'];
        /*//$mapperKeys = $this->controller->exportValue( $this->_name, 'mapper' );
        print_r($mapperKeys);
        $fields = array();
        //print_r($mapperKeys);
        foreach($mapperKeys as $key) {
            if($key[1]) {
                $fields[$key[1]]= array($key[2],$key[3]);
            }
        }
        $returnFields = array($fields);
        // print_r($returnFields);
        */
        CRM_Export_BAO_Export::exportContacts($mapperKeys);
        
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
