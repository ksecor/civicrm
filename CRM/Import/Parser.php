<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/


require_once 'CRM/Utils/String.php';
require_once 'CRM/Utils/Type.php';

require_once 'CRM/Import/Field.php';

abstract class CRM_Import_Parser {

    const
        MAX_ERRORS      = 25,
        MAX_WARNINGS    = 25,
        VALID           =  1,
        WARNING         =  2,
        ERROR           =  4,
        CONFLICT        =  8,
        STOP            = 16,
        DUPLICATE       = 32,
        MULTIPLE_DUPE   = 64;

    /**
     * various parser modes
     */
    const
        MODE_MAPFIELD = 1,
        MODE_PREVIEW  = 2,
        MODE_SUMMARY  = 4,
        MODE_IMPORT   = 8;

    /**
     * codes for duplicate record handling
     */
    const
        DUPLICATE_SKIP = 1,
        DUPLICATE_REPLACE = 2,
        DUPLICATE_UPDATE = 4,
        DUPLICATE_FILL = 8;

    protected $_fileName;

    /**#@+
     * @access protected
     * @var integer
     */

    /**
     * imported file size
     */
    protected $_fileSize;

    /**
     * seperator being used
     */
    protected $_seperator;

    /**
     * total number of lines in file
     */
    protected $_lineCount;

    /**
     * total number of non empty lines
     */
    protected $_totalCount;

    /**
     * running total number of valid lines
     */
    protected $_validCount;

    /**
     * running total number of invalid rows
     */
    protected $_invalidRowCount;

    /**
     * maximum number of invalid rows to store
     */
    protected $_maxErrorCount;

    /**
     * array of error lines, bounded by MAX_ERROR
     */
    protected $_errors;

    /**
     * total number of conflict lines
     */
    protected $_conflictCount;

    /**
     * array of conflict lines
     */
    protected $_conflicts;

    /**
     * total number of duplicate (from database) lines
     */
    protected $_duplicateCount;

    /**
     * array of duplicate lines
     */
    protected $_duplicates;

    /**
     * running total number of warnings
     */
    protected $_warningCount;

    /**
     * maximum number of warnings to store
     */
    protected $_maxWarningCount = self::MAX_WARNINGS;

    /**
     * array of warning lines, bounded by MAX_WARNING
     */
    protected $_warnings;

    /**
     * array of all the fields that could potentially be part
     * of this import process
     * @var array
     */
    protected $_fields;

    /**
     * array of the fields that are actually part of the import process
     * the position in the array also dictates their position in the import
     * file
     * @var array
     */
    protected $_activeFields;

    /**
     * array to keep track of the ids of custom fields
     *
     * @var array
     */
    protected $_customMap;


    /**
     * cache the count of active fields
     *
     * @var int
     */
    protected $_activeFieldCount;

    /**
     * maximum number of non-empty/comment lines to process
     *
     * @var int
     */
    protected $_maxLinesToProcess;

    /**
     * cache of preview rows
     *
     * @var array
     */
    protected $_rows;


    /**
     * filename of error data
     *
     * @var string
     */
    protected $_errorFileName;


    /**
     * filename of conflict data
     *
     * @var string
     */
    protected $_conflictFileName;


    /**
     * filename of duplicate data
     *
     * @var string
     */
    protected $_duplicateFileName;

    function __construct() {
        $this->_maxLinesToProcess = 0;
        $this->_maxErrorCount = self::MAX_ERRORS;
    }

    abstract function init();

