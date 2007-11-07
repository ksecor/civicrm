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

require_once 'CRM/Core/Component/Info.php';

/**
 * This class introduces component to the system and provides all the 
 * information about it. It needs to extend CRM_Core_Component_Info
 * abstract class.
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */
class CRM_Mailing_Info extends CRM_Core_Component_Info
{

    // docs inherited from interface
    public function getInfo()
    {
        return array( 'name'           => 'CiviMail',
                      'translatedName' => ts('CiviMail'),
                      'title'          => 'CiviCRM Mailing Engine',
                      'url'            => 'mailing',
                      'perm'           => array( 'access CiviMail', 'access CiviMail subscribe/unsubscribe pages' ),
                      'search'         => 0 );
    }
    
    // docs inherited from interface    
    public function getActivityTypes()
    {
        return null;
    }
    
}
