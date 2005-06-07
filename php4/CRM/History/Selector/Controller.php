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
 * This class is a generic class to be used when we want to display
 * a list of rows along with a set of associated actions
 *
 * Centralizing this code enables us to write a generic lister and enables
 * us to automate the export process. To use this class, the object has to
 * implement the Selector/Api.interface.php class
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */



require_once 'CRM/Core/Selector/Controller.php';
require_once 'CRM/Core/Error.php';
require_once 'CRM/Core/Report/Excel.php';
require_once 'CRM/Utils/System.php';
require_once 'CRM/Utils/Pager.php';
require_once 'CRM/Utils/Sort.php';
require_once 'CRM/Core/Report/Excel.php';
require_once 'CRM/Core/Selector/Controller.php';

class CRM_History_Selector_Controller extends CRM_Core_Selector_Controller {


    function CRM_History_Selector_Controller($object, $pageID, $sortID, $action, $store = null, $output = CRM_HISTORY_SELECTOR_CONTROLLER_TEMPLATE)
    {
        CRM_Core_Error::le_method();
        CRM_Core_Error::debug_var('object', $object);
        parent:: CRM_History_Selector_Controller($object, $pageID, $sortID, $action, $store, $output = CRM_HISTORY_SELECTOR_CONTROLLER_TEMPLATE);
        //CRM_Core_Error::debug_var('store', $store);
        CRM_Core_Error::ll_method();
    }


    function run()
    {

        CRM_Core_Error::le_method();

        CRM_Core_Error::debug_var('this', $this);

        $columnHeaders =& $this->_object->getColumnHeaders( $this->_action, $this->_output );

        if ($this->_output == CRM_HISTORY_SELECTOR_CONTROLLER_EXPORT|| $this->_output == CRM_HISTORY_SELECTOR_CONTROLLER_SCREEN) {
            $rows          =& $this->_object->getRows( $this->_action,
                                                       0, 0,
                                                       $this->_sort,
                                                       $this->_output );
            if ( $this->_output == CRM_HISTORY_SELECTOR_CONTROLLER_EXPORT) {
                CRM_Core_Report_Excel::writeCSVFile( $this->_object->getExportFileName( ),
                                                     $columnHeaders,
                                                     $rows );
                exit(1);
            } else {
                $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->assign_by_ref( 'rows'         , $rows          );
            }
        } else {
            $rows          =& $this->_object->getRows( $this->_action,
                                                       $this->_pagerOffset,
                                                       $this->_pagerRowCount,
                                                       $this->_sort,
                                                       $this->_output );
            $rowsEmpty = count( $rows ) ? false : true;
            
            if ( $this->_output & CRM_HISTORY_SELECTOR_CONTROLLER_SESSION) {
                $this->_store->set( 'columnHeaders', $columnHeaders );
                $this->_store->set( 'rows'         , $rows          );
                $this->_store->set( 'rowCount'     , $this->_total  );
                $this->_store->set( 'rowsEmpty'    , $rowsEmpty     );
            }

            // always store the current pageID and sortID
            $this->_store->set( CRM_UTILS_PAGER_PAGE_ID      , $this->_pager->getCurrentPageID       ( ) );
            $this->_store->set( CRM_UTILS_SORT_SORT_ID       , $this->_sort->getCurrentSortID        ( ) );
            $this->_store->set( CRM_UTILS_SORT_SORT_DIRECTION, $this->_sort->getCurrentSortDirection ( ) );
            $this->_store->set( CRM_UTILS_PAGER_PAGE_ROWCOUNT, $this->_pager->_perPage                   );
            
            if ( $this->_output & CRM_HISTORY_SELECTOR_CONTROLLER_TEMPLATE) {
                $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->assign_by_ref( 'pager'  , $this->_pager   );
                $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->assign_by_ref( 'sort'   , $this->_sort    );
                
                $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->assign_by_ref( 'columnHeaders', $columnHeaders );
                $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->assign_by_ref( 'rows'         , $rows          );
                $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->assign       ( 'rowsEmpty'    , $rowsEmpty     );
                
                if ( $this->_embedded ) {
                    return;
                }
            
                $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->assign( 'tplFile', $this->_object->getTemplateFileName() ); 
                if ( $this->_print ) {
                    $content = $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->fetch( 'CRM/print.tpl' );
                } else {
                    $content = $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->fetch( 'CRM/index.tpl' );
                }
                echo CRM_Utils_System::theme( 'page', $content, null, $this->_print );
            }
        }

    }
    
    
    /**
     * Move the variables from the session to the template
     *
     * @return void
     * @access public
     */
    function moveFromSessionToTemplate()
    {
        $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->assign_by_ref( 'pager'  , $this->_pager   );
        $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->assign_by_ref( 'sort'   , $this->_sort    );
        
        $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->assign_by_ref( 'columnHeaders', $this->_store->get( 'columnHeaders' ) );
        $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->assign_by_ref( 'rows'         , $this->_store->get( 'rows' )          );
        $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->assign       ( 'rowsEmpty'    , $this->_store->get( 'rowsEmpty' )     );

        if ($this->_embedded) {
            return;
        }
            
        $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->assign( 'tplFile', $this->_object->getTemplateFileName() );
        if ( $this->_print ) {
            $content = $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->fetch( 'CRM/print.tpl' );
        } else {
            $content = $GLOBALS['_CRM_HISTORY_SELECTOR_CONTROLLER']['_template']->fetch( 'CRM/index.tpl' );
        }
        echo CRM_Utils_System::theme( 'page', $content, null, $this->_print );
    }
}

?>
