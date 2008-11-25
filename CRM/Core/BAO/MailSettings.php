<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2008
 * $Id$
 *
 */

require_once 'CRM/Core/DAO/MailSettings.php';

class CRM_Core_BAO_MailSettings extends CRM_Core_DAO_MailSettings
{

    /**
     * Return the DAO object containing to the default row of 
     * civicrm_mail_settings and cache it for further calls
     *
     * @return object  DAO with the default mail settings set
     */
    static function &defaultDAO()
    {
        static $dao = null;
        if (!$dao) {
            $dao = new self;
            $dao->is_default = 1;
            $dao->find(true);
        }
        return $dao;
    }

    /**
     * Return the domain from the default set of settings
     *
     * @return string  default domain
     */
    static function defaultDomain()
    {
        return self::defaultDAO()->domain;
    }

    /**
     * Return the localpart from the default set of settings
     *
     * @return string  default localpart
     */
    static function defaultLocalpart()
    {
        return self::defaultDAO()->localpart;
    }

}
