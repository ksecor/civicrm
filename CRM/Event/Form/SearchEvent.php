<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/OptionGroup.php';

class CRM_Event_Form_SearchEvent extends CRM_Core_Form 
{
    function setDefaultValues( )
    {
        $defaults = array( );
        $today= CRM_Utils_Date::getToday( );
        list($year, $month, $day) = explode ('-', $today);
        $eventStartDate = date( 'Y-m-d', mktime( 0, 0, 0, $month, $day - 5, $year) );
        $defaults['start_date'] = $eventStartDate;
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
                    array(CRM_Core_DAO::getAttribute('CRM_Event_DAO_Event', 'title'), 
                          'style' => 'width: 80%') );
        
        $event_type = CRM_Core_OptionGroup::values( 'event_type', false );
        
        foreach($event_type as $eventId => $eventName) {
            $this->addElement('checkbox', "event_type_id[$eventId]", 'Event Type', $eventName);
        }
       
        $this->add('date', 'start_date', ts('From'), CRM_Core_SelectValues::date('relative')); 
        $this->addRule('start_date', ts('Select a valid Event FROM date.'), 'qfDate'); 
        
        $this->add('date', 'end_date', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $this->addRule('end_date', ts('Select a valid Event TO date.'), 'qfDate'); 
        
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
            $fields = array( 'title', 'event_type_id', 'start_date', 'end_date' );
            foreach ( $fields as $field ) {
                if ( isset( $params[$field] ) &&
                     ! CRM_Utils_System::isNull( $params[$field] ) ) {
                    $parent->set( $field, $params[$field] );
                } else {
                    $parent->set( $field, null );
                }
            }
        }
    }
}

?>
