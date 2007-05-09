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

            // create a unique identifier for the event
            $config =& CRM_Core_Config::singleton( );
            $content .= "UID:CiviCRM_EventID_" . $event['event_id'] . "@" . $config->userFrameworkBaseURL . "\n";

            if ( $event['description'] ) {
                $content .= "DESCRIPTION:" . self::escapeText( $event['description'] )  . "\n";
            }

            if ( $event['event_type'] ) {
                $content .= "CATEGORIES:" . self::escapeText( $event['event_type'] )  . "\n";
            }
            
            if ( $event['start_date'] && $event['end_date'] ) {
                $content .= "DTSTART;VALUE=DATE:" . gmdate("Ymd\THis\Z", strtotime($event['start_date'])) . "\n";
                $content .= "DTEND;VALUE=DATE:" . gmdate("Ymd\THis\Z", strtotime($event['end_date'])) . "\n";
            }
            
            if ( $event['location'] ) {
                $content .= "LOCATION:" . self::escapeText( $event['location'] ) . "\n";
            }

            if ( $event['contact_email'] ) {
                $content .= "ORGANIZER:MAILTO:" . self::escapeText( $event['contact_email'] ) . "\n";
            }
            
            if ( $event['url'] ) {
                $content .= "URL:" . $event['url'] . "\n";
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
     * Send the ICalendar to the browser with the specified content type
     * - 'text/calendar' : used for downloaded ics file
     * - 'text/plain'    : used for iCal formatted feed
     * - 'text/xml'      : used for gData or rss formatted feeds
     *
     * @access public
     * 
     * @param string $content_type 
     * 
     * @param string $filename The file name (for downloads)
     *
     * @param string $disposition How the file should be sent ('attachment' for downloads)
     * 
     * @param string $charset The character set to use, defaults to
     * 'us-ascii'.
     *
     * @param string $calendar The calendar data to be published.
     * 
     * @return void
     * 
     */ 
    function send( $calendar, $content_type = 'text/calendar', $charset = 'us-ascii', $fileName, $disposition  )
    {
        header( "Content-Type: $content_type;" . 
                'Content-Language: en_US;' . 
                'profile="ICalendar"; ' .
                'charset=' . $charset
                );
        
        if ( $content_type == 'text/calendar') {
            header('Content-Length: ' . strlen($calendar));
            header("Content-Disposition: $disposition; filename=\"$fileName\"");
        }
        
        echo $calendar;
    }
}

?>
