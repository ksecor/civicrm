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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Contribute_Form_Task extends CRM_Core_Form
{
    /**
     * the task being performed
     *
     * @var int
     */
    protected $_task;

    /**
     * The array that holds all the contact ids
     *
     * @var array
     */
    protected $_contributionIds;

    /**
     * build all the data structures needed to build the form
     *
     * @param
     * @return void
     * @access public
     */
    function preProcess( ) 
    {
        $this->_contributionIds = array( );

        $values = $this->controller->exportValues( 'Search' );
        
        $this->_task = $values['task'];
        $contributeTasks = CRM_Contribute_Task::tasks();
        $this->assign( 'taskName', $contributeTasks[$this->_task] );

        // all contacts or action = save a search
        if ( $values['radio_ts'] == 'ts_all' ) {
            // need to perform action on all contacts
            // fire the query again and get the contact id's + display name
            $contact =& new CRM_Contribute_BAO_Contribute();
            $fv = $this->get( 'formValues' );
            $query =& new CRM_Contact_BAO_Query( $fv, null, null, false, false, true ); 
            $result = $query->searchQuery( 0, 0, null );
            while ( $result->fetch( ) ) {
                $this->_contributionIds[] = $result->contribution_id;
            }
        } else if ( $values['radio_ts'] == 'ts_sel' ) {
            // selected contacts only
            // need to perform actio on only selected contacts
            foreach ( $values as $name => $value ) {
                if ( substr( $name, 0, CRM_Core_Form::CB_PREFIX_LEN ) == CRM_Core_Form::CB_PREFIX ) {
                    $this->_contributionIds[] = substr( $name, CRM_Core_Form::CB_PREFIX_LEN );
                }
            }
        }
        
        $this->assign( 'totalSelectedContributions', count( $this->_contributionIds ) );
    }

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) 
    {
    }

    /**
     * simple shell that derived classes can call to add buttons to
     * the form with a customized title for the main Submit
     *
     * @param string $title title of the main button
     * @param string $type  button type for the form after processing
     * @return void
     * @access public
     */
    function addDefaultButtons( $title, $nextType = 'next', $backType = 'back' ) {
        $this->addButtons( array(
                                 array ( 'type'      => $nextType,
                                         'name'      => $title,
                                         'isDefault' => true   ),
                                 array ( 'type'      => $backType,
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

}

?>
