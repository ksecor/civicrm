<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */


/**
 * This class is for exporting contact
 *
 */
class CRM_Contact_BAO_Export {
    
    /**
     * Function to get the list the export fields
     *
     * @param int $exportContact type of export
     *
     * @access public
     */
    function exportContacts( $selectAll, $ids, $params, $order = null, $fields = null, $moreReturnProperties = null ) {
        $headerRows       = array();
        $primary          = false;
        $returnProperties = array( );
        $origFields       = $fields;
        
        if ( $fields ) {
            //construct return properties 
            $locationTypes =& CRM_Core_PseudoConstant::locationType();
            
            foreach ( $fields as $key => $value) {
                $fieldName   = CRM_Utils_Array::value( 1, $value );
                
                if ( ! $fieldName ) {
                    continue;
                }
                
                $contactType = CRM_Utils_Array::value( 0, $value );
                $locTypeId   = CRM_Utils_Array::value( 2, $value );
                $phoneTypeId = CRM_Utils_Array::value( 3, $value );
                
                if ( is_numeric($locTypeId) ) {
                    if ($phoneTypeId) {
                        $returnProperties['location'][$locationTypes[$locTypeId]]['phone-' .$phoneTypeId] = 1;
                    } else {
                        $returnProperties['location'][$locationTypes[$locTypeId]][$fieldName] = 1;
                    }
                } else {
                    $returnProperties[$fieldName] = 1;
                }
            }
        } else {
            $primary = true;
            $fields = CRM_Contact_BAO_Contact::exportableFields( 'All', true, true );
            foreach ($fields as $key => $var) { 
                if ( $key &&
                     ( substr($key,0, 6) !=  'custom' ) ) { //for CRM=952
                    $returnProperties[$key] = 1;
                }
            }
        }
        
        if ($primary) {
            $returnProperties['location_type'] = 1;
            $returnProperties['im_provider'  ] = 1;
            $returnProperties['phone_type'   ] = 1;
        }

        if ( $moreReturnProperties ) {
            $returnProperties = array_merge( $returnProperties, $moreReturnProperties );
        }

        $session =& CRM_Core_Session::singleton( );

        if ( $selectAll ) {
            if ($primary) {
                $query =& new CRM_Contact_BAO_Query( $params, $returnProperties, $fields );
            } else {
                $query =& new CRM_Contact_BAO_Query( $params, $returnProperties );
            }
        } else {
            $idParams = array( );
            foreach ($ids as $id) { 
                $idParams[] = array( CRM_Core_Form::CB_PREFIX . $id, '=', 1, 0, 0 );
            }
            if ($primary) {
                $query =& new CRM_Contact_BAO_Query( $idParams, $returnProperties, $fields, true );         
            } else {
                $query =& new CRM_Contact_BAO_Query( $idParams, $returnProperties, null, true );         
            }
        }
        
        list( $select, $from, $where ) = $query->query( );
        // make sure the groups stuff is included only if specifically specified
        // by the fields param (CRM-1969), else we limit the contacts outputted to only
        // ones that are part of a group
        if ( $origFields &&
             CRM_Utils_Array::value( 'groups', $returnProperties ) ) {
            $groupClause = " ( civicrm_group_contact.status = 'Added' OR civicrm_group_contact.status is NULL ) ";
            if ( empty( $where ) ) {
                $where = "WHERE $groupClause";
            } else {
                $where = "$where AND $groupClause";
            }
        }

        $queryString = "$select $from $where";

        if ( CRM_Utils_Array::value( 'tags'  , $returnProperties ) || 
             CRM_Utils_Array::value( 'groups', $returnProperties ) ||
             CRM_Utils_Array::value( 'notes' , $returnProperties ) ) { 
            $queryString .= " GROUP BY contact_a.id";
        }
        
        if ( $order ) {
            list( $field, $dir ) = explode( ' ', $order, 2 );
            $field = trim( $field );
            if ( CRM_Utils_Array::value( $field, $returnProperties ) ) {
                $queryString .= " ORDER BY $order";
            }
        }
        

        //hack for student data
        require_once 'CRM/Core/OptionGroup.php';
        $multipleSelectFields = array( 'preferred_communication_method' => 1 );

        if ( CRM_Core_Permission::access( 'Quest' ) ) { 
            require_once 'CRM/Quest/BAO/Student.php';
            $studentFields = array();
            $studentFields = CRM_Quest_BAO_Student::$multipleSelectFields;
            $multipleSelectFields = array_merge( $multipleSelectFields, $studentFields );
        }

        $dao =& CRM_Core_DAO::executeQuery( $queryString, CRM_Core_DAO::$_nullArray );
        $header = false;

        $contactDetails = array( );
        while ($dao->fetch()) {
            $row = array( );
            $validRow = false;
            foreach ($dao as $key => $varValue) {
                $flag = false;
                foreach ($returnProperties as $propKey => $props) {
                    if (is_array($props)) {
                        foreach($props as $propKey1=>$prop) {
                            foreach($prop as $propkey2=>$prop1) {
                                $locationfield = str_replace( ' ', '_', $propKey1."-".$propkey2 );
                                if( $locationfield == $key) {
                                    $flag = true;
                                }
                            }
                        }
                    }
                } 

                if (array_key_exists($key, $returnProperties)) {
                    $flag = true;
                }
                if ($key == 'contact_id' && array_key_exists( 'id' , $returnProperties)) {
                    $flag = true;
                }

                if ($flag) {
                    if ( isset( $varValue ) && $varValue != '' ) {
                        if ( $cfID = CRM_Core_BAO_CustomField::getKeyID($key) ) {
                            $row[$key] = CRM_Core_BAO_CustomField::getDisplayValue( $varValue, $cfID, $query->_options );
                        } else if ( array_key_exists($key ,$multipleSelectFields ) ){
                            $paramsNew = array($key => $varValue );
                            if ( $key == 'test_tutoring') {
                                $name = array( $key => array('newName' => $key ,'groupName' => 'test' ));
                            } else if (substr( $key, 0, 4) == 'cmr_') { //for  readers group
                                $name = array( $key => array('newName' => $key, 'groupName' => substr($key, 0, -3) ));
                            } else {
                                $name = array( $key => array('newName' => $key ,'groupName' => $key ));
                            }
                            CRM_Core_OptionGroup::lookupValues( $paramsNew, $name, false );
                            $row[$key] = $paramsNew[$key];
                            
                        } else {
                            $row[$key] = $varValue;
                        }
                        $validRow  = true;
                    } else {
                        $row[$key] = '';
                    }
                   
                    if ( ! $header ) {
                        if (isset($query->_fields[$key]['title'])) {
                            $headerRows[] = $query->_fields[$key]['title'];
                        } else if ($key == 'phone_type'){
                            $headerRows[] = 'Phone Type';
                        } else if ($key == 'contact_id'){
                            $headerRows[] = $query->_fields['id']['title'];
                        } else {
                            $keyArray = explode('-', $key);
                            
                            $hdr      = $keyArray[0];

                            if ( CRM_Utils_Array::value( 1, $keyArray ) ) {
                                $hdr .= "-" . $query->_fields[$keyArray[1]]['title'];
                            }

                            if ( CRM_Utils_Array::value( 2, $keyArray ) ) {
                                $hdr .= " " . $keyArray[2];
                            }
                            $headerRows[] = $hdr;
                        
                        }
                    }
                }
            }
            if ( $validRow ) {
                $contactDetails[] = $row;
            }
            $header = true;
        }

        require_once 'CRM/Core/Report/Excel.php';
        CRM_Core_Report_Excel::writeCSVFile( self::getExportFileName( ), $headerRows, $contactDetails );
                
        exit();
    }

    function exportCustom( $customSearchClass, $formValues, $order ) {
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $customSearchClass ) . '.php' );
        eval( '$search = new ' . $customSearchClass . '( $formValues );' );

        $includeContactIDs = false;
        if ( $formValues['radio_ts'] == 'ts_sel' ) {
            $includeContactIDs = true;
        }

        $sql    = $search->all( 0, 0, $order, $includeContactIDs );

        $columns = $search->columns( );

        $header = array_keys  ( $columns );
        $fields = array_values( $columns );

        $rows = array( );
        $dao =& CRM_Core_DAO::executeQuery( $sql,
                                            CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch( ) ) {
            $row = array( );

            foreach ( $fields as $field ) {
                $row[$field] = $dao->$field;
            }
            $rows[] = $row;
        }

        require_once 'CRM/Core/Report/Excel.php';
        CRM_Core_Report_Excel::writeCSVFile( self::getExportFileName( ), $header, $rows );
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
}


