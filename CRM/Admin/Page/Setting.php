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

require_once 'CRM/Core/Page.php';

/**
 * Page for displaying list of categories for Settings
 */
class CRM_Admin_Page_Setting extends CRM_Core_Page {

    function run() {
        
        CRM_Utils_System::setTitle(ts("Global Settings"));
        
        $allTabs  = array( );
        
        $tabs = array( 'component'    => ts( 'Components'           ), 
                       'path'         => ts( 'File System Paths'    ),
                       'site'         => ts( 'Site URLs'            ),
                       'smtp'         => ts( 'SMTP Server'          ),
                       'mapping'      => ts( 'Mapping and Geocoding'),
                       'payment'      => ts( 'Online Payment'       ),
                       'localization' => ts( 'Localization'         ),
                       'address'      => ts( 'Address Formatting'   ),
                       'date'         => ts( 'Date'      ),
                       'misc'         => ts( 'Miscellaneous'        ),
                       'debug'        => ts( 'Debugging'            ),
                       );
        
        foreach ( $tabs as $k => $v ) {
            $allTabs[$v] = CRM_Utils_System::url( "civicrm/admin/setting/$k",
                                                  "reset=1&snippet=1}" ); 
        }
        
        $this->assign( 'dojoIncludes', "dojo.require('dojo.widget.TabContainer');dojo.require('dojo.widget.ContentPane');dojo.require('dojo.widget.LinkPane');" );
        $this->assign( 'allTabs'     , $allTabs     );

        parent::run();
    }   
}




?>
