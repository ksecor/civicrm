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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions      |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
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
    * Turn an array of events into a valid iCalendar file
    *
    * @param $events
    *   An array of associative arrays where
    *      'summary'       => Title of event (Text)
    *      'description'   => Description of event (Text)
    *      'start_date'    => Start date of all-day event in YYYYMMDD format (Required, if no start)
    *      'end_date'      => End date of all-day event in YYYYMMDD format (Optional)
    *      'location'      => Location of event (Text)
    *      'url'           => URL to provide link for Event Info page
    *      'contact_email' => email of event organiser
    *
    * @return  Text of a iCalendar file
    */
    function iCalendar( &$events ) 
    {
        $content = "BEGIN:VCALENDAR\nVERSION:2.0\n";
        $content .= "PRODID:-//CiviCRM//NONSGML CiviEvent iCal//EN\n";
        
        foreach ( $events as $uid => $event ) {
            $content .= "BEGIN:VEVENT\n";
            $content .= "SUMMARY:" . self::escapeText( $event['summary'] ) . "\n";

            if ( $event['description'] ) {
                $content .= "DESCRIPTION:" . self::escapeText( $event['description'] )  . "\n";
            }

            if ( $event['start_date'] && $event['end_date'] ) {
                $content .= "DTSTART;VALUE=DATE:" . $event['start_date'] . "\n";
                $content .= "DTEND;VALUE=DATE:" . $event['end_date'] . "\n";
            }
            
            if ( $event['location'] ) {
                $content .= "LOCATION:" . self::escapeText( $event['location'] ) . "\n";
            }

            if ( $event['contact_email'] ) {
                $content .= "ORGANIZER:MAILTO:" . self::escapeText( $event['contact_email'] ) . "\n";
            }
            
            if ( $event['url'] ) {
                $content .= "ATTACH;FMTTYPE=text/html:" . $event['url'] . "\n";
            }
             
            $content .= "END:VEVENT\n";
        }
        $content .= "END:VCALENDAR\n";
      
        return $content;
    }

    /**
     * Escape text elements for safe ICalendar use
     *
     * @param $text Text to escape
     *
     * @return  Escaped text
     *
     */
    function escapeText( $text ) 
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