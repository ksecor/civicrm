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
require_once 'CRM/Core/DAO/MappingField.php';

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
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function preProcess() {
        parent::preProcess( );

        $this->_columnCount = array();
        $this->_columnCount = $this->get('columnCount');

        if (! $this->_columnCount[1] ) {
            $this->_columnCount[1] = 1;
        }

        if (! $this->_columnCount[2] ) {
            $this->_columnCount[2] = 1;
        }

        $this->_loadedMappingId =  $this->get('savedMapping');
    }
    
    public function buildQuickForm( ) {
        require_once "CRM/Core/BAO/Mapping.php";
        CRM_Core_BAO_Mapping::buildMappingForm($this, 'Search Builder', $this->_mappingId, $this->_columnCount);
        
        $this->addButtons( array(
                                 array ( 'type'      => 'refresh',
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
        $session =& CRM_Core_Session::singleton();
        $session->set('isAdvanced', '2');

        $config =& CRM_Core_Config::singleton( );
        $config->oldInputStyle = 0;

        $params = $this->controller->exportValues( $this->_name );
        for ($x = 1; $x <= 3; $x++ ) {
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

        $session =& CRM_Core_Session::singleton();
        $session->set('isSearchBuilder', '1');

        // get user submitted values
        // get it from controller only if form has been submitted, else preProcess has set this
        if ( ! empty( $_POST ) ) {
            //$this->_formValues = $this->controller->exportValues($this->_name);
            
            $this->_formValues = CRM_Core_BAO_Mapping::returnFormatedFields($params);

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

        // retrieve ssID values only if formValues is null, i.e. form has never been posted
        if ( empty( $this->_formValues ) && isset( $this->_ssID ) ) {
            $this->_formValues = CRM_Contact_BAO_SavedSearch::getFormValues( $this->_ssID );
        }

        if ( isset( $this->_groupID ) && ! CRM_Utils_Array::value( 'group', $this->_formValues ) ) {
            $this->_formValues['group'] = array( $this->_groupID => 1 );
        }
      
        $this->postProcessCommon( );


    }
    
}

?>
