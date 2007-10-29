<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Contact/Form/Search/Interface.php';
class CRM_Contact_Form_Search_Custom_Sample implements CRM_Contact_Form_Search_Interface {

    protected $_formValues;

    protected $_columns;

    function __construct( &$formValues ) {
        $this->_formValues =& $formValues;

        $this->_columns = array( ts('Contact Id')   => 'contact_id'  ,
                                 ts('Contact Type') => 'contact_type',
                                 ts('Name')         => 'sort_name', );
    }

    function buildForm( &$form ) {
        $form->add( 'text',
                    'household_name',
                    ts( 'Household Name' ),
                    true );
    }

    function count( &$queryParams ) {
        return $this->sql( $queryParams,
                           'count(contact_a.id) as total' );
    }

    function alphabet( &$queryParams ) {
        return $this->sql( $queryParams,
                           'DISTINCT UPPER(LEFT(contact_a.sort_name, 1)) as sort_name' );
    }

    function contactIDs( &$queryParams,
                         $offset, $rowcount, $sort ) {
        $selectClause = "
contact_a.id           as contact_id
";
        return $this->sql( $queryParams,
                           $selectClause );

    }

    function all( &$queryParams,
                  $offset, $rowcount, $sort ) {
        $selectClause = "
contact_a.id           as contact_id  ,
contact_a.contact_type as contact_type,
contact_a.sort_name    as sort_name
";
        return $this->sql( $queryParams,
                           $selectClause );

    }



    function sql( &$queryParams,
                  $selectClause ) {
        $name = CRM_Utils_Array::value( 'household_name',
                                        $this->_formValues );
        if ( $name == null ) {
            $name = '';
        }
        if ( strpos( $name, '%' ) === false ) {
            $name = "%{$name}%";
        }

        $queryParams[1] = array( $name, 'String' );
        
        $sql = "
SELECT $selectClause
  FROM civicrm_contact contact_a
 WHERE contact_a.contact_type = 'Household'
  AND  contact_a.household_name LIKE %1";
        return $sql;
    }

    function templateFile( ) {
        return 'CRM/Contact/Form/Search/Custom/Sample.tpl';
    }

    function &columns( ) {
        return $this->_columns;
    }

}

?>
