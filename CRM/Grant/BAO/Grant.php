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

require_once 'CRM/Grant/DAO/Grant.php';

class CRM_Grant_BAO_Grant extends CRM_Grant_DAO_Grant 
{


    /**
     * the name of option value group from civicrm_option_group table
     * that stores grant statuses
     */
    static $statusGroupName = 'grant_status';
    
    
    /**
     * the name of option value group from civicrm_option_group table
     * that stores grant statuses
     */
    static $typeGroupName = 'grant_type';    

    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }


    /**
     * Function to get events Summary
     *
     * @static
     * @return array Array of event summary values
     */
    static function getGrantSummary( $admin = false )
    {

        $query = 
" SELECT status_id, count(id) as status_total 
  FROM civicrm_grant  GROUP BY status_id";
        
        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        
        require_once 'CRM/Core/OptionGroup.php';
        $status = array( );
        $status = CRM_Core_OptionGroup::values( 'grant_status' );

        while ( $dao->fetch( ) ) {
            $stats[$dao->status_id] = array( 'label' => $status[$dao->status_id],
                                             'total' => $dao->status_total );
            $summary['total_grants'] += $dao->status_total;
        }

        $summary['per_status'] = $stats;
//        $summary['total_grants'] = array_sum( $stats );
        
        return $summary;
    }


    /**
     * Function to get events Summary
     *
     * @static
     * @return array Array of event summary values
     */
    static function getGrantStatusOptGroup( ) {
        require_once 'CRM/Core/BAO/OptionGroup.php';
        
        $config =& CRM_Core_Config::singleton( );

        $params = array( );
        $params['domain_id'] = $config->domainID( );
        $params['name'] = CRM_Grant_BAO_Grant::$statusGroupName;

        $defaults = array();
        
        $bao = new CRM_Core_BAO_OptionGroup( );
        $og = $bao->retrieve( $params, $defaults );

        if( ! $og ) {
            CRM_Core_Error::fatal('No option group for grant statuses - database discrepancy! Make sure you loaded civicrm_data.mysql');
        }

        return $og;
    }


    static function getGrantStatuses( ) {

        $og = CRM_Grant_BAO_Grant::getGrantStatusOptGroup();

        require_once 'CRM/Core/BAO/OptionValue.php';
        $dao = new CRM_Core_DAO_OptionValue( );
        
        $dao->option_group_id = $og->id;
        $dao->find();
        
        $statuses = array();
        
        while ( $dao->fetch( ) ) {
            $statuses[$dao->id] = $dao->label;
        }

        return $statuses;
    }


    /**
     * Function to retrieve grant types.
     * 
     * @static
     * @return array Array of grant summary statistics
     */
    static function getGrantTypes( ) {
        require_once 'CRM/Core/BAO/OptionValue.php';
        return CRM_Core_OptionGroup::values( CRM_Grant_BAO_Grant::$typeGroupName );
    }

    /**
     * Function to retrieve statistics for grants.
     * 
     * @static
     * @return array Array of grant summary statistics
     */
     static function getGrantStatistics( $admin = false ) {
         
         $grantStatuses = array(); 
         
         
         
     }
 
    
    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Grant_BAO_ManageGrant object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $grant  = new CRM_Grant_DAO_Grant( );
        $grant->copyValues( $params );
        if ( $grant->find( true ) ) {
            CRM_Core_DAO::storeValues( $grant, $defaults );
            return $grant;
        }
        return null;
    }

    /**
     * function to add grant
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add(&$params, &$ids)
    {
        require_once 'CRM/Utils/Hook.php';
        
        if ( CRM_Utils_Array::value( 'grant', $ids ) ) {
            CRM_Utils_Hook::pre( 'edit', 'Grant', $ids['grant_id'], $params );
        } else {
            CRM_Utils_Hook::pre( 'create', 'Grant', null, $params ); 
        }
        
        $grant =& new CRM_Grant_DAO_Grant( );
        $grant->domain_id = CRM_Core_Config::domainID( );
        $grant->id = CRM_Utils_Array::value( 'grant', $ids );
        
        $grant->copyValues( $params );
        $result = $grant->save( );
        
        if ( CRM_Utils_Array::value( 'grant', $ids ) ) {
            CRM_Utils_Hook::post( 'edit', 'Grant', $grant->id, $grant );
        } else {
            CRM_Utils_Hook::post( 'create', 'Grant', $grant->id, $grant );
        }
        
        return $result;
    }
    
    /**
     * function to create the event
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * 
     */
    public static function create( &$params, &$ids) 
    {
        CRM_Core_DAO::transaction('BEGIN');
        
        $grant = self::add($params, $ids);
        
        if ( is_a( $grant, 'CRM_Core_Error') ) {
            CRM_Core_DAO::transaction( 'ROLLBACK' );
            return $grant;
        }
        
        $session = & CRM_Core_Session::singleton();
        $id = $session->get('userID');
        if ( !$id ) {
            $id = $params['contact_id'];
        } 
        if ( CRM_Utils_Array::value('note', $params) ) {
            require_once 'CRM/Core/BAO/Note.php';
            $noteParams = array(
                                'entity_table'  => 'civicrm_grant',
                                'note'          => $params['note'],
                                'entity_id'     => $grant->id,
                                'contact_id'    => $id,
                                'modified_date' => date('Ymd')
                                );
            
            CRM_Core_BAO_Note::add( $noteParams, $ids['note'] );
        }        
        // Log the information on successful add/edit of Grant
        require_once 'CRM/Core/BAO/Log.php';
        $logParams = array(
                        'entity_table'  => 'civicrm_grant',
                        'entity_id'     => $grant->id,
                        'modified_id'   => $id,
                        'modified_date' => date('Ymd')
                        );
        
        CRM_Core_BAO_Log::add( $logParams );
        
        // add custom field values
        if (CRM_Utils_Array::value('custom', $params)) {
            foreach ($params['custom'] as $customValue) {
                $cvParams = array(
                                  'entity_table'    => 'civicrm_grant',
                                  'entity_id'       => $grant->id,
                                  'value'           => $customValue['value'],
                                  'type'            => $customValue['type'],
                                  'custom_field_id' => $customValue['custom_field_id'],
                                  'file_id'         => $customValue['file_id'],
                                  );
                if ($customValue['id']) {
                    $cvParams['id'] = $customValue['id'];
                }
                require_once 'CRM/Core/BAO/CustomValue.php';
                CRM_Core_BAO_CustomValue::create($cvParams);
            }
        }

        CRM_Core_DAO::transaction('COMMIT');
        
        return $grant;
    }
     
    /**
     * Function to delete the grant
     *
     * @param int $id  grant id
     *
     * @access public
     * @static
     *
     */
    static function del( $id )
    { 
        require_once 'CRM/Grant/DAO/Grant.php';
        $grant     = & new CRM_Grant_DAO_Grant( );
        $grant->id = $id; 
        
        $grant->find();
        while ($grant->fetch() ) {
            return $grant->delete();
        }
        return false;
    }
    
    /**
     * Function to get current/future Grants 
     *
     * @param $all boolean true if events all are required else returns current and future events
     *
     * @static
     */
    static function getGrants( $all = false, $id = false) 
    {
        $query = "SELECT `id`, `title`, `start_date` FROM `civicrm_event`";
        
        if ( !$all ) {
            //$endDate = CRM_Utils_Date::isoToMysql(date('Y-m-d',mktime(00,00,00, date('n') + 1, date('d'), date('Y') )) );
            
            $endDate = date( 'YmdHis' );
                        
            $query .= " WHERE `end_date` >= {$endDate};";
        }
        if ( $id ) {
            $query .= " WHERE `id` = {$id};";
        }

        $events = array( );
        
        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch( ) ) {
            $events[$dao->id] = $dao->title . ' - '.CRM_Utils_Date::customFormat($dao->start_date);
        }
        
        return $events;
    }
    

   
    /**
     * function to get the complete information of an event
     *
     * @param  date    $start    the start date for the event
     * @param  integer $type     the type id for the event 
     *
     * @return  array  $all      array of all the events that are searched
     * @static
     * @access public
     */      
    static function &getCompleteInfo( $start = null, $type = null ) 
    {
        
        if ( $start ) {
            // get events with start_date >= requested start
            $condition =  CRM_Utils_Type::escape( $start, 'Date' );
        } else {
            // get events with start date >= today
            $condition =  date("Ymd");
        }
        if ( $type ) {
            $condition = $condition . " AND civicrm_event.event_type_id = " . CRM_Utils_Type::escape( $type, 'Integer' );
        }

        // Get the Id of Option Group for Grant Types
        require_once 'CRM/Core/DAO/OptionGroup.php';
        $optionGroupDAO = new CRM_Core_DAO_OptionGroup();
        $optionGroupDAO->name = 'event_type';
        $optionGroupId = null;
        if ($optionGroupDAO->find(true) ) {
            $optionGroupId = $optionGroupDAO->id;
        }
        
        $params = array( 1 => array( $optionGroupId, 'Integer' ),
                         2 => array( CRM_Core_Config::domainID( ),
                                     'Integer' ) );
        
        $query = "
SELECT
  civicrm_event.id as event_id,
  civicrm_email.email as email,
  civicrm_event.title as summary,
  civicrm_event.start_date as start,
  civicrm_event.end_date as end,
  civicrm_event.description as description,
  civicrm_event.is_show_location as is_show_location,
  civicrm_option_value.label as event_type,
  civicrm_location.name as location_name,
  civicrm_address.street_address as street_address,
  civicrm_address.supplemental_address_1 as supplemental_address_1,
  civicrm_address.supplemental_address_2 as supplemental_address_2,
  civicrm_address.city as city,
  civicrm_address.postal_code as postal_code,
  civicrm_address.postal_code_suffix as postal_code_suffix,
  civicrm_state_province.abbreviation as state,
  civicrm_country.name as country,
  civicrm_location_type.name as location_type
FROM civicrm_event
LEFT JOIN civicrm_location ON (civicrm_location.entity_table = 'civicrm_event' AND
                               civicrm_event.id = civicrm_location.entity_id )
LEFT JOIN civicrm_address ON civicrm_location.id = civicrm_address.location_id
LEFT JOIN civicrm_state_province ON civicrm_address.state_province_id = civicrm_state_province.id
LEFT JOIN civicrm_country ON civicrm_address.country_id = civicrm_country.id
LEFT JOIN civicrm_location_type ON civicrm_location_type.id = civicrm_location.location_type_id
LEFT JOIN civicrm_email ON civicrm_location.id = civicrm_email.location_id
LEFT JOIN  civicrm_option_value ON (
                                    civicrm_event.event_type_id = civicrm_option_value.value AND
                                    civicrm_option_value.option_group_id = %1 )
WHERE civicrm_event.is_active = 1 
      AND civicrm_event.domain_id = %2
      AND civicrm_event.is_public = 1 
      AND civicrm_event.start_date >= ". $condition .
" ORDER BY   civicrm_event.start_date ASC";

        $dao =& CRM_Core_DAO::executeQuery( $query, $params );

        $all = array( );
        $config =& CRM_Core_Config::singleton( );

        while ( $dao->fetch( ) ) {
        
            $info                     = array( );
            $info['event_id'     ]    = $dao->event_id;
            $info['uid'          ]    = "CiviCRM_GrantID_" . $dao->event_id . "@" . $config->userFrameworkBaseURL;
            $info['summary'      ]    = $dao->summary;
            $info['description'  ]    = $dao->description;
            $info['start_date'   ]    = $dao->start;
            $info['end_date'     ]    = $dao->end;
            $info['contact_email']    = $dao->email;
            $info['event_type'   ]    = $dao->event_type;
            $info['is_show_location'] = $dao->is_show_location;
  
  
            $address = '';
            require_once 'CRM/Utils/String.php';
            CRM_Utils_String::append( $address, ', ',
                                      array( $dao->location_name) );
            $addrFields = array(
                            'street_address'         => $dao->street_address,
                            'supplemental_address_1' => $dao->supplemental_address_1,
                            'supplemental_address_2' => $dao->supplemental_address_2,
                            'city'                   => $dao->city,
                            'state_province'         => $dao->state,
                            'postal_code'            => $dao->postal_code,
                            'postal_code_suffix'     => $dao->postal_code_suffix,
                            'country'                => $dao->country,
                            'county'                 => null
                            );           
            
            require_once 'CRM/Utils/Address.php';
            CRM_Utils_String::append( $address, ', ',
                                      CRM_Utils_Address::format($addrFields) );
            $info['location'     ] = $address;
            $info['url'          ] = CRM_Utils_System::url( 'civicrm/event/info', 'reset=1&id=' . $dao->event_id, true, null, false );
           
            $all[] = $info;
        }
        
        return $all;
    }

    /**
     * This function is to make a copy of a Grant, including
     * all the fields in the event Wizard
     *
     * @param int $id the event id to copy
     *
     * @return void
     * @access public
     */
    static function copy( $id )
    {
        $fieldsToPrefix = array( 'title' => ts( 'Copy of ' ) );
                
        $copyGrant      =& CRM_Core_DAO::copyGeneric( 'CRM_Grant_DAO_Grant', array( 'id' => $id ), null, $fieldsToPrefix );
        $copyGrantPage  =& CRM_Core_DAO::copyGeneric( 'CRM_Grant_DAO_GrantPage', 
                                                      array( 'event_id'    => $id),
                                                      array( 'event_id'    => $copyGrant->id ));
        $copyCustom     =& CRM_Core_DAO::copyGeneric( 'CRM_Core_DAO_CustomOption',  
                                                      array( 'entity_id'    => $id,
                                                             'entity_table' => 'civicrm_event_page'),
                                                      array( 'entity_id'    => $copyGrant->id ));
        $copyUF         =& CRM_Core_DAO::copyGeneric( 'CRM_Core_DAO_UFJoin',
                                                      array( 'entity_id'    => $id,
                                                             'entity_table' => 'civicrm_event'),
                                                      array( 'entity_id'    => $copyGrant->id ));
        $copyCustomData =& CRM_Core_DAO::copyGeneric( 'CRM_Core_DAO_CustomValue',
                                                      array( 'entity_id'    => $id,
                                                             'entity_table' => 'civicrm_event'),
                                                      array( 'entity_id'    => $copyGrant->id ));

             
        require_once 'CRM/Core/BAO/Location.php';
        require_once 'CRM/Grant/Form/ManageGrant/Location.php';
        $params  = array( 'entity_id' => $id ,'entity_table' => 'civicrm_event');
        $location = CRM_Core_BAO_Location::getValues($params, $values, $ids, 1);
        
        $values['entity_id']    = $copyGrant->id ;
        $values['entity_table'] = 'civicrm_event';
        
        $values['location'][1]['id'] = null;
        $values['location'][1]['contact_id'] = null;
        unset($values['location'][1]['address']['id']);
        unset($values['location'][1]['address']['location_id']);
        $values['location'][1]['phone'][1]['id'] = null;
        $values['location'][1]['phone'][1]['location_id'] = null;
        $values['location'][1]['email'][1]['id'] = null;
        $values['location'][1]['email'][1]['location_id'] = null;
        $values['location'][1]['im'][1]['id'] = null;
        $values['location'][1]['im'][1]['location_id'] = null;
        
        $ids = array();
        
        CRM_Core_BAO_Location::add( $values, $ids, 1, false );
    }

}
?>
