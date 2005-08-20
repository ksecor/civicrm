<?php

ini_set( 'include_path', ".:../packages" );

if ( substr( phpversion( ), 0, 1 ) != 5 ) {
    echo phpversion( ) . ', ' . substr( phpversion( ), 0, 1 ) . "\n";
    echo "
CiviCRM requires a PHP Version >= 5
Please upgrade your php / webserver configuration
Alternatively you can get a version of CiviCRM that matches your PHP version
";
    exit( );
}

require_once '../modules/config.inc.php';
require_once('DB.php');
require_once 'CRM/Core/Config.php';
require_once 'CRM/Utils/Tree.php';
require_once 'CRM/Core/Error.php';

$dsn_domain  = "mysql://civicrm:Mt!Everest@localhost/civicrm";

$db_domain = DB::connect($dsn_domain);
if ( DB::isError( $db_domain ) ) {
    die( "Cannot connect to civicrm db via $dsn, " . $db_domain->getMessage( ) );
}

$file = 'schema/Schema.xml';

$sqlCodePath = '../sql/';
$phpCodePath = '../';

$dbXML =& parseInput( $file );
// print_r( $dbXML );

$database =& getDatabase( $dbXML );
// print_r( $database );

$classNames = array( );

$tables   =& getTables( $dbXML, $database );
resolveForeignKeys( $tables, $classNames );
$tables = orderTables( $tables );

//echo "\n\n\n\n\n*****************************************************************************\n\n";
//print_r($tables);

$tree1 = array();

foreach ($tables as $k => $v) {
    $tableName = $k;
    $tree1[$tableName] = array();

    if(!isset($v['foreignKey'])) {
        continue;
    }
    foreach ($v['foreignKey'] as $k1 => $v1) {
        if ( !in_array($v1['table'], $tree1[$tableName]) )
            $tree1[$tableName][] = $v1['table'];
    }
}

$frTable = array();
foreach ($tables as $key => $value) {
    if(!isset($value['foreignKey'])) {
        continue;
    }

    foreach ($value['foreignKey'] as $k1 => $v1) {
        $frTable[$value['name']][$v1['table']] = $v1['name'];
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

//print_r($tree2);
//print_r($frTable);

$domainTree =& new CRM_Utils_Tree('civicrm_domain');

foreach($tree2 as $key => $val) {
    foreach($val as $k => $v) {
        $node =& $domainTree->createNode($v);            
        $domainTree->addNode($key, $node);
        $fKey = $frTable[$v][$key];
        $domainTree->addData($v, $fKey);
        
    }
}


//$domainTree->display();

$tempTree = $domainTree->getTree();

leafIter($tempTree['rootNode'], null, null);

function leafIter(&$tree, $nameArray, $fKeyArray)
{
    if ( !isset($nameArray) ) {
        $nameArray = array();
        $fKeyArray = array();
    }

    $node = $tree;
    
    if ( count($node['children']) ) {
        
        $nameArray[] = $node['name'];
        $fKeyArray[] = $node['data']['fKey'];
        $tempNameArray = array_reverse($nameArray);
        $tempFKeyArray = array_reverse($fKeyArray);
        
        
        $table = array();
        for ($idx = 0; $idx<count($tempNameArray); $idx++) {
            $table[] = $tempNameArray[$idx];
        }
        
        $tables = implode(" ,",$table);
        
        for ($idx = 0; $idx<count($nameArray)-1; $idx++) {
            $whereCondition[] = "". $nameArray[$idx] .".". $fKeyArray[$idx] ." = ".$nameArray[$idx+1].".id";
        }
        
        $whereCondition[] = "".$nameArray[$idx].".id = 1";
        
        $whereClause = implode(" AND ", $whereCondition);
        
        $strQuery = 'SELECT '. $table[0] .'.* INTO OUTFILE "/tmp/'. $table[0] .'.sql" FROM '. $tables .' WHERE '. $whereClause ;

        $command = "mysql -uroot civicrm -e '".$strQuery."'";

        //echo $command."\n\n";

        system($command);

    } else {
        $nameArray[] = $node['name'];
        $fKeyArray[] = $node['data']['fKey'];
        $nameArray = array_reverse($nameArray);
        $fKeyArray = array_reverse($fKeyArray);
        
        
        $table = array();
        for ($idx = 0; $idx<count($nameArray); $idx++) {
            $table[] = $nameArray[$idx];
        }
        
        $tables = implode(" ,",$table);
        
        for ($idx = 0; $idx<count($nameArray)-1; $idx++) {
            $whereCondition[] = "". $nameArray[$idx] .".". $fKeyArray[$idx] ." = ".$nameArray[$idx+1].".id";
        }
        
        $whereCondition[] = "".$nameArray[$idx].".id = 1";
        
        $whereClause = implode(" AND ", $whereCondition);
        
        $strQuery = 'SELECT '. $table[0] .'.* INTO OUTFILE "/tmp/'. $table[0] .'.sql" FROM '. $tables .' WHERE '. $whereClause ;

        $command = "mysql -uroot civicrm -e '".$strQuery."'";

        //echo $command."\n\n";

        system($command);
    }
   

    foreach($node['children'] as &$childNode) {
        
        leafIter($childNode, $nameArray, $fKeyArray);      
    }    
}


exit(1);

//-------------------------------------------------------------


function &parseInput( $file ) {
    $dom = DomDocument::load( $file );
    $dom->xinclude( );
    $dbXML = simplexml_import_dom( $dom );
    return $dbXML;
}

function &getDatabase( &$dbXML ) {
    $database = array( 'name' => trim( (string ) $dbXML->name ) );

    $attributes = '';
    checkAndAppend( $attributes, $dbXML, 'character_set', 'DEFAULT CHARACTER SET ', '' );
    checkAndAppend( $attributes, $dbXML, 'collate', 'COLLATE ', '' );
    $database['attributes'] = $attributes;

    
    $tableAttributes = '';
    checkAndAppend( $tableAttributes, $dbXML, 'table_type', 'ENGINE=', '' );
    //$database['tableAttributes'] = trim( $tableAttributes . ' ' . $attributes );
    $database['tableAttributes_modern'] = trim( $tableAttributes . ' ' . $attributes );
    $database['tableAttributes_simple'] = trim( $tableAttributes );

    $database['comment'] = value( 'comment', $dbXML, '' );

    return $database;
}

function &getTables( &$dbXML, &$database ) {
    $tables = array();
    foreach ( $dbXML->tables as $tablesXML ) {
        foreach ( $tablesXML->table as $tableXML ) {
            getTable( $tableXML, $database, $tables );
        }
    }

    return $tables;
}

function resolveForeignKeys( &$tables, &$classNames ) {
    foreach ( array_keys( $tables ) as $name ) {
        resolveForeignKey( $tables, $classNames, $name );
    }
}

function resolveForeignKey( &$tables, &$classNames, $name ) {
    if ( ! array_key_exists( 'foreignKey', $tables[$name] ) ) {
        return;
    }
    
    foreach ( array_keys( $tables[$name]['foreignKey'] ) as $fkey ) {
        $ftable = $tables[$name]['foreignKey'][$fkey]['table'];
        if ( ! array_key_exists( $ftable, $classNames ) ) {
            echo "$ftable is not a valid foreign key table in $name";
            continue;
        }
        $tables[$name]['foreignKey'][$fkey]['className'] = $classNames[$ftable];
    }
    
}

function orderTables( &$tables ) {
    $ordered = array( );

    while ( ! empty( $tables ) ) {
        foreach ( array_keys( $tables ) as $name ) {
            if ( validTable( $tables, $ordered, $name ) ) {
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
    $base  = value( 'base', $tableXML ) . '/DAO/';
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
                    'comment'    => value( 'comment', $tableXML ) );
    
    $fields  = array( );
    foreach ( $tableXML->field as $fieldXML ) {
        getField( $fieldXML, $fields );
    }
    $table['fields' ] =& $fields;
    
    $table['hasEnum'] = false;
    foreach ($table['fields'] as $field) {
        if ($field['crmType'] == 'CRM_Utils_Type::T_ENUM') {
            $table['hasEnum'] = true;
            break;
        }
    }

    if ( value( 'primaryKey', $tableXML ) ) {
        getPrimaryKey( $tableXML->primaryKey, $fields, $table );
    }

    if ( value( 'index', $tableXML ) ) {
        $index   = array( );
        foreach ( $tableXML->index as $indexXML ) {
            getIndex( $indexXML, $fields, $index );
        }
        $table['index' ] =& $index;
    }

    if ( value( 'foreignKey', $tableXML ) ) {
        $foreign   = array( );
        foreach ( $tableXML->foreignKey as $foreignXML ) {
            getForeignKey( $foreignXML, $fields, $foreign );
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
        $field['size'   ] = getSize($field['length']);
        break;

    case 'char':
        $field['sqlType'] = 'char(' . (int ) $fieldXML->length . ')';
        $field['phpType'] = 'string';
        $field['crmType'] = 'CRM_Utils_Type::T_STRING';
        $field['length' ] = (int ) $fieldXML->length;
        $field['size'   ] = getSize($field['length']);
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
        $field['rows']    = value( 'rows', $fieldXML );
        $field['cols']    = value( 'cols', $fieldXML );
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

    $field['required'] = value( 'required', $fieldXML );
    $field['comment' ] = value( 'comment' , $fieldXML );
    $field['default' ] = value( 'default' , $fieldXML );
    $field['import'  ] = value( 'import'  , $fieldXML );
    $field['rule'    ] = value( 'rule'    , $fieldXML );
    $field['title'   ] = value( 'title'   , $fieldXML );
    if ( ! $field['title'] ) {
        $field['title'] = composeTitle( $name );
    }
    $field['headerPattern'] = value( 'headerPattern', $fieldXML );
    $field['dataPattern'] = value( 'dataPattern', $fieldXML );

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
    $auto = value( 'autoincrement', $primaryXML );
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
    if (value('unique', $indexXML)) {
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
    $table = trim( value( 'table' , $foreignXML ) );
    $foreignKey = array( 'name'       => $name,
                         'table'      => $table,
                         'key'        => trim( value( 'key'   , $foreignXML ) ),
                         'import'     => value( 'import', $foreignXML, false ),
                         'className'  => null, // we do this matching in a seperate phase (resolveForeignKeys)
                         'attributes' => trim( value( 'attributes', $foreignXML, 'ON DELETE CASCADE' ) ),
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
    append( $attributes, ' ', trim($value) );
        
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

?>
