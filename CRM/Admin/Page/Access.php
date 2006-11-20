<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author David Greenberg <dave@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/Page/Basic.php';

/**
 * Dashboard page for managing Access Control
 * For initial version, this page only contains static links - so this class is empty for now.
 */
class CRM_Admin_Page_Access extends CRM_Core_Page 
{
    function run( ) {
        $config =& CRM_Core_Config::singleton( );
        if ( $config->userFrameworkVersion < 5 ) {
            $ufAccessURL = CRM_Utils_System::url( 'admin/access' );
        } else {
            $ufAccessURL = CRM_Utils_System::url( 'admin/user/access' );
        }
        
        $this->assign('ufAccessURL', $ufAccessURL);
        return parent::run();
    }
}

?>
