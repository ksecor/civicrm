<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
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
 * This class handles all SOAP client requests.
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'api/utils.php';
require_once 'api/Contact.php';
require_once 'api/Mailer.php';

class CRM_Utils_SoapServer
{
    /**
     * Cache the actual UF Class
     */
    public $ufClass;

    /**
     * Class constructor.  This caches the real user framework class locally,
     * so we can use it for authentication and validation.
     *
     * @param  string $uf       The userframework class
     */
    public function __construct() {
        $this->ufClass = array_shift(func_get_args());
    }

    /**
     * Simple PING function to test for liveness.
     *
     * @param string $var   The string to be echoed
     * @return string       The ponged string
     * @access public
     */
    public function ping($var) {
        $session =& CRM_Core_Session::singleton();
        $key = $session->get('key');
        return "PONG: $var ($key)";
    }

    
    /**
     * Authentication wrapper to the UF Class
     *
     * @param string $name      Login name
     * @param string $pass      Password
     * @return string           The SOAP Client key
     * @access public
     * @static
     */
    public function authenticate($name, $pass) {
        if (empty($this->ufClass)) {
            return null;
        }

        eval ('$result =& ' . $this->ufClass . '::authenticate($name, $pass);');

        if (empty($result)) {
            return null;
        }
        return $_SESSION;

        return $result[2];
    }

}

?>
