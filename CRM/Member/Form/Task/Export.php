<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 * @copyright Donald A. Lobo (c) 2005
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
        $queryParams =  $this->get( 'queryParams' );
        $query       =& new CRM_Contact_BAO_Query( $queryParams, null, null, false, false, 
                                                   CRM_Contact_BAO_Query::MODE_MEMBER );
        
       
        $header = array('contact_id'             => ts('Contact ID'),
                        'membership_id'          => ts('Membership ID'),
                        'sort_name'              => ts('Sort Name'), 
                        'display_name'           => ts('Display Name'),
                        'membership_type'        => ts('Membership Type'),
                        'join_date'              => ts('Member Since'),
                        'start_date'             => ts('Start/Renew Date'),
                        'end_date'               => ts('End Date'),
                        'source'                 => ts('Source'),
                        'status_id'              => ts('Status')
                        );
        
        $properties = array_keys( $header );

        $result = $query->searchQuery( 0, 0, null,
                                       false, false,
                                       false, false,
                                       false,
                                       $this->_memberClause );

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
