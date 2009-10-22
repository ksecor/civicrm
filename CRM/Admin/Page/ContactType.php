<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Page/Basic.php';
require_once 'CRM/Contact/BAO/ContactType.php';

/**
 * Page for displaying list of contact Subtypes
 */
class CRM_Admin_Page_ContactType extends CRM_Core_Page_Basic 
{ 
   
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;
    
    /**
     * Get BAO Name
     *
     * @return string Classname of BAO.
     */
    function getBAOName() 
    {
        return 'CRM_Contact_BAO_ContactType';
    }
    
    /**
     * Get action Links
     *
     * @return array (reference) of action links
     */
    
    function &links()
        {
            if (!(self::$_links)) {
                self::$_links =
                    array(
                          CRM_Core_Action::UPDATE  => 
                          array(
                                'name'  => ts('Edit'),
                                'url'   => 'civicrm/admin/options/subtype',
                                'qs'    => 'action=update&id=%%id%%&reset=1',
                                'title' => ts('Edit Contact SubType') 
                                ),
                          CRM_Core_Action::DISABLE => 
                          array(
                                'name'  => ts('Disable'),
                                'extra' => 'onclick = "enableDisable( %%id%%,\''. 
                                'CRM_Contact_BAO_ContactType' . '\',\'' . 'enable-disable' . 
                                '\' );"',
                                'ref'   => 'disable-action',
                                'title' => ts('Disable Contact SubType') 
                                ),
                          CRM_Core_Action::ENABLE  => 
                          array(
                                'name'  => ts('Enable'),
                                'extra' => 'onclick = "enableDisable( %%id%%,\''. 
                                'CRM_Contact_BAO_ContactType' . '\',\'' . 'disable-enable' .
                                '\' );"',
                                'ref'   => 'enable-action',
                                'title' => ts('Enable Contact SubType') 
                                ),
                          CRM_Core_Action::DELETE  => 
                          array(
                                'name'  => ts('Delete'),
                                'url'   => 'civicrm/admin/options/subtype',
                                'qs'    => 'action=delete&id=%%id%%',
                                'title' => ts('Delete Contact SubType') 
                                )
                          );
            }
            return self::$_links;
        }
    
    function run()
    {   
        $action = CRM_Utils_Request::retrieve( 'action', 'String',$this, false, 0 ); 
        $this->assign( 'action', $action );
        $id = CRM_Utils_Request::retrieve( 'id', 'Positive',$this, false, 0 );
        if( !$action ) {
            $this->browse( );
        }
        parent::run();    
    }
    function browse()
    {  
        $rows = CRM_Contact_BAO_ContactType::subTypeInfo( null , true );
        foreach( $rows  as $key=>$value ) { 
            $rows[$key]['action'] = CRM_Core_Action::formLink( self::links(), null, 
                                                               array('id' =>$value['id'] ) );
        }
        $this->assign( 'rows' ,$rows);
    }
    
    /**
     * Get name of edit form
     *
     * @return string Classname of edit form.
     */
    function editForm() 
    {
        return 'CRM_Admin_Form_ContactType';
    }
    
    /**
     * Get edit form name
     *
     * @return string name of this page.
     */
    function editName() 
    {
        return 'Contact Types';
    }
    
    /**
     * Get user context.
     *
     * @return string user context.
     */
    function userContext($mode = null) 
    {
        return 'civicrm/admin/options/subtype';
    }
}

