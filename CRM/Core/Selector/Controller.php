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

class CRM_Core_Selector_Controller {

    /**
     * constants to determine if we should store
     * the output in the session or template
     * @var int
     */
    const
        SESSION   = 1,
        TEMPLATE  = 2,
        TRANSFER  = 4, // move the values from the session to the template
        EXPORT    = 8,
        SCREEN    = 16;

    /**
     * a CRM Object that implements CRM_Core_Selector_API
     * @var object
     */
    private $_object;
    
    /*
     * the CRM_Utils_Sort object
     * @var object
     */
    private $_sort;

    /*
     * the current column to sort on
     * @var int
     */
    private $_sortID;

    /*
     * the sortOrder array
     * @var array
     */
    private $_sortOrder;

    /*
     * the CRM_Utils_Pager object
     * @var object
     */
    private $_pager;

    /*
     * the pageID
     * @var int
     */
    private $_pageID;
    
    /*
     * offset
     * @var int
     */
    private $_pagerOffset;

    /**
     * number of rows to return
     * @var int
     */
    private $_pagerRowCount;

    /**
     * total number of rows
     * @var int
     */
    private $_total;

    /* the objectAction for the WebObject */
    private $_action;

    /**
     * This caches the content for the display system
     *
     * @var string
     */
    protected $_content;

    /**
     * Is this object being embedded in another object. If
     * so the display routine needs to not do any work. (The
     * parent object takes care of the display)
     *
     * @var boolean
     */
    protected $_embedded = false;

    /**
     * Are we in print mode? if so we need to modify the display
     * functionality to do a minimal display :)
     *
     * @var boolean
     */
    protected $_print = false;

    /**
     * cache the smarty template for efficiency reasons
     *
     * @var CRM_Core_Smarty
     */
    static protected $_template;

    /**
     * Array of properties that the controller dumps into the output object
     *
     * @var array
     * @static
     */
    static $_properties = array( 'columnHeaders', 'rows', 'rowsEmpty' );

    /**
     * The storage object (typically a form or a page)
     *
     * @var Object
     */
    protected $_store;

    /**
     * Output target, session, template or both?
     *
     * @var int
     */
    protected $_output;

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
    function __construct($object, $pageID, $sortID, $action, $store = null, $output = self::TEMPLATE) {
        $this->_object = $object;
        $this->_pageID = $pageID ? $pageID : 1;
        $this->_sortID = $sortID ? $sortID : null;
        $this->_action = $action;
        $this->_store  = $store;
        $this->_output = $output;

        $params = array(
                        'pageID'  => $this->_pageID
                        );

        // let the constructor initialize this, should happen only once
        if ( ! isset( self::$_template ) ) {
            self::$_template = CRM_Core_Smarty::singleton( );
        }

        $this->_sortOrder =& $this->_object->getSortOrder($action);
        $this->_sort      =  new CRM_Utils_Sort( $this->_sortOrder, $this->_sortID );

        /*
         * if we are in transfer mode, do not goto database, use the 
         * session values instead
         */
        if ( $output == self::TRANSFER ) {
            $params['total'] = $this->_store->get( 'rowCount' );
        } else {
            $params['total'] = $this->_object->getTotalCount($action);
        }
        $this->_total = $params['total'];
        $this->_object->getPagerParams($action, $params);

        /*
         * Set the default values of RowsPerPage
         */
        $storeRowCount = $store->get( CRM_Utils_Pager::PAGE_ROWCOUNT );
        if ( $storeRowCount ) {
            $params['rowCount'] = $storeRowCount;
        } else if ( ! isset( $params['rowCount'] ) ) {
            $params['rowCount'] = CRM_Utils_Pager::ROWCOUNT;
        }

        $this->_pager = new CRM_Utils_Pager( $params );
        list($this->_pagerOffset, $this->_pagerRowCount) = $this->_pager->getOffsetAndRowCount();
    }

    /**
     * have the GET vars changed, i.e. pageId or sortId that forces us to recompute the search values
     *
     * @param int $reset are we being reset
     *
     * @return boolean   if the GET params are different from the session params
     * @access public
     */
    function hasChanged( $reset ) {
        /**
         * if we are in reset state, i.e the store is cleaned out, we return false
         * we also return if we dont have a record of the sort id or page id
         */
        if ( $reset || $this->_store->get( CRM_Utils_Pager::PAGE_ID ) == null || $this->_store->get( CRM_Utils_Sort::SORT_ID ) == null ) {
            return false;
        }

        /*
        echo "Current Sort ID: " . $this->_sort->getCurrentSortID ( ) . '_' . $this->_sort->getCurrentSortDirection ( ) . "<p>";
        echo "Stored Sort ID: " . $this->_store->get( CRM_Utils_Sort::SORT_ID  ) . '_' . $this->_store->get( CRM_Utils_Sort::SORT_DIRECTION ) . "<p>";
        */

        if ( $this->_store->get( CRM_Utils_Pager::PAGE_ID       ) != $this->_pager->getCurrentPageID       ( ) ||
             $this->_store->get( CRM_Utils_Sort::SORT_ID        ) != $this->_sort->getCurrentSortID        ( ) || 
             $this->_store->get( CRM_Utils_Sort::SORT_DIRECTION ) != $this->_sort->getCurrentSortDirection ( ) ) {
            return true;
        }
        return false;
    }

