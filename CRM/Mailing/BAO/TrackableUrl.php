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

require_once 'CRM/Mailing/DAO/TrackableUrl.php';

class CRM_Mailing_BAO_TrackableUrl extends CRM_Mailing_DAO_TrackableUrl {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Given a url, mailing id and queue event id, find or construct a
     * trackable url and redirect url.
     *
     * @param string $url       The target url to track
     * @param int $mailing_id   The id of the mailing
     * @param int $queue_id     The queue event id (contact clicking through)
     * @return string $redirect The redirect/tracking url
     * @static
     */
    public static function getTrackerURL($url, $mailing_id, $queue_id) {
        $redirect = "http://FIXME.COM/";
        $tracker =& new CRM_Mailing_BAO_TrackableUrl();
        $tracker->url = $url;
        $tracker->mailing_id = $mailing_id;
        
        if (! $tracker->find(true)) {
            $tracker->save();
        }
        $id = $tracker->id;
        
        return $redirect . "?q=$queue_id&u=$id";
    }

    public static function scan_and_replace(&$msg, $mailing_id, $queue_id) {
        preg_replace(
//     '|(?:([^:/?#]+):)?(?://([^/?#]*))?([^?#]*)(?:\?([^#]*))?(?: +#(.*))|e', 
    '|(https?(?://([^/?#]*))?([^?#]*)(?:\?([^#]*))?(?: +#(.*))|eim', 
    "CRM_Mailing_BAO_TrackableUrl::getTrackerURL('\\1', $mailing_id, $queue_id)", 
    $msg);
    }
}

?>