    function run( $fileName,
                  $seperator = ',',
                  &$mapper,
                  $skipColumnHeader = false,
                  $mode = self::MODE_PREVIEW,
                  $onDuplicate = self::DUPLICATE_SKIP) {
        $this->init();

        $this->_seperator = $seperator;

        $fd = fopen( $fileName, "r" );
        if ( ! $fd ) {
            return false;
        }

        $this->_lineCount  = $this->_warningCount   = 0;
        $this->_invalidRowCount = $this->_validCount     = 0;
        $this->_totalCount = $this->_conflictCount = 0;
    
        $this->_errors   = array();
        $this->_warnings = array();
        $this->_conflicts = array();

        $this->_fileSize = number_format( filesize( $fileName ) / 1024.0, 2 );
        
        if ( $mode == self::MODE_MAPFIELD ) {
            $this->_rows = array( );
        } else {
            $this->_activeFieldCount = count( $this->_activeFields );
        }

        if ( $mode == self::MODE_IMPORT ) {
            //get the key of email field
            foreach($mapper as $key => $value) {
                if ( strtolower($value) == 'email' ) {
                    $emailKey = $key;
                    break;
                }
            }
        }
        
//         $email = array();
        while ( ! feof( $fd ) ) {
            $this->_lineCount++;

            $values = fgetcsv( $fd, 8192, $seperator );

            self::encloseScrub($values);

            // skip column header if we're not in mapfield mode
            if ( $mode != self::MODE_MAPFIELD && $skipColumnHeader ) {
                    $skipColumnHeader = false;
                    continue;
            }

// XXX:    2005-06-27 17:01:13 by Brian McFee <brmcfee@gmail.com>
// What the hell was this block of code for? 
//
//             if ( $mode == self::MODE_IMPORT ) {
//                 if ( in_array($values[$emailKey], $email)) {
//                     continue;
//                 } else {
//                     array_push($email, $values[$emailKey]);
//                 }
//             }
            
            if ( ! $values || empty( $values ) ) {
                continue;
            }

            if ( $mode != self::MODE_IMPORT ) {
                $this->_totalCount++;
            }

            if ( $mode == self::MODE_MAPFIELD ) {
                $returnCode = $this->mapField( $values );
            } else if ( $mode == self::MODE_PREVIEW ) {
                $returnCode = $this->preview( $values );
            } else if ( $mode == self::MODE_SUMMARY ) {
                $returnCode = $this->summary( $values );
            } else if ( $mode == self::MODE_IMPORT ) {
                $returnCode = $this->import( $onDuplicate, $values );
            } else {
                $returnCode = self::ERROR;
            }
            
            // note that a line could be valid but still produce a warning
            if ( $returnCode & self::VALID ) {
                $this->_validCount++;
                if ( $mode == self::MODE_MAPFIELD ) {
                    $this->_rows[]           = $values;
                    $this->_activeFieldCount = max( $this->_activeFieldCount, count( $values ) );
                }
            }

            if ( $returnCode & self::WARNING ) {
                $this->_warningCount++;
                if ( $this->_warningCount < $this->_maxWarningCount ) {
                    $this->_warningCount[] = $line;
                }
            } 

            if ( $returnCode & self::ERROR ) {
                $this->_invalidRowCount++;
                if ( $this->_invalidRowCount < $this->_maxErrorCount ) {
                    array_unshift($values, $this->_lineCount);
                    $this->_errors[] = $values;
                }
            } 

            if ( $returnCode & self::CONFLICT ) {
                $this->_conflictCount++;
                array_unshift($values, $this->_lineCount);
                $this->_conflicts[] = $values;
            } 
            
            if ( $returnCode & self::DUPLICATE ) {
                if ( $returnCode & self::MULTIPLE_DUPE ) {
                    /* TODO: multi-dupes should be counted apart from singles
                     * on non-skip action */
                }
                $this->_duplicateCount++;
                array_unshift($values, $this->_lineCount);
                $this->_duplicates[] = $values;
            }

            // we give the derived class a way of aborting the process
            // note that the return code could be multiple code or'ed together
            if ( $returnCode & self::STOP ) {
                break;
            }

            // if we are done processing the maxNumber of lines, break
            if ( $this->_maxLinesToProcess > 0 && $this->_validCount >= $this->_maxLinesToProcess ) {
                break;
            }
        }

        fclose( $fd );

        if ($mode == self::MODE_PREVIEW) {
            $headers = array_merge( array(  ts('Record Number'),
                                            ts('Reason')), 
                                    $mapper);
                
            if ($this->_invalidRowCount) {
                $this->_errorFileName = $fileName . '.errors';
                self::exportCSV($this->_errorFileName, $headers, $this->_errors);
            }
            if ($this->_conflictCount) {
                $this->_conflictFileName = $fileName . '.conflicts';
                self::exportCSV($this->_conflictFileName, $headers, $this->_conflicts);
            }
        }
        if ($mode == self::MODE_IMPORT && $this->_duplicateCount) {
            $headers = array_merge( array(  ts('Record Number'), 
                                            ts('View Contact URL')),
                                    $mapper);

            $this->_duplicateFileName = $fileName . '.duplicates';
            self::exportCSV($this->_duplicateFileName, $headers, $this->_duplicates);
        }

        return $this->fini();
    }

    abstract function mapField( &$values );
    abstract function preview( &$values );
    abstract function summary( &$values );
    abstract function import ( $onDuplicate, &$values );

    abstract function fini();

    /**
     * Given a list of the importable field keys that the user has selected
     * set the active fields array to this list
     *
     * @param array mapped array of values
     *
     * @return void
     * @access public
     */
    function setActiveFields( $fieldKeys ) {
        $this->_activeFieldCount = count( $fieldKeys );
        foreach ( $fieldKeys as $key ) {
            $this->_activeFields[] =& $this->_fields[$key];
        }
    }

