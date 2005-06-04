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


define( 'CRM_IMPORT_PARSER_MAX_ERRORS',25);
define( 'CRM_IMPORT_PARSER_MAX_WARNINGS',25);
define( 'CRM_IMPORT_PARSER_VALID',1);
define( 'CRM_IMPORT_PARSER_WARNING',2);
define( 'CRM_IMPORT_PARSER_ERROR',4);
define( 'CRM_IMPORT_PARSER_DUPLICATE',8);
define( 'CRM_IMPORT_PARSER_STOP',16);
define( 'CRM_IMPORT_PARSER_MODE_PREVIEW',1);
define( 'CRM_IMPORT_PARSER_MODE_SUMMARY',2);
define( 'CRM_IMPORT_PARSER_MODE_IMPORT',4);


require_once 'CRM/Import/Field.php';
require_once 'CRM/Utils/String.php';
require_once 'CRM/Utils/Type.php';

require_once 'CRM/Import/Field.php';

 class CRM_Import_Parser {

    
            
          
                  
                
                  
              
                  /**
     * various parser modes
     */
    
          
          
           protected $_fileName;

    /**#@+
     * @access protected
     * @var integer
     */

    /**
     * imported file size
     */
    var $_fileSize;

    /**
     * seperator being used
     */
    var $_seperator;

    /**
     * total number of lines in file
     */
    var $_lineCount;

    /**
     * total number of non empty lines
     */
    var $_totalCount;

    /**
     * running total number of valid lines
     */
    var $_validCount;

    /**
     * running total number of errors
     */
    var $_errorCount;

    /**
     * total number of duplicate lines
     */
    var $_duplicateCount;

    /**
     * maximum number of errors to store
     */
    var $_maxErrorCount = CRM_IMPORT_PARSER_MAX_ERRORS;

    /**
     * array of error lines, bounded by MAX_ERROR
     */
    var $_errors;

    /**
     * running total number of warnings
     */
    var $_warningCount;

    /**
     * maximum number of warnings to store
     */
    var $_maxWarningCount = CRM_IMPORT_PARSER_MAX_WARNINGS;

    /**
     * array of warning lines, bounded by MAX_WARNING
     */
    var $_warnings;

    /**
     * array of all the fields that could potentially be part
     * of this import process
     * @var array
     */
    var $_fields;

    /**
     * array of the fields that are actually part of the import process
     * the position in the array also dictates their position in the import
     * file
     * @var array
     */
    var $_activeFields;

    /**
     * cache the count of active fields
     *
     * @var int
     */
    var $_activeFieldCount;

    /**
     * maximum number of non-empty/comment lines to process
     *
     * @var int
     */
    var $_maxLinesToProcess;

    /**
     * cache of preview rows
     *
     * @var array
     */
    var $_rows;

    function CRM_Import_Parser() {
        $this->_maxLinesToProcess = 0;
    }

    // abstract function init();

    function run( $fileName,
                  $seperator = ',',
                  &$mapper,
                  $skipColumnHeader = false,
                  $mode = CRM_IMPORT_PARSER_MODE_PREVIEW) {
        $this->init();

        $this->_seperator = ',';

        $fd = fopen( $fileName, "r" );
        if ( ! $fd ) {
            return false;
        }

        $this->_lineCount  = $this->_warningCount   = 0;
        $this->_errorCount = $this->_validCount     = 0;
        $this->_totalCount = $this->_duplicateCount = 0;
    
        $this->_errors   = array();
        $this->_warnings = array();

        $this->_fileSize = number_format( filesize( $fileName ) / 1024.0, 2 );
        
        if ( $preview == CRM_IMPORT_PARSER_MODE_PREVIEW) {
            $this->_rows = array( );
        } else {
            $this->_activeFieldCount = count( $this->_activeFields );
        }

        if ( $mode == CRM_IMPORT_PARSER_MODE_IMPORT) {
            //get the key of email field
            foreach($mapper as $key => $value) {
                if ( $value == 'Email' ) {
                    $emailKey = $key;
                    break;
                }
            }

            $this->_totalCount     = $importData['totalRowCount'];
            $this->_errorCount     = $importData['invalidRowCount'];
            $this->_duplicateCount = $importData['duplicateRowCount'];
        }
        
        $email = array();
        while ( ! feof( $fd ) ) {
            $this->_lineCount++;

            $values = fgetcsv( $fd, 8192, $seperator );

            // skip column header if data is imported
            if ( $mode == CRM_IMPORT_PARSER_MODE_IMPORT) {
                if ($skipColumnHeader ) {
                    $skipColumnHeader = 0;
                    continue;
                }

                if ( in_array($values[$emailKey], $email)) {
                    continue;
                } else {
                    array_push($email, $values[$emailKey]);
                }
            }
            
            if ( ! $values || empty( $values ) ) {
                continue;
            }

            if ( $mode != CRM_IMPORT_PARSER_MODE_IMPORT) {
                $this->_totalCount++;
            }

            if ( $mode == CRM_IMPORT_PARSER_MODE_PREVIEW) {
                $returnCode = $this->preview( $values );
            } else if ( $mode == CRM_IMPORT_PARSER_MODE_SUMMARY) {
                $returnCode = $this->summary( $values );
            } else if ( $mode == CRM_IMPORT_PARSER_MODE_IMPORT) {
                $returnCode = $this->import( $values );
            } else {
                $returnCode = CRM_IMPORT_PARSER_ERROR;
            }
            
            // note that a line could be valid but still produce a warning
            if ( $returnCode & CRM_IMPORT_PARSER_VALID) {
                $this->_validCount++;
                if ( $mode == CRM_IMPORT_PARSER_MODE_PREVIEW) {
                    $this->_rows[]           = $values;
                    $this->_activeFieldCount = max( $this->_activeFieldCount, count( $values ) );
                }
            }

            if ( $returnCode & CRM_IMPORT_PARSER_WARNING) {
                $this->_warningCount++;
                if ( $this->_warningCount < $this->_maxWarningCount ) {
                    $this->_warningCount[] = $line;
                }
            } 

            if ( $returnCode & CRM_IMPORT_PARSER_ERROR) {
                //$this->_errorCount++;
                if ( $this->_errorCount < $this->_maxErrorCount ) {
                    $this->_errorCount[] = $line;
                }
            } 

            if ( $returnCode & CRM_IMPORT_PARSER_DUPLICATE) {
                $this->_duplicateCount++;
            } 

            // we give the derived class a way of aborting the process
            // note that the return code could be multiple code or'ed together
            if ( $returnCode & CRM_IMPORT_PARSER_STOP) {
                break;
            }

            // if we are done processing the maxNumber of lines, break
            if ( $this->_maxLinesToProcess > 0 && $this->_validCount >= $this->_maxLinesToProcess ) {
                break;
            }
        }

        fclose( $fd );

        return $this->fini();
    }

    // abstract function preview( &$values );
    // abstract function summary( &$values );
    // abstract function import ( &$values );

    // abstract function fini();

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
        for ( $i = 0; $i < count( $elements ); $i++ ) {
            $this->_activeFields[$i]->setValue( $elements[$i] );
        }

        // reset all the values that we did not have an equivalent import element
        for ( ; $i < $this->_activeFieldCount; $i++ ) {
            $this->_activeFields[$i]->resetValue();
        }

        // now validate the fields and return false if error
        $valid = CRM_IMPORT_PARSER_VALID;
        for ( $i = 0; $i < $this->_activeFieldCount; $i++ ) {
            if ( ! $this->_activeFields[$i]->validate() ) {
                // no need to do any more validation
                $valid = CRM_IMPORT_PARSER_ERROR;
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
        // does not work for php4 - we shld revert back to this one after we stop developing for php4        
        //foreach ( $this->_fields as $name => &$field ) {
        foreach ($this->_fields as $name => $field ) {
            $values[$name] = $field->_title;
        }
        return $values;
    }

    function addField( $name, $title, $type = CRM_UTILS_TYPE_T_INT, $required = false, $payload = null, $active = false ) {
        $this->_fields[$name] = new CRM_Import_Field($name, $title, $type, $required, $payload, $active);
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
    function set( $store ) {
        $store->set( 'fileSize'   , $this->_fileSize          );
        $store->set( 'lineCount'  , $this->_lineCount         );
        $store->set( 'seperator'  , $this->_seperator         );
        $store->set( 'fields'     , $this->getSelectValues( ) );
        $store->set( 'columnCount', $this->_activeFieldCount  );

        $store->set( 'totalRowCount'    , $this->_totalCount     );
        $store->set( 'validRowCount'    , $this->_validCount     );
        $store->set( 'invalidRowCount'  , $this->_errorCount     );
        $store->set( 'duplicateRowCount', $this->_duplicateCount );

        if ( isset( $this->_rows ) && ! empty( $this->_rows ) ) {
            $store->set( 'dataValues', $this->_rows );
        }
    }

}

?>