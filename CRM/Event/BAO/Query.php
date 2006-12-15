<?php 
/* 
 +--------------------------------------------------------------------+ 
 | CiviCRM version 1.6                                                | 
 +--------------------------------------------------------------------+ 
 | Copyright CiviCRM LLC (c) 2004-2006                                  | 
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
 | http://www.civicrm.org/licensing/                                 | 
 +--------------------------------------------------------------------+ 
*/ 
 
/** 
 * 
 * 
 * @package CRM 
 * @author Donald A. Lobo <lobo@civicrm.org> 
 * @copyright CiviCRM LLC (c) 2004-2006 
 * $Id$ 
 * 
 */ 

class CRM_Event_BAO_Query 
{
    
    static function &getFields( ) 
    {
        require_once 'CRM/Event/DAO/Event.php';
        $fields =& CRM_Event_DAO_Event::import( );
        return $fields;
    }
    

    /** 
     * build select for CiviEvent 
     * 
     * @return void  
     * @access public  
     */
    static function select( &$query ) 
    {
        
        if ( $query->_mode & CRM_Contact_BAO_Query::MODE_EVENT ) {

            $query->_select['participant_id'] = "civicrm_participant.id as participant_id";
            $query->_element['participant_id'] = 1;
            $query->_tables['civicrm_participant'] = 1;
            $query->_whereTables['civicrm_participant'] = 1;
           
            //add status
            $query->_select['status_id' ]  = "civicrm_participant.status_id as status_id";
            $query->_element['status_id']  = 1;
            
            //add event level
            $query->_select['event_level' ]  = "civicrm_participant.event_level as event_level";
            $query->_element['event_level']  = 1;
            
            //add event title
            $query->_select['title'] = "civicrm_event.title as event_title";
            $query->_element['title'] = 1;
            $query->_tables['civicrm_event'] = 1;
            $query->_whereTables['civicrm_event'] = 1;
            
            //add start date / end date
            $query->_select['start_date']  = "civicrm_event.start_date as start_date";
            $query->_element['start_date'] = 1;
            $query->_select['end_date']  = "civicrm_event.end_date as end_date";
            $query->_element['end_date'] = 1;
        }
    }

    static function where( &$query ) 
    {
        foreach ( array_keys( $query->_params ) as $id ) {
            if ( substr( $query->_params[$id][0], 0, 6) == 'event_' ) {
                self::whereClauseSingle( $query->_params[$id], $query );
            }
        }
    }
    
  
    static function whereClauseSingle( &$values, &$query ) 
    {

        list( $name, $op, $value, $grouping, $wildcard ) = $values;
        
        //CRM_Core_Error::debug("nm", $value);

        switch( $name ) {
            
        case 'event_start_date_low':
        case 'event_start_date_high':
            $query->dateQueryBuilder( $values,
                                      'civicrm_event', 'event_start_date', 'start_date', 'Start Date' );
            return;

        case 'event_end_date_low':
        case 'event_end_date_high':
            $query->dateQueryBuilder( $values,
                                       'civicrm_event', 'event_end_date', 'end_date', 'End Date' );
            return;

        case 'event_title':
            
            $value = strtolower(addslashes(trim($value)));

            $query->_where[$grouping][] = "civicrm_event.title $op '{$value}'";
            $query->_qill[$grouping ][] = ts( 'Event %2 %1', array( 1 => $value, 2 => $op) );
            $query->_tables['civicrm_event'] = $query->_whereTables['civicrm_event'] = 1;
            return;

        case 'event_participant_status':

            foreach ($value as $k => $v) {
                if ($v) {
                    $val[$k] = $k;
                }
            } 

            $status = implode (',' ,$val);

            if (count($val) > 1) {
                $op = 'IN';
                $status = "({$status})";
            }     

            require_once 'CRM/Event/PseudoConstant.php';
            $statusTypes  = CRM_Event_PseudoConstant::participantStatus( );

            $names = array( );
            foreach ( $val as $id => $dontCare ) {
                $names[] = $statusTypes[$id];
            }
            $query->_qill[$grouping][]  = ts('Participant Status %1', array( 1 => $op ) ) . ' ' . implode( ' ' . ts('or') . ' ', $names );
            
            $query->_where[$grouping][] = "civicrm_participant.status_id {$op} {$status}";
            $query->_tables['civicrm_participant'] = $query->_whereTables['civicrm_participant'] = 1;
            return;
        }
    }

    static function from( $name, $mode, $side ) 
    {
        $from = null;
        switch ( $name ) {
        
        case 'civicrm_participant':
            $from = " INNER JOIN civicrm_participant ON civicrm_participant.contact_id = contact_a.id ";
            break;
    
        case 'civicrm_event':
            $from = " INNER JOIN civicrm_event ON civicrm_participant.event_id = civicrm_event.id ";
            break;
        }
        return $from;
    }
    
    static function defaultReturnProperties( $mode ) 
    {
        $properties = null;
        if ( $mode & CRM_Contact_BAO_Query::MODE_EVENT ) {
            $properties = array(  
                                'contact_type'           => 1, 
                                'sort_name'              => 1, 
                                'display_name'           => 1,
                                'start_date'             => 1,
                                'end_date'               => 1,
                                //'status_id'              => 1,
                                'title'                  => 1
                                );
        }
        return $properties;
    }

    static function buildSearchForm( &$form ) 
    {
        $config =& CRM_Core_Config::singleton( );
        $domainID = CRM_Core_Config::domainID( );
        $dataURL = $config->userFrameworkResourceURL . "extern/ajax.php?q=civicrm/event&d={$domainID}&s=%{searchString}";
        
        $form->assign( 'dataURL', $dataURL );
        
        // Date selects for date 
        $form->add('date', 'event_start_date_low', ts('Event Date - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('event_start_date_low', ts('Select a valid date.'), 'qfDate'); 
 
        $form->add('date', 'event_end_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('event_end_date_high', ts('Select a valid date.'), 'qfDate'); 

        require_once 'CRM/Event/PseudoConstant.php';
        $statusValues = CRM_Event_PseudoConstant::participantStatus(); 

        foreach ( $statusValues as $k => $v ) {
            $status[] = HTML_QuickForm::createElement('advcheckbox', $k , null, $v );
        }
        $form->addGroup($status, 'event_participant_status', ts('Participant status'));
        
        $form->assign( 'validCiviEvent', true );
    }

    static function searchAction( &$row, $id ) 
    {
    }
  
}

?>