    function setActiveFieldValues( $elements ) {
//          CRM_Core_Error::debug('f', $this->_activeFields);
        for ( $i = 0; $i < count( $elements ); $i++ ) {
            $this->_activeFields[$i]->setValue( $elements[$i] );
        }

        // reset all the values that we did not have an equivalent import element
        for ( ; $i < $this->_activeFieldCount; $i++ ) {
            $this->_activeFields[$i]->resetValue();
        }

        // now validate the fields and return false if error
        $valid = self::VALID;
        for ( $i = 0; $i < $this->_activeFieldCount; $i++ ) {
            if ( ! $this->_activeFields[$i]->validate() ) {
                // no need to do any more validation
                $valid = self::ERROR;
                break;
            }
        }
        return $valid;
    }

    /**
     * function to format the field values for input to the api
     *
     * @return array (reference ) associative array of name/value pairs
     * @access public
     */
    function &getActiveFieldParams( ) {
        $params = array( );
        for ( $i = 0; $i < $this->_activeFieldCount; $i++ ) {
            if ( isset( $this->_activeFields[$i]->_value ) ) {
                $params[$this->_activeFields[$i]->_name] = $this->_activeFields[$i]->_value;
            }
        }
        return $params;
    }

    function getSelectValues() {
        $values = array();
        foreach ($this->_fields as $name => $field ) {
            $values[$name] = $field->_title;
        }
        return $values;
    }

    function getHeaderPatterns() {
        $values = array();
        foreach ($this->_fields as $name => $field ) {
            $values[$name] = $field->_headerPattern;
        }
        return $values;
    }

    function getDataPatterns() {
        $values = array();
        foreach ($this->_fields as $name => $field ) {
            $values[$name] = $field->_dataPattern;
        }
        return $values;
    }

    function addField( $name, $title, $type = CRM_Utils_Type::T_INT, $headerPattern = '//', $dataPattern = '//') {
        $this->_fields[$name] =& new CRM_Import_Field($name, $title, $type, $headerPattern, $dataPattern);
    }

    /**
     * setter function
     *
     * @param int $max 
     *
     * @return void
     * @access public
     */
    function setMaxLinesToProcess( $max ) {
        $this->_maxLinesToProcess = $max;
    }

    /**
     * Store parser values
     *
     * @param CRM_Core_Session $store 
     *
     * @return void
     * @access public
     */
    function set( $store, $mode = self::MODE_SUMMARY ) {
        if ($mode == self::MODE_SUMMARY ) {
            $store->set( 'fileSize'   , $this->_fileSize          );
            $store->set( 'lineCount'  , $this->_lineCount         );
            $store->set( 'seperator'  , $this->_seperator         );
            $store->set( 'fields'     , $this->getSelectValues( ) );
            $store->set( 'headerPatterns', $this->getHeaderPatterns( ) );
            $store->set( 'dataPatterns', $this->getDataPatterns( ) );
            $store->set( 'columnCount', $this->_activeFieldCount  );

            $store->set( 'totalRowCount'    , $this->_totalCount     );
            $store->set( 'validRowCount'    , $this->_validCount     );
            $store->set( 'invalidRowCount'  , $this->_invalidRowCount     );
            $store->set( 'conflictRowCount', $this->_conflictCount );
        
            $store->set( 'customMap', $this->_customMap );        
        
            if ($this->_invalidRowCount) {
                $store->set( 'errorsFileName', $this->_errorFileName );
            }
            if ($this->_conflictCount) {
                $store->set( 'conflictsFileName', $this->_conflictFileName );
            }
            if ( isset( $this->_rows ) && ! empty( $this->_rows ) ) {
                $store->set( 'dataValues', $this->_rows );
            }
        } else if ($mode == self::MODE_IMPORT) {
            $store->set( 'duplicateRowCount', $this->_duplicateCount );
            if ($this->_duplicateCount) {
                $store->set( 'duplicatesFileName', $this->_duplicateFileName );
            }
        }
    }

    /**
     * Export data to a CSV file
     *
     * @param string $filename
     * @param array $header
     * @param data $data
     * @return void
     * @access public
     */
    static function exportCSV($fileName, $header, $data) {
        $output = array();
        $fd = fopen($fileName, 'w');

        foreach ($header as $key => $value) {
            $header[$key] = "\"$value\"";
        }
        $output[] = implode(',', $header);

        foreach ($data as $datum) {
            foreach ($datum as $key => $value) {
                $datum[$key] = "\"$value\"";
            }
            $output[] = implode(',', $datum);
        }
        fwrite($fd, implode("\n", $output));
        fclose($fd);
    }

    /** 
     * Remove single-quote enclosures from a value array (row)
     *
     * @param array $values
     * @param string $enclosure
     * @return void
     * @static
     * @access public
     */
    static function encloseScrub(&$values, $enclosure = "'") {
        if (empty($values)) 
            return;

//         CRM_Core_Error::debug('pre scrub', $values);
        foreach ($values as $k => $v) {
            $values[$k] = preg_replace("/^$enclosure(.*)$enclosure$/", '$1', $v);
        }
//         CRM_Core_Error::debug('post scrub', $values);
    }

}

?>
