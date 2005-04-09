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

require_once 'CRM/Form.php';

/**
 * This class gets the name of the file to upload
 */
class CRM_Import_Form_MapField extends CRM_Form {

    /**
     * cache of preview data values
     *
     * @var array
     */
    protected $_dataValues;

    /**
     * mapper fields
     *
     * @var array
     */
    protected $_mapperFields;

    /**
     * number of columns in import file
     *
     * @var int
     */
    protected $_columnCount;

    /**
     * class constructor
     */
    function __construct($name, $state, $mode = self::MODE_NONE) {
        parent::__construct($name, $state, $mode);
    }

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess( ) {
        $this->_mapperFields = $this->get( 'fields' );

        $this->_columnCount = $this->get( 'columnCount' );
        $this->assign( 'columnCount' , $this->_columnCount );
        $this->assign( 'dataValues'  , $this->get( 'dataValues' ) );
        $this->assign( 'rowDisplayCount', 2 );
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        $this->_defaults = array( );
        $mapperKeys      = array_keys( $this->_mapperFields );

        for ( $i = 0; $i < $this->_columnCount; $i++ ) {
            $this->add( 'select', "mapper[$i]", "Mapper for Field $i", $this->_mapperFields );
            $this->_defaults["mapper[$i]"] = $mapperKeys[$i];
        }
        $this->setDefaults( $this->_defaults );

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => 'Continue',
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'back',
                                         'name'      => 'Previous' ),
                                 array ( 'type'      => 'reset',
                                         'name'      => 'Reset'),
                                 array ( 'type'      => 'cancel',
                                         'name'      => 'Cancel' ),
                                 )
                           );
    }

    /**
     * Process the mapped fields
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        $fileName  = $this->controller->exportValue( 'UploadFile', 'uploadFile' );
        $seperator = ',';

        $mapperKeys = array( );
        $mapper     = array( );
        $mapperKeys = $this->controller->exportValue( $this->_name, 'mapper' );
        for ( $i = 0; $i < $this->_columnCount; $i++ ) {
            $mapper[$i]     = $this->_mapperFields[$mapperKeys[$i]];
        }

        $this->set( "mapper"    , $mapper     );

        $parser = new CRM_Import_Parser_Contact( $mapperKeys );
        $parser->import( $fileName, $seperator, CRM_Import_Parser::MODE_SUMMARY );

        // add all the necessary variables to the form
        $parser->set( $this );
    }

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) {
        return 'Match Fields';
    }

    
}

?>