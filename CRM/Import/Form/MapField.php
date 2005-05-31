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

require_once 'CRM/Core/Form.php';

/**
 * This class gets the name of the file to upload
 */
class CRM_Import_Form_MapField extends CRM_Core_Form {

    /**
     * cache of preview data values
     *
     * @var array
     * @access protected
     */
    protected $_dataValues;

    /**
     * mapper fields
     *
     * @var array
     * @access protected
     */
    protected $_mapperFields;

    /**
     * number of columns in import file
     *
     * @var int
     * @access protected
     */
    protected $_columnCount;

    /**
     * Function to set variables up before form is built
     *
     * @param none
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $this->_mapperFields = $this->get( 'fields' );

        $this->_columnCount = $this->get( 'columnCount' );
        $this->assign( 'columnCount' , $this->_columnCount );
        $this->assign( 'dataValues'  , $this->get( 'dataValues' ) );
        
        $skipColumnHeader = $this->controller->exportValue( 'UploadFile', 'skipColumnHeader' );

        if ( $skipColumnHeader ) {
            $this->assign( 'skipColumnHeader' , $skipColumnHeader );
            $this->assign( 'rowDisplayCount', 3 );
        } else {
            $this->assign( 'rowDisplayCount', 2 );
        }
        
    }

    /**
     * Function to actually build the form
     *
     * @param none
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        $this->_defaults = array( );
        $mapperKeys      = array_keys( $this->_mapperFields );

        for ( $i = 0; $i < $this->_columnCount; $i++ ) {
            $this->add( 'select', "mapper[$i]", "Mapper for Field $i", $this->_mapperFields );
            $this->_defaults["mapper[$i]"] = $mapperKeys[$i];
        }
        $this->setDefaults( $this->_defaults );

        $this->addButtons( array(
                                 array ( 'type'      => 'back',
                                         'name'      => ts('<< Previous') ),
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Continue >>'),
                                         'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    /**
     * Process the mapped fields and map it into the uploaded file
     * preview the file and extract some summary statistics
     *
     * @param none
     * @return void
     * @access public
     */
    public function postProcess()
    {
        $fileName         = $this->controller->exportValue( 'UploadFile', 'uploadFile' );
        $skipColumnHeader = $this->controller->exportValue( 'UploadFile', 'skipColumnHeader' );

        $seperator = ',';

        $mapperKeys = array( );
        $mapper     = array( );
        $mapperKeys = $this->controller->exportValue( $this->_name, 'mapper' );
        for ( $i = 0; $i < $this->_columnCount; $i++ ) {
            $mapper[$i]     = $this->_mapperFields[$mapperKeys[$i]];
        }

        $this->set( 'mapper'    , $mapper     );

        $parser = new CRM_Import_Parser_Contact( $mapperKeys );
        $parser->run( $fileName, $seperator,
                      $mapper, 
                      $skipColumnHeader,
                      CRM_Import_Parser::MODE_SUMMARY );

        // add all the necessary variables to the form
        $parser->set( $this );
    }

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @param none
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('Match Fields');
    }

    
}

?>
