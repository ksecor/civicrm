<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

require_once 'CRM/Contribute/Form/Task.php';
require_once 'CRM/Contribute/Selector/Search.php';
require_once 'CRM/Core/Selector/Controller.php';

/**
 * This class provides the functionality to save a search
 * Saved Searches are used for saving frequently used queries
 */
class CRM_Contribute_Form_Task_Export extends CRM_Contribute_Form_Task {

    /**
     * Build the form - it consists of
     *    - displaying the QILL (query in local language)
     *    - displaying elements for saving the search
     *
     * @access public
     * @return void
     */
    function buildQuickForm()
    {
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Export Contributions'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'back',
                                         'name'      => ts('Done') ),
                                 )
                           );
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return void
     */
    public function postProcess()
    { 
        // create the selector, controller and run - store results in session
        $queryParams =  $this->get( 'queryParams' );
        $returnProperties =& CRM_Contact_BAO_Query::defaultReturnProperties( CRM_Contact_BAO_Query::MODE_CONTRIBUTE );

        // also add addresss fields
        $addressProperties    = array( 
                                      'street_address'         => 1, 
                                      'supplemental_address_1' => 1, 
                                      'supplemental_address_2' => 1, 
                                      'city'                   => 1, 
                                      'postal_code'            => 1, 
                                      'postal_code_suffix'     => 1, 
                                      'state_province'         => 1, 
                                      'country'                => 1,
                                      'geo_code_1'             => 1,
                                      'geo_code_2'             => 1,
                                      'email'                  => 1, 
                                      'phone'                  => 1, 
                                      'im'                     => 1, 
                                      );
        $returnProperties = array_merge( $returnProperties, $addressProperties );
        
        $query            =& new CRM_Contact_BAO_Query( $queryParams, $returnProperties, null, false, false, 
                                                        CRM_Contact_BAO_Query::MODE_CONTRIBUTE );
        $properties = array( 'contact_id', 'contribution_id' );
        $header     = array( ts( 'Contact ID' ), ts( 'Contribution ID' ) );
        foreach ( $returnProperties as $name => $dontCare ) {
            $properties[] = $name;
            if ( CRM_Utils_Array::value( $name, $query->_fields ) &&
                 CRM_Utils_Array::value( 'title', $query->_fields[$name] ) ) {
                $header[] = $query->_fields[$name]['title'];
            } else {
                $header[] = $name;
            }
        }
        // header fixed for colomns are not expotable
        $headerArray = array('image_URL'     => 'Image URL',
                             'contact_type'  => 'Contact Type',
                             'sort_name'     => 'Sort Name',
                             'display_name'  => 'Display Name',
                             );
        
        foreach( $header as $key => $value) {
            if( array_key_exists( $value, $headerArray )) {
                $header[$key] = $headerArray[$value]; 
            }
        }
        $result = $query->searchQuery( 0, 0, null,
                                       false, false,
                                       false, false,
                                       false,
                                       $this->_contributionClause );

        $rows = array( ); 
        while ( $result->fetch( ) ) {
            $row   = array( );
            $valid = false;

            foreach ( $properties as $property ) {
                if ($property == "is_test") {
                    $row[] = $result->$property ? "Yes" : "No";
                    if ( $result->$property ) {
                        $row[] = $result->$property;
                    } 
                } else {
                    $row[] = $result->$property;
                }
                if ( ! CRM_Utils_System::isNull( $result->$property ) ) {
                    $valid = true;
                }
            }
            if ( $valid ) {
                $rows[] = $row;
            }
        }

        require_once 'CRM/Core/Report/Excel.php'; 
        CRM_Core_Report_Excel::writeCSVFile( self::getExportFileName( ), $header, $rows ); 
        exit( );
    }

    /**
     * return a filename for this export
     *
     * @return string the export file name
     * @access public
     * @static
     */
    static function getExportFileName( ) {
        return ts( 'CiviCRM Contributions' );
    }
}

?>
