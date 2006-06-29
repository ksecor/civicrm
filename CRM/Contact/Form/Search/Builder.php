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

require_once 'CRM/Contact/Form/Search.php';
require_once "CRM/Core/BAO/Mapping.php";

/**
 * This class if for search builder processing
 */
class CRM_Contact_Form_Search_Builder extends CRM_Contact_Form_Search
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
    protected $_columnCount;

    /**
     * number of blocks to be shown
     *
     * @var int
     * @access protected
     */
    protected $_blockCount;
    
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function preProcess() {
        parent::preProcess( );
        //get the block count
        $this->_blockCount = $this->get('blockCount');
        if ( !$this->_blockCount ) {
            $this->_blockCount = 3;
        }

        //get the column count
        $this->_columnCount = array();
        $this->_columnCount = $this->get('columnCount');
        
        for ( $i = 1; $i < $this->_blockCount; $i++ ){
            if ( !$this->_columnCount[$i] ) {
                $this->_columnCount[$i] = 1;
            }
        }

        $this->_loadedMappingId =  $this->get('savedMapping');
    }
    
    public function buildQuickForm( ) {
        //get the saved search mapping id
        $mappingId = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_SavedSearch', $this->_ssID, 'mapping_id' );
            
        CRM_Core_BAO_Mapping::buildMappingForm($this, 'Search Builder', $mappingId, $this->_columnCount, $this->_blockCount);
        
        $this->buildQuickFormCommon();
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
    
    public function normalizeFormValues( ) {
    }

    public function &convertFormValues( &$formValues ) {
        return CRM_Core_BAO_Mapping::formattedFields( $formValues );
    }

    public function &returnProperties( ) {
        return CRM_Core_BAO_Mapping::returnProperties( $this->_formValues );
    }

    /**
     * Process the uploaded file
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        $session =& CRM_Core_Session::singleton();
        $session->set('isAdvanced', '2');
        $session->set('isSearchBuilder', '1');

        $params = $this->controller->exportValues( $this->_name );
        
        if (!empty($params)) {
            if ( $params['addBlock'] )  { 
                $this->_blockCount = $this->_blockCount + 1;
                $this->set( 'blockCount', $this->_blockCount );
                return;
            }
            
            for ($x = 1; $x <= $this->_blockCount; $x++ ) {
                if ( $params['addMore'][$x] )  {
                    $this->_columnCount[$x] = $this->_columnCount[$x] + 1;
                    $this->set( 'columnCount', $this->_columnCount );
                    return;
                }
            }
            
            foreach ($params['mapper'] as $key => $value) {
                foreach ($value as $k => $v) {
                    if ($v[0]) {
                        $checkEmpty++;
                    }
                }
            }
            
            if (!$checkEmpty ) {
                require_once 'CRM/Utils/System.php';            
                CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/contact/search/builder', '_qf_Builder_display=true' ) );
            }
            
        }
        
        // get user submitted values
        // get it from controller only if form has been submitted, else preProcess has set this
        if ( ! empty( $_POST ) ) {
            $this->_formValues = $this->controller->exportValues($this->_name);

            // set the group if group is submitted
            if ($this->_formValues['uf_group_id']) {
                $this->set( 'id', $this->_formValues['uf_group_id'] ); 
            } else {
                $this->set( 'id', '' ); 
            }
            
            // also reset the sort by character 
            $this->_sortByCharacter = null; 
            $this->set( 'sortByCharacter', null ); 
        }

        $this->_params =& $this->convertFormValues( $this->_formValues );
        $this->_returnProperties =& $this->returnProperties( );
        $this->postProcessCommon( );
    }
    
}

?>
