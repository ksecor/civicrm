<?php

require_once 'CRM/String.php';

class CRM_Reports_Excel {
  /**
   * Code copied from phpMyAdmin - Lobo
   *
   * Outputs the content of a table in CSV format
   *
   * Last revision 14 July 2001: Patch for limiting dump size from
   * vinay@sanisoft.com & girish@sanisoft.com
   *
   * @param   string   the header fields
   * @param   string   the table rows field
   * @param   string   the result buffer
   *
   * @return  boolean always true
   *
   * @access  public
   */
  function makeCSVTable( &$header, &$rows, &$result )
  {
    // Handles the "separator" and the optional "enclosed by" characters
    $sep     = ',';
    $enc_by  = '"';

    // double the "enclosed by" character
    $esc_by  = $enc_by;

    $add_character = "\015\012";

    $schema_insert = '';
    foreach ( $header as $field ) {
      if ($enc_by == '') {
        $schema_insert .= $field;
      } else {
        $schema_insert .= $enc_by
                        . str_replace($enc_by, $esc_by . $enc_by, $field)
                        . $enc_by;
      }
      $schema_insert     .= $sep;
    } // end while

    $result .= trim(substr($schema_insert, 0, -1));
    $result .= $add_character;

    $i = 0;
    $fields_cnt = count($header);

    foreach ( $rows as $row ) {
      $schema_insert = '';
      foreach ( $row as $j => $value ) {
        if (!isset($value)) {
          //$schema_insert .= 'NULL';
		  $schema_insert .= '';
        } else if ($value == '0' || $value != '') {
          // loic1 : always enclose fields
          $value = ereg_replace("\015(\012)?", "\012", $value);
           if ($enc_by == '') {
            $schema_insert .= $value;
          } else {
            $schema_insert .= $enc_by
                           . str_replace($enc_by, $esc_by . $enc_by, $value)
                           . $enc_by;
          }
        } else {
          $schema_insert .= '';
        }
         
        if ($j < $fields_cnt-1) {
          $schema_insert .= $sep;
        }
      } // end for

      $result .= $schema_insert;
      $result .= $add_character;

      ++$i;

    } // end for

    return TRUE;
  } // end of the 'getTableCsv()' function

  function writeCSVFile( &$fileName, &$header, &$rows ) {
    
    $now       = gmdate('D, d M Y H:i:s') . ' GMT';
    $mime_type = 'text/x-csv';
    $ext       = 'csv';

    $fileName = CRM_String::munge( $fileName );

    // send the write header statements to the browser
    header('Content-Type: ' . $mime_type); 
    header('Expires: ' . $now);

    // lem9 & loic1: IE need specific headers
    $isIE = strstr( $_SERVER['HTTP_USER_AGENT'], 'MSIE' );
    if ( $isIE ) {
      header('Content-Disposition: inline; filename="' . $fileName . '.' . $ext . '"');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
    } else {
      header('Content-Disposition: attachment; filename="' . $fileName . '.' . $ext . '"');
      header('Pragma: no-cache');
    }
    
    $result = '';
    CRM_Reports_Excel::makeCSVTable( $header, $rows, $result );

    echo $result;
  }

}

?>
