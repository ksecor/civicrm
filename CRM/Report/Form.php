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
            $label = ( is_array($fltrProperties) && 
                       array_key_exists('label', $fltrProperties) ) ? $fltrProperties['label'] : $field;
            $filterFields[$label] = $field;

            switch ( $fltrProperties['type'] ) {
            case 'integer':
                $operationList = array( 'Is less than', 'Is equal to', 'Is greater than' );
                foreach ( $operationList as $listing ) {
                    $operations[str_replace(' ', '_', strtolower($listing))] = $listing; 
                }
                $this->addRadio( "{$field}_operation", ts( 'Operator:' ), $operations );
                break;
            case 'date':
            default:
                // consider type as string
                $operationList = array( 'Is equal to', 'Starts with', 'Ends with' );
                foreach ( $operationList as $listing ) {
                    $operations[str_replace(' ', '_', strtolower($listing))] = $listing; 
                }
                $this->addRadio( "{$field}_operation", ts( 'Operator:' ), $operations );
                break;
            }
            $this->add( 'text', "{$field}_operation_value", ts('Value') );
        }

        $this->assign( 'filterFields', $filterFields );
    }

}



