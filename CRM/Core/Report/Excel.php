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


require_once 'CRM/Core/String.php';

class CRM_Core_Report_Excel {
    /**
     * Code copied from phpMyAdmin (v2.6.1-pl3)
     * File: PHPMYADMIN/libraries/export/csv.php
     * Function: PMA_exportData
     *
     * Outputs a result set with a given header
     * in the string buffer result
     *
     * @param   string   $header (reference ) column headers
     * @param   string   $rows   (reference ) result set rows
     * @param   boolean  $print should the output be printed
     *
     * @return  mixed    empty if output is printed, else output
     *
     * @access  public
     */
    function makeCSVTable( &$header, &$rows, $print = true)
    {
        $result = '';

        $seperator     = ',';
        $enclosed      = '"';
        $escaped       = $enclosed;
        $add_character = "\015\012";

        $schema_insert = '';
        foreach ( $header as $field ) {
            if ($enclosed == '') {
                $schema_insert .= stripslashes($field);
            } else {
                $schema_insert .=
                      $enclosed
                    . str_replace($enclosed, $escaped . $enclosed, stripslashes($field))
                    . $enclosed;
            }
            $schema_insert     .= $seperator;
        } // end while

        // need to add PMA_exportOutputHandler functionality out here, rather than
        // doing it the moronic way of assembling a buffer
        $out = trim(substr($schema_insert, 0, -1)) . $add_character;
        if ( $print ) {
            echo $out;
        } else {
            $result .= $out;
        }

        $i = 0;
        $fields_cnt = count($header);

        foreach ( $rows as $row ) {
            $schema_insert = '';
            foreach ( $row as $j => $value ) {
                if (!isset($value) || is_null($value)) {
                    $schema_insert .= '';
                } else if ($value == '0' || $value != '') {
                    // loic1 : always enclose fields
                    $value = ereg_replace("\015(\012)?", "\012", $value);
                    if ($enclosed == '') {
                        $schema_insert .= $value;
                    } else {
                        $schema_insert .=
                              $enclosed
                            . str_replace($enclosed, $escaped . $enclosed, $value)
                            . $enclosed;
                    }
                } else {
                    $schema_insert .= '';
                }
         
                if ($j < $fields_cnt-1) {
                    $schema_insert .= $seperator;
                }
            } // end for

            $out = $schema_insert . $add_character;
            if ( $print ) {
                echo $out;
            } else {
                $result .= $out;
            }

            ++$i;

        } // end for

        if ( $print ) {
            return;
        } else {
            return $result;
        }
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
    
        self::makeCSVTable( $header, $rows, true );
    }

}

?>
