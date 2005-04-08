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


require_once 'CRM/String.php';
require_once 'CRM/Type.php';

require_once 'CRM/Import/Field.php';

class CRM_Import_Parser {

    const
        MAX_ERRORS   = 25,
        MAX_WARNINGS = 25,
        VALID        =  1,
        WARNING      =  2,
        ERROR        =  4,
        STOP         =  8;

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
     * running total number of valid lines
     */
    protected $_validCount;

    /**
     * running total number of errors
     */
    protected $_errorCount;

    /**
     * maximum number of errors to store
     */
    protected $_maxErrorCount = self::MAX_ERRORS;

    /**
     * array of error lines, bounded by MAX_ERROR
     */
    protected $_errors;

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

    function __construct() {
        parent::__construct();

        $this->_fields       = array();
        $this->_activeFields = array();

        $this->_maxLinesToProcess = 0;
    }

    abstract function init();

    function import( $fileName, $seperator = ',' ) {
        $this->init();

        $this->_seperator = ',';

        $fd = fopen( $fileName, "r" );
        if ( ! $fd ) {
            return false;
        }

        $this->_lineCount = $this->_warningCount = $this->_errorCount = $this->_validCount = 0;
    
        $this->_errors   = array();
        $this->_warnings = array();

        $this->_fileSize = number_format( filesize( $fileName ) / 1024.0, 2 );

        $this->_activeFieldCount = count( $this->_activeFields );

        while ( ! feof( $fd ) ) {
            $fields = fgetcsv( $fd, 8192, $seperator );
            $this->_lineCount++;
            if ( ! $fields || empty( $fields ) ) {
                continue;
            }

            $returnCode = $this->process( $fields );

            // note that a line could be valid but still produce a warning
            if ( $returnCode & self::VALID ) {
                $this->_validCount++;
            }

            if ( $returnCode & self::WARNING ) {
                $this->_warningCount++;
                if ( $this->_warningCount < $this->_maxWarningCount ) {
                    $this->_warningCount[] = $line;
                }
            } 

            if ( $returnCode & self::ERROR ) {
                $this->_errorCount++;
                if ( $this->_errorCount < $this->_maxErrorCount ) {
                    $this->_errorCount[] = $line;
                }
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

        return $this->fini();
    }

    abstract function process( $line );

    abstract function fini();

    function setActiveFields( $elements ) {
        for ( $j = 0; $j < count( $elements ); $j++ ) {
            $this->_activeFields[$j]->setValue( $elements[$j] );
        }

        // reset all the values that we did not have an equivalent import element
        for ( ; $j < $this->_activeFieldCount; $j++ ) {
            $this->_activeFields[$j]->resetValue();
        }

        // now validate the fields and return false if error
        $valid = self::VALID;
        for ( $j = 0; $j < $this->_activeFieldCount; $j++ ) {
            if ( $this->_activeFields[$j]->validate() ) {
                $valid = self::ERROR;
            }
        }
        return $valid;
    }

    function getSelectValues() {
        $values = array();
        foreach ( $this->_fields as $field ) {
            $values[$field->iname] = $field->name;
        }
        return $values;
    }

    function addField( $name, $title, $type = CRM_Type::T_INT, $required = false, $payload = null, $active = false ) {
        $field = new CRM_Import_Field($name, $title, $type, $required, $payload, $active);
        $this->_fields[] =& $field;
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
     * @param CRM_Session $store 
     *
     * @return void
     * @access public
     */
    function set( $store ) {
        $store->set( 'fileSize'  , $this->_fileSize   );
        $store->set( 'lineCount' , $this->_lineCount  );
        $store->set( 'seperator' , $this->_seperator  );
    }
}

?>
