<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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
 * Component stores all the static and dynamic information of the various
 * CiviCRM components
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Event_ComponentInfo
{

    public function info()
    {
        return array( 'title'   => ts('CiviCRM Event Engine'),
                      'url'     => 'event',
                      'perm'    => array( 'access CiviEvent',
                                          'edit event participants',
                                          'register for events' ),
                      'search'  => 1 );
    }
    
    
    protected function activityTypes()
    {
        $types = array();
        $types['Event'] = array( 'title' => ts('Event'),
                                 'callback' => 'CRM_Event_Page_EventInfo::run()' );
        return $types;
    }

}
