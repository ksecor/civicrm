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

require_once 'CRM/Utils/Pager.php';
require_once 'CRM/Utils/Sort.php';
require_once 'CRM/Core/Report/Excel.php';
require_once 'CRM/Core/Selector/Controller.php';

class CRM_Contact_Controller extends CRM_Core_Selector_Controller {

    /**
     * Class constructor
     *
     * @param CRM_Core_Selector_API $object  an object that implements the selector API
     * @param int               $pageID  default pageID
     * @param int               $sortID  default sortID
     * @param int               $action  the actions to potentially support
     * @param CRM_Core_Page|CRM_Core_Form $store   place in session to store some values
     * @param int               $output  what do we so with the output, session/template//both
     *
     * @return Object
     * @access public
     */
    function __construct($object, $pageID, $sortID, $action, $store = null, $output = self::TEMPLATE)
    {
        parent:: __construct($object, $pageID, $sortID, $action, $store, $output = self::TEMPLATE);
    }


    /**
     * Heart of the Controller. This is where all the action takes place
     *
     *   - The rows are fetched and stored depending on the type of output needed
     *
     *   - For export/printing all rows are selected.
     *
     *   - for displaying on screen paging parameters are used to display the
     *     required rows.
     *
     *   - also depending on output type of session or template rows are appropriately stored in session
     *     or template variables are updated.
     *
     *
     * @param none
     * @return none
     *
     */
    function run()
    {

        CRM_Core_Error::le_method();


        // get the column headers
        $columnHeaders =& $this->_object->getColumnHeaders( $this->_action, $this->_output );

        // we need to get the rows if we are exporting or printing them
        if ($this->_output == self::EXPORT || $this->_output == self::SCREEN ) {

            CRM_Core_Error::debug_log_message('breakpoint 10');


            // get rows (without paging criteria)
            $rows          =& $this->_object->getRows( $this->_action,
                                                       0, 0,
                                                       $this->_sort,
                                                       $this->_output );
            if ( $this->_output == self::EXPORT ) {
                // export the rows.
                CRM_Core_Report_Excel::writeCSVFile( $this->_object->getExportFileName( ),
                                                     $columnHeaders,
                                                     $rows );
                exit(1);
            } else {
                // assign to template and display them.
                self::$_template->assign_by_ref( 'rows'         , $rows          );
            }
        } else {


            CRM_Core_Error::debug_log_message('breakpoint 20');

            // output requires paging/sorting capability with QILL for search criteria display



            // get rows with paging criteria
            $rows          =& $this->_object->getRows( $this->_action,
                                                       $this->_pagerOffset,
                                                       $this->_pagerRowCount,
                                                       $this->_sort,
                                                       $this->_output );
            $rowsEmpty = count( $rows ) ? false : true;
            
            // get the query in local language - used by search contact selectors.
            $qill = $this->_object->getMyQILL();
            
            // if we need to store in session, lets update session
            if ( $this->_output & self::SESSION ) {

                CRM_Core_Error::debug_log_message('breakpoint 30');

                $this->_store->set( 'columnHeaders', $columnHeaders );
                $this->_store->set( 'rows'         , $rows          );
                $this->_store->set( 'rowCount'     , $this->_total  );
                $this->_store->set( 'rowsEmpty'    , $rowsEmpty     );
                $this->_store->set( 'qill'         , $qill          );
            }



            CRM_Core_Error::debug_log_message('breakpoint 40');

            // always store the current pageID and sortID
            $this->_store->set( CRM_Utils_Pager::PAGE_ID      , $this->_pager->getCurrentPageID       ( ) );
            $this->_store->set( CRM_Utils_Sort::SORT_ID       , $this->_sort->getCurrentSortID        ( ) );
            $this->_store->set( CRM_Utils_Sort::SORT_DIRECTION, $this->_sort->getCurrentSortDirection ( ) );
            $this->_store->set( CRM_Utils_Pager::PAGE_ROWCOUNT, $this->_pager->_perPage                   );
            

            // if we need to display on screen, lets assign vars to the template
            if ( $this->_output & self::TEMPLATE ) {


                CRM_Core_Error::debug_log_message('breakpoint 50');

                parent::$_template->assign_by_ref( 'pager'  , $this->_pager   );
                parent::$_template->assign_by_ref( 'sort'   , $this->_sort    );
                
                parent::$_template->assign_by_ref( 'columnHeaders', $columnHeaders );
                parent::$_template->assign_by_ref( 'rows'         , $rows          );
                parent::$_template->assign       ( 'rowsEmpty'    , $rowsEmpty     );
                parent::$_template->assign       ( 'qill'         , $qill          );
                
                if ( $this->_embedded ) {

                    CRM_Core_Error::debug_log_message('breakpoint 60');
                    return;
                }

                CRM_Core_Error::debug_log_message('breakpoint 70');
                
                parent::$_template->assign( 'tplFile', $this->_object->getTemplateFileName() ); 


                CRM_Core_Error::debug_var('tplFile', $this->_object->getTemplateFileName());
                

                if ( $this->_print ) {
                    $content = parent::$_template->fetch( 'CRM/print.tpl' );
                } else {
                    $content = parent::$_template->fetch( 'CRM/index.tpl' );
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
        parent::$_template->assign('qill', $this->_store->get('qill'));
        parent::moveFromSessionToTemplate();
    }
}

?>
