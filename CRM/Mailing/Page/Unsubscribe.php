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

class CRM_Mailing_Page_Unsubscribe extends CRM_Core_Page 
{
    function run() {
        require_once 'CRM/Utils/Request.php';
        $job_id   = CRM_Utils_Request::retrieve( 'jid', 'Integer', CRM_Core_DAO::$_nullObject );
        $queue_id = CRM_Utils_Request::retrieve( 'qid', 'Integer', CRM_Core_DAO::$_nullObject );
        $hash     = CRM_Utils_Request::retrieve( 'h'  , 'String' , CRM_Core_DAO::$_nullObject );
        
        if ( ! $job_id   ||
             ! $queue_id ||
             ! $hash ) {
            CRM_Core_Error::fatal( ts( "Missing input parameters" ) );
        }

        $cancel  = CRM_Utils_Request::retrieve( '_qf_unsubscribe_cancel', 'String', CRM_Core_DAO::$_nullObject,
                                                false, null, $_REQUEST );
        if ( $cancel ) {
            $config = CRM_Core_Config::singleton( );
            CRM_Utils_System::redirect( $config->userFrameworkBaseURL );
        }

        $confirm = CRM_Utils_Request::retrieve( 'confirm', 'Boolean', CRM_Core_DAO::$_nullObject,
                                                false, null, $_REQUEST );

        require_once 'CRM/Mailing/Event/BAO/Queue.php';
        list( $displayName, $email ) = CRM_Mailing_Event_BAO_Queue::getContactInfo($queue_id);
        $this->assign( 'display_name', $displayName);
        $this->assign( 'email'       , $email );
        $this->assign( 'confirm'     , $confirm );

        if ( $confirm ) { 
            require_once 'CRM/Mailing/Event/BAO/Unsubscribe.php';
            CRM_Mailing_Event_BAO_Unsubscribe::unsub_from_mailing($job_id, $queue_id, $hash);
        } else {
            $confirmURL = CRM_Utils_System::url( 'civicrm/mailing/unsubscribe',
                                                 "reset=1&jid={$job_id}&qid={$queue_id}&h={$hash}&confirm=1" );
            $this->assign( 'confirmURL', $confirmURL );
        }
        
        parent::run();
    }
}
?>
