<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 * Menu for the contribute module
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Menu.php';

class CRM_Quest_Menu {

    static function &main( ) {
        $items = array(
                       array( 
                             'path'    => 'civicrm/quest/preapp', 
                             'query'   => 'reset=1',
                             'title'   => ts( '2006 College Prep Scholarship Application' ), 
                             'access'  => CRM_Core_Permission::check( 'edit Quest Application' ),
                             'type'    => CRM_Core_Menu::CALLBACK,  
                             'crmType' => CRM_Core_Menu::CALLBACK,
                             'weight'  => 0, 
                             ),
                       array( 
                              'path'    => 'civicrm/quest/preapp/view', 
                              'query'   => 'reset=1',
                              'title'   => ts( '2006 College Prep Scholarship Application' ), 
                              'access'  => CRM_Core_Permission::check( 'view Quest Application' ),
                              'type'    => CRM_Core_Menu::CALLBACK,  
                              'crmType' => CRM_Core_Menu::CALLBACK,
                              'weight'  => 0, 
                              ),
                       array( 
                              'path'    => 'civicrm/quest/matchapp', 
                              'query'   => 'reset=1',
                              'title'   => ts( '2006 College Match Application' ), 
                              'access'  => CRM_Core_Permission::check( 'edit Quest Application' ),
                              'type'    => CRM_Core_Menu::CALLBACK,  
                              'crmType' => CRM_Core_Menu::CALLBACK,
                              'weight'  => 0, 
                              ),
                       array( 
                              'path'    => 'civicrm/quest/matchapp/view', 
                              'query'   => 'reset=1',
                              'title'   => ts( '2006 College Match Application' ), 
                              'access'  => CRM_Core_Permission::check( 'view Quest Application' ),
                              'type'    => CRM_Core_Menu::CALLBACK,  
                              'crmType' => CRM_Core_Menu::CALLBACK,
                              'weight'  => 0, 
                              ),
                       array( 
                              'path'    => 'civicrm/quest/schoolsearch', 
                              'query'   => 'reset=1',
                              'title'   => ts( 'QuestBridge School Search' ), 
                              'access'  => CRM_Core_Permission::check( 'edit Quest Application' ),
                              'type'    => CRM_Core_Menu::CALLBACK,  
                              'crmType' => CRM_Core_Menu::CALLBACK,
                              'weight'  => 0, 
                              ),
                       array( 
                              'path'    => 'civicrm/quest/counselor', 
                              'query'   => 'reset=1',
                              'title'   => ts( '2006 College Match Recommendation Form' ), 
                              'access'  => CRM_Core_Permission::check( 'edit Quest Recommendation' ),
                              'type'    => CRM_Core_Menu::CALLBACK,  
                              'crmType' => CRM_Core_Menu::CALLBACK,
                              'weight'  => 0, 
                              ),
                       array( 
                              'path'    => 'civicrm/quest/counselor/view', 
                              'query'   => 'reset=1',
                              'title'   => ts( '2006 College Match Recommendation Form' ), 
                              'access'  => CRM_Core_Permission::check( 'view Quest Recommendation' ),
                              'type'    => CRM_Core_Menu::CALLBACK,  
                              'crmType' => CRM_Core_Menu::CALLBACK,
                              'weight'  => 0, 
                              ),
                       array( 
                              'path'    => 'civicrm/quest/teacher', 
                              'query'   => 'reset=1',
                              'title'   => ts( '2006 College Match Recommendation Form' ), 
                              'access'  => CRM_Core_Permission::check( 'edit Quest Recommendation' ),
                              'type'    => CRM_Core_Menu::CALLBACK,  
                              'crmType' => CRM_Core_Menu::CALLBACK,
                              'weight'  => 0, 
                              ),
                       array( 
                              'path'    => 'civicrm/quest/teacher/view', 
                              'query'   => 'reset=1',
                              'title'   => ts( '2006 College Match Recommendation Form' ), 
                              'access'  => CRM_Core_Permission::check( 'view Quest Recommendation' ),
                              'type'    => CRM_Core_Menu::CALLBACK,  
                              'crmType' => CRM_Core_Menu::CALLBACK,
                              'weight'  => 0, 
                              ),
                       array( 
                             'path'    => 'civicrm/quest/verify',
                             'query'   => 'reset=1',
                             'title'   => ts( 'QuestBridge Recommender Verification' ), 
                             'access'  => 1,
                             'type'    => CRM_Core_Menu::CALLBACK,  
                             'crmType' => CRM_Core_Menu::CALLBACK,
                             'weight'  => 0, 
                             ),
                       
                       );
        return $items;
    }

}

?>
