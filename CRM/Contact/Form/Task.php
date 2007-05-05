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

require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Contact_Form_Task extends CRM_Core_Form
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
    public $_contactIds;

    /**
     * build all the data structures needed to build the form
     *
     * @param
     * @return void
     * @access public
     */
    function preProcess( ) 
    {
        $this->_contactIds = array( );

        // get the submitted values of the search form
        // we'll need to get fv from either search or adv search in the future
        if ( $this->_action == CRM_Core_Action::ADVANCED ) {
            $values = $this->controller->exportValues( 'Advanced' );
        } else if ( $this->_action == CRM_Core_Action::PROFILE ) {
            $values = $this->controller->exportValues( 'Builder' );
        } else {
            $values = $this->controller->exportValues( 'Search' );
        }

        require_once 'CRM/Contact/Task.php';

        $this->_task = $values['task'];
        $crmContactTaskTasks = CRM_Contact_Task::taskTitles();
        $this->assign( 'taskName', $crmContactTaskTasks[$this->_task] );

        // all contacts or action = save a search
        if (($values['radio_ts'] == 'ts_all') || ($this->_task == CRM_Contact_Task::SAVE_SEARCH)) {
            // need to perform action on all contacts
            // fire the query again and get the contact id's + display name
            $sortID = null;
            if ( $this->get( CRM_Utils_Sort::SORT_ID  ) ) {
                $sortID = CRM_Utils_Sort::sortIDValue( $this->get( CRM_Utils_Sort::SORT_ID  ),
                                                       $this->get( CRM_Utils_Sort::SORT_DIRECTION ) );
            }

            require_once 'CRM/Contact/Selector.php';
            $selector  =& new CRM_Contact_Selector( );
            $sortOrder =& $selector->getSortOrder( $this->_action );
            $sort      =& new CRM_Utils_Sort( $sortOrder, $sortID );

            $params  =  $this->get( 'queryParams' );
            $query   =& new CRM_Contact_BAO_Query( $params );

            $dao = $query->searchQuery( 0, 0, $sort,
                                        false, false, false,
                                        false, false );
            while ( $dao->fetch( ) ) {
                $this->_contactIds[] = $dao->contact_id;
            }
        } else if($values['radio_ts'] == 'ts_sel') {
            // selected contacts only
            // need to perform action on only selected contacts
            foreach ( $values as $name => $value ) {
                if ( substr( $name, 0, CRM_Core_Form::CB_PREFIX_LEN ) == CRM_Core_Form::CB_PREFIX ) {
                    $this->_contactIds[] = substr( $name, CRM_Core_Form::CB_PREFIX_LEN );
                }
            }
        }

        $this->assign( 'totalSelectedContacts', count( $this->_contactIds ) );
    }

    /**
     * This function sets the default values for the form. Relationship that in edit/view action
     * the default values are retrieved from the database
     * 
     * @access public
     * @return void
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        return $defaults;
    }
    

    /**
     * This function is used to add the rules for form.
     *
     * @return void
     * @access public
     */
    function addRules( )
    {
    }


    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->addDefaultButtons(ts('Confirm Action'));        
    }

       
    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return void
     */
    public function postProcess() 
    {
    }//end of function

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
