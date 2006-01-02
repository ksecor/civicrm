<?php

ini_set( 'include_path', ".:../packages:.." );
ini_set( 'memory_limit', '16M'              );


$versionFile = "version.xml";
$versionXML = & parseInput( $versionFile );
$build_version = $versionXML->version_no;
if ($build_version < 1.1) {
    echo "The Database is not compatible for this version";
    exit();
}

if ( substr( phpversion( ), 0, 1 ) != 5 ) {
    echo phpversion( ) . ', ' . substr( phpversion( ), 0, 1 ) . "\n";
    echo "
CiviCRM requires a PHP Version >= 5
Please upgrade your php / webserver configuration
Alternatively you can get a version of CiviCRM that matches your PHP version
";
    exit( );
}

require_once 'Smarty/Smarty.class.php';
require_once 'PHP/Beautifier.php';

// for SQL l10n use
//define('CIVICRM_GETTEXT_RESOURCEDIR', '../l10n');
require_once '../civicrm.settings.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/I18n.php';
require_once 'CRM/Utils/Tree.php';

function createDir( $dir, $perm = 0755 ) {
    if ( ! is_dir( $dir ) ) {
        mkdir( $dir, $perm, true );
    }
}

$smarty =& new Smarty( );
$smarty->template_dir = './templates';

$compileDir = '/tmp/templates_c';
if (file_exists($compileDir)) {
    $oldTemplates = preg_grep('/tpl\.php$/', scandir($compileDir));
    foreach ($oldTemplates as $templateFile) {
        unlink($compileDir . '/' . $templateFile);
    }
}
$smarty->compile_dir = $compileDir;

createDir( $smarty->compile_dir );

$smarty->clear_all_cache();

$file = 'schema/Schema.xml';

$sqlCodePath = '../sql/';
$phpCodePath = '../';

echo "Parsing input file $file\n";
$dbXML =& parseInput( $file );
//print_r( $dbXML );

echo "Extracting database information\n";
$database =& getDatabase( $dbXML );
// print_r( $database );

$classNames = array( );

echo "Extracting table information\n";
$tables   =& getTables( $dbXML, $database );
resolveForeignKeys( $tables, $classNames );
$tables = orderTables( $tables );

