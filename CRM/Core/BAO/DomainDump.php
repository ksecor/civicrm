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

/** 
 *  this file contains functions for gender
 */

require_once 'DB.php';

class CRM_Core_BAO_DomainDump  
{
    static function backupData ( ) 
    {
        $file = 'modules/civicrm/xml/schema/Schema.xml';
        
        $dbXML = self::parseInput( $file );
        // print_r( $dbXML );
        
        $database = self::getDatabase( $dbXML );
        // print_r( $database );
        
        $classNames = array( );
        
        $tables   =& self::getTables( $dbXML, $database );
        self::resolveForeignKeys( $tables, $classNames );
        $tables = self::orderTables( $tables );
        
        $tree1 = array();
        
        foreach ($tables as $k => $v) {
            $tableName = $k;
            $tree1[$tableName] = array();
            
            if(!isset($v['foreignKey'])) {
                continue;
            }
            foreach ($v['foreignKey'] as $k1 => $v1) {
                if ( !in_array($v1['table'], $tree1[$tableName]) ) {
                    $tree1[$tableName][] = $v1['table'];
                }
            }
        }
        
        //create a foregin key link table
        $frTable = array();
        foreach ($tables as $key => $value) {
            if(!isset($value['foreignKey'])) {
                continue;
            }
            
            foreach ($value['foreignKey'] as $k1 => $v1) {
                if ( is_array($frTable[$value['name']]) ) {
                    if ( !array_key_exists($v1['table'], $frTable[$value['name']])) {
                        $frTable[$value['name']][$v1['table']] = $v1['name'];
                    }
                } else {
                    $frTable[$value['name']][$v1['table']] = $v1['name'];
                }
            }
        }

        $tree2 = array();
        foreach ($tree1 as $k => $v) {
            foreach ($v as $k1 => $v1) {
                if (!isset($tree2[$v1])) {
                    $tree2[$v1] = array();
                }
                if ( !array_key_exists($k, $tree2[$v1]) ) {
                    if ( $v1 != $k)
                        $tree2[$v1][] = $k;
                }
            }
        }
        
        //create the domain tree
        $domainTree =& new CRM_Utils_Tree('civicrm_domain');
        
        foreach($tree2 as $key => $val) {
            foreach($val as $k => $v) {
                $node =& $domainTree->findNode($v);
                if(!$node) {
                    $node =& $domainTree->createNode($v);            
                }
                $domainTree->addNode($key, $node);               
            }
        }
        
        foreach($frTable as $key => $val) {
            foreach($val as $k => $v ) {
                $fKey = $frTable[$key];
                $domainTree->addData($k, $key, $fKey);
            }
        }
        
        
        $tempTree = $domainTree->getTree();
        
        self::getDomainDump($tempTree['rootNode'], null, $frTable);
        global $UNION_ARRAY;
        
        $unionArray = $UNION_ARRAY;
        
        // get the path of mysqldump
        $tempPath = exec('whereis mysqldump');
        list ($temp, $mysqlDumpPath) = explode(":", $tempPath);
        
        //we get the upload folder for storing the huge backup data
        $config =& new CRM_Core_Config();
        
        $fileName = $config->uploadDir.'domainDump.sql';

        foreach ( $unionArray as $key => $val) {
            $tableName = $key;
            
            if (is_array($val)) {        
                $sql = implode(" UNION ", $val);
            }
            
            $dao =& new CRM_Core_DAO();
            $query = $dao->query($sql);
            
            $ids = array();
            while ( $dao->fetch(  ) ) {
                $ids[] = $dao->id; 
            }
            
            //$fileName = $BACKUP_PATH.$tableName.".sql";
            //$fileName = '/tmp/domainDump.sql';
            
            if ( !empty($ids) ) {
                
                //$dumpCommand = "mysqldump  ".$MYSQL_USER." --opt --single-transaction  civicrm ". $key ." -w 'id IN ( ".implode(",", $ids)." ) ' > " . $fileName;
                
                $dumpCommand = $mysqlDumpPath."  -ucivicrm -pMt\!Everest --opt --single-transaction  civicrm ". $key ." -w 'id IN ( ".implode(",", $ids)." ) ' >> " . $fileName;

                //echo "<br><br>";
                exec($dumpCommand); 
            } 
        }

        //exit(1);
        CRM_Core_Session::setStatus( ts('Backup Database completed.') );
        CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/admin', 'reset=1' ) );
        
    }

