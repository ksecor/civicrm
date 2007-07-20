<?php 

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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

class CRM_Event_BAO_Query 
{
    
    static function &getFields( ) 
    {
        $fields = array( );
        require_once 'CRM/Event/DAO/Event.php';
        $fields = array_merge( $fields, CRM_Event_DAO_Event::import( ) );
        $fields = array_merge( $fields, self::getParticipantFields( ) );
            
        return $fields;
    }

    static function &getParticipantFields( $onlyParticipant = false ) 
    {
        require_once 'CRM/Event/BAO/Participant.php';
        $fields =& CRM_Event_BAO_Participant::importableFields( 'Individual', true, $onlyParticipant );
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
        if ( ( $query->_mode & CRM_Contact_BAO_Query::MODE_EVENT ) ||
             CRM_Utils_Array::value( 'participant_id', $query->_returnProperties ) ) {
            
            $query->_select['participant_id'] = "civicrm_participant.id as participant_id";
            $query->_element['participant_id'         ] = 1;
            $query->_tables['civicrm_participant'] = $query->_whereTables['civicrm_participant'] = 1;

            //add event level
            if ( CRM_Utils_Array::value( 'event_level', $query->_returnProperties ) ) {
                $query->_select['event_level' ]  = "civicrm_participant.event_level as event_level";
                $query->_element['event_level']  = 1;
            }
        
            //add event title
            if ( CRM_Utils_Array::value( 'event_title', $query->_returnProperties ) ) {
                $query->_select['event_title'] = "civicrm_event.title as event_title";
                $query->_element['event_title'] = 1;
                $query->_tables['civicrm_event'] = 1;
                $query->_whereTables['civicrm_event'] = 1;
            }
        
            //add start date / end date
            if ( CRM_Utils_Array::value( 'event_start_date', $query->_returnProperties ) ) {
                $query->_select['event_start_date']  = "civicrm_event.start_date as event_start_date";
                $query->_element['event_start_date'] = 1;
            }
            
            if ( CRM_Utils_Array::value( 'event_end_date', $query->_returnProperties ) ) {
                $query->_select['event_end_date']  = "civicrm_event.end_date as event_end_date";
                $query->_element['event_end_date'] = 1;
            }
        
            //add status
            if ( CRM_Utils_Array::value( 'participant_status_id', $query->_returnProperties ) ) {
                $query->_select['participant_status_id' ]  = "civicrm_participant.status_id as participant_status_id";
                $query->_element['participant_status_id']  = 1;
            }
            
            //add role
            if ( CRM_Utils_Array::value( 'participant_role_id', $query->_returnProperties ) ) {
                $query->_select['participant_role_id' ]  = "civicrm_participant.role_id as participant_role_id";
                $query->_element['participant_role_id']  = 1;
            }
            
            //add register date
            if ( CRM_Utils_Array::value( 'participant_register_date', $query->_returnProperties ) ) {
                $query->_select['participant_register_date' ]  = "civicrm_participant.register_date as participant_register_date";
                $query->_element['participant_register_date']  = 1;
            }
            
            //add source
            if ( CRM_Utils_Array::value( 'participant_source', $query->_returnProperties ) ) {
                $query->_select['participant_source' ]  = "civicrm_participant.source as participant_source";
                $query->_element['participant_source']  = 1;
                $query->_tables['civicrm_participant'] = $query->_whereTables['civicrm_participant'] = 1;
            }
            
            //participant note
            if ( CRM_Utils_Array::value( 'participant_note', $query->_returnProperties ) ) {
                $query->_select['participant_note' ] = "civicrm_note.note as participant_note";
                $query->_element['participant_note'] = 1;
                $query->_tables['participant_note' ] = 1;
                $query->_whereTables['civicrm_note'] = 1;
            }
        }
    }

