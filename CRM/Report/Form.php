<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

class CRM_Report_Form extends CRM_Core_Form {

    /**
     * The id of the report instance
     *
     * @var integer
     */
    protected $_id;

    /**
     * The id of the report template
     *
     * @var integer;
     */
    protected $_templateID;

    /**
     * The report title
     *
     * @var string
     */
    protected $_title;

    /**
     * The set of all columns in the report. An associative array
     * with column name as the key and attribues as the value
     *
     * @var array
     */
    protected $_columns;

    /**
     * The set of filters in the report
     *
     * @var array
     */
    protected $_filters;

    /**
     * 
     */
    function __construct( ) {
        parent::__construct( );
    }

    function addColumns( ) {
        $options = array();
        
        foreach ( $this->_columns as $table => $tblProperties ) {
            foreach ( $tblProperties['fields'] as $field => $fldProperties ) {
                $label = ( is_array($fldProperties) && 
                           array_key_exists('label', $fldProperties) ) ? $fldProperties['label'] : $field;
                $options[$label] = $field;
            } 
        }
        $this->addCheckBox( 'select_columns', ts('Select Columns'), $options );
    }

    function addFilters( ) {
        $options = $filterFields = array();

        foreach ( $this->_filters as $field => $fltrProperties ) {
            // make a list of filter fields
            $label = ( is_array($fltrProperties) && 
                       array_key_exists('label', $fltrProperties) ) ? $fltrProperties['label'] : $field;
            $filterFields[$label] = $field;

            // get ready with option value pair
            $operations = self::getOperationPair( $fltrProperties['type'] );
            
            // build form elements based on types
            switch ( $fltrProperties['type'] ) {
            case 'integer':
            default:
                // default type is string
                $this->addRadio( "{$field}_operation", ts( 'Operator:' ), $operations, 
                                 array('onclick' =>"return showHideMaxMinVal( '$field', this.value );"), '<br/>' );
                break;
            }
            
            // we need text box for value input
            $this->add( 'text', "{$field}_operation_value", ts('Value') );

            // and a min value input box
            $this->add( 'text', "{$field}_operation_min", ts('Min') );

            // and a max value input box
            $this->add( 'text', "{$field}_operation_max", ts('Max') );
        }

        $this->assign( 'filterFields', $filterFields );
    }

    function buildQuickForm( ) {
        $this->addColumns( );
        
        $this->addFilters( );

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Next'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    static function getOperationPair( $type = "string" ) {
        // FIXME: At some point we should move these key-val pairs 
        // to option_group and option_value table.

        switch ( $type ) {
        case 'money':
        case 'integer':
            return array( 'lt'  => 'Is less than', 
                          'eq'  => 'Is equal to', 
                          'gt'  => 'Is greater than',
                          'bw'  => 'Is between',
                          );
            break;
        case 'date':
        default:
            // type is string
            return array( 'like' => 'Is equal to', 
                          'sw'   => 'Starts with', 
                          'ew'   => 'Ends with',
                          );
        }
    }

    static function getValueQuery( $value, $operator = "like" ) {
        switch ( $operator ) {
        case 'eq':
            return "= {$value}"; 
        case 'lt':
            return "< {$value}"; 
        case 'lte':
            return "<= {$value}"; 
        case 'gt':
            return "> {$value}"; 
        case 'gte':
            return ">= {$value}"; 
        default:
            // type is string
            return "like '%{$value}%'"; 
        }
    }
}
