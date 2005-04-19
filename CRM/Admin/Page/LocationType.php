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

require_once 'CRM/Page.php';

class CRM_Admin_Page_LocationType {

    /**
     * the location type id being viewed
     * @int
     */
    protected $_locationTypeId;
    
    /**
     * class constructor
     *
     * @return CRM_Page
     */
    function __construct( ) {

    }

    function run($page) 
    {
        $op = CRM_Request::retrieve( 'op', $page, false, 'browse' );
        $page->assign( 'op', $op );
        
        switch ( $op ) {
        case 'dact':
            self::deactivate( );
            break;
            
        case 'edit':
            self::edit($page, CRM_Form::MODE_UPDATE);
            break;
            
        case 'add':
            self::edit( $page, CRM_Form::MODE_ADD );
            break;
        }
        
        self::browse( $page );
    }

    static function browse( $page ) 
    {
        $locationType = new CRM_Contact_DAO_LocationType( );

        $locationType->orderBy( 'name asc' );

        $values = array( );
        $locationType->find( );
        while ( $locationType->fetch( ) ) {
            $values[$locationType->id] = array( );
            $locationType->storeValues( $values[$locationType->id] );
        }
        $page->assign( 'locationTypes', $values );
    }

   static function edit( $page, $mode, $locationTypeId = null ) 
    {
        
        $locationTypeId = $_GET['ltid'];
        $controller = new CRM_Controller_Simple( 'CRM_Admin_Form_LocationType', 'Location Types', $mode );

       // set the userContext stack
        $session = CRM_Session::singleton();
        $config  = CRM_Config::singleton();
        $session->pushUserContext( $config->httpBase . 'admin/contact/locType&op=browse' );
        
        if (!$locationTypeId) {
            $locationTypeId = $controller->get( 'locationTypeId' );
        }

        $controller->reset( );
        $controller->set( 'locationTypeId'   , $locationTypeId );

        $controller->process( );
        $controller->run( );
    }

    static function deactivate ( ) 
    {
        $locationTypeId = $_GET['ltid'];
        $status = $_GET['st'];

        $locType                   = new CRM_Contact_DAO_LocationType( );
        if ($status) {
            $locType->is_active    = false;
        } else {
            $locType->is_active    = true;
        }

        if($locationTypeId) {
            $locType->id = $locationTypeId;
            $locType->save( );
        }

    }
    
}

?>