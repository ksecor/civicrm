<?php

class CRM_Import extends CRM_Base {

  const
    MAX_ERRORS   = 25,
    MAX_WARNINGS = 25,
    VALID_LINE   =  1,
    WARNING_LINE =  2,
    ERROR_LINE   =  4,
    STOP_LINE    =  8;

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
  protected $_activeFieldCount;

  function __construct() {
    parent::__construct();
  }

  abstract function init();

  function import( $fileName ) {
    $this->init();

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
      $line = fgets( $fd, 8192 );
      $this->_lineCount++;

      // check if line is white space or comments, if so skip
      if ( empty( $line )                    ||
           CRM_String::isWhiteSpace( $line ) ||
           CRM_String::isComment   ( $line ) ) {
        continue;
      }


      $elements = CRM_String::explodeLine( $line, $seperator, true );

      $returnCode = $this->setActiveFields( $elements );
      if ( $returnCode & self::VALID_LINE ) {
        $returnCode = $this->process( $line );
      }

      // note that a line could be valid but still produce a warning
      if ( $returnCode & self::VALID_LINE ) {
        $this->_validCount++;
      }

      if ( $returnCode & self::WARNING_LINE ) {
        $this->_warningCount++;
        if ( $this->_warningCount < $this->_maxWarningCount ) {
          push( $this->_warningCount, $line );
        }
      } 

      if ( $returnCode & self::ERROR_LINE ) {
        $this->_errorCount++;
        if ( $this->_errorCount < $this->_maxErrorCount ) {
          push( $this->_errorCount, $line );
        }
      } 

      // we give the derived class a way of aborting the process
      // note that the return code could be multiple code or'ed together
      if ( $returnCode & self::STOP_LINE ) {
        break;
      }
    }


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
    $valid = true;
    for ( $j = 0; $j < $this->_activeFieldCount; $j++ ) {
      if ( $this->_activeFields[$j]->validate() ) {
        return self::ERROR_LINE;
      }
    }
    return self::VALID_LINE;
  }

}

?>
