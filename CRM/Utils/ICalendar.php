<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * @file
 * API for event export in iCalendar format 
 * as outlined in Internet Calendaring and 
 * Scheduling Core Object Specification
 * 
 */
class CRM_Utils_ICalendar 
{
    /**
     * Escape text elements for safe ICalendar use
     *
     * @param $text Text to escape
     *
     * @return  Escaped text
     *
     */
    static function formatText( $text ) 
    {
        $text = strip_tags($text);
        $text = str_replace("\"", "DQUOTE", $text);
        $text = str_replace("\\", "\\\\", $text);
        $text = str_replace(",", "\,", $text);
        $text = str_replace(":", "\":\"", $text);
        $text = str_replace(";", "\;", $text);
        $text = str_replace("\n", "\n ", $text);
        return $text;
    }

    /**
     * Escape text elements for safe ICalendar use
     *
     * @param $text Text to escape
     *
     * @return  Escaped text
     *
     */
    static function formatDate( $date, $gdata = false )
    {
        if ( $gdata ) {
            return gmdate( "Y-m-d\TH:i:s.000\Z",
                           strtotime( $date ) );
        } else {
            return gmdate( "Ymd\THis\Z",
                           strtotime( $date ) );
        }
    }

    /**
     *
     * Send the ICalendar as a downloadable file.
     *
     * @access public
     * 
     * @param string $filename The file name to give the ICalendar
     *
     * @param string $disposition How the file should be sent, either
     * 'inline' or 'attachment'.
     * 
     * @param string $charset The character set to use, defaults to
     * 'us-ascii'.
     *
     * @param string $format The contents of the file
     * to be published
     * 
     * @return void
     * 
     */ 
    function send( $fileName, $disposition = 'attachment', $charset = 'us-ascii', $format )
    {
        header(
               'Content-Type: text/calendar;' .
               'Content-Language: en_US;' . 
               'profile="ICalendar"; ' .
               'charset=' . $charset
               );
   
        header('Content-Length: ' . strlen($format));
        header("Content-Disposition: $disposition; filename=\"$fileName\"");
        echo $format;
    }
}

?>
