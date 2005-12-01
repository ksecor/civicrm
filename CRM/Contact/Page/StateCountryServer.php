<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';
require_once 'CRM/Contact/Server/StateCountry.php';

define ('JPSPAN_ERROR_DEBUG',TRUE);

require_once 'JPSpan.php';

require_once JPSPAN . 'Server/PostOffice.php';

class CRM_Contact_Page_StateCountryServer extends CRM_Core_Page { 

    function run ($set) 
    {
        $S = & new JPSpan_Server_PostOffice();
        $S->addHandler(new CRM_Contact_Server_StateCountry());
        
        if ( $set ) {
            // Compress the Javascript
            // define('JPSPAN_INCLUDE_COMPRESS',TRUE);
                        
            $S->displayClient();
        } else {
            // Include error handler - PHP errors, warnings and notices serialized to JS
            require_once 'packages/JPSpan/ErrorHandler.php';
            $S->serve();
        }
    }
}

?>