    function run( ) {
        $columnHeaders =& $this->_object->getColumnHeaders( $this->_action, $type );
        if ( $this->_output == self::EXPORT || $this->_output == self::SCREEN ) {
            $rows          =& $this->_object->getRows( $this->_action,
                                                       0, 0,
                                                       $this->_sort,
                                                       $this->_output );
            if ( $this->_output == self::EXPORT ) {
                CRM_Core_Report_Excel::writeCSVFile( $this->_object->getExportFileName( ),
                                                     $columnHeaders,
                                                     $rows );
                exit(1);
            } else {
                self::$_template->assign_by_ref( 'rows'         , $rows          );
            }
        } else {
            $rows          =& $this->_object->getRows( $this->_action,
                                                       $this->_pagerOffset,
                                                       $this->_pagerRowCount,
                                                       $this->_sort,
                                                       $this->_output );
            $rowsEmpty = count( $rows ) ? false : true;
            $qill = $this->_object->getMyQILL();
            
            if ( $this->_output & self::SESSION ) {
                $this->_store->set( 'columnHeaders', $columnHeaders );
                $this->_store->set( 'rows'         , $rows          );
                $this->_store->set( 'rowCount'     , $this->_total  );
                $this->_store->set( 'rowsEmpty'    , $rowsEmpty     );
                $this->_store->set( 'qill'         , $qill          );
            }

            // always store the current pageID and sortID
            $this->_store->set( CRM_Utils_Pager::PAGE_ID      , $this->_pager->getCurrentPageID       ( ) );
            $this->_store->set( CRM_Utils_Sort::SORT_ID       , $this->_sort->getCurrentSortID        ( ) );
            $this->_store->set( CRM_Utils_Sort::SORT_DIRECTION, $this->_sort->getCurrentSortDirection ( ) );
            $this->_store->set( CRM_Utils_Pager::PAGE_ROWCOUNT, $this->_pager->_perPage                   );
            
            if ( $this->_output & self::TEMPLATE ) {
                self::$_template->assign_by_ref( 'pager'  , $this->_pager   );
                self::$_template->assign_by_ref( 'sort'   , $this->_sort    );
                
                self::$_template->assign_by_ref( 'columnHeaders', $columnHeaders );
                self::$_template->assign_by_ref( 'rows'         , $rows          );
                self::$_template->assign       ( 'rowsEmpty'    , $rowsEmpty     );
                self::$_template->assign       ( 'qill'         , $qill          );
                
                if ( $this->_embedded ) {
                    return;
                }
            
                self::$_template->assign( 'tplFile', $this->_object->getTemplateFileName() ); 
                if ( $this->_print ) {
                    $content = self::$_template->fetch( 'CRM/print.tpl' );
                } else {
                    $content = self::$_template->fetch( 'CRM/index.tpl' );
                }
                echo CRM_Utils_System::theme( 'page', $content, null, $this->_print );
            }
        }

    }
    
    function getPager() {
        return $this->_pager;
    }

    function getSort() {
        return $this->_sort;
    }
    
    /**
     * Move the variables from the session to the template
     *
     * @return void
     * @access public
     */
    function moveFromSessionToTemplate( ) {
        self::$_template->assign_by_ref( 'pager'  , $this->_pager   );
        self::$_template->assign_by_ref( 'sort'   , $this->_sort    );

        self::$_template->assign_by_ref( 'columnHeaders', $this->_store->get( 'columnHeaders' ) );
        self::$_template->assign_by_ref( 'rows'         , $this->_store->get( 'rows' )          );
        self::$_template->assign       ( 'rowsEmpty'    , $this->_store->get( 'rowsEmpty' )     );
        self::$_template->assign       ( 'qill'         , $this->_store->get( 'qill' )          );

        if ( $this->_embedded ) {
            return;
        }
            
        self::$_template->assign( 'tplFile', $this->_object->getTemplateFileName() );
        if ( $this->_print ) {
            $content = self::$_template->fetch( 'CRM/print.tpl' );
        } else {
            $content = self::$_template->fetch( 'CRM/index.tpl' );
        }
        echo CRM_Utils_System::theme( 'page', $content, null, $this->_print );
    }

    /**
     * setter for embedded 
     *
     * @param boolean $embedded
     *
     * @return void
     * @access public
     */
    function setEmbedded( $embedded  ) {
        $this->_embedded = $embedded;
    }

    /**
     * getter for embedded 
     *
     * @return boolean return the embedded value
     * @access public
     */
    function getEmbedded( ) {
        return $this->_embedded;
    }

    /**
     * setter for print 
     *
     * @param boolean $print
     *
     * @return void
     * @access public
     */
    function setPrint( $print  ) {
        $this->_print = $print;
    }

    /**
     * getter for print 
     *
     * @return boolean return the print value
     * @access public
     */
    function getPrint( ) {
        return $this->_print;
    }

}

?>
