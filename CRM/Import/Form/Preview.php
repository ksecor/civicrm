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
        $this->_columnCount  = $this->get( 'columnCount' );
       
        //get the data from the session
        $aData = $this->get('dataValues');
        $aMapper = $this->get('mapper');

        //print_r($aData);
        //print_r($aMapper);
        
        //print_r($_SESSION);

        $lngEmailKey = 0;
        $lngPhoneKey = 0;
        //get the key of email and phone field
        foreach($aMapper as $lngKey => $varValue) {
            if($varValue == 'Email'){
                $lngEmailKey = $lngKey;
            }
            if($varValue == 'Phone'){
                $lngPhoneKey = $lngKey;
            }
        }

        // if the  email is present check for duplicate emails and also keep the count
        if($lngEmailKey > 0 ) {
            
            $aEmail = array();
            $lngDuplicateEmail = 1;
            $lngIncorrectRecord = 0;
            
            foreach($aData as $lngKey => $varValue) {
                // check the duplicate emails
                if ( in_array($varValue[$lngEmailKey], $aEmail)) {
                    $lngDuplicateEmail++;
                } else {
                    array_push($aEmail, $varValue[$lngEmailKey]);
                }
                
                //check for valid email/phone
                if (!CRM_Utils_Rule::email($varValue[$lngEmailKey]) || !CRM_Utils_Rule::phone($varValue[$lngPhoneKey])) {
                    $lngIncorrectRecord++;            
                }
            }
        }

        //echo $lngDuplicateEmail;
        //echo $lngIncorrectEmail;
        $this->set('duplicateRowCount', $lngDuplicateEmail);
        $this->set('invalidRowCount', $lngIncorrectRecord);

        $this->assign( 'rowDisplayCount', 2 );

        $properties = array( 'mapper', 'dataValues', 'columnCount',
                             'totalRowCount', 'validRowCount', 'invalidRowCount', 'duplicateRowCount' );
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
                                         'name'      => '<< Previous' ),
                                 array ( 'type'      => 'next',
                                         'name'      => 'Import Now >>',
                                         'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => 'Cancel' ),
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
        return 'Preview';
    }

    /**
     * Process the mapped fields and map it into the uploaded file
     * preview the file and extract some summary statistics
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        $fileName  = $this->controller->exportValue( 'UploadFile', 'uploadFile' );
        $seperator = ',';

        $mapperKeys = $this->controller->exportValue( 'MapField', 'mapper' );

        $parser = new CRM_Import_Parser_Contact( $mapperKeys );
        $parser->run( $fileName, $seperator, CRM_Import_Parser::MODE_IMPORT );

        // add all the necessary variables to the form
        $parser->set( $this );
    }
}

?>