    static function where( &$query ) 
    {
        foreach ( array_keys( $query->_params ) as $id ) {
            if ( substr( $query->_params[$id][0], 0, 6) == 'event_' ||
                 substr( $query->_params[$id][0], 0, 12) == 'participant_') {
                self::whereClauseSingle( $query->_params[$id], $query );
            }
        }
    }
    
  
    static function whereClauseSingle( &$values, &$query ) 
    {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;

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

        case 'event_type':
            
            $value = ucwords(strtolower(addslashes(trim($value))));

            require_once 'CRM/Core/OptionGroup.php';
            require_once 'CRM/Utils/Array.php';

            $eventTypes  = CRM_Core_OptionGroup::values("event_type" );
            $eventId = CRM_Utils_Array::key($value, $eventTypes);
            $query->_where[$grouping][] = "civicrm_participant.event_id = civicrm_event.id and civicrm_event.event_type_id = '{$eventId}'";
            $query->_qill[$grouping ][] = ts( 'Event Type - %1', array( 1 => $value ) );
            $query->_tables['civicrm_event'] = $query->_whereTables['civicrm_event'] = 1;
            return;
          
        case 'participant_test':
            $query->_where[$grouping][] = "civicrm_participant.is_test $op $value";
            if ( $value ) {
                $query->_qill[$grouping][]  = ts("Test Participants Only");
            }
            $query->_tables['civicrm_participant'] = $query->_whereTables['civicrm_participant'] = 1;
            
            return;

        case 'participant_status_id':

            $val = array( );
            if ( is_array( $value ) ) {
                foreach ($value as $k => $v) {
                    if ($v) {
                        $val[$k] = $k;
                    }
                } 
                $status = implode (',' ,$val);
            } else {
                $status = $value;
            }

            if (count($val) > 1) {
                $op = 'IN';
                $status = "({$status})";
            }     

            require_once 'CRM/Event/PseudoConstant.php';
            $statusTypes  = CRM_Event_PseudoConstant::participantStatus( );

            $names = array( );

            if ( !empty($val) ) {
                foreach ( $val as $id => $dontCare ) {
                    $names[] = $statusTypes[$id];
                }
            } else {
                $names[] = $statusTypes[$value];
            }

            $query->_qill[$grouping][]  = ts('Participant Status %1', array( 1 => $op ) ) . ' ' . implode( ' ' . ts('or') . ' ', $names );
            
            $query->_where[$grouping][] = "civicrm_participant.status_id {$op} {$status}";
            $query->_tables['civicrm_participant'] = $query->_whereTables['civicrm_participant'] = 1;
            return;

        case 'participant_role_id':

            $val = array( );
            if ( is_array( $value ) ) {
                foreach ($value as $k => $v) {
                    if ($v) {
                        $val[$k] = $k;
                    }
                } 
                $role = implode (',' ,$val);
            } else {
                $role = $value;
            }

            if (count($val) > 1) {
                $op = 'IN';
                $role = "({$role})";
            }     

            require_once 'CRM/Event/PseudoConstant.php';
            $roleTypes  = CRM_Event_PseudoConstant::participantRole( );

            $names = array( );
            if ( !empty($val) ) {
                foreach ( $val as $id => $dontCare ) {
                    $names[] = $roleTypes[$id];
                }
            } else {
                $names[] = $roleTypes[$value];
            }

            $query->_qill[$grouping][]  = ts('Participant Role %1', array( 1 => $op ) ) . ' ' . implode( ' ' . ts('or') . ' ', $names );
            
            $query->_where[$grouping][] = "civicrm_participant.role_id {$op} {$role}";
            $query->_tables['civicrm_participant'] = $query->_whereTables['civicrm_participant'] = 1;
            return;

        case 'participant_source':
            $query->_where[$grouping][] = "civicrm_participant.source $op '$value'";
            $query->_qill[$grouping][]  = ts("Participant Source" ) . " $op $value";
            $query->_tables['civicrm_participant'] = $query->_whereTables['civicrm_participant'] = 1;
            return;

        case 'participant_register_date':
            $query->dateQueryBuilder( $values,
                                       'civicrm_participant', 'participant_register_date', 'register_date', 'Register Date' );
            return;

        case 'participant_id':
            $query->_where[$grouping][] = "civicrm_participant.id $op $value";
            $query->_tables['civicrm_participant'] = $query->_whereTables['civicrm_participant'] = 1;
            return;

        case 'event_id':
            $query->_where[$grouping][] = "civicrm_event.id $op $value";
            $query->_tables['civicrm_event'] = $query->_whereTables['civicrm_event'] = 1;
            return;

        case 'participant_contact_id':
            $query->_where[$grouping][] = "civicrm_participant.contact_id $op $value";
            $query->_tables['civicrm_participant'] = $query->_whereTables['civicrm_participant'] = 1;
            return;
            
        case 'event_type_id':
            $query->_where[$grouping][] = "civicrm_event.event_type_id $op $value";
            $query->_tables['civicrm_event'] = $query->_whereTables['civicrm_event'] = 1;
            return;

        case 'event_is_public':
            $query->_where[$grouping][] = "civicrm_event.is_public $op $value";
            $query->_tables['civicrm_event'] = $query->_whereTables['civicrm_event'] = 1;
            return;

        }
    }

    static function from( $name, $mode, $side ) 
    {
        $from = null;
        switch ( $name ) {
        
        case 'civicrm_participant':
            $from = " LEFT JOIN civicrm_participant ON civicrm_participant.contact_id = contact_a.id ";
            break;
    
        case 'civicrm_event':
            $from = " INNER JOIN civicrm_event ON civicrm_participant.event_id = civicrm_event.id ";
            break;

        case 'participant_note':
            $from .= " $side JOIN civicrm_note ON ( civicrm_note.entity_table = 'civicrm_participant' AND
                                                        civicrm_participant.id = civicrm_note.entity_id )";
            break;

        }
        return $from;
    }

    /**
     * getter for the qill object
     *
     * @return string
     * @access public
     */
    function qill( ) {
        return (isset($this->_qill)) ? $this->_qill : "";
    }
   
    static function defaultReturnProperties( $mode ) 
    {
        $properties = null;
        if ( $mode & CRM_Contact_BAO_Query::MODE_EVENT ) {
            $properties = array(  
                                'contact_type'              => 1, 
                                'sort_name'                 => 1, 
                                'display_name'              => 1,
                                'event_title'               => 1,
                                'event_start_date'          => 1,
                                'event_end_date'            => 1,
                                'participant_id'            => 1,
                                'participant_status_id'     => 1,
                                'participant_role_id'       => 1,
                                'participant_register_date' => 1,
                                'participant_source'        => 1,
                                'event_level'               => 1,
                                'participant_is_test'       => 1
                                );
       
            // also get all the custom participant properties
            require_once "CRM/Core/BAO/CustomField.php";
            $fields = CRM_Core_BAO_CustomField::getFieldsForImport('Participant');
            if ( ! empty( $fields ) ) {
                foreach ( $fields as $name => $dontCare ) {
                    $properties[$name] = 1;
                }
            }
        }

        return $properties;
    }

    static function buildSearchForm( &$form ) 
    {
        $config =& CRM_Core_Config::singleton( );
        $domainID = CRM_Core_Config::domainID( );

        $dataURLEvent     = CRM_Utils_System::url( 'civicrm/ajax/event',
                                                   "d={$domainID}&s=%{searchString}",
                                                   true, null, false);
        $dataURLEventType = CRM_Utils_System::url( 'civicrm/ajax/eventType',
                                                   "d={$domainID}&s=%{searchString}",
                                                   true, null, false);
        
        $form->assign( 'dojoIncludes', "dojo.require('dojo.widget.ComboBox');" );
        
        $dojoAttributesEvent     = " dojoType='ComboBox' mode='remote' dataUrl='{$dataURLEvent}' ";
        $dojoAttributesEventType = " dojoType='ComboBox' mode='remote' dataUrl='{$dataURLEventType}' ";
        
        $title =& $form->add('text', 'event_title', ts('Event Name'), $dojoAttributesEvent );
        $type  =& $form->add('text', 'event_type',  ts('Event Type'), $dojoAttributesEventType );

        if ( $title->getValue( ) ) {
            $form->assign( 'event_title_value',  $title->getValue( ) );
        } else {
            $fv  =& $form->getFormValues( );
            $val = CRM_Utils_Array::value( 'event_title', $fv);
            if ( $val ) {
                $form->assign( 'event_title_value',  $val );
            }
        }

        if ( $type->getValue( ) ) {
            $form->assign( 'event_type_value',  $type->getValue( ) );
        } else {
            $fv  =& $form->getFormValues( );
            $val = CRM_Utils_Array::value( 'event_type', $fv);
            if ( $val ) {
                $form->assign( 'event_type_value',  $val );
            }
        }

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
        $form->addGroup($status, 'participant_status_id', ts('Participant Status'));
        
        //adding participant role
        $roleValues = CRM_Event_PseudoConstant::participantRole();
        foreach ( $roleValues as $k => $v ) {
            $role[] = HTML_QuickForm::createElement('advcheckbox', $k , null, $v );
        }
        $form->addGroup($role, 'participant_role_id', ts('Participant Role'));

        $form->addElement( 'checkbox', 'participant_test' , ts( 'Find Test Participants Only?' ) );

        // add all the custom  searchable fields
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $groupDetails = CRM_Core_BAO_CustomGroup::getGroupDetail( null, true, array( 'Participant' ) );
        if ( $groupDetails ) {
            require_once 'CRM/Core/BAO/CustomField.php';
            $form->assign('participantGroupTree', $groupDetails);
            foreach ($groupDetails as $group) {
                foreach ($group['fields'] as $field) {
                    $fieldId = $field['id'];
                    $elementName = 'custom_' . $fieldId;
                    CRM_Core_BAO_CustomField::addQuickFormElement( $form,
                                                                   $elementName,
                                                                   $fieldId,
                                                                   false, false, true );
                }
            }
        }

        $form->assign( 'validCiviEvent', true );
    }
    
    static function searchAction( &$row, $id ) 
    {
    }

    static function tableNames( &$tables ) 
    {
        //add participant table 
        if ( CRM_Utils_Array::value( 'civicrm_event', $tables ) ) {
            $tables = array_merge( array( 'civicrm_participant' => 1), $tables );
        }
    }
  
}

?>
