<?php

ini_set( 'include_path', ".:../packages" );

require_once 'Smarty/Smarty.class.php';
require_once 'PHP/Beautifier.php';

function createDir( $dir, $perm = 0755 ) {
    if ( ! is_dir( $dir ) ) {
        mkdir( $dir, $perm, true );
    }
}

$smarty = new Smarty( );
$smarty->template_dir = './templates';
$smarty->compile_dir  = '/tmp/templates_c';

createDir( $smarty->compile_dir );

$file = 'schema/Schema.xml';

$codePath    = "./gen/";
$sqlCodePath = $codePath . "sql/";
$phpCodePath = '../';
// $phpCodePath = $codePath . "php/";

echo "Parsing input file $file\n";
$dbXML =& parseInput( $file );
// print_r( $dbXML );

echo "Extracting database information\n";
$database =& getDatabase( $dbXML );
// print_r( $database );

echo "Extracting table information\n";
$tables   =& getTables( $dbXML, $database );
// print_r( $tables );

$smarty->assign_by_ref( 'database', $database );
$smarty->assign_by_ref( 'tables'  , $tables   );

echo "Generating sql file\n";
$sql = $smarty->fetch( 'schema.tpl' );
createDir( $sqlCodePath );
$fd = fopen( $sqlCodePath . "Contacts.sql", "w" );
fputs( $fd, $sql );
fclose($fd);


$beautifier = new PHP_Beautifier(); // create a instance
$beautifier->addFilter('ArrayNested');
$beautifier->addFilter('Pear'); // add one or more filters
$beautifier->addFilter('NewLines', array( 'after' => 'class, public, require, comment' ) ); // add one or more filters
$beautifier->setIndentChar(' ');
$beautifier->setIndentNumber(4);
$beautifier->setNewLine("\n");

foreach ( array_keys( $tables ) as $name ) {
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

function convertName( $name, $skipDBPrefix = true, $pre = '', $post = '' ) {
    $names = explode( '_', strtolower($name) );
    
    $start = $skipDBPrefix ? 1 : 0;
    $fileName = '';
    for ( $i = $start; $i < count($names); $i++ ) {
        if ( strtolower( $names[$i] ) == 'im' ) {
            $names[$i] = 'IM';
        }
        $fileName .= ucfirst( $names[$i] );
    }
    return $pre . $fileName . $post;
}

function &parseInput( $file ) {
    $dom = DomDocument::load( $file );
    $dom->xinclude( );
    $dbXML = simplexml_import_dom( $dom );
    return $dbXML;
}

function &getDatabase( &$dbXML ) {
    $database = array( 'name' => trim( $dbXML->name ) );

    $attributes = '';
    checkAndAppend( $attributes, $dbXML, 'character_set', 'DEFAULT CHARACTER SET ', '' );
    checkAndAppend( $attributes, $dbXML, 'collate', 'COLLATE ', '' );
    $database['attributes'] = $attributes;

    $tableAttributes = '';
    checkAndAppend( $tableAttributes, $dbXML, 'table_type', 'ENGINE=', '' );
    $database['tableAttributes'] = trim( $tableAttributes . ' ' . $attributes );

    $database['comment'] = value( 'comment', $dbXML, '' );

    return $database;
}

function &getTables( &$dbXML, &$database ) {
    $tables = array();
    foreach ( $dbXML->table as $tableXML ) {
        getTable( $tableXML, $database, $tables );
    }

    return $tables;
}

function getTable( $tableXML, &$database, &$tables ) {
    $name  = trim($tableXML->name );
    $base  = value( 'base', $tableXML ) . '/DAO/';
    $pre   = str_replace( '/', '_', $base );
    $class = convertName( $name, true, $pre, '' );
    $table = array( 'name'       => $name,
                    'base'       => $base,
                    'fileName'   => convertName( $name, true, '', '.php' ),
                    'className'  => $class,
                    'attributes' => trim($database['tableAttributes']),
                    'comment'    => value( 'comment', $tableXML ) );
    
    $fields  = array( );
    foreach ( $tableXML->field as $fieldXML ) {
        getField( $fieldXML, $fields );
    }
    $table['fields' ] =& $fields;

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
    $name  = trim( $fieldXML->name );
    $field = array( 'name' => $name );
    
    $type = (string ) $fieldXML->type;
    switch ( $type ) {
    case 'varchar':
        $field['sqlType'] = 'varchar(' . $fieldXML->length . ')';
        $field['phpType'] = 'string';
        $field['crmType'] = 'CRM_Type::T_STRING';
        $field['length' ] = $fieldXML->length;
        $field['size'   ] = getSize($field['length']);
        break;

    case 'char':
        $field['sqlType'] = 'char(' . $fieldXML->length . ')';
        $field['phpType'] = 'string';
        $field['crmType'] = 'CRM_Type::T_STRING';
        $field['length' ] = $fieldXML->length;
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
        $field['crmType'] = 'CRM_Type::T_ENUM';
        break;

    default:
        $field['sqlType'] = $field['phpType'] = $type;
        if ( $type == 'int unsigned' ) {
            $field['crmType'] = 'CRM_Type::T_INT';
        } else {
            $field['crmType'] = 'CRM_Type::T_' . strtoupper( $type );
        }
        
        break;
    }

    $field['required'] = value( 'required', $fieldXML );
    $field['comment' ] = value( 'comment' , $fieldXML );
    $field['default' ] = value( 'default' , $fieldXML );

    $fields[$name] =& $field;
}

function getPrimaryKey( &$primaryXML, &$fields, &$table ) {
    $name = trim( $primaryXML->name );
    
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

function getIndex( &$indexXML, &$fields, &$indices ) {
    $name      = trim( $indexXML->name );
    $fieldName = trim( $indexXML->fieldName );

    /** need to make sure there is a field of type name */
    if ( ! array_key_exists( $fieldName, $fields ) ) {
        echo "index $name does not have a  field definition, ignoring\n";
        return;
    }

    $index = array( 'name'      => $name,
                    'fieldName' => $fieldName,
                    'unique'     => value( 'unique', $indexXML ) );
    $indices[$name] =& $index;
}

function getForeignKey( &$foreignXML, &$fields, &$foreignKeys ) {
    $name = trim( $foreignXML->name );
    
    /** need to make sure there is a field of type name */
    if ( ! array_key_exists( $name, $fields ) ) {
        echo "foreign $name does not have a  field definition, ignoring\n";
      return;
    }

    /** need to check for existence of table and key **/
    $foreignKey = array( 'name'       => $name,
                         'table'      => trim( value( 'table' , $foreignXML ) ),
                         'key'        => trim( value( 'key'   , $foreignXML ) ),
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
        return 'CRM_Form::TWO';
    } else if ( $maxLength <= 4 ) {
        return 'CRM_Form::FOUR';
    } else if ( $maxLength <= 8 ) {
        return 'CRM_Form::EIGHT';
    } else if ( $maxLength <= 16 ) {
        return 'CRM_Form::TWELVE';
    } else if ( $maxLength <= 32 ) {
        return 'CRM_Form::MEDIUM';
    } else if ( $maxLength <= 64 ) {
        return 'CRM_Form::BIG';
    } else {
        return 'CRM_Form::HUGE';
    }
}

?>

