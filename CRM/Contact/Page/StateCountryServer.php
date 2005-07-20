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

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';
require_once 'CRM/Contact/Server/StateCountryServer.php';

define ('JPSPAN_ERROR_DEBUG',TRUE);

require_once 'JPSpan.php';

require_once JPSPAN . 'Server/PostOffice.php';

class CRM_Contact_Page_StateCountryServer extends CRM_Core_Page { 

    function run ($set) 
    {
        $S = & new JPSpan_Server_PostOffice();
        $S->addHandler(new CRM_Contact_Server_StateCountryServer());
        
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
