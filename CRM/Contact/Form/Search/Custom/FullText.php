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

class CRM_Contact_Form_Search_Custom_FullText
   implements CRM_Contact_Form_Search_Interface {

    protected $_formValues;

    protected $_columns;

    protected $_text  = null;

    protected $_table = null;

    protected $_tableName = null;

    protected $_tableFields = null;
    
    protected $_cacheContactTable = 'civicrm_temp_contact_cache';

    function __construct( &$formValues ) {
        $this->_formValues =& $formValues;

        $this->_text   = CRM_Utils_Array::value( 'text',
                                                 $formValues );
        $this->_table = CRM_Utils_Array::value( 'table',
                                                 $formValues );

        if ( ! $this->_text ) {
            $this->_text   = CRM_Utils_Request::retrieve( 'text', 'String',
                                                          CRM_Core_DAO::$_nullObject );
            if ( $this->_text ) {
                $formValues['text'] = $this->_text;
            }
        }

        if ( ! $this->_table ) {
            $this->_table   = CRM_Utils_Request::retrieve( 'table', 'String',
                                                          CRM_Core_DAO::$_nullObject );
            if ( $this->_table ) {
                $formValues['table'] = $this->_table;
            }
        }
        

        // fix text to include wild card characters at begining and end
        if ( $this->_text ) {
            $this->_text = strtolower( addslashes( $this->_text ) );
            if ( strpos( $this->_text, '%' ) === false ) {
                $this->_text = "'%{$this->_text}%'";
            } else {
                $this->_text = "'{$this->_text}'";
            }

            $this->buildTempTable( );
        
            $this->fillTable( );
        }

    }

    function __destruct( ) {
    }

    function buildTempTable( ) {
        $randomNum = md5( uniqid( ) );
        $this->_tableName = "civicrm_temp_custom_{$randomNum}";

        $sql = "DROP TABLE IF EXISTS {$this->_tableName}";
        CRM_Core_DAO::executeQuery( $sql );
        
        $this->_tableFields =
            array(
                  'id' => 'int unsigned NOT NULL AUTO_INCREMENT',
                  'table_name' => 'varchar(16)',
                  'contact_id' => 'int unsigned',
                  'display_name' => 'varchar(128)',
                  'assignee_contact_id' => 'int unsigned',
                  'assignee_display_name' => 'varchar(128)',
                  'target_contact_id' => 'int unsigned',
                  'target_display_name' => 'varchar(128)',
                  'activity_id' => 'int unsigned',
                  'case_id' => 'int unsigned',
                  'subject' => ' varchar(255)',
                  'details' => ' varchar(255)',
                  );
                  
        $sql = "
CREATE TABLE {$this->_tableName} (
";

        foreach ( $this->_tableFields as $name => $desc ) {
            $sql .= "$name $desc,\n"; 
        }

        $sql .= "
  PRIMARY KEY ( id )
) ENGINE=HEAP
";

        CRM_Core_DAO::executeQuery( $sql );
    }

    function fillTable( ) {
        $sql = "DROP TABLE IF EXISTS {$this->_cacheContactTable}";
        CRM_Core_DAO::executeQuery( $sql );

        $sql = "
CREATE TABLE {$this->_cacheContactTable} (
  id INT PRIMARY KEY AUTO_INCREMENT,
  contact_id INT,
  display_name VARCHAR(64)
) ENGINE=HEAP;
";
        CRM_Core_DAO::executeQuery( $sql );

        $sql = "
INSERT INTO  {$this->_cacheContactTable} ( contact_id, display_name )
SELECT  id contact_id, display_name
FROM    civicrm_contact                
WHERE   display_name LIKE {$this->_text}
";
        CRM_Core_DAO::executeQuery( $sql );
        
        if ( ! $this->_table ||
             $this->_table == 'Contact') {
            $this->fillContact( );
        }

        if ( ! $this->_table ||
             $this->_table == 'Activity') {
            $this->fillActivity( );
        }

        if ( ! $this->_table ||
             $this->_table == 'Case') {
            $this->fillCase( );
        }
        
        $sql = "DROP TABLE IF EXISTS {$this->_cacheContactTable}";
        CRM_Core_DAO::executeQuery( $sql );
    }

    function fillContact( ) {
        $sql = "
INSERT INTO {$this->_tableName}
( contact_id, display_name, table_name )
SELECT DISTINCT( c.contact_id ), c.display_name, 'Contact'
FROM {$this->_cacheContactTable} c
LEFT JOIN civicrm_address ca ON c.id = ca.contact_id
LEFT JOIN civicrm_email   ce ON c.id = ce.contact_id
LEFT JOIN civicrm_phone   cp ON c.id = cp.contact_id
WHERE c.contact_id IS NOT NULL
OR    ca.street_address LIKE {$this->_text}
OR    ca.city LIKE {$this->_text}
OR    ce.email LIKE {$this->_text}
OR    cp.phone LIKE {$this->_text}
";

        if ( ! $this->_table ) {
            $sql .= " LIMIT 10 ";
        }
        CRM_Core_DAO::executeQuery( $sql );
    }

    function fillActivity( ) {

        $sql = "
INSERT INTO {$this->_tableName}
( table_name, activity_id, subject, details, contact_id, display_name, assignee_contact_id, assignee_display_name, target_contact_id, target_display_name )
SELECT    'Activity', ca.id, ca.subject, substring(ca.details, 250),
           c1.contact_id, c1.display_name,
           c2.contact_id, c2.display_name,
           c3.contact_id, c3.display_name
FROM      civicrm_activity ca
LEFT JOIN civicrm_activity_assignment caa ON caa.activity_id = ca.id
LEFT JOIN {$this->_cacheContactTable} c1 ON caa.assignee_contact_id = c1.id
LEFT JOIN civicrm_activity_target cat ON cat.activity_id = ca.id
LEFT JOIN {$this->_cacheContactTable} c2 ON cat.target_contact_id = c2.id
LEFT JOIN {$this->_cacheContactTable} c3 ON ca.source_contact_id = c3.id
WHERE ca.subject      LIKE {$this->_text}
   OR ca.details      LIKE {$this->_text}
   OR c1.contact_id   IS NOT NULL
   OR c2.contact_id   IS NOT NULL
   OR c3.contact_id   IS NOT NULL
";         
        if ( ! $this->_table ) {
            $sql .= " LIMIT 10 ";
        }

        CRM_Core_DAO::executeQuery( $sql );
    }

    function fillCase( ) {
        $sql = "
INSERT INTO {$this->_tableName}
( table_name, contact_id, display_name, case_id )
SELECT    'Case', c.contact_id, c.display_name, cc.id
FROM      civicrm_case cc 
LEFT JOIN civicrm_case_contact ccc ON cc.id = ccc.case_id
LEFT JOIN {$this->_cacheContactTable} c ON ccc.contact_id = c.contact_id
WHERE   c.contact_id   IS NOT NULL
";

        if ( ! $this->_table ) {
            $sql .= " LIMIT 10 ";
        }

        CRM_Core_DAO::executeQuery( $sql );
    }

    function buildForm( &$form ) {
        $form->add( 'text',
                    'text',
                    ts( 'Find' ),
                    true );

        // also add a select box to allow the search to be constrained
        $tables = array( ''          => ts( 'All Tables' ),
                         'Contact'   => ts( 'Contacts' ),
                         'Activity'  => ts( 'Activities' ),
                         'Case'      => ts( 'Cases' ) );
        $form->add( 'select',
                    'table',
                    ts( 'Tables' ),
                    $tables );

        /**
         * You can define a custom title for the search form
         */
         $this->setTitle('Full Text Search');
         
    }

    function &columns( ) {
        $this->_columns = array( ts('Contact Id')      => 'contact_id'    ,
                                 ts('Name')            => 'display_name'  );

        return $this->_columns;
    }

    function summary( ) {
        $summary = array( 'Contact'  => array( ),
                          'Activity' => array( ),
                          'Case'     => array( ) );
        
        
        // now iterate through the table and add entries to the relevant section
        $sql = "SELECT * FROM {$this->_tableName}";
        $dao = CRM_Core_DAO::executeQuery( $sql );

        
        while ( $dao->fetch( ) ) {
            $row = array( );
            foreach ( $this->_tableFields as $name => $dontCare ) {
                $row[$name] = $dao->$name;
            }
            $summary[$dao->table_name][] = $row;
        }
        
        if ( ! $this->_table ) {
            $summary['addShowAllLink'] = true;
        }

        return $summary;
    }

    function count( ) {
        return CRM_Core_DAO::singleValueQuery( "SELECT count(id) FROM {$this->_tableName}" );
    }

    function contactIDs( $offset = 0, $rowcount = 0, $sort = null) {
        return CRM_Core_DAO::singleValueQuery( "SELECT contact_id FROM {$this->_tableName}" );
    }

    function all( $offset = 0, $rowcount = 0, $sort = null,
                  $includeContactIDs = false ) {
        return "
SELECT 
  contact_a.contact_id   as contact_id  ,
  contact_a.display_name as display_name
FROM
  {$this->_tableName} contact_a
";
    }
    
    function from( ) {
        return null;
    }

    function where( $includeContactIDs = false ) {
        return null;
    }

    function templateFile( ) {
        return 'CRM/Contact/Form/Search/Custom/FullText.tpl';
    }

    function setDefaultValues( ) {
        return array( );
    }

    function alterRow( &$row ) {
    }
    
    function setTitle( $title ) {
        if ( $title ) {
            CRM_Utils_System::setTitle( $title );
        }
    }
}


