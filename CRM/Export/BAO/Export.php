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
 * This class contains the funtions for Component export
 *
 */
class CRM_Export_BAO_Export
{
    /**
     * Function to get the list the export fields
     *
     * @param int    $selectAll user preference while export
     * @param array  $ids  contact ids
     * @param array  $params associated array of fields
     * @param string $order order by clause
     * @param array  $associated array of fields
     * @param array  $moreReturnProperties additional return fields
     * @param int    $exportMode export mode
     * @param string $componentClause component clause
     *
     * @static
     * @access public
     */
    static function exportComponents( $selectAll, $ids, $params, $order = null, 
                                      $fields = null, $moreReturnProperties = null, 
                                      $exportMode = CRM_Export_Form_Select::CONTACT_EXPORT,
                                      $componentClause = null )
    {
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
                    //hack to fix component fields
                    if ( $fieldName == 'event_id' ) {
                        $returnProperties['event_title'] = 1;
                    } else {
                        $returnProperties[$fieldName] = 1;
                    }
                }
            }
            
            // hack to add default returnproperty based on export mode
            if ( $exportMode == CRM_Export_Form_Select::CONTRIBUTE_EXPORT ) {
                $returnProperties['contribution_id'] = 1;
            } else if ( $exportMode == CRM_Export_Form_Select::EVENT_EXPORT ) {
                $returnProperties['participant_id'] = 1;
            } else if ( $exportMode == CRM_Export_Form_Select::MEMBER_EXPORT ) {
                $returnProperties['membership_id'] = 1;
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
            
            if ( $primary ) {
                $returnProperties['location_type'   ] = 1;
                $returnProperties['im_provider'     ] = 1;
                $returnProperties['phone_type'      ] = 1;
                $returnProperties['current_employer'] = 1;
            }
            
            $paymentFields = false;
            $queryMode = CRM_Contact_BAO_Query::MODE_CONTACTS;
            
            switch ( $exportMode )  {
            case CRM_Export_Form_Select::CONTRIBUTE_EXPORT :
                $queryMode = CRM_Contact_BAO_Query::MODE_CONTRIBUTE;
                break;
            case CRM_Export_Form_Select::EVENT_EXPORT :
                $queryMode = CRM_Contact_BAO_Query::MODE_EVENT;
                $paymentFields  = true;
                $paymentTableId = "participant_id";
                break;
            case CRM_Export_Form_Select::MEMBER_EXPORT :
                $queryMode = CRM_Contact_BAO_Query::MODE_MEMBER;
                $paymentFields  = true;
                $paymentTableId = "membership_id";
                break;
            }
            
            if ( $queryMode != CRM_Contact_BAO_Query::MODE_CONTACTS ) {
                $componentReturnProperties =& CRM_Contact_BAO_Query::defaultReturnProperties( $queryMode );
                $returnProperties          = array_merge( $returnProperties, $componentReturnProperties );
                
                // unset groups, tags, notes for components
                foreach ( array( 'groups', 'tags', 'notes' ) as $value ) {
                    unset( $returnProperties[$value] );
                }
            }
        }
        
        if ( $moreReturnProperties ) {
            $returnProperties = array_merge( $returnProperties, $moreReturnProperties );
        }
        //crm_core_error::debug('$returnProperties', $returnProperties ); exit();
        $query =& new CRM_Contact_BAO_Query( 0, $returnProperties, null, false, false, $queryMode ); 

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
                $where .= " AND $groupClause";
            }
        }

        if ( $componentClause ) {
            if ( empty( $where ) ) {
                $where = "WHERE $componentClause";
            } else {
                $where .= " AND $componentClause";
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
            $studentFields = array( );
            $studentFields = CRM_Quest_BAO_Student::$multipleSelectFields;
            $multipleSelectFields = array_merge( $multipleSelectFields, $studentFields );
        }
        //crm_core_error::debug('$queryString', $queryString ); exit();
        $dao =& CRM_Core_DAO::executeQuery( $queryString, CRM_Core_DAO::$_nullArray );
        $header = false;
        
        $addPaymentHeader = false;
        if ( $paymentFields ) {
            $addPaymentHeader = true;
            //special return properties for event and members
            $paymentHeaders = array( ts('Total Amount'), ts('Contribution Status'), ts('Received Date'),
                                     ts('Payment Instrument'), ts('Transaction ID'));
            
            // get payment related in for event and members
            $paymentDetails = CRM_Contribute_BAO_Contribution::getContributionDetails( $exportMode, $ids );
        }
        
        $componentDetails = $headerRows = array( );
        $setHeader = true;
        while ( $dao->fetch( ) ) {
            $row = array( );
            //first loop through returnproperties so that we return what is required, and in same order.
            foreach( $returnProperties as $field => $value ) {
                //we should set header only once
                if ( $setHeader ) { 
                    if ( isset( $query->_fields[$field]['title'] ) ) {
                        $headerRows[] = $query->_fields[$field]['title'];
                    } else if ($field == 'phone_type'){
                        $headerRows[] = 'Phone Type';
                    } else if ( is_array( $value ) && $field == 'location' ) {
                        // fix header for location type case
                        foreach ( $value as $ltype => $val ) {
                            foreach ( array_keys($val) as $fld ) {
                                $type = explode('-', $fld );
                                $hdr = "{$ltype}-" . $query->_fields[$type[0]]['title'];
                                
                                if ( CRM_Utils_Array::value( 1, $type ) ) {
                                    $hdr .= " " . $type[1];
                                }
                                $headerRows[] = $hdr;
                            }
                        }
                    } else {
                        $headerRows[] = $field;
                    }
                }

                //build row values (data)
                if ( property_exists( $dao, $field ) ) {
                    $fieldValue = $dao->$field;
                } else {
                    $fieldValue = '';
                }
                
                if ( $field == 'id' ) {
                    $row[$field] = $dao->contact_id;
                } else if ( is_array( $value ) && $field == 'location' ) {
                    // fix header for location type case
                    foreach ( $value as $ltype => $val ) {
                        foreach ( array_keys($val) as $fld ) {
                            $type = explode('-', $fld );
                            $fldValue = "{$ltype}-" . $type[0];
                            
                            if ( CRM_Utils_Array::value( 1, $type ) ) {
                                $fldValue .= "-" . $type[1];
                            }
                            
                            $row[$fldValue] = $dao->$fldValue;
                        }
                    }
                } else if ( isset( $fieldValue ) && $fieldValue != '' ) {
                    //check for custom data
                    if ( $cfID = CRM_Core_BAO_CustomField::getKeyID( $field ) ) {
                        $row[$field] = CRM_Core_BAO_CustomField::getDisplayValue( $fieldValue, $cfID, $query->_options );
                    } else if ( array_key_exists( $field, $multipleSelectFields ) ) {
                        //option group fixes
                        $paramsNew = array( $field => $fieldValue );
                        if ( $field == 'test_tutoring') {
                            $name = array( $field => array('newName' => $field ,'groupName' => 'test' ));
                        } else if (substr( $field, 0, 4) == 'cmr_') { //for  readers group
                            $name = array( $field => array('newName' => $field, 'groupName' => substr($field, 0, -3) ));
                        } else {
                            $name = array( $field => array('newName' => $field ,'groupName' => $field ));
                        }
                        CRM_Core_OptionGroup::lookupValues( $paramsNew, $name, false );
                        $row[$field] = $paramsNew[$field];
                    } else {
                        //normal fields
                        $row[$field] = $fieldValue;
                    }
                } else {
                    // if field is empty or null
                    $row[$field] = '';
                }
            }
            
            //build header only once
            $setHeader = false;
        
            // add payment headers if required
            if ( $addPaymentHeader && $paymentFields ) {
                $headerRows = array_merge( $headerRows, $paymentHeaders );
                $addPaymentHeader = false;
            }

            // add payment related information
            if ( $paymentFields && isset( $paymentDetails[ $row[$paymentTableId] ] ) ) {
                $row = array_merge( $row, $paymentDetails[ $row[$paymentTableId] ] );
            }

            //remove organization name for individuals if it is set for current employer
            if ( $row['contact_type'] == 'Individual' ) {
                $row['organization_name'] = '';
            }
            
            // add component info
            $componentDetails[] = $row;         
        }
        
        require_once 'CRM/Core/Report/Excel.php';
        CRM_Core_Report_Excel::writeCSVFile( self::getExportFileName( 'csv', $exportMode ), $headerRows, $componentDetails );
        exit();
    }

    /**
     * name of the export file based on mode
     *
     * @param string $output type of output
     * @param int    $mode export mode
     * @return string name of the file
     */
    function getExportFileName( $output = 'csv', $mode = CRM_Export_Form_Select::CONTACT_EXPORT ) 
    {
        switch ( $mode ) {
        case CRM_Export_Form_Select::CONTACT_EXPORT : 
            return ts('CiviCRM Contact Search');
            
        case CRM_Export_Form_Select::CONTRIBUTE_EXPORT : 
            return ts('CiviCRM Contribution Search');
            
        case CRM_Export_Form_Select::MEMBER_EXPORT : 
            return ts('CiviCRM Member Search');
            
        case CRM_Export_Form_Select::EVENT_EXPORT : 
            return ts('CiviCRM Participant Search');
        }
    }


    /**
     * handle the export case. this is a hack, so please fix soon
     *
     * @param $args array this array contains the arguments of the url
     *
     * @static
     * @access public
     */
    static function invoke( $args ) 
    {
        // FIXME:  2005-06-22 15:17:33 by Brian McFee <brmcfee@gmail.com>
        // This function is a dirty, dirty hack.  It should live in its own
        // file.
        $session =& CRM_Core_Session::singleton();
        $type = $_GET['type'];
        
        if ($type == 1) {
            $varName = 'errors';
            $saveFileName = 'Import_Errors.csv';
        } else if ($type == 2) {
            $varName = 'conflicts';
            $saveFileName = 'Import_Conflicts.csv';
        } else if ($type == 3) {
            $varName = 'duplicates';
            $saveFileName = 'Import_Duplicates.csv';
        } else if ($type == 4) {
            $varName = 'mismatch';
            $saveFileName = 'Import_Mismatch.csv';
        }else {
            /* FIXME we should have an error here */
            return;
        }
        
        // FIXME: a hack until we have common import
        // mechanisms for contacts and contributions
        $realm = CRM_Utils_Array::value('realm',$_GET);
        if ($realm == 'contribution') {
            $controller = 'CRM_Contribute_Import_Controller';
        } else if ( $realm == 'membership' ) {
            $controller = 'CRM_Member_Import_Controller';
        } else if ( $realm == 'event' ) {
            $controller = 'CRM_Event_Import_Controller';
        } else if ( $realm == 'activity' ) {
            $controller = 'CRM_Activity_Import_Controller';
        } else {
            $controller = 'CRM_Import_Controller';
        }
        
        require_once 'CRM/Core/Key.php';
        $qfKey = CRM_Core_Key::get( $controller );
        
        $fileName = $session->get($varName . 'FileName', "{$controller}_{$qfKey}");
        
        $config =& CRM_Core_Config::singleton( ); 
        
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv');
        header('Content-Length: ' . filesize($fileName) );
        header('Content-Disposition: attachment; filename=' . $saveFileName);
        
        readfile($fileName);
        
        exit();
    }

}