    function getDomainDump( &$tree, $nameArray, $frTable )
    {
        if ( !isset($nameArray) ) {
            $nameArray = array();
        }
        
        //bad hack 
        if ( !isset($UNION_ARRAY) ) {
            global $UNION_ARRAY;
        }
        
        $config =& CRM_Core_Config::singleton( );
        
        //global $DOMAIN_ID;
        
        $node = $tree;

        $nameArray[] = $node['name'];
        $tempNameArray = array_reverse($nameArray);
        
        $table = array();
        for ($idx = 0; $idx<count($nameArray); $idx++) {
            $table[] = $nameArray[$idx];
        }
    
        if ( $tempNameArray[0] != 'civicrm_activity_history' ) { 
            $tables = implode(",", $table);
            for ($idx = 0; $idx<count($nameArray)-1; $idx++) {
                $foreignKey = $tempNameArray[$idx+1];
                $whereCondition[] = "". $tempNameArray[$idx] .".". $frTable[$tempNameArray[$idx]][$foreignKey] ." = ".$tempNameArray[$idx+1].".id";
            } 
            $whereCondition[] = "civicrm_domain.id = ".$config->domainID();    
        } else {
            $tables = ' civicrm_domain, civicrm_contact, civicrm_activity_history';
            $whereCondition[] = "". $tempNameArray[0] .".entity_id = civicrm_contact.id AND civicrm_contact.domain_id = civicrm_domain.id AND civicrm_domain.id = 1 ";
        }
        
        $whereClause = implode(" AND ", $whereCondition);
        
        //store the queries traversed thru different path
        $sql = 'SELECT '. $tempNameArray[0] .'.id FROM '. $tables .' WHERE '. $whereClause ;       
        
        $UNION_ARRAY[$tempNameArray[0]][] = $sql;
        //$unionArray[$tempNameArray[0]][] = $sql;
        
        
        
        if ( !empty($node['children']) ) {
            foreach($node['children'] as &$childNode) {
                self::getDomainDump($childNode, $nameArray, $frTable, $domainId);      
            }    
        } 

        //return $unionArray;
    }
    
    
    function &parseInput( $file ) {
        $dom = DomDocument::load( $file );
        $dom->xinclude( );
        $dbXML = simplexml_import_dom( $dom );
        return $dbXML;
    }
    
    function &getDatabase( &$dbXML ) {
        $database = array( 'name' => trim( (string ) $dbXML->name ) );
        
        $attributes = '';
        self::checkAndAppend( $attributes, $dbXML, 'character_set', 'DEFAULT CHARACTER SET ', '' );
        self::checkAndAppend( $attributes, $dbXML, 'collate', 'COLLATE ', '' );
        $database['attributes'] = $attributes;
        
        
        $tableAttributes = '';
        self::checkAndAppend( $tableAttributes, $dbXML, 'table_type', 'ENGINE=', '' );
        //$database['tableAttributes'] = trim( $tableAttributes . ' ' . $attributes );
        $database['tableAttributes_modern'] = trim( $tableAttributes . ' ' . $attributes );
        $database['tableAttributes_simple'] = trim( $tableAttributes );
        
        $database['comment'] = self::value( 'comment', $dbXML, '' );
        
        return $database;
    }
    
    function &getTables( &$dbXML, &$database ) {
        $tables = array();
        foreach ( $dbXML->tables as $tablesXML ) {
            foreach ( $tablesXML->table as $tableXML ) {
                self::getTable( $tableXML, $database, $tables );
            }
        }
        
        return $tables;
    }
    
    function resolveForeignKeys( &$tables, &$classNames ) {
        foreach ( array_keys( $tables ) as $name ) {
            self::resolveForeignKey( $tables, $classNames, $name );
        }
    }
    
