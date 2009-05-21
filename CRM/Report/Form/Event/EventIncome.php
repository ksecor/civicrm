<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
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

require_once 'CRM/Report/Form.php';
require_once 'CRM/Event/PseudoConstant.php';
require_once 'CRM/Contribute/PseudoConstant.php';
require_once 'CRM/Core/OptionGroup.php';

class CRM_Report_Form_Event_EventIncome extends CRM_Report_Form {

    protected $_summary = null;
    
    
    function __construct( ) {

        $this->_columns = 
            array( 
                  'civicrm_event' =>
                  array( 'dao'     => 'CRM_Event_DAO_Event',
                         'filters' => 
                         array( 'id' => 
                                array( 'title'   => ts( 'Event ID' ),
                                       'default' => 'eq' ), 
                                ),
                         ),
                  );
        
        parent::__construct( );
    }
    
    function preProcess( ) {
        $this->assign( 'reportTitle', ts('Event Income Summary Report' ) );
        parent::preProcess( );
    }
    
    function buildEventReport( $eventID ) { 
        
        $eventTitle         = CRM_Event_PseudoConstant::event( $eventID );      
        $participantStatus  = CRM_Event_PseudoConstant::participantStatus( );
        $participantRole    = CRM_Event_PseudoConstant::participantRole( );
        $paymentInstruments = CRM_Contribute_PseudoConstant::paymentInstrument();

        $rows = $eventSummary = $roleRows = $statusRows = $instrumentRows = array( );
        
        $this->assign( 'mixedType', true );
        
        require_once 'CRM/Utils/Money.php';
        require_once 'CRM/Core/DAO/OptionGroup.php';
        $optionGroupDAO = new CRM_Core_DAO_OptionGroup();
        $optionGroupDAO->name = 'event_type';
        $optionGroupId = null;
        if ($optionGroupDAO->find(true) ) {
            $optionGroupId = $optionGroupDAO->id;
        }

        $sql = "
            SELECT  civicrm_event.title            as event_title,
                    civicrm_event.max_participants as max_participants, 
                    civicrm_event.start_date       as start_date,
                    civicrm_event.end_date         as end_date, 
                    civicrm_option_value.label     as event_type, 
                    SUM(civicrm_participant.fee_amount) as total,
                    COUNT(civicrm_participant.id)  as participant
            FROM       civicrm_event
            LEFT JOIN  civicrm_option_value ON (
                                                civicrm_event.event_type_id = civicrm_option_value.value AND
                                                civicrm_option_value.option_group_id = {$optionGroupId} )
            LEFT JOIN civicrm_participant ON ( civicrm_event.id = civicrm_participant.event_id )
            WHERE     civicrm_event.id = {$eventID}
            GROUP BY  civicrm_event.id
            ";
        $eventDAO  = CRM_Core_DAO::executeQuery( $sql );
        while ( $eventDAO->fetch( ) ) {
            $eventSummary['Title']      = $eventDAO->event_title;
            $eventSummary['Max Participants'] = $eventDAO->max_participants;
            $eventSummary['Start Date']       = CRM_Utils_Date::customFormat( $eventDAO->start_date );
            $eventSummary['End Date']         = CRM_Utils_Date::customFormat( $eventDAO->end_date );
            $eventSummary['Event Type']       = $eventDAO->event_type;
            $eventSummary['Event Income']     = CRM_Utils_Money::format( $eventDAO->total);
            $eventSummary['Registered Participant']  = $eventDAO->participant;
        }
        $this->assign_by_ref( 'summary', $eventSummary );

        //Total Participant Registerd for the Event
        $pariticipantCount = "
            SELECT COUNT(civicrm_participant.id ) 
            FROM     civicrm_participant
            WHERE    civicrm_participant.event_id = {$eventID} AND 
                     civicrm_participant.is_test  = 0 
            GROUP BY civicrm_participant.event_id
             ";
        $count  = CRM_Core_DAO::singleValueQuery( $pariticipantCount );

        
        //Count the Participant by Role ID for Event
        $role = "
            SELECT civicrm_participant.role_id         as ROLEID, 
                   COUNT( civicrm_participant.id )     as participant, 
                   SUM(civicrm_participant.fee_amount) as amount
            FROM     civicrm_participant
            WHERE    civicrm_participant.event_id = {$eventID} AND
                     civicrm_participant.is_test  = 0 
            GROUP BY civicrm_participant.role_id
            ";

        $roleDAO  = CRM_Core_DAO::executeQuery( $role );
       
        while ( $roleDAO->fetch( ) ) {
            $roleRows[$participantRole[$roleDAO->ROLEID]][] = $roleDAO->participant;
            $roleRows[$participantRole[$roleDAO->ROLEID]][] = round( ( $roleDAO->participant / $count ) * 100, 2 );
            $roleRows[$participantRole[$roleDAO->ROLEID]][] = $roleDAO->amount;
        }
        $rows['Role'] = $roleRows;


        //Count the Participant by status ID for Event
        $status = "
            SELECT civicrm_participant.status_id       as STATUSID, 
                   COUNT( civicrm_participant.id )     as participant, 
                   SUM(civicrm_participant.fee_amount) as amount
            FROM     civicrm_participant
            WHERE    civicrm_participant.event_id = {$eventID} AND
                     civicrm_participant.is_test  = 0 
            GROUP BY civicrm_participant.status_id
            ";

        $statusDAO = CRM_Core_DAO::executeQuery( $status );
      
        while ( $statusDAO->fetch( ) ) {
            $statusRows[$participantStatus[$statusDAO->STATUSID]][] = $statusDAO->participant;
            $statusRows[$participantStatus[$statusDAO->STATUSID]][] = round( ( $statusDAO->participant / $count ) * 100, 2 );
            $statusRows[$participantStatus[$statusDAO->STATUSID]][] = $statusDAO->amount;
        }

        $rows['Status'] = $statusRows;

        //Count the Participant by payment instrument ID for Event
        //e.g. Credit Card, Check,Cash etc
        $paymentInstrument = "
            SELECT c.payment_instrument_id as INSTRUMENT, 
                   COUNT( c.id )           as participant, 
                   SUM(p.fee_amount)       as amount
            FROM      civicrm_participant  p
            LEFT JOIN civicrm_participant_payment pp ON(pp.participant_id = p.id )
            LEFT JOIN civicrm_contribution c ON ( pp.contribution_id = c.id)
            WHERE     p.event_id = {$eventID} AND
                      p.is_test  = 0
            GROUP BY  c.payment_instrument_id
            ";

        $instrumentDAO = CRM_Core_DAO::executeQuery( $paymentInstrument );
       
        while ( $instrumentDAO->fetch( ) ) {
            //allow only if instrument is present in contribution table
            if ( $instrumentDAO->INSTRUMENT ) {
                $instrumentRows[$paymentInstruments[$instrumentDAO->INSTRUMENT]][] = $instrumentDAO->participant;
                $instrumentRows[$paymentInstruments[$instrumentDAO->INSTRUMENT]][] = 
                    round(($instrumentDAO->participant / $count ) * 100, 2 );
                $instrumentRows[$paymentInstruments[$instrumentDAO->INSTRUMENT]][] = $instrumentDAO->amount;
            }
        }
        $rows['Payment Method'] = $instrumentRows;
        
        $this->assign_by_ref( 'rows', $rows );
    }
    static function formRule( &$fields, &$files, $self ) {  
        $errors = array( );

        //allow only equal operator
        if ( ! $fields['id_op'] == 'eq' ) {
            $errors['id_op'] = ts('Please select equal operator');
        } 
        
        if ( $fields['id_value'] <= 0 || !ctype_digit( $fields['id_value'] ) ) {
            $errors['id_value'] = ts('Please select valid event ID');  
        } else {
            if ( !CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event', 
                                               $fields['id_value'], 'id' ) ) {
                $errors['id_value'] = ts('Entered Event ID not Exist');  
            }
        }
        return $errors;
    }

    function postProcess( ) {
        $this->_params = $this->controller->exportValues( $this->_name );
        if ( empty( $this->_params ) &&
             $this->_force ) {
            $this->_params = $this->_formValues;
        }
        $this->processReportMode( );

        $this->buildEventReport( $this->_params['id_value'] );

        parent::postProcess( );
    }   
}