<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Member/Form/Task.php';
require_once 'CRM/Member/Selector/Search.php';
require_once 'CRM/Core/Selector/Controller.php';

/**
 * This class provides the functionality to export group of contact
 */
class CRM_Member_Form_Task_Export extends CRM_Member_Form_Task {

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
                                         'name'      => ts('Export Members'),
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
        $queryParams       =  $this->get( 'queryParams' );

        $returnProperties  =& CRM_Contact_BAO_Query::defaultReturnProperties( CRM_Contact_BAO_Query::MODE_MEMBER );
        
        // also add addresss fields
        $addressProperties = array('street_address'         => 1, 
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
        
        $query =& new CRM_Contact_BAO_Query( $queryParams, $returnProperties, null, false, false, 
                                             CRM_Contact_BAO_Query::MODE_MEMBER );
        
        $header = array( 'contact_id'             => ts('Contact ID'),
                         'membership_id'          => ts('Membership ID'),
                         'contact_type'           => ts('Contact Type'),
                         'display_name'           => ts('Display Name'),
                         'membership_type'        => ts('Membership Type'),
                         'start_date'             => ts('Start Date'),
                         'end_date'               => ts('End Date'),
                         'source'                 => ts('Source'),
                         'status_id'              => ts('Status')
                         );
        
        foreach ( $returnProperties as $name => $dontCare ) {
            $properties[] = $name;

            if( CRM_Utils_Array::value( 'title', $query->_fields[$name] ) )  {
                if ( CRM_Utils_Array::value( $name, $query->_fields ) ) { 
                    $header[$name] = $query->_fields[$name]['title'];
                }   else { 
                    $header[$name] = $name;
                }
            }
        }
        
        $result     = $query->searchQuery( 0, 0, null,
                                           false, false,
                                           false, false,
                                           false,
                                           $this->_memberClause );
        
        $properties = array_keys( $header );
        
        require_once 'CRM/Member/PseudoConstant.php';
        $statusTypes  = CRM_Member_PseudoConstant::membershipStatus( );
        
        $rows = array( ); 
        while ( $result->fetch( ) ) {
            $row   = array( );
            $valid = false;
            
            foreach ( $properties as $property ) {
                if ($property == 'status_id') {
                    $row[] = $statusTypes[$result->$property];
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
        return ts( 'CiviCRM Members' );
    }
}

?>