    function resolveForeignKey( &$tables, &$classNames, $name ) {
        if ( ! array_key_exists( 'foreignKey', $tables[$name] ) ) {
            return;
        }
        
        foreach ( array_keys( $tables[$name]['foreignKey'] ) as $fkey ) {
            $ftable = $tables[$name]['foreignKey'][$fkey]['table'];
            if ( ! array_key_exists( $ftable, $classNames ) ) {
                //echo "$ftable is not a valid foreign key table in $name <br>";
                continue;
            }
            $tables[$name]['foreignKey'][$fkey]['className'] = $classNames[$ftable];
        }
        
    }
    
    function orderTables( &$tables ) {
        $ordered = array( );
        
        while ( ! empty( $tables ) ) {
            foreach ( array_keys( $tables ) as $name ) {
                if ( self::validTable( $tables, $ordered, $name ) ) {
                    $ordered[$name] = $tables[$name];
                    unset( $tables[$name] );
                }
            }
        }
        return $ordered;
        
    }
    
    function validTable( &$tables, &$valid, $name ) {
        if ( ! array_key_exists( 'foreignKey', $tables[$name] ) ) {
            return true;
        }
        
        foreach ( array_keys( $tables[$name]['foreignKey'] ) as $fkey ) {
            $ftable = $tables[$name]['foreignKey'][$fkey]['table'];
            if ( ! array_key_exists( $ftable, $valid ) && $ftable !== $name ) {
                return false;
            }
        }
        return true;
    }
    
    function getTable( $tableXML, &$database, &$tables ) {
        global $classNames;
        
        $name  = trim((string ) $tableXML->name );
        $klass = trim((string ) $tableXML->class );
        $base  = self::value( 'base', $tableXML ) . '/DAO/';
        $pre   = str_replace( '/', '_', $base );
        $classNames[$name]  = $pre . $klass;
    
        $table = array( 'name'       => $name,
                        'base'       => $base,
                        'fileName'   => $klass . '.php',
                        'objectName' => $klass,
                        'labelName'  => substr($name, 8),
                        'className'  => $classNames[$name],
                        //'attributes' => trim($database['tableAttributes']),
                        'attributes_simple' => trim($database['tableAttributes_simple']),
                        'attributes_modern' => trim($database['tableAttributes_modern']),
                        'comment'    => self::value( 'comment', $tableXML ) );
        
        $fields  = array( );
        foreach ( $tableXML->field as $fieldXML ) {
            self::getField( $fieldXML, $fields );
        }
        $table['fields' ] =& $fields;
        
        $table['hasEnum'] = false;
        foreach ($table['fields'] as $field) {
            if ($field['crmType'] == 'CRM_Utils_Type::T_ENUM') {
                $table['hasEnum'] = true;
                break;
            }
        }
        
        if ( self::value( 'primaryKey', $tableXML ) ) {
            self::getPrimaryKey( $tableXML->primaryKey, $fields, $table );
        }
        
        if ( self::value( 'index', $tableXML ) ) {
            $index   = array( );
            foreach ( $tableXML->index as $indexXML ) {
                self::getIndex( $indexXML, $fields, $index );
            }
            $table['index' ] =& $index;
        }
        
        if ( self::value( 'foreignKey', $tableXML ) ) {
            $foreign   = array( );
            foreach ( $tableXML->foreignKey as $foreignXML ) {
                self::getForeignKey( $foreignXML, $fields, $foreign );
            }
            $table['foreignKey' ] =& $foreign;
        }
        
        $tables[$name] =& $table;
        return;
    }
    
