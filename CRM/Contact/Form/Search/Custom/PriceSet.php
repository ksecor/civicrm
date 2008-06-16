<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Contact/Form/Search/Custom/Base.php';

class CRM_Contact_Form_Search_Custom_PriceSet
   extends    CRM_Contact_Form_Search_Custom_Base
   implements CRM_Contact_Form_Search_Interface {

    function __construct( &$formValues ) {
        parent::__construct( $formValues );

        $this->_columns = array( ts('Contact Id')      => 'contact_id'    ,
                                 ts('Participant Id' ) => 'participant_id',
                                 ts('Name')            => 'display_name'  );

    }

    function priceSetDAO( $eventID = null ) {

        // get all the events that have a price set associated with it
        $sql = "
SELECT e.id    as id,
       e.title as title,
       p.price_set_id as price_set_id
FROM   civicrm_event      e,
       civicrm_event_page ep,
       civicrm_price_set_entity  p

WHERE  p.entity_table = 'civicrm_event_page'
AND    p.entity_id    = ep.id
AND    ep.event_id     = e.id
";

        $params = array( );
        if ( $eventID ) {
            $params[1] = array( $eventID, 'Integer' );
            $sql .= " AND e.id = $eventID";
        }

        $dao = CRM_Core_DAO::executeQuery( $sql,
                                           $params );
        return $dao;
    }

    function buildForm( &$form ) {
        $dao = $this->priceSetDAO( );

        $event = array( );
        while ( $dao->fetch( ) ) {
            $event[$dao->id] = $dao->title;
        }

        if ( empty( $event ) ) {
            CRM_Core_Error::fatal( ts( 'There are no events with Price Sets' ) );
        }

        $form->add( 'select',
                    'event_id',
                    ts( 'Event' ),
                    $event,
                    true );

        /**
         * You can define a custom title for the search form
         */
         $this->setTitle('Price Set Export');
         
         /**
         * if you are using the standard template, this array tells the template what elements
         * are part of the search criteria
         */
         $form->assign( 'elements', array( 'event_id' ) );
    }

    function &columns( ) {
        // for the selected event, find the price set and all the columns associated with it.
        // create a column for each field and option group within it
        $dao = $this->priceSetDAO( $this->_formValues['event_id'] );

        if ( $dao->fetch( ) &&
             ! $dao->price_set_id ) {
            CRM_Core_Error::fatal( ts( 'There are no events with Price Sets' ) );
        }

        // get all the fields and all the option values associated with it
        require_once 'CRM/Core/BAO/PriceSet.php';
        $priceSet = CRM_Core_BAO_PriceSet::getSetDetail( $dao->price_set_id );
        if ( is_array( $priceSet[$dao->price_set_id] ) ) {
            foreach ( $priceSet[$dao->price_set_id]['fields'] as $key => $value ) {
                if ( is_array( $value['options'] ) ) {
                    foreach ( $value['options'] as $oKey => $oValue ) {
                        $this->_columns[$oValue['label']] = $oValue['label'];
                    }
                }
            }
        }

        return $this->_columns;
    }

    function summary( ) {
        return null;
    }

    function all( $offset = 0, $rowcount = 0, $sort = null,
                  $includeContactIDs = false ) {
        $selectClause = "
contact_a.id           as contact_id  ,
contact_a.contact_type as contact_type,
contact_a.sort_name    as sort_name,
state_province.name    as state_province
";
        return $this->sql( $selectClause,
                           $offset, $rowcount, $sort,
                           $includeContactIDs, null );

    }
    
    function from( ) {
        return "
FROM      civicrm_contact contact_a
LEFT JOIN civicrm_address address ON ( address.contact_id       = contact_a.id AND
                                       address.is_primary       = 1 )
LEFT JOIN civicrm_email           ON ( civicrm_email.contact_id = contact_a.id AND
                                       civicrm_email.is_primary = 1 )
LEFT JOIN civicrm_state_province state_province ON state_province.id = address.state_province_id
";
    }

    function where( $includeContactIDs = false ) {
        $params = array( );
        $where  = "contact_a.contact_type   = 'Household'";

        $count  = 1;
        $clause = array( );
        $name   = CRM_Utils_Array::value( 'household_name',
                                          $this->_formValues );
        if ( $name != null ) {
            if ( strpos( $name, '%' ) === false ) {
                $name = "%{$name}%";
            }
            $params[$count] = array( $name, 'String' );
            $clause[] = "contact_a.household_name LIKE %{$count}";
            $count++;
        }

        $state = CRM_Utils_Array::value( 'state_province_id',
                                         $this->_formValues );
        if ( $state ) {
            $params[$count] = array( $state, 'Integer' );
            $clause[] = "state_province.id = %{$count}";
        }

        if ( ! empty( $clause ) ) {
            $where .= ' AND ' . implode( ' AND ', $clause );
        }


        return $this->whereClause( $where, $params );
    }

    function templateFile( ) {
        return 'CRM/Contact/Form/Search/Custom/Sample.tpl';
    }

    function setDefaultValues( ) {
        return array( 'household_name'    => '', );
    }

    function alterRow( &$row ) {
    }
    
    function setTitle( $title ) {
        if ( $title ) {
            CRM_Utils_System::setTitle( $title );
        } else {
            CRM_Utils_System::setTitle(ts('Search'));
        }
    }
}


