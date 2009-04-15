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
    
    protected $_textID  = null;

    protected $_table = null;

    protected $_tableName = null;

    protected $_entityIDTableName = null;

    protected $_tableFields = null;

    protected $_limitClause = null;
    
    protected $_limitNumber = 10;

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
            if ( is_numeric( $this->_text ) ) {
                $this->_textID = $this->_text;
            } 

            $this->_text = strtolower( addslashes( $this->_text ) );
            if ( strpos( $this->_text, '%' ) === false ) {
                $this->_text = "'%{$this->_text}%'";
            } else {
                $this->_text = "'{$this->_text}'";
            }

        } else {
            $this->_text = "'%'";
        }

        if ( ! $this->_table ) {
            $this->_limitClause = " LIMIT {$this->_limitNumber}";
        }

        $this->buildTempTable( );
        
        $this->fillTable( );

    }

    function __destruct( ) {
    }

    function buildTempTable( ) {
        $randomNum = md5( uniqid( ) );
        $this->_tableName = "civicrm_temp_custom_details_{$randomNum}";

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
                  'activity_type_id' => 'int unsigned',
                  'case_id' => 'int unsigned',
                  'subject' => ' varchar(255)',
                  'details' => ' varchar(255)',
                  );
                  
        $sql = "
CREATE TEMPORARY TABLE {$this->_tableName} (
";

        foreach ( $this->_tableFields as $name => $desc ) {
            $sql .= "$name $desc,\n"; 
        }

        $sql .= "
  PRIMARY KEY ( id )
) ENGINE=HEAP DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci
";
        CRM_Core_DAO::executeQuery( $sql );

        $this->_entityIDTableName = "civicrm_temp_custom_entityID_{$randomNum}";
        $sql = "
CREATE TEMPORARY TABLE {$this->_entityIDTableName} (
  id int unsigned NOT NULL AUTO_INCREMENT,
  entity_id int unsigned NOT NULL,
  
  UNIQUE INDEX unique_entity_id ( entity_id ),
  PRIMARY KEY ( id )
) ENGINE=HEAP DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci
";
        CRM_Core_DAO::executeQuery( $sql );
    }

    function fillTable( ) {
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
    }

    function fillCustomInfo( &$tables,
                             $extends ) {
        
        $sql = "
SELECT cg.table_name, cf.column_name
FROM   civicrm_custom_group cg,
       civicrm_custom_field cf
WHERE  cf.custom_group_id = cg.id
AND    cg.extends IN $extends
AND    cg.is_active = 1
AND    cf.is_active = 1
AND    cf.is_searchable = 1
AND    cf.html_type IN ( 'Text', 'TextArea', 'RichTextEditor' )
";

        $dao = CRM_Core_DAO::executeQuery( $sql );
        while ( $dao->fetch( ) ) {
            if ( ! array_key_exists( $dao->table_name,
                                     $tables ) ) {
                $tables[$dao->table_name] = array( 'id' => 'entity_id',
                                                   'fields' => array( ) );
            }
            $tables[$dao->table_name]['fields'][$dao->column_name] = null;
        }
    }

    function runQueries( &$tables ) {
        $sql = "TRUNCATE {$this->_entityIDTableName}";
        CRM_Core_DAO::executeQuery( $sql );
        
        foreach ( $tables as $tableName => $tableValues ) {
            if ( $tableName == 'sql' ) {
                foreach ( $tableValues as $sqlStatement ) {
                    $sql = "
REPLACE INTO {$this->_entityIDTableName} ( entity_id )
$sqlStatement
{$this->_limitClause}
";
                    CRM_Core_DAO::executeQuery( $sql );
                }
            } else {
                $clauses = array( );

                foreach ( $tableValues['fields'] as $fieldName => $fieldType ) {
                    if ( $fieldType == 'Int') {
                        if ( $this->_textID ) {
                            $clauses[] = "$fieldName = {$this->_textID}";
                        }
                    } else {
                        $clauses[] = "$fieldName LIKE {$this->_text}";
                    } 
                }
                
                if ( empty( $clauses ) ) {
                    continue;
                }
                
                $whereClause = implode( ' OR ', $clauses );

                $sql = "
REPLACE INTO {$this->_entityIDTableName} ( entity_id )
SELECT  {$tableValues['id']}
FROM    $tableName
WHERE   ( $whereClause )
AND     {$tableValues['id']} IS NOT NULL
{$this->_limitClause}
";
                CRM_Core_DAO::executeQuery( $sql );
            }
        }
    }


    function fillContactIDs( ) {
        $tables = 
            array( 'civicrm_contact' => array( 'id' => 'id',
                                               'fields' => array( 'display_name' => null) ),
                   'civicrm_address' => array( 'id' => 'contact_id',
                                               'fields' => array( 'street_address' => null,
                                                                  'city' => null,
                                                                  'postal_code' => null ) ),
                   'civicrm_email'   => array( 'id' => 'contact_id',
                                               'fields' => array( 'email' => null ) ),
                   'civicrm_phone'   => array( 'id' => 'contact_id',
                                               'fields' => array( 'phone' => null ) ),
                   );
        
        // get the custom data info
        $this->fillCustomInfo( $tables,
                               "( 'Contact', 'Individual', 'Organization', 'Household' )" );
         
        $this->runQueries( $tables );
   }

    function fillContact( ) {

        $this->fillContactIDs( );

        $sql = "
INSERT INTO {$this->_tableName}
( contact_id, display_name, table_name )
SELECT c.id, c.display_name, 'Contact'
FROM   civicrm_contact c, {$this->_entityIDTableName} ct
WHERE  c.id = ct.entity_id
{$this->_limitClause}
";

        CRM_Core_DAO::executeQuery( $sql );
    }

    function fillActivityIDs( ) {
        $contactSQL = array( );

        $contactSQL[] = "
SELECT ca.id 
FROM   civicrm_activity ca, civicrm_contact c
WHERE  ca.source_contact_id = c.id
AND    c.display_name LIKE {$this->_text}
";

        $contactSQL[] = "
SELECT ca.id 
FROM   civicrm_activity ca, civicrm_activity_target cat, civicrm_contact c
WHERE  cat.activity_id = ca.id
AND    cat.target_contact_id = c.id
AND    c.display_name LIKE {$this->_text}
";

        $contactSQL[] = "
SELECT ca.id 
FROM   civicrm_activity ca, civicrm_activity_assignment caa, civicrm_contact c
WHERE  caa.activity_id = ca.id
AND    caa.assignee_contact_id = c.id
AND    c.display_name LIKE {$this->_text}
";
        

                   
        $tables = array( 'civicrm_activity' => array( 'id' => 'id',
                                                      'fields' => array( 'subject' => null,
                                                                         'details' => null ) ),
                         'sql'              => $contactSQL );
        $this->fillCustomInfo( $tables,
                               "( 'Activity' )" );

        $this->runQueries( $tables );
    }

    function fillActivity( ) {
        
        $this->fillActivityIDs( ) ;

        $sql = "
INSERT INTO {$this->_tableName}
( table_name, activity_id, subject, details, contact_id, display_name, assignee_contact_id, assignee_display_name, target_contact_id, target_display_name, activity_type_id )
SELECT    'Activity', ca.id, substr(ca.subject, 1, 50), substr(ca.details, 1, 250),
           c1.id, c1.display_name,
           c2.id, c2.display_name,
           c3.id, c3.display_name,
           ca.activity_type_id
FROM       {$this->_entityIDTableName} eid, civicrm_activity ca
LEFT JOIN  civicrm_contact c1 ON ca.source_contact_id = c1.id
LEFT JOIN  civicrm_activity_assignment caa ON caa.activity_id = ca.id
LEFT JOIN  civicrm_contact c2 ON caa.assignee_contact_id = c2.id
LEFT JOIN  civicrm_activity_target cat ON cat.activity_id = ca.id
LEFT JOIN  civicrm_contact c3 ON cat.target_contact_id = c3.id
WHERE ca.id = eid.entity_id
{$this->_limitClause}
";         

        CRM_Core_DAO::executeQuery( $sql );
    }

    function fillCase( ) {
        $sql = "
INSERT INTO {$this->_tableName}
( table_name, contact_id, display_name, case_id )
SELECT    'Case', c.id, c.display_name, cc.id
FROM      civicrm_case cc 
LEFT JOIN civicrm_case_contact ccc ON cc.id = ccc.case_id
LEFT JOIN civicrm_contact c ON ccc.contact_id = c.id
WHERE   c.display_name LIKE {$this->_text}
{$this->_limitClause}
";

        CRM_Core_DAO::executeQuery( $sql );
        if ( $this->_textID ) { 
            $sql = "
INSERT INTO {$this->_tableName}
  ( table_name, contact_id, display_name, case_id )
SELECT    'Case', c.id, c.display_name, cc.id
FROM      civicrm_case cc 
LEFT JOIN civicrm_case_contact ccc ON cc.id = ccc.case_id
LEFT JOIN civicrm_contact c ON ccc.contact_id = c.id
WHERE     cc.id = {$this->_textID}
{$this->_limitClause}
    ";

            CRM_Core_DAO::executeQuery( $sql );
        }
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
        
        $activityTypes = CRM_Core_PseudoConstant::activityType( true, true );
        while ( $dao->fetch( ) ) {
            $row = array( );
            foreach ( $this->_tableFields as $name => $dontCare ) {
                if ( $name != 'activity_type_id' ) {
                    $row[$name] = $dao->$name;
                } else {
                    $row['activity_type'] = $activityTypes[$dao->$name];
                }
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


