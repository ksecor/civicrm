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
 * This class previews the uplaoded file and returns summary
 * statistics
 */
class CRM_Import_Form_Preview extends CRM_Core_Form {

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $skipColumnHeader = $this->controller->exportValue( 'UploadFile', 'skipColumnHeader' );
       
        //get the data from the session
        $dataValues         = $this->get('dataValues');
        $mapper             = $this->get('mapper');
        $invalidRowCount    = $this->get('invalidRowCount');
        $conflictRowCount  = $this->get('conflictRowCount');

        if ( $skipColumnHeader ) {
            $this->assign( 'skipColumnHeader' , $skipColumnHeader );
            $this->assign( 'rowDisplayCount', 3 );
        } else {
            $this->assign( 'rowDisplayCount', 2 );
        }
        

        if ( $invalidRowCount > 0 ) {
            $this->set('downloadErrorRecords', 
                '<a href="' 
                . CRM_Utils_System::url('civicrm/export', 'type=1') 
                . '">' . ts('Download Errors') . '</a>');
        }
        if ( $conflictRowCount > 0 ) {
            $this->set('downloadConflictRecords', 
                '<a href="'
                . CRM_Utils_System::url('civicrm/export', 'type=2') 
                . '">' . ts('Download Conflicts') . '</a>');
        }

        
        $properties = array( 'mapper', 'dataValues', 'columnCount',
                             'totalRowCount', 'validRowCount', 
                             'invalidRowCount', 'conflictRowCount',
                             'downloadErrorRecords', 'downloadConflictRecords');
        foreach ( $properties as $property ) {
            $this->assign( $property, $this->get( $property ) );
        }
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        $this->addButtons( array(
                                 array ( 'type'      => 'back',
                                         'name'      => ts('<< Previous') ),
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Import Now >>'),
                                         'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) {
        return ts('Preview');
    }

    /**
     * Process the mapped fields and map it into the uploaded file
     * preview the file and extract some summary statistics
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        $fileName         = $this->controller->exportValue( 'UploadFile', 'uploadFile' );
        $skipColumnHeader = $this->controller->exportValue( 'UploadFile', 'skipColumnHeader' );
        $invalidRowCount    = $this->get('invalidRowCount');
        $conflictRowCount  = $this->get('conflictRowCount');
        
        $seperator = ',';

        $mapper = $this->controller->exportValue( 'MapField', 'mapper' );

        $parser =& new CRM_Import_Parser_Contact( $mapper );
        $parser->run( $fileName, $seperator, 
                      $mapper,
                      $skipColumnHeader,
                      CRM_Import_Parser::MODE_IMPORT );

        // add all the necessary variables to the form
        $parser->set( $this, CRM_Import_Parser::MODE_IMPORT );

        // check if there is any error occured

        $errorStack =& CRM_Core_Error::singleton();
        $errors     = $errorStack->getErrors();

        $errorMessage = array();
        
        $config =& CRM_Core_Config::singleton( );

        if( is_array( $errors ) ) {
            foreach($errors as $key => $value) {
                $errorMessage[] = $value['message'];
            }
            
            $errorFile = $fileName . '.error.log';
            
            if ( $fd = fopen( $errorFile, 'w' ) ) {
                fwrite($fd, implode('\n', $errorMessage));
            }
            fclose($fd);

            $this->set('errorFile', $errorFile);
        }
    }
}

?>
