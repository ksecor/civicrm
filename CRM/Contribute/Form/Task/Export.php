<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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
        $fv         =  $this->get( 'formValues' );
        $query      =& new CRM_Contact_BAO_Query( $fv, null, null, false, false, 
                                                  CRM_Contact_BAO_Query::MODE_ALL );
        $returnProperties =& CRM_Contact_BAO_Query::defaultReturnProperties( CRM_Contact_BAO_Query::MODE_ALL );
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

        $result = $query->searchQuery( 0, 0, null,
                                       false, false,
                                       false, false,
                                       false,
                                       $this->_contributionClause );

        $rows = array( ); 
        while ( $result->fetch( ) ) {
            $row   = array( );
            $valid = false;

            $row[] = $result->contact_id;
            $row[] = $result->contribution_id;

            foreach ( $properties as $property ) {
                $row[] = $result->$property;
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

    static function getExportFileName( ) {
        return ts( 'CiviCRM Contributions' );
    }
}

?>
