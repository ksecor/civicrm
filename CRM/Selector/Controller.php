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

require_once 'CRM/Pager.php';
require_once 'CRM/Sort.php';
require_once 'CRM/Report/Excel.php';

class CRM_Selector_Controller {

    /**
     * constants to determine if we should store
     * the output in the session or template
     * @var int
     */
    const
        SESSION   = 1,
        TEMPLATE  = 2,
        BOTH      = 3, // this is primarily done for ease of use
        TRANSFER  = 4; // move the values from the session to the template

    /**
     * a CRM Object that implements CRM_Selector_api
     * @var object
     */
    private $_object;
    
    /*
     * the CRM_Sort object
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
     * the CRM_Pager object
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

    /* the objectAction for the WebObject */
    private $_action;

    /**
     * This caches the content for the display system
     *
     * @var string
     */
    protected $_content;

    /**
     * Output target, session, template or both?
     *
     * @var int
     */
    protected $_output;

    /**
     * Array of properties that the controller dumps into the output object
     *
     * @var array
     * @static
     */
    static $_properties = array( 'pager', 'sort', 'columnHeaders', 'rows', 'rowsEmpty' );

    function __construct($object, $pageID, $sortID, $action, $output = self::TEMPLATE) {
        $this->_object = $object;
        $this->_pageID = $pageID ? $pageID : 1;
        $this->_sortID = $sortID;
        $this->_action = $action;
        $this->_output = $output;
        

        CRM_Error::le_method();

        $params = array(
                        'total'   => $this->_object->getTotalCount($action),
                        'pageID'  => $this->_pageID
                        );
        $this->_object->getPagerParams($action, $params);

        /*
         * Set the default values of RowsPerPage
         */
        // $params['rowCount'] = $params['rowCount'] ? $params['rowCount'] : CRM_Pager::ROWCOUNT;
        // This is a hack to make it easier to debug
        $params['rowCount'] = 5;
        $this->_pager = new CRM_Pager( $params );
        list($this->_pagerOffset, $this->_pagerRowCount) =
        $this->_pager->getOffsetAndRowCount();
        $this->_sortOrder = $this->_object->getSortOrder($action);
        $this->_sort = new CRM_Sort( $this->_sortOrder, $this->_sortID );
    }


    function run( ) {

        CRM_Error::le_method();

        //print $this->_pager->getPageID();
        $config  = CRM_Config::singleton ();
        $session = CRM_Session::singleton();

        if ( $this->_output & self::TRANSFER ) {
            
            CRM_Error::debug_log_message("breakpoint 10");

            $this->moveFromSessionToTemplate( );
            return;
        }

        $pager         = $this->_pager->toArray();
        $sort          = $this->_sort->toArray ();
        $columnHeaders = $this->_object->getColumnHeaders( $this->_action );
        $rows          = $this->_object->getRows( $this->_action,
                                                  $this->_pagerOffset,
                                                  $this->_pagerRowCount,
                                                  $this->_sort );
        $rowsEmpty = count( $rows ) ? false : true;

        if ( $this->_output & self::TEMPLATE ) {
            $template = SmartyTemplate::singleton($config->templateDir, $config->templateCompileDir);
            $template->assign_by_ref( 'config' , $config  );
            $template->assign_by_ref( 'session', $session );

            foreach ( self::$_properties as $property ) {
                $template->assign_by_ref( $property, $$property );
            }

            CRM_Error::debug_log_message("breakpoint 20");

            $this->_content = $template->fetch( $this->_object->getTemplateFileName(), $config->templateDir );
        }

        if ( $this->_output & self::SESSION ) {

            CRM_Error::debug_log_message("breakpoint 30");

            $prefix = $this->_object->getModuleName( $this->_action );
            foreach ( self::$_properties as $property ) {
                $session->set( $property, $$property, $prefix );
            }
        }

        CRM_Error::ll_method();
    }

    function moveFromSessionToTemplate( ) {
        $config  = CRM_Config::singleton ();
        $session = CRM_Session::singleton();

        $prefix = $this->_object->getModuleName( $this->_action );
        
        $template = SmartyTemplate::singleton($config->templateDir, $config->templateCompileDir);
        $template->assign_by_ref( 'config' , $config  );
        $template->assign_by_ref( 'session', $session );

        foreach ( self::$_properties as $property ) {
            $template->assign_by_ref( $property,
                                      $session->get( $property, $prefix ) );
        }

    }

    function getPager() {
        return $this->_pager;
    }

    function getSort() {
        return $this->_sort;
    }
    
    function export() {
        $fileName = $this->_object->getExportFileName     ( $this->_action );
        $headers  = $this->_object->getExportColumnHeaders( $this->_action );
        $rows     = $this->_object->getExportRows         ( $this->_action );

        /**
         * At this point we need to remove the 'action' export from the session
         * Else this might result in an infinite loop
         * Will do this once we figure out how we handle session data
         */

        CRM_Report_Excel::writeCSVFile( $fileName, $headers, $rows );

        exit();
    }

    /**
     * setter for content
     *
     * @param string
     * @return void
     * @access public
     */
    function setContent(&$content) {
        $this->_content =& $content;
    }

    /**
     * getter for content
     *
     * @return void
     * @access public
     */
    function &getContent() {
        return $this->_content;
    }


}

?>
