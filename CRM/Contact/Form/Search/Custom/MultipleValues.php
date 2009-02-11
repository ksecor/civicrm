<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
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

require_once 'CRM/Contact/Form/Search/Custom/Base.php';

class CRM_Contact_Form_Search_Custom_MultipleValues
   extends    CRM_Contact_Form_Search_Custom_Base
   implements CRM_Contact_Form_Search_Interface {

    protected $_groupTree;
    protected $_tables;
    protected $_options;

    function __construct( &$formValues ) {
        parent::__construct( $formValues );

        require_once 'CRM/Core/BAO/CustomGroup.php';
        $this->_groupTree = CRM_Core_BAO_CustomGroup::getTree( "'Contact', 'Individual', 'Organization', 'Household'",
                                                               CRM_Core_DAO::$_nullObject,
                                                               null, -1 );

        $this->_columns = array( ts('Contact Id')   => 'contact_id',
                                 ts('Contact Type') => 'contact_type',
                                 ts('Name')         => 'sort_name' );

        $this->_customGroupIDs = CRM_Utils_Array::value( 'custom_group', $formValues );

        if ( ! empty( $this->_customGroupIDs ) ) {
            $this->addColumns( );
        }
    }

    function addColumns( ) {
        // add all the fields for chosen groups
        $this->_tables = $this->_options = array( );
        foreach ( $this->_groupTree as $groupID => $group ) {
            if ( ! CRM_Utils_Array::value( $groupID, $this->_customGroupIDs ) ) {
                continue;
            }

            // now handle all the fields
            foreach ( $group['fields'] as $fieldID => $field ) {
                $this->_columns[$field['label']] = "custom_{$field['id']}";
                if ( ! array_key_exists( $group['table_name'], $this->_tables ) ) {
                    $this->_tables[$group['table_name']] = array( );
                }
                $this->_tables[$group['table_name']][$field['id']] = $field['column_name'];

                // also build the option array
                $this->_options[$field['id']] = array( );
                CRM_Core_BAO_CustomField::buildOption( $field,
                                                       $this->_options[$field['id']] );
            }
        }
    }

    function buildForm( &$form ) {
        /**
         * You can define a custom title for the search form
         */
        $this->setTitle('Multiple Value Custom Group Search and Export');

        $form->add( 'text',
                    'sort_name',
                    ts( 'Contact Name' ),
                    true );
        if ( empty( $this->_groupTree ) ) {
            CRM_Core_Error::statusBounce( ts("Atleast one Custom Group must be present, for Custom Group search."),
                                          CRM_Utils_System::url( 'civicrm/contact/search/custom/list',
                                                                 'reset=1') );
        }
        // add the checkbox for custom_groups
        foreach ( $this->_groupTree as $groupID => $group ) {
            if ( $groupID == 'info' ) {
                continue;
            }
            $form->addElement('checkbox', "custom_group[$groupID]", null, $group['title'] );
        }
    }

    function summary( ) {
        $summary = array( 'summary' => 'This is a summary',
                          'total' => 50.0 );
        return $summary;
    }

    function all( $offset = 0, $rowcount = 0, $sort = null,
                  $includeContactIDs = false ) {
        //redirect if custom group not select in search criteria
        if ( !CRM_Utils_Array::value( 'custom_group', $this->_formValues ) ) {
            CRM_Core_Error::statusBounce( ts("You must select at least one Custom Group as a search criteria."),
                                          CRM_Utils_System::url( 'civicrm/contact/search/custom',
                                                                 "reset=1&csid={$this->_formValues['customSearchID']}",
                                                                 false, null, false, true ) );
        }
        $selectClause = "
contact_a.id           as contact_id  ,
contact_a.contact_type as contact_type,
contact_a.sort_name    as sort_name,
";

        $customClauses = array( );
        foreach ( $this->_tables as $tableName => $fields ) {
            foreach ( $fields as $fieldID => $fieldName ) {
                $customClauses[ ] = "{$tableName}.{$fieldName} as custom_{$fieldID}";
            }
        }
        $selectClause .= implode( ',', $customClauses );

        return $this->sql( $selectClause,
                           $offset, $rowcount, $sort,
                           $includeContactIDs, null );

    }
    
    function from( ) {
        $from = "FROM      civicrm_contact contact_a";
        
        $customFrom = array( );
        foreach ( $this->_tables as $tableName => $fields ) {
            $customFrom[ ] = " LEFT JOIN $tableName ON {$tableName}.entity_id = contact_a.id ";
        }
        return $from . implode( ' ', $customFrom );
    }

    function where( $includeContactIDs = false ) {
        $count  = 1;
        $clause = array( );
        $params = array( );
        $name   = CRM_Utils_Array::value( 'sort_name',
                                          $this->_formValues );
        if ( $name != null ) {
            if ( strpos( $name, '%' ) === false ) {
                $name = "%{$name}%";
            }
            $params[$count] = array( $name, 'String' );
            $clause[] = "contact_a.sort_name LIKE %{$count}";
            $count++;
        }

        $where = '( 1 )';
        if ( ! empty( $clause ) ) {
            $where .= ' AND ' . implode( ' AND ', $clause );
        }

        return $this->whereClause( $where, $params );
    }

    function templateFile( ) {
        return 'CRM/Contact/Form/Search/Custom/MultipleValues.tpl';
    }

    function setDefaultValues( ) {
        return array( );
    }

    function alterRow( &$row ) {
        foreach ( $this->_options as $fieldID => $values ) {
            if ( in_array( $values['attributes']['html_type'],
                           array( 'CheckBox', 'Radio', 'Select', 'Multi-Select' ) ) ) {
                if ( $row["custom_{$fieldID}"] &&
                     array_key_exists( $row["custom_{$fieldID}"],
                                       $values ) ) {
                    $row["custom_{$fieldID}"] = $values[$row["custom_{$fieldID}"]];
                }
            }
        }
    }
    
    function setTitle( $title ) {
        CRM_Utils_System::setTitle( $title );
    }

}


