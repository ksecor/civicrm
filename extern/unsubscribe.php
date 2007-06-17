<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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

require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/Error.php';
require_once 'CRM/Core/Page.php';

class extern_unsubscribe extends CRM_Core_Page 
{
    function run() {
        require_once 'CRM/Utils/Array.php';
        $job_id   = CRM_Utils_Array::value( 'jid', $_GET );
        $queue_id = CRM_Utils_Array::value( 'qid', $_GET );
        $hash     = CRM_Utils_Array::value( 'h'  , $_GET );

        if ( ! $job_id   ||
             ! $queue_id ||
             ! $hash ) {
            echo "Missing input parameters\n";
            exit( );
        }
        
        require_once 'CRM/Mailing/Event/BAO/Unsubscribe.php';
        CRM_Mailing_Event_BAO_Unsubscribe::unsub_from_mailing($job_id, $queue_id, $hash);
        $displayName = CRM_Mailing_Event_BAO_Unsubscribe::getContactInfo($queue_id);
        
        $this->assign('display_name', $displayName);
        
        parent::run();
    }
}
?>
