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

/**
 * defines a simple implemenation of a drupal block.
 * blocks definitions and html are in a smarty template file
 *
 */
define( 'CRM_CORE_BLOCK_SHORTCUTS',0);
define( 'CRM_CORE_BLOCK_ADD',1);
define( 'CRM_CORE_BLOCK_SEARCH',2);
define( 'CRM_CORE_BLOCK_MENU',4);
$GLOBALS['_CRM_CORE_BLOCK']['_properties'] =  array(
                                   CRM_CORE_BLOCK_SHORTCUTS   => array( 'template' => 'Shortcuts.tpl',
                                                               'info'     => 'CiviCRM Shortcuts',
                                                               'subject'  => 'CiviCRM Shortcuts',
                                                               'active'   => true ),
                                   CRM_CORE_BLOCK_ADD         => array( 'template' => 'Add.tpl',
                                                               'info'     => 'CiviCRM Quick Add',
                                                               'subject'  => 'New Individual',
                                                               'active'   => true ),
                                   CRM_CORE_BLOCK_SEARCH      => array( 'template' => 'Search.tpl',
                                                               'info'     => 'CiviCRM Search',
                                                               'subject'  => 'Contact Search',
                                                               'active'   => true ),
                                   CRM_CORE_BLOCK_MENU        => array( 'template' => 'Menu.tpl',
                                                               'info'     => 'CiviCRM Menu',
                                                               'subject'  => 'CiviCRM',
                                                               'active'   => true ),
                                   
                                   );
$GLOBALS['_CRM_CORE_BLOCK']['shortCuts'] =  array( array( 'path'  => 'civicrm/contact/addI',
                                          'qs'    => 'c_type=Individual&reset=1',
                                          'title' => 'New Individual' ),
                                   array( 'path'  => 'civicrm/contact/addO',
                                          'qs'    => 'c_type=Organization&reset=1',
                                          'title' => 'New Organization' ),
                                   array( 'path'  => 'civicrm/contact/addH',
                                          'qs'    => 'c_type=Household&reset=1',
                                          'title' => 'New Household' ),
                                   array( 'path'  => 'civicrm/group/add',
                                          'qs'    => 'reset=1',
                                          'title' => 'New Group' ) );


require_once 'CRM/Utils/System.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/Smarty.php';
class CRM_Core_Block {

    /**
     * the following blocks are supported
     *
     * @var int
     */
    
          
                
             
               /**
     * template file names for the above blocks
     */
    

                                                    
    /**
     * class constructor
     *
     */
    function CRM_Core_Block( ) {
    }

    /**
     * Creates the info block for drupal
     *
     * @return array 
     * @access public
     */
     function getInfo( ) {
        $block = array( );
        foreach ( $GLOBALS['_CRM_CORE_BLOCK']['_properties'] as $id => $value ) {
            if ( $value['active'] ) {
                $block[$id]['info'] = $value['info'];
            }
        }
        return $block;
    }

    /**
     * set the post action values for the block.
     *
     * php is lame and u cannot call functions from static initializers
     * hence this hack
     *
     * @return void
     * @access private
     */
     function setTemplateValues( $id ) {
        if ( $id == CRM_CORE_BLOCK_SHORTCUTS) {
            CRM_Core_Block::setTemplateShortcutValues( );
        } else if ( $id == CRM_CORE_BLOCK_ADD) {
            $GLOBALS['_CRM_CORE_BLOCK']['_properties'][CRM_CORE_BLOCK_ADD]['templateValues'] =
                array( 'postURL'           => CRM_Utils_System::url( 'civicrm/contact/addI', 'reset=1&c_type=Individual' ) );
        } else if ( $id == CRM_CORE_BLOCK_SEARCH) {
            $GLOBALS['_CRM_CORE_BLOCK']['_properties'][CRM_CORE_BLOCK_SEARCH]['templateValues'] =
                array( 'postURL'           => CRM_Utils_System::url( 'civicrm/contact/search', 'reset=1' ) ,
                       'advancedSearchURL' => CRM_Utils_System::url( 'civicrm/contact/search/advanced', 'reset=1' ) );
        } else if ( $id == CRM_CORE_BLOCK_MENU) {
            CRM_Core_Block::setTemplateMenuValues( );
        }
    }

    /**
     * create the list of shortcuts for the application and format is as a block
     *
     * @return void
     * @access private
     */
     function setTemplateShortcutValues( ) {
        

        $values = array( );
        foreach ( $GLOBALS['_CRM_CORE_BLOCK']['shortCuts'] as $short ) {
            $value = array( );
            $value['url'  ] = CRM_Utils_System::url( $short['path'], $short['qs'] );
            $value['title'] = $short['title'];
            $values[] = $value;
        }
        $GLOBALS['_CRM_CORE_BLOCK']['_properties'][CRM_CORE_BLOCK_SHORTCUTS]['templateValues'] = array( 'shortCuts' => $values );
    }

    /**
     * create the list of shortcuts for the application and format is as a block
     *
     * @return void
     * @access private
     */
     function setTemplateMenuValues( ) {
        $items = civicrm_menu( true );
        $values = array( );

        foreach ( $items as $item ) {
            if ( $item['type'] == MENU_NORMAL_ITEM && $item['access'] ) {
                $value = array( );
                $value['url'  ] = CRM_Utils_System::url( $item['path'], CRM_Utils_Array::value( 'qs', $item ) );
                $value['title'] = $item['title'];
                $value['class'] = 'leaf';
                if ( strpos( $_REQUEST['q'], $item['path'] ) === 0 ) {
                    $value['active'] = 'class="active"';
                } else {
                    $value['active'] = '';
                }
                $values[$item['weight']] = $value;
            }
        }
        ksort($values);
        $GLOBALS['_CRM_CORE_BLOCK']['_properties'][CRM_CORE_BLOCK_MENU]['templateValues'] = array( 'menu' => $values );
    }

    /**
     * Given an id creates a subject/content array
     *
     * @param int $id id of the block
     *
     * @return array
     * @access public
     */
     function getContent( $id ) {
        CRM_Core_Block::setTemplateValues( $id );
        $block = array( );
        if ( ! $GLOBALS['_CRM_CORE_BLOCK']['_properties'][$id]['active'] ) {
            return null;
        }

        $block['subject'] = CRM_Core_Block::fetch( $id, 'Subject.tpl',
                                         array( 'subject' => $GLOBALS['_CRM_CORE_BLOCK']['_properties'][$id]['subject'] ) );
        $block['content'] = CRM_Core_Block::fetch( $id, $GLOBALS['_CRM_CORE_BLOCK']['_properties'][$id]['template'],
                                         $GLOBALS['_CRM_CORE_BLOCK']['_properties'][$id]['templateValues'] );

        return $block;
    }

    /**
     * Given an id and a template, fetch the contents
     *
     * @param int    $id         id of the block
     * @param string $fileName   name of the template file
     * @param array  $properties template variables
     *
     * @return array
     * @access public
     */
     function fetch( $id, $fileName, $properties ) {
        $template =& CRM_Core_Smarty::singleton( );

        if ( $properties ) {
            $template->assign( $properties );
        }

        return $template->fetch( "CRM/Block/" . $fileName );
    }

}

?>