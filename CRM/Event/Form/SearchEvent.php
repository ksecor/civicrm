<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/OptionGroup.php';

class CRM_Event_Form_SearchEvent extends CRM_Core_Form 
{
    function setDefaultValues( )
    {
        $defaults = array( );
        $defaults['eventsByDates'] = 0;

        require_once 'CRM/Core/ShowHideBlocks.php';
        $this->_showHide =& new CRM_Core_ShowHideBlocks( );
        if ( !CRM_Utils_Array::value('eventsByDates',$defaults) ) {
            $this->_showHide->addHide( 'id_fromToDates' );
        }

        $this->_showHide->addToTemplate( );
        return $defaults;
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    public function buildQuickForm( ) 
    {
        $this->add( 'text', 'title', ts( 'Find' ),
                    array(CRM_Core_DAO::getAttribute('CRM_Event_DAO_Event', 'title') ) );
        
        $event_type = CRM_Core_OptionGroup::values( 'event_type', false );
        
        foreach($event_type as $eventId => $eventName) {
            $this->addElement('checkbox', "event_type_id[$eventId]", 'Event Type', $eventName);
        }
       
        $eventsByDates = array();
        $searchOption  = array( ts('Show Current and Upcoming Events'), ts('Search All or by Date Range') );
        $this->addRadio( 'eventsByDates', ts( 'Events by Dates' ), $searchOption, array('onclick' =>"return showHideByValue('eventsByDates','1','id_fromToDates','block','radio',true);"), "<br />");

        $this->addDate( 'start_date', ts('From'), false, array( 'formatType' => 'relative') ); 
        $this->addDate( 'end_date', ts('To'), false, array( 'formatType' => 'relative') ); 
        
        $this->addButtons(array( 
                                array ('type'      => 'refresh', 
                                       'name'      => ts('Search'), 
                                       'isDefault' => true ), 
                                ) ); 
    }


    function postProcess( ) 
    {
        $params = $this->controller->exportValues( $this->_name );
        $parent = $this->controller->getParent( );
        $parent->set( 'searchResult', 1 );
        if ( ! empty( $params ) ) {
            $fields = array( 'title', 'event_type_id', 'start_date', 'end_date', 'eventsByDates' );
            foreach ( $fields as $field ) {
                if ( isset( $params[$field] ) &&
                     ! CRM_Utils_System::isNull( $params[$field] ) ) {
                        if ( substr( $field, -4 ) == 'date' ) {
                            $parent->set( $field, CRM_Utils_Date::unformat( CRM_Utils_Date::processDate( $params[$field] ), '' ) );
                        } else {
                            $parent->set( $field, $params[$field] );
                        }
                } else {
                    $parent->set( $field, null );
                }
            }
        }
    }
}


