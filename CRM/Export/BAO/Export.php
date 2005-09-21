<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

/**
 * This class is for exporting contact
 *
 */
class CRM_Export_BAO_Export {
    
    /**
     * Function to get the list the export fields
     *
     * @param int $exportContact type of export
     *
     * @access public
     */
    function exportContacts($fields = '') {
        //$fields      = array();
        $headerRows  = array();
        $returnProperties = array();

         //get all the exportable fields for contact types.
        if(!$fields) {
            $fields  = CRM_Export_BAO_Export::getExportableFields();
            
        }
       
        foreach ($fields as $key => $varValue) {
            foreach ($varValue as $key1 => $var) {
                if ($key1) {
                    /* if (!in_array($key1, $headerRows )) { 
                        $headerRows[] = $key1;
                    }*/
                    $returnProperties[$key1] = 1;
                }
            }
        }
        
        // print_r($returnProperties);
        
        $session =& new CRM_Core_Session();
        $contactIds = $session->get('contactIds');
        
        foreach ($contactIds as $id) { 
            $params = array();
            $params['id'] = $id;

            $queryString = CRM_Contact_BAO_Query::query( $params, $returnProperties ); 
            
            $dao =& new CRM_Core_DAO();
            
            $dao->query($queryString);
            $dao->fetch();
            foreach ($dao as $key => $varValue) {
                if (array_key_exists($key, $returnProperties)) {
                    $contactDetails[$id][$key] = $varValue;
                    if (!in_array($key, $headerRows )) { 
                        $headerRows[] = $key;
                    }
                    
                }
            }
        }
        
        CRM_Core_Report_Excel::writeCSVFile( self::getExportFileName( ), $headerRows, $contactDetails );
       
        exit();
    }

    /**
     * name of export file.
     *
     * @param string $output type of output
     * @return string name of the file
     */
    function getExportFileName( $output = 'csv') {
        return ts('CiviCRM Contact Search');
    }

    
    /**
     * Function to get the exportable fields
     *
     * return array $exportableFields
     */
    static function getExportableFields($contactType = '') {
        $exportableFields = array ();
        if ($contactType) {
            eval('$exportableFields['.$contactType.']   =& CRM_Contact_BAO_Contact::exportableFields('.$contactType.');');
        } else {
            $exportableFields['Individual']   =& CRM_Contact_BAO_Contact::exportableFields('Individual');
            $exportableFields['Household']    =& CRM_Contact_BAO_Contact::exportableFields('Household');
            $exportableFields['Organization'] =& CRM_Contact_BAO_Contact::exportableFields('Organization');
        }
        return $exportableFields;
    }
}

?>