    function getField( &$fieldXML, &$fields ) {
        $name  = trim( (string ) $fieldXML->name );
        $field = array( 'name' => $name );
        
        $type = (string ) $fieldXML->type;
        switch ( $type ) {
        case 'varchar':
            $field['sqlType'] = 'varchar(' . (int ) $fieldXML->length . ')';
            $field['phpType'] = 'string';
            $field['crmType'] = 'CRM_Utils_Type::T_STRING';
            $field['length' ] = (int ) $fieldXML->length;
            $field['size'   ] = self::getSize($field['length']);
            break;
        
        case 'char':
            $field['sqlType'] = 'char(' . (int ) $fieldXML->length . ')';
            $field['phpType'] = 'string';
            $field['crmType'] = 'CRM_Utils_Type::T_STRING';
            $field['length' ] = (int ) $fieldXML->length;
            $field['size'   ] = self::getSize($field['length']);
            break;
            
        case 'enum':
            $value = (string ) $fieldXML->values;
            $field['sqlType'] = 'enum(';
            $field['values']  = array( );
            $values = explode( ',', $value );
            $first = true;
            foreach ( $values as $v ) {
                $v = trim($v);
                $field['values'][]  = $v;
                
                if ( ! $first ) {
                    $field['sqlType'] .= ', ';
                }
                $first = false;
                $field['sqlType'] .= "'$v'";
            }
            $field['sqlType'] .= ')';
            $field['phpType'] = $field['sqlType'];
            $field['crmType'] = 'CRM_Utils_Type::T_ENUM';
            break;
            
        case 'text':
            $field['sqlType'] = $field['phpType'] = $type;
            $field['crmType'] = 'CRM_Utils_Type::T_' . strtoupper( $type );
            $field['rows']    = self::value( 'rows', $fieldXML );
            $field['cols']    = self::value( 'cols', $fieldXML );
            break;
            
        case 'datetime':
            $field['sqlType'] = $field['phpType'] = $type;
            $field['crmType'] = 'CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME';
            break;
            
        case 'boolean':
            // need this case since some versions of mysql do not have boolean as a valid column type and hence it
            // is changed to tinyint. hopefully after 2 yrs this case can be removed.
            $field['sqlType'] = 'tinyint';
            $field['phpType'] = $type;
            $field['crmType'] = 'CRM_Utils_Type::T_' . strtoupper($type);
            break;
            
        default:
            $field['sqlType'] = $field['phpType'] = $type;
            if ( $type == 'int unsigned' ) {
                $field['crmType'] = 'CRM_Utils_Type::T_INT';
            } else {
                $field['crmType'] = 'CRM_Utils_Type::T_' . strtoupper( $type );
            }
            
            break;
        }
        
        $field['required'] = self::value( 'required', $fieldXML );
        $field['comment' ] = self::value( 'comment' , $fieldXML );
        $field['default' ] = self::value( 'default' , $fieldXML );
        $field['import'  ] = self::value( 'import'  , $fieldXML );
        $field['rule'    ] = self::value( 'rule'    , $fieldXML );
        $field['title'   ] = self::value( 'title'   , $fieldXML );
        if ( ! $field['title'] ) {
            $field['title'] = self::composeTitle( $name );
        }
        $field['headerPattern'] = self::value( 'headerPattern', $fieldXML );
        $field['dataPattern'] = self::value( 'dataPattern', $fieldXML );
        
        $fields[$name] =& $field;
    }
    
    function composeTitle( $name ) {
        $names = explode( '_', strtolower($name) );
        $title = '';
        for ( $i = 0; $i < count($names); $i++ ) {
            if ( $names[$i] === 'id' || $names[$i] === 'is' ) {
                // id's do not get titles
                return null;
            }
            
            if ( $names[$i] === 'im' ) {
                $names[$i] = 'IM';
            } else {
                $names[$i] = ucfirst( trim($names[$i]) );
            }
            
            $title = $title . ' ' . $names[$i];
        }
        return trim($title);
    }
    
    function getPrimaryKey( &$primaryXML, &$fields, &$table ) {
        $name = trim( (string ) $primaryXML->name );
        
        /** need to make sure there is a field of type name */
        if ( ! array_key_exists( $name, $fields ) ) {
            echo "primary key $name does not have a  field definition, ignoring\n";
            return;
        }
        
        // set the autoincrement property of the field
        $auto = self::value( 'autoincrement', $primaryXML );
        $fields[$name]['autoincrement'] = $auto;
        $primaryKey = array( 'name'          => $name,
                             'autoincrement' => $auto );
        $table['primaryKey'] =& $primaryKey;
    }
    
    function getIndex(&$indexXML, &$fields, &$indices)
    {
        //echo "\n\n*******************************************************\n";
        //echo "entering getIndex\n";
        
        $index = array();
        $indexName = trim((string)$indexXML->name);   // empty index name is fine
        $index['name'] = $indexName;
        $index['field'] = array();
        
        // populate fields
        foreach ($indexXML->fieldName as $v) {
            $fieldName = (string)($v);
            $index['field'][] = $fieldName;
        }
        
        // check for unique index
        if (self::value('unique', $indexXML)) {
            $index['unique'] = true;
        }
        
        //echo "\$index = \n";
        //print_r($index);
        
        // field array cannot be empty
        if (empty($index['field'])) {
            echo "No fields defined for index $indexName\n";
            return;
        }
        
        // all fieldnames have to be defined and should exist in schema.
        foreach ($index['field'] as $fieldName) {
            if (!$fieldName) {
                echo "Invalid field defination for index $indexName\n";
                return;
            }
            if (!array_key_exists($fieldName, $fields)) {
                echo "Table does not contain $fieldName\n";
                print_r( $fields );
                exit( );
                return;
            }
        }
        $indices[$indexName] =& $index;
    }
    
    
    function getForeignKey( &$foreignXML, &$fields, &$foreignKeys ) {
        $name = trim( (string ) $foreignXML->name );
        
        /** need to make sure there is a field of type name */
        if ( ! array_key_exists( $name, $fields ) ) {
            echo "foreign $name does not have a  field definition, ignoring\n";
            return;
        }
        
        /** need to check for existence of table and key **/
        global $classNames;
        $table = trim( self::value( 'table' , $foreignXML ) );
        $foreignKey = array( 'name'       => $name,
                             'table'      => $table,
                             'key'        => trim( self::value( 'key'   , $foreignXML ) ),
                             'import'     => self::value( 'import', $foreignXML, false ),
                             'className'  => null, // we do this matching in a seperate phase (resolveForeignKeys)
                             'attributes' => trim( self::value( 'attributes', $foreignXML, 'ON DELETE CASCADE' ) ),
                             );
        $foreignKeys[$name] =& $foreignKey;
    }
    
    function value( $key, &$object, $default = null ) {
        if ( isset( $object->$key ) ) {
            return (string ) $object->$key;
        }
        return $default;
    }
    
    function checkAndAppend( &$attributes, &$object, $name, $pre = null, $post = null ) {
        if ( ! isset( $object->$name ) ) {
            return;
        }
        
        $value = $pre . trim($object->$name) . $post;
        self::append( $attributes, ' ', trim($value) );
        
    }
    
    function append( &$str, $delim, $name ) {
        if ( empty( $name ) ) {
            return;
        }
        
        if ( is_array( $name ) ) {
            foreach ( $name as $n ) {
                if ( empty( $n ) ) {
                    continue;
                }
                if ( empty( $str ) ) {
                    $str = $n;
                } else {
                    $str .= $delim . $n;
                }
            }
        } else {
            if ( empty( $str ) ) {
                $str = $name;
            } else {
                $str .= $delim . $name;
            }
        }
    }
    
    /**
     * four
     * eight
     * twelve
     * sixteen
     * medium (20)
     * big (30)
     * huge (45)
     */
    
    function getSize( $maxLength ) {
        if ( $maxLength <= 2 ) {
            return 'CRM_Utils_Type::TWO';
        } 
        if ( $maxLength <= 4 ) {
            return 'CRM_Utils_Type::FOUR';
        } 
        if ( $maxLength <= 8 ) {
            return 'CRM_Utils_Type::EIGHT';
        } 
        if ( $maxLength <= 16 ) {
            return 'CRM_Utils_Type::TWELVE';
        } 
        if ( $maxLength <= 32 ) {
            return 'CRM_Utils_Type::MEDIUM';
        } 
        if ( $maxLength <= 64 ) {
            return 'CRM_Utils_Type::BIG';
        } 
        return 'CRM_Utils_Type::HUGE';
    }



}

?>
