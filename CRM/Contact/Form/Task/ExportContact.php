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
 * This class provides the functionality to export contacts
 *
 */
class CRM_Contact_Form_Task_ExportContact extends CRM_Contact_Form_Task {

    /**
     * class constructor
     *
     */
    function __construct( $name, $state, $mode = self::MODE_NONE ) {
        parent::__construct($name, $state, $mode);
    }

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) {
        /*
         * initialize the task and row fields
         */
        parent::preProcess( );
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) {
        // print_r($_SESSION);

        $this->addDefaultButtons( 'Export' );
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        //print_r($_SESSION);
        $session = CRM_Session::singleton( );        

        $aColumnHeader = $this->get( 'columnHeaders' );
        $aContact    = $this->get( 'rows' );

        $aSelectedContact    = $session->get( 'selectedContacts' );
        
        //print_r($aColumnHeader);
        
        //build the column headers for export
        $strHeader = '';
        foreach ($aColumnHeader as $lngKey => $varValue){
            if (strlen(trim($varValue['name']))) {
                $strHeader .= $varValue['name'].",";
            }
        }
        $strHeader = substr($strHeader, 0, (strlen($strHeader)-1) );
        $strHeader .= "\n";

        // building the data(rows) for exporting contact
        $strRows = '';
        foreach ($aContact as $lngKey => $varValue) {
            if(array_key_exists($varValue['contact_id'], $aSelectedContact)) {
                $strRows .= str_replace(',',' ',$varValue['sort_name']).",".str_replace(',',' ',$varValue['street_address']).",".str_replace(',',' ',$varValue['city']).",".str_replace(',',' ',$varValue['state']).",".str_replace(',',' ',$varValue['country']).",".$varValue['postal_code'].",".$varValue['email'].",".$varValue['phone'];
                $strRows .= " \n";
            }
        }

        // build the srting that has to wriitten to the file
        $strFileData = $strHeader.$strRows;
        
        //function to save the file 
        CRM_Contact_Form_Task_ExportContact::saveFile($strFileData);
        
    }//end of function

    
    /**
     * This function is used to save the file
     * 
     * @param string $strFileData this string contains the data that has to be exported
     * $param string $type file extension
     *
     * @access public
     * @static
     */
    
    static function saveFile ($strFileData, $type = "csv") {
  
        $strFileName = "Contact".time().$type;
    
        // create the file
        if (!$handle = fopen("/tmp/".$strFileName, "x")) {
            echo "Cannot create file.";
            exit;
        }
        // Write $strFileData to our opened file.
        if (fwrite($handle, $strFileData) === FALSE) {
            echo "Cannot write to file ($strFileName)";
            exit;
        }
        
        fclose($handle);
            
        // this tells what type of file
        header('Content-type: application/csv');
        
        // to promt save dialog and file that is displayed
        header('Content-Disposition: attachment; filename="Contact.csv"');
        
        // original destination where file is created.
        readfile('/tmp/'.$strFileName);

        //delete the temporary file
        unlink('/tmp/'.$strFileName);
    }
    
    /**
     * Function to escape the special characters
     * 
     * @param char this is character that has to be escaped. 
     * 
     * @return string with the escape character
     * 
     * @public
     *
     */
    function test () {
    }
 
}

?>