//echo "\n\n\n\n\n*****************************************************************************\n\n";
// print_r($tables);
// exit(1);

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
        if(@!array_key_exists($v1['table'], $frTable[$value['name']])) {
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
$temp = '';
foreach($tree2 as $key => $val) {
    foreach($val as $k => $v) {
        $node =& $domainTree->findNode($v, $temp);
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

//$domainTree->display();
//exit();
$tempTree = $domainTree->getTree();

getDomainDump($tempTree['rootNode'], null, $frTable);
global $UNION_ARRAY;

$unionArray = $UNION_ARRAY;

$dd = fopen($sqlCodePath . "civicrm_backup.mysql", "w");
foreach ( $unionArray as $key => $val) {
    
    if (is_array($val)) {        
        $sql = implode(" UNION ", $val);
    }
    $write = $key."|".$sql."\n";
    fwrite($dd, $write);
}

fclose($dd);

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
        foreach($node['children'] as $key => $childNode) {
            $cNode =& $node['children'][$key];                
            getDomainDump($cNode, $nameArray, $frTable);      
        }    
    } 
}
//echo "\n\n***********************************************\n\n";


$smarty->assign_by_ref( 'database', $database );
$smarty->assign_by_ref( 'tables'  , $tables   );
$tmpArray = array_keys( $tables );
$tmpArray = array_reverse( $tmpArray );
$smarty->assign_by_ref( 'dropOrder', $tmpArray );
$smarty->assign( 'mysql', 'modern' );



echo "Generating sql file\n";
$sql = $smarty->fetch( 'schema.tpl' );

createDir( $sqlCodePath );
$fd = fopen( $sqlCodePath . "civicrm_41.mysql", "w" );
fputs( $fd, $sql );
fclose($fd);

// now generate the mysql4.0 version
$smarty->assign( 'mysql', 'simple' );
echo "Generating mysql 4.0 file\n";
$sql = $smarty->fetch( 'schema.tpl' );

createDir( $sqlCodePath );
$fd = fopen( $sqlCodePath . "civicrm_40.mysql", "w" );
fputs( $fd, $sql );
fclose($fd);



// write the civicrm data file fixing the domain
// id variable and translate the {ts}-tagged strings
$smarty->clear_all_assign();
$smarty->assign('civicrmDomainId', 1);
$smarty->assign('build_version',$build_version);

$config =& CRM_Core_Config::singleton();

$locales = preg_grep('/^[a-z][a-z]_[A-Z][A-Z]$/', scandir($config->gettextResourceDir));
if (!in_array('en_US', $locales)) array_unshift($locales, 'en_US');

foreach ($locales as $locale) {

    $config->lcMessages = $locale;
    $smarty->assign('locale', $locale);

    $data = '';
    $data .= $smarty->fetch('civicrm_country.tpl');
    $data .= $smarty->fetch('civicrm_state_province.tpl');
    $data .= $smarty->fetch('civicrm_data.tpl');

    // write the data file
    $filename = 'civicrm_data';
    if ($locale != 'en_US') $filename .= ".$locale";
    $filename .= '.mysql';
    $fd = fopen( $sqlCodePath . $filename, "w" );
    fputs( $fd, $data );
    fclose( $fd );

}



$sample = file_get_contents( $smarty->template_dir . '/civicrm_sample.tpl' );
$sample = str_replace( '%%CIVICRM_DOMAIN_ID%%', 1, $sample );
$fd = fopen( $sqlCodePath . "civicrm_sample.mysql", "w" );
fputs( $fd, $sample );
fclose( $fd );

$beautifier =& new PHP_Beautifier(); // create a instance
$beautifier->addFilter('ArrayNested');
$beautifier->addFilter('Pear'); // add one or more filters
$beautifier->addFilter('NewLines', array( 'after' => 'class, public, require, comment' ) ); // add one or more filters
$beautifier->setIndentChar(' ');
$beautifier->setIndentNumber(4);
$beautifier->setNewLine("\n");

foreach ( array_keys( $tables ) as $name ) {
    $smarty->clear_all_cache();
    echo "Generating $name as " . $tables[$name]['fileName'] . "\n";
    $smarty->clear_all_assign( );

    $smarty->assign_by_ref( 'table', $tables[$name] );
    $php = $smarty->fetch( 'dao.tpl' );

    $beautifier->setInputString( $php );
    
    if ( empty( $tables[$name]['base'] ) ) {
        echo "No base defined for $name, skipping output generation\n";
        continue;
    }

    $directory = $phpCodePath . $tables[$name]['base'];
    createDir( $directory );
    $beautifier->setOutputFile( $directory . $tables[$name]['fileName'] );
    $beautifier->process(); // required
    
    $beautifier->save( );
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
    checkAndAppend( $attributes, $dbXML, 'character_set', 'DEFAULT CHARACTER SET ', '' );
    checkAndAppend( $attributes, $dbXML, 'collate', 'COLLATE ', '' );
    $database['attributes'] = $attributes;

    
    $tableAttributes_modern = $tableAttributes_simple = '';
    checkAndAppend( $tableAttributes_modern, $dbXML, 'table_type', 'ENGINE=', '' );
    checkAndAppend( $tableAttributes_simple, $dbXML, 'table_type', 'TYPE=', '' );
    $database['tableAttributes_modern'] = trim( $tableAttributes_modern . ' ' . $attributes );
    $database['tableAttributes_simple'] = trim( $tableAttributes_simple );

    $database['comment'] = value( 'comment', $dbXML, '' );

    return $database;
}

function &getTables( &$dbXML, &$database ) {
    global $build_version ;
    $tables = array();
    foreach ( $dbXML->tables as $tablesXML ) {
        foreach ( $tablesXML->table as $tableXML ) {
            if ( value( 'drop', $tableXML, 0 ) > 0 and value( 'drop', $tableXML, 0 ) <= $build_version) {
                continue;
            }

            if ( value( 'add', $tableXML, 0 ) <= $build_version) {
                getTable( $tableXML, $database, $tables );
            }
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
        $tables[$name]['foreignKey'][$fkey]['fileName']  = str_replace( '_', '/', $classNames[$ftable] ) . '.php';
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
    global $build_version ;
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
        if ( value( 'drop', $fieldXML, 0 ) > 0 and value( 'drop', $fieldXML, 0 ) <= $build_version) {
            continue;
        }
        if ( value( 'add', $fieldXML, 0 ) <= $build_version) {
            getField( $fieldXML, $fields );
        }
    }

    $table['fields' ] =& $fields;
    // print_r($table['fields' ]);
    //Anil
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
            if ( value( 'drop', $indexXML, 0 ) > 0 and value( 'drop', $indexXML, 0 ) <= $build_version) { 
                continue; 
            } 

            getIndex( $indexXML, $fields, $index );
        }
        $table['index' ] =& $index;
    }

    if ( value( 'foreignKey', $tableXML ) ) {
        $foreign   = array( );
        foreach ( $tableXML->foreignKey as $foreignXML ) {
            // print_r($foreignXML);
            
            if ( value( 'drop', $foreignXML, 0 ) > 0 and value( 'drop', $foreignXML, 0 ) <= $build_version) {
                continue;
            }
            if ( value( 'add', $foreignXML, 0 ) <= $build_version) {
                getForeignKey( $foreignXML, $fields, $foreign );
            }
            
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

    case 'decimal':
        $field['sqlType'] = 'decimal(20,2)';
        $field['phpType'] = 'float';
        $field['crmType'] = 'CRM_Utils_Type::T_MONEY';
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
    if( value( 'export'  , $fieldXML )) {
        $field['export'  ]= value( 'export'  , $fieldXML );
    } else {
        $field['export'  ]= value( 'import'  , $fieldXML );
    }
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
                         'export'     => value( 'import', $foreignXML, false ),
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

