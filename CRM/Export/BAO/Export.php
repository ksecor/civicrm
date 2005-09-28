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
       
        
        if($fields) {
            $location = array();
            $locationType = array("Work"=>array(),"Home"=>array(),"Main"=>array(),"Other"=>array());
            $returnFields = $fields;
           
            foreach($returnFields as $key => $field) {
                $flag = true ;
                $phone_type = "";
                $phoneFlag = false;
                if( $field[3] && $field[1] == 'phone' ) {
                   
                    if($field[3] == 'Phone') {
                        $phone_type = $field[1]."-"."Phone";
                    } else if($field[3] == 'Mobile') {
                        $phone_type = $field[1]."-"."Mobile";
                    } else if($field[3] == 'Fax') {
                        $phone_type = $field[1]."-"."Fax";
                    } else if($field[3] == 'Pager') {
                        $phone_type = $field[1]."-"."Pager";
                    }
                    
                    $phoneFlag = true ;
                }
                if( $field[2] ) {
                    if ($field[2] == 1) {
                        if ($phoneFlag) {
                            $locationType["Home"][$phone_type] = 1;
                        } else {
                            $locationType["Home"][$field[1]] = 1;  
                        }
                    }else if ($field[2] == 2) {
                        if ($phoneFlag) {
                            $locationType["Work"][$phone_type] = 1;
                        } else {
                            $locationType["Work"][$field[1]] = 1;
                        }
                    }else if ($field[2] == 3) {
                        if ($phoneFlag) {
                            $locationType["Main"][$phone_type] = 1;
                        } else {
                            $locationType["Main"][$field[1]] = 1;
                        }
                    }else if ($field[2] == 4) {
                        if ($phoneFlag) {
                            $locationType["Other"][$phone_type] = 1;
                        } else {
                            $locationType["Other"][$field[1]] = 1;
                        }
                    }
                    $flag = false;   
                } 
                
                if ($flag) {
                    $returnProperties[$field[1]] = 1; 
                }
            }
            $returnProperties['location'] = $locationType;
           
        } else {
            $fields  = CRM_Export_BAO_Export::getExportableFields();
            $selectedAll = true;
            foreach ($fields as $key => $varValue) {
                foreach ($varValue as $key1 => $var) {
                    if ($key1) {
                        $returnProperties[$key1] = 1;
                    }
                }
            }
            
        }
       
        // print_r($returnProperties);
        
        $session =& new CRM_Core_Session();
        $contactIds = $session->get('contactIds');
        $selectedAll = $session->get('selectedAll');
       
        if( $selectedAll ) {
           
            //$params = array();
            $queryString = CRM_Contact_BAO_Query::getQuery( $params, $returnProperties );
           
            $dao =& CRM_Core_DAO::executeQuery($queryString);
            while ($dao->fetch()) {
                foreach ($dao as $key => $varValue) {
                    $flag = false;
                    foreach($returnProperties as $propKey=>$props) {
                        if (is_array($props)) {
                            
                            foreach($props as $propKey1=>$prop) {
                                foreach($prop as $propkey2=>$prop1) {
                                    //echo $propKey1."-".$propkey2."  ".$key; 
                                    if($propKey1."-".$propkey2 == $key) {
                                        $flag = true;
                                    }
                                }
                            }
                        }
                    }    
                    if(array_key_exists($key, $returnProperties)) {
                        $flag = true;
                    }
                    if ($flag) {
                        $contactDetails[$dao->contact_id][$key] = $varValue;
                        if (!in_array($key, $headerRows )) { 
                            $headerRows[] = $key;
                        }
                        
                    }
                    
                }
            }
            
            
        } else {
           
            foreach ($contactIds as $id) { 
                $params = array();
                $params['id'] = $id;
               
                $queryString = CRM_Contact_BAO_Query::getQuery( $params, $returnProperties ); 
               
                $dao =& CRM_Core_DAO::executeQuery($queryString);
                $dao->fetch();
                
                foreach ($dao as $key => $varValue) {
                    //echo substr($key, 0, 1);
                    $flag = false;
                    foreach($returnProperties as $propKey=>$props) {
                        if (is_array($props)) {
                            
                            foreach($props as $propKey1=>$prop) {
                                foreach($prop as $propkey2=>$prop1) {
                                    //echo $propKey1."-".$propkey2."  ".$key; 
                                    if($propKey1."-".$propkey2 == $key) {
                                        $flag = true;
                                    }
                                }
                            }
                        }
                    }    
                    if(array_key_exists($key, $returnProperties)) {
                        $flag = true;
                    }
                    if( $flag ) {
                        $contactDetails[$id][$key] = $varValue;
                        if (!in_array($key, $headerRows )) { 
                            $headerRows[] = $key;
                        }
                    }
                }
                
            }
        }
        //print_r($contactDetails);
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
