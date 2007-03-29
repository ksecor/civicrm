<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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

require_once 'CRM/Core/Page.php';

/**
 * ICalendar class
 *
 */
class CRM_Event_Page_ICalendar extends CRM_Core_Page
{
    /**
     * Heart of the iCalendar data assignment process. The runner gets all the meta
     * data for the event and calls the  method to output the iCalendar
     * to the user.
     *
     * @return void
     */
    function run( )
    {
        $type     = CRM_Utils_Request::retrieve('type', 'Positive',$this, false, 0);
        $start    = CRM_Utils_Request::retrieve('start', 'Positive',$this, false, 0);
        $iCalPage = CRM_Utils_Request::retrieve('page', 'Positive',$this, false, 0);

        require_once "CRM/Event/BAO/Event.php";
        $info = CRM_Event_BAO_Event::getCompleteInfo( $start, $type );

        require_once "CRM/Utils/ICalendar.php";
        $format = CRM_Utils_ICalendar::iCalendar( $info );

        if( $iCalPage == 1) {
            echo $format;
            exit();
        }

        CRM_Utils_ICalendar::send( 'civicrm_ical.ics', 'attachment', 'utf-8', $format );
        exit( );
    }
}

?>
