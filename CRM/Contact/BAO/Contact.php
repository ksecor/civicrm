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
 | at http://www.openngo.org/faqs/licensing.html                      |
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

require_once 'CRM/Core/DAO/Note.php';
require_once 'CRM/Core/Form.php';

require_once 'CRM/Contact/DAO/Contact.php';

require_once 'CRM/Core/DAO/Location.php';
require_once 'CRM/Core/DAO/Address.php';
require_once 'CRM/Core/DAO/Phone.php';
require_once 'CRM/Core/DAO/Email.php';



/**
 * rare case where because of inheritance etc, we actually store a reference
 * to the dao object rather than inherit from it
 */

class CRM_Contact_BAO_Contact extends CRM_Contact_DAO_Contact 
{
    /**
     * the types of communication preferences
     *
     * @var array
     */
    static $_commPrefs = array( 'do_not_phone', 'do_not_email', 'do_not_mail', 'do_not_trade' );

    /**
     * static field for all the contact information that we can potentially import
     *
     * @var array
     * @static
     */
    static $_importableFields = null;

    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * check if the logged in user has permissions for the operation type
     *
     * @param int    $id   contact id
     * @param string $type the type of operation (view|edit)
     *
     * @return boolean true if the user has permission, false otherwise
     * @access public
     * @static
     */
    static function permissionedContact( $id, $type = CRM_Core_Permission::VIEW ) {
        $tables     = array( );
        $permission = CRM_Core_Permission::whereClause( $type, $tables );
        $from       = CRM_Contact_BAO_Query::fromClause( $tables );
        $query = "
SELECT count(DISTINCT civicrm_contact.id) 
       $from
WHERE civicrm_contact.id = " . CRM_Utils_Type::escape($id, 'Integer') . 
" AND $permission";

        $dao =& new CRM_Core_DAO( );
        $dao->query($query);
        
        $result = $dao->getDatabaseResult();
        $row    = $result->fetchRow();
        return ( $row[0] > 0 ) ? true : false;
    }
    
    /**
     * given an id return the relevant contact details
     *
     * @param int $id           contact id
     *
     * @return the contact object
     * @static
     * @access public
     */
    static function contactDetails( $id ) {
        if ( ! $id ) {
            return null;
        }
        
        $select = "
SELECT DISTINCT
  civicrm_contact.id                  as contact_id               ,
  civicrm_contact.home_URL            as home_URL                 ,
  civicrm_contact.image_URL           as image_URL                ,
  civicrm_contact.legal_identifier    as legal_identifier         ,
  civicrm_contact.external_identifier as external_identifier      ,
  civicrm_contact.nick_name           as nick_name                ,
  civicrm_individual.id               as individual_id            ,
  civicrm_location.id                 as location_id              ,
  civicrm_address.id                  as address_id               ,
  civicrm_email.id                    as email_id                 ,
  civicrm_phone.id                    as phone_id                 ,
  civicrm_individual.first_name       as first_name               ,
  civicrm_individual.middle_name      as middle_name              ,
  civicrm_individual.last_name        as last_name                ,
  civicrm_individual.prefix           as prefix                   ,
  civicrm_individual.suffix           as suffix                   ,
  civicrm_address.street_address      as street_address           ,
  civicrm_address.supplemental_address_1 as supplemental_address_1,
  civicrm_address.supplemental_address_2 as supplemental_address_2,
  civicrm_address.city                as city                     ,
  civicrm_address.postal_code         as postal_code              ,
  civicrm_address.postal_code_suffix  as postal_code_suffix       ,
  civicrm_state_province.name         as state                    ,
  civicrm_country.name                as country                  ,
  civicrm_email.email                 as email                    ,
  civicrm_phone.phone                 as phone                    ,
  civicrm_im.name                     as im                       ";

        $tables = array( 'civicrm_individual'     => 1,
                         'civicrm_location'       => 1,
                         'civicrm_address'        => 1,
                         'civicrm_email'          => 1,
                         'civicrm_phone'          => 1,
                         'civicrm_im'             => 1,
                         'civicrm_state_province' => 1,
                         'civicrm_country'        => 1,
                         );

        $from = CRM_Contact_BAO_Query::fromClause( $tables );
        $where = " WHERE civicrm_contact.id = " . CRM_Utils_Type::escape($id, 'Integer');
        $query = "$select $from $where";

        $dao =& new CRM_Core_DAO( );
        $dao->query($query);
        if ( $dao->fetch( ) ) {
            return $dao;
        }
        return null;
    }

    /**
     * Find contacts which match the criteria
     *
     * @param string $matchClause the matching clause
     * @param  array $tables (reference ) add the tables that are needed for the select clause
     * @param int    $id          the current contact id (hence excluded from matching)
     *
     * @return string                contact ids if match found, else null
     * @static
     * @access public
     */
    static function matchContact( $matchClause, &$tables, $id = null ) {
        $config =& CRM_Core_Config::singleton( );
        $query  = "SELECT DISTINCT civicrm_contact.id as id";
        $query .= CRM_Contact_BAO_Query::fromClause( $tables );
        $query .= " WHERE $matchClause ";
        if ( $id ) {
            $query .= " AND civicrm_contact.id != " . CRM_Utils_Type::escape($id, 'Integer') ;
        }

        $dao =& new CRM_Core_DAO( );
        $dao->query($query);
        $ids = array( );
        while ( $dao->fetch( ) ) {
            $ids[] = $dao->id;
        }
        return implode( ',', $ids );
    }

    /**
     * Get all the emails for a specified contact_id, with the primary email being first
     *
     * @param int $id the contact id
     *
     * @return array  the array of email id's
     * @access public
     * @static
     */
    static function allEmails( $id ) {
        if ( ! $id ) {
            return null;
        }

        $query = "
SELECT email, civicrm_location_type.name as locationType, civicrm_email.is_primary as is_primary
FROM    civicrm_contact
LEFT JOIN civicrm_location ON ( civicrm_location.entity_table = 'civicrm_contact' AND
                                civicrm_contact.id = civicrm_location.entity_id )
LEFT JOIN civicrm_location_type ON ( civicrm_location.location_type_id = civicrm_location_type.id )
LEFT JOIN civicrm_email ON ( civicrm_location.id = civicrm_email.location_id )
WHERE
  civicrm_contact.id = " . CRM_Utils_Type::escape($id, 'Integer') . "
ORDER BY
  civicrm_location.is_primary DESC, civicrm_email.is_primary DESC";
        
        $dao =& new CRM_Core_DAO( );
        $dao->query($query);
        $emails = array( );
        while ( $dao->fetch( ) ) {
            $emails[$dao->email] = array( 'locationType' => $dao->locationType,
                                          'is_primary'      => $dao->is_primary );
        }
        return $emails;
    }

    /**
     * create and query the db for an contact search
     *
     * @param array    $formValues array of reference of the form values submitted
     * @param int      $action   the type of action links
     * @param int      $offset   the offset for the query
     * @param int      $rowCount the number of rows to return
     * @param boolean  $count    is this a count only query ?
     * @param boolean  $includeContactIds should we include contact ids?
     * @param boolean  $sortByChar if true returns the distinct array of first characters for search results
     * @param boolean  $groupContacts if true, use a single mysql group_concat statement to get the contact ids
     *
     * @return CRM_Contact_DAO_Contact 
     * @access public
     */
    function searchQuery(&$fv, $offset, $rowCount, $sort, 
                         $count = false, $includeContactIds = false, $sortByChar = false,
                         $groupContacts = false, $returnQuery = false )
    {
//         my_print_r($fv, 'FormVal');
//         my_print_r($offset, 'Offset');
//         my_print_r($rowCount, 'RowCount');
//         my_print_r($sort, 'Sort');
//         my_print_r($count, 'Count');
//         my_print_r($includeContactIds, 'IncludeContactId');
//         my_print_r($sortByChar, 'SortByChar');
//         my_print_r($groupContacts, 'GroupContacts');
        
        $config =& CRM_Core_Config::singleton( );

        $select = $from = $where = $order = $limit = '';

        $tables = array( );
        if( $count ) {
            $select = "SELECT count(DISTINCT civicrm_contact.id) ";
        } else if ( $sortByChar ) {
            $select = "SELECT DISTINCT UPPER(LEFT(civicrm_contact.sort_name, 1)) as sort_name";
        } else if ( $groupContacts ) {
            $select  = "SELECT DISTINCT civicrm_contact.id as id";
        } else {
            $select = self::selectClause( $tables );

            if ( CRM_Utils_Array::value( 'cb_group', $fv ) ) {
                $select .= ', civicrm_group_contact.status as status';
            }
        }
        $where      = self::whereClause( $fv, $includeContactIds, $tables );
        $permission = CRM_Core_Permission::whereClause( CRM_Core_Permission::VIEW, $tables );
        
        if ( empty( $where ) ) {
            $where = " WHERE $permission ";
        } else {
            $where = " WHERE $where AND $permission ";
        }

        $from = CRM_Contact_BAO_Query::fromClause( $tables );
        if (!$count) {
            if ($sort) {
                $order = " ORDER BY " . $sort->orderBy(); 
            } else if ($sortByChar) { 
                $order = " ORDER BY LEFT(civicrm_contact.sort_name, 1) ";
            }
            if ( $rowCount > 0 ) {
                $limit = " LIMIT $offset, $rowCount ";
            }
        }

        // building the query string
        $query = $select . $from . $where . $order . $limit;
        //echo "<pre>$query</pre>";
        if ($returnQuery) {
            return $query;
        }
        
        if ( $count ) {
            return CRM_Core_DAO::singleValueQuery( $query );
        }
        

        $dao =& CRM_Core_DAO::executeQuery( $query );
        if ( $groupContacts ) {
            $ids = array( );
            while ( $dao->fetch( ) ) {
                $ids[] = $crmDAO->id;
            }
            return implode( ',', $ids );
        }
        
        return $dao;
    }

    /**
     * create the default select clause
     *
     * @param  array $tables (reference ) add the tables that are needed for the select clause
     *
     * @return string the select clause
     * @access public
     * @static
     */
    static function selectClause( &$tables ) {
        $tables['civicrm_location']       = 1;
        $tables['civicrm_address']        = 1;
        $tables['civicrm_phone']          = 1;
        $tables['civicrm_email']          = 1;
        $tables['civicrm_state_province'] = 1;
        $tables['civicrm_country']        = 1;

        return "
SELECT DISTINCT civicrm_contact.id as contact_id,
  civicrm_contact.sort_name as sort_name,
  civicrm_contact.display_name as display_name,
  civicrm_address.street_address as street_address,
  civicrm_address.city as city,
  civicrm_address.postal_code as postal_code,
  civicrm_address.postal_code_suffix as postal_code_suffix,
  civicrm_address.geo_code_1 as latitude,
  civicrm_address.geo_code_2 as longitude,
  civicrm_state_province.abbreviation as state,
  civicrm_country.name as country,
  civicrm_email.email as email,
  civicrm_phone.phone as phone,
  civicrm_contact.contact_type as contact_type
";
    }
    
    /**
     * create the where clause for a contact search
     *
     * @param array    $formValues array of reference of the form values submitted
     * @param boolean  $includeContactIds should we include contact ids?
     * @param  array $tables (reference ) add the tables that are needed for the select clause
     *
     * @return string  the where clause without the permissions hook (important)
     * @access public
     * @static
     */
    static function whereClause( &$fv, $includeContactIds = false, &$tables)
    {
        $where = '';

        /*
         * sample formValues for query 
         *
         * Get me all contacts of type individual or organization who are members of group 1 "Newsletter Subscribers"
         * and are categorized as "Non Profit" (catid 1) or "Volunteer" (catid 5) 

        $fv = Array
            (
             [cb_contact_type] => Array
             (
              [Individual] => 1
              [Organization] => 1
              )
             
             [cb_group] => Array
             (
              [1] => 1
              )
             
             [cb_group_contact_status] => Array
             (
              [Added] => 1
              [Removed] => 0
              [Pending] => 0
              )
             
             [cb_tag] => Array
             (
              [1] => 1
              [5] => 1
              )
             
             [last_name] => 
             [first_name] => 
             [street_name] => 
             [city] => 
             [state_province] => 
             [country] => 
             [postal_code] => 
             [postal_code_low] => 
             [postal_code_high] => 
             )

        */


        // stores all the "AND" clauses
        $andArray = array();

        $config =& CRM_Core_Config::singleton( );
        $andArray['domain'] = 'civicrm_contact.domain_id = ' . $config->domainID( ) . ' ';

        // check for contact type restriction
        if ( CRM_Utils_Array::value( 'cb_contact_type', $fv ) ) {
            $andArray['contact_type'] = "(contact_type IN (";
            foreach ($fv['cb_contact_type']  as $k => $v) {
                $andArray['contact_type'] .= "'" . CRM_Utils_Type::escape($k, 'String') ."',"; 
            }            
            // replace the last comma with the parentheses.
            $andArray['contact_type'] = rtrim($andArray['contact_type'], ",");
            $andArray['contact_type'] .= "))";
        }
        
        // check for group restriction
        if ( CRM_Utils_Array::value( 'cb_group', $fv ) ) {
            $andArray['group'] = "(civicrm_group_contact.group_id IN (" . implode( ',', array_keys($fv['cb_group']) ) . ')';
            
            $statii = array();
            $in = false;
            if (CRM_Utils_Array::value( 'cb_group_contact_status', $fv)
                && is_array($fv['cb_group_contact_status'])) {
                foreach ($fv['cb_group_contact_status'] as $k => $v) {
                    if ($v) {
                        if ($k == 'Added') {
                            $in = true;
                        }
                        $statii[] = "'" . CRM_Utils_Type::escape($k, 'String') . "'";
                    }
                }
            } else {
                $statii[] = '"Added"';
                $in = true;
            }
            $andArray['group'] .= ' AND civicrm_group_contact.status IN (' 
                                .  implode(', ', $statii) .'))';
            
            $tables['civicrm_group_contact'] = 1;
            
            if ($in) {
                $ssWhere = array();
                $group =& new CRM_Contact_BAO_Group();
                foreach (array_keys($fv['cb_group']) as $group_id) {
                    $group->id = $group_id;
                    $group->find(true);
                    if (isset($group->saved_search_id)) {
                        if ( $config->mysqlVersion >= 4.1 ) {
                            $sfv =& CRM_Contact_BAO_SavedSearch::getFormValues(
                                $group->saved_search_id);
                                
                            $smarts =& self::searchQuery($sfv, 0, 0, null, 
                                        false, false, false, true, true);
                            $ssWhere[] = "
                            (civicrm_contact.id IN ($smarts) 
                            AND civicrm_contact.id NOT IN (
                            SELECT contact_id FROM civicrm_group_contact
                            WHERE civicrm_group_contact.group_id = " 
                            . CRM_Utils_Type::escape($group_id, 'Integer') ."
                            AND civicrm_group_contact.status = 'Removed'
                            ))";
                        } else {
                            $ssw = CRM_Contact_BAO_SavedSearch::whereClause(
                                $group->saved_search_id, $tables);
                            /* FIXME: bug with multiple group searches */
                            $ssWhere[] = "($ssw AND (civicrm_group_contact.id is null OR (civicrm_group_contact.group_id = $group_id AND civicrm_group_contact.status = 'Added')))";
                        }
                    }
                    $group->reset();
                    $group->selectAdd('*');
                }
                if (count($ssWhere)) {
                    $tables['civicrm_group_contact'] = 
                        "civicrm_contact.id = civicrm_group_contact.contact_id AND civicrm_group_contact.group_id IN (" . implode(',', array_keys($fv['cb_group'])) . ')';
                    $andArray['group']  = "(({$andArray['group']}) OR ("
                                        . implode(' OR ', $ssWhere) 
                                        .'))';
                }
            }
        }
        
        // check for tag restriction
        if ( CRM_Utils_Array::value( 'cb_tag', $fv ) ) {
            $andArray['tag'] .= "(tag_id IN (" . implode( ',', array_keys($fv['cb_tag']) ) . '))';

            $tables['civicrm_entity_tag'] = 1;
        }
        
        if ( CRM_Utils_Array::value( 'sort_name', $fv ) ) {
            $name = trim($fv['sort_name']);
            $sub  = array( );
            // if we have a comma in the string, search for the entire string
            if ( strpos( $name, ',' ) !== false ) {
                $sub[] = " ( LOWER(civicrm_contact.sort_name) LIKE '%" . strtolower(addslashes($name)) . "%' )";
                $sub[] = " ( LOWER(civicrm_email.email)       LIKE '%" . strtolower(addslashes($name)) . "%' )";
                $tables['civicrm_location'] = 1;
                $tables['civicrm_email']    = 1;
            } else {
                // split the string into pieces
                $pieces =  explode( ' ', $name );
                foreach ( $pieces as $piece ) {
                    $sub[] = " ( LOWER(civicrm_contact.sort_name) LIKE '%" . strtolower(addslashes(trim($piece))) . "%' ) ";
                    $sub[] = " ( LOWER(civicrm_email.email)       LIKE '%" . strtolower(addslashes(trim($piece))) . "%' )";
                }
                $tables['civicrm_location'] = 1;
                $tables['civicrm_email']    = 1;
            }
            $andArray['sort_name'] = ' ( ' . implode( '  OR ', $sub ) . ' ) ';
        }

        // sortByCharacter
        if ( CRM_Utils_Array::value( 'sortByCharacter', $fv ) ) {
            $name = trim($fv['sortByCharacter']);

            $cond = " LOWER(civicrm_contact.sort_name) LIKE '" . strtolower(addslashes($name)) . "%'";
            if ( CRM_Utils_Array::value( 'sort_name', $andArray ) ) {
                $andArray['sort_name'] = '(' . $andArray['sort_name'] . "AND ( $cond ))";
            } else {
                $andArray['sort_name'] = "( $cond )";
            }
        }

        if ( $includeContactIds ) {
            $contactIds = array( );
            foreach ( $fv as $name => $value ) {
                if ( substr( $name, 0, CRM_Core_Form::CB_PREFIX_LEN ) == CRM_Core_Form::CB_PREFIX ) {
                    $contactIds[] = substr( $name, CRM_Core_Form::CB_PREFIX_LEN );
                }
            }
            if ( ! empty( $contactIds ) ) {
                $andArray['cid'] = " ( civicrm_contact.id in (" . implode( ',', $contactIds ) . " ) ) ";
            }
        }
        
        $fields = array( 'street_name'=> 1, 'city' => 1, 'state_province' => 2, 'country' => 3 );
        foreach ( $fields as $field => $value ) {
            if ( CRM_Utils_Array::value( $field, $fv ) ) {
                $tables['civicrm_location'] = 1;
                $tables['civicrm_address']  = 1;

                if ( $value == 1 ) {
                    $andArray[$field] = " ( LOWER(civicrm_address." . $field .  ") LIKE '%" . strtolower( addslashes( $fv[$field] ) ) . "%' )";
                } else { 
                    $andArray[$field] = ' ( civicrm_address.' . $field .  '_id = ' . $fv[$field] . ') ';
                    if ( $value == 2 ) {
                        $tables['civicrm_state_province'] = 1;
                    } else {
                        $tables['civicrm_country'] = 1;
                    }
                }
            }
        }

        // postal code processing
        if ( CRM_Utils_Array::value( 'postal_code'     , $fv ) ||
             CRM_Utils_Array::value( 'postal_code_low' , $fv ) ||
             CRM_Utils_Array::value( 'postal_code_high', $fv ) ) {
            $tables['civicrm_location'] = 1;
            $tables['civicrm_address']   = 1;

            // we need to do postal code processing
            $pcORArray   = array();
            $pcANDArray  = array();

            if ($fv['postal_code']) {
                $pcORArray[] = ' ( civicrm_address.postal_code = ' . $fv['postal_code'] . ' ) ';
            }
            if ($fv['postal_code_low']) {
                $pcANDArray[] = ' ( civicrm_address.postal_code >= ' . $fv['postal_code_low'] . ' ) ';
            }
            if ($fv['postal_code_high']) {
                $pcANDArray[] = ' ( civicrm_address.postal_code <= ' . $fv['postal_code_high'] . ' ) ';
            }            

            if ( ! empty( $pcANDArray ) ) {
                $pcORArray[] = ' ( ' . implode( ' AND ', $pcANDArray ) . ' ) ';
            }

            $andArray['postal_code'] = ' ( ' . implode( ' OR ', $pcORArray ) . ' ) ';
        }

        if ( CRM_Utils_Array::value( 'cb_location_type', $fv ) ) {
            
            // processing for location type - check if any locations checked
            $andArray['location_type'] = "(civicrm_location.location_type_id IN (" . implode( ',', array_keys($fv['cb_location_type']) ) . '))';
        }
        
        // processing for primary location
        if ( CRM_Utils_Array::value( 'cb_primary_location', $fv ) ) {
            $andArray['cb_primary_location'] = ' ( civicrm_location.is_primary = 1 ) ';
            
            $tables['civicrm_location'] = 1;
        }


        // processing activity type, from and to date
        // check for activity type
        if ( CRM_Utils_Array::value( 'activity_type', $fv ) ) {
            $name = trim($fv['activity_type']);
            // split the string into pieces
            $pieces =  explode( ' ', $name );
            $first = true;
            $cond  = ' ( ';
            foreach ( $pieces as $piece ) {
                if ( ! $first ) {
                    $cond .= ' OR';
                } else {
                    $first = false;
                }
                $cond .= " LOWER(civicrm_activity_history.activity_type) LIKE '%" . strtolower(addslashes(trim($piece))) . "%'";
            }
            $cond .= ' ) ';
            $andArray['activity_type'] = "( $cond )";

            $tables['civicrm_activity_history'] = 1;
        }

        // from date

        if ( isset($fv['activity_from_date']) &&
             ( $activityFromDate = CRM_Utils_Date::format(array_reverse(CRM_Utils_Array::value('activity_from_date', $fv))))) {
            $andArray['activity_from_date'] = " ( civicrm_activity_history.activity_date >= '$activityFromDate' ) ";
            $tables['civicrm_activity_history'] = 1;
        }
        if (isset($fv['activity_to_date']) &&
            ($activityToDate = (CRM_Utils_Date::format(array_reverse(CRM_Utils_Array::value('activity_to_date', $fv)))))) {            
            $andArray['activity_to_date'] = " ( civicrm_activity_history.activity_date <= '$activityToDate' ) ";
            $tables['civicrm_activity_history'] = 1;
        }
        
        //Start Custom data Processing 

        /*

        The query below works fine (using self joins)

SELECT t1.entity_id

FROM civicrm_custom_value t1,
     civicrm_custom_value t2,
     civicrm_custom_value t3
 
WHERE t1.custom_field_id = 1
  AND t2.custom_field_id = 2
  AND t3.custom_field_id = 5
 
  AND t1.int_data = 1
  AND t2.char_data LIKE '%Congress%'
  AND t3.char_data LIKE '%PhD%'
 
  AND t1.entity_id = t2.entity_id
  AND t2.entity_id = t3.entity_id;

        */

//         CRM_Core_Error::debug_var('fv', $fv);
        $params = array();

        if ( is_array( $fv ) && ! empty( $fv ) ) {
            foreach ($fv as $k => $v) {
                if ( substr( $k, 0, 10 ) != 'customData' ) {
                    continue;
                }
                
                list($str, $groupId, $fieldId, $elementName) = explode('_', $k, 4);
                if ($v != '') {
                    $params[$fieldId] = $v;
                }
            }

            if ( ! empty( $params ) ) {
                $sql = CRM_Core_BAO_CustomValue::whereClause($params); 
                if ( $sql ) {
                    $tables['civicrm_custom_value'] = 1;
                    $andArray['custom_value'] = $sql;
                }
            }
        }


        // final AND ing of the entire query.
        if (!empty($andArray)) {
            $where = ' ( ' . implode( ' AND ', $andArray ) . ' ) ';
        }

        return $where;
    }

    /**
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Contact_BAO_Contact object
     * @access public
     * @static
     */
    static function add(&$params, &$ids)
    {
        $contact =& new CRM_Contact_BAO_Contact();
        
        $contact->copyValues($params);
        
        $contact->domain_id = CRM_Utils_Array::value( 'domain' , $ids, CRM_Core_Config::domainID( ) );
        $contact->id        = CRM_Utils_Array::value( 'contact', $ids );
        
        if ($contact->contact_type == 'Individual') {
            $sortName = "";
            $firstName  = CRM_Utils_Array::value('first_name', $params, '');
            $middleName = CRM_Utils_Array::value('middle_name', $params, '');
            $lastName   = CRM_Utils_Array::value('last_name' , $params, '');
            $prefix     = CRM_Utils_Array::value('prefix'    , $params, '');
            $suffix     = CRM_Utils_Array::value('suffix'    , $params, '');
            
            // a comma should only be present if both first_name and last name are present.
            if ($firstName && $lastName) {
                $sortName = "$lastName, $firstName";
            } else {
                if (empty($firstName) || empty($lastName)) {
                    $sortName = $lastName . $firstName;
                } else {
                    $individual =& new CRM_Contact_BAO_Individual();
                    $individual->contact_id = $contact->id;
                    $individual->find();
                    while($individual->fetch()) {
                        $individualLastName = $individual->last_name;
                        $individualFirstName = $individual->first_name;
                        $individualPrefix = $individual->prefix;
                        $individualSuffix = $individual->suffix;
                        $individualMiddleName = $individual->middle_name;
                    }
                    
                    if (empty($lastName) && !empty($individualLastName)) {
                        $lastName = $individualLastName;
                    } 
                    
                    if (empty($firstName) && !empty($individualFirstName)) {
                        $firstName = $individualFirstName;
                    }
                                                            
                    if (empty($prefix) && !empty($individualPrefix)) {
                        $prefix = $individualPrefix;
                    }
                    
                    if (empty($middleName) && !empty($individualMiddleName)) {
                        $middleName = $individualMiddleName;
                    }
                    
                    if (empty($suffix) && !empty($individualSuffix)) {
                        $suffix = $individualSuffix;
                    }
                    
                    $sortName = "$lastName, $firstName";
                }
            }
            $contact->sort_name    = trim($sortName);
            $contact->display_name =
                trim( $prefix . ' ' . $firstName . ' ' . $middleName . ' ' . $lastName . ' ' . $suffix );
            $contact->display_name = str_replace( '  ', ' ', $contact->display_name );

            if ( CRM_Utils_Array::value( 'location', $params ) ) {
                foreach ($params['location'] as $locBlock) {
                    if (! $locBlock['is_primary']) {
                        continue;
                    }
                    $email = $locBlock['email'][1]['email'];
                    break;
                }
            }

            if (empty($contact->display_name)) {
                if (isset($email)) {
                    $contact->display_name = $email;
                }
            }
            if (empty($contact->sort_name)) {
                if (isset($email)) {
                    $contact->sort_name = $email;
                }
            }
        } else if ($contact->contact_type == 'Household') {
            $contact->display_name = $contact->sort_name = CRM_Utils_Array::value('household_name', $params, '');
        } else {
            $contact->display_name = $contact->sort_name = CRM_Utils_Array::value('organization_name', $params, '') ;
        }

        // preferred communication block
        $privacy = CRM_Utils_Array::value('privacy', $params);
        if ($privacy && is_array($privacy)) {
            foreach (self::$_commPrefs as $name) {
                $contact->$name = CRM_Utils_Array::value($name, $privacy, false);
            }
        }
	 
        return $contact->save();
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params input parameters to find object
     * @param array $values output values of the object
     * @param array $ids    the array that holds all the db ids
     *
     * @return CRM_Contact_BAO_Contact|null the found object or null
     * @access public
     * @static
     */
    static function &getValues( &$params, &$values, &$ids ) {

        $contact =& new CRM_Contact_BAO_Contact( );

        $contact->copyValues( $params );

        if ( $contact->find(true) ) {
            $ids['contact'] = $contact->id;
            $ids['domain' ] = $contact->domain_id;

            CRM_Core_DAO::storeValues( $contact, $values );

            $privacy = array( );
            foreach ( self::$_commPrefs as $name ) {
                if ( isset( $contact->$name ) ) {
                    $privacy[$name] = $contact->$name;
                }
            }
            if ( !empty($privacy) ) {
                $values['privacy'] = $privacy;
            }

            CRM_Contact_DAO_Contact::addDisplayEnums($values);

            return $contact;
        }
        return null;
    }

    /**
     * takes an associative array and creates a contact object and all the associated
     * derived objects (i.e. individual, location, email, phone etc)
     *
     * This function is invoked from within the web form layer and also from the api layer
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     * @param int   $maxLocationBlocks the maximum number of location blocks to process
     *
     * @return object CRM_Contact_BAO_Contact object 
     * @access public
     * @static
     */
    static function create(&$params, &$ids, $maxLocationBlocks)
    {
        CRM_Core_DAO::transaction('BEGIN');
        
        $contact = self::add($params, $ids);

        $params['contact_id'] = $contact->id;

        // invoke the add operator on the contact_type class
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_BAO_" . $params['contact_type']) . ".php");
        eval('$contact->contact_type_object =& CRM_Contact_BAO_' . $params['contact_type'] . '::add($params, $ids);');

        $location = array();
        for ($locationId = 1; $locationId <= $maxLocationBlocks; $locationId++) { // start of for loop for location
            $location[$locationId] = CRM_Core_BAO_Location::add($params, $ids, $locationId);
        }
        $contact->location = $location;
	
        // add notes
        if ( CRM_Utils_Array::value( 'note', $params ) ) {
            if (is_array($params['note'])) {
                foreach ($params['note'] as $note) {  
                    $noteParams = array(
                                        'entity_id'     => $contact->id,
                                        'entity_table'  => 'civicrm_contact',
                                        'note'          => $note['note']
                                        );
                    CRM_Core_BAO_Note::add($noteParams);
                }
            } else {
                    $noteParams = array(
                                        'entity_id'     => $contact->id,
                                        'entity_table'  => 'civicrm_contact',
                                        'note'          => $params['note']
                                        );
                    CRM_Core_BAO_Note::add($noteParams);
            }
        }
        // update the UF email if that has changed
        CRM_Core_BAO_UFMatch::updateUFEmail( $contact->id );


        // add custom field values
        if ( CRM_Utils_Array::value( 'custom', $params ) ) {
            foreach ($params['custom'] as $customValue) {
                $cvParams = array(
                    'entity_table' => 'civicrm_contact',
                    'entity_id' => $contact->id,
                    'value' => $customValue['value'],
                    'type' => $customValue['type'],
                    'custom_field_id' => $customValue['custom_field_id'],
                );
                
                CRM_Core_BAO_CustomValue::create($cvParams);
            }
        }
        
        $subscriptionParams = array('contact_id' => $contact->id,
                                    'status' => 'Added',
                                    'method' => 'Admin');
        CRM_Contact_BAO_SubscriptionHistory::create($subscriptionParams);

        CRM_Core_DAO::transaction('COMMIT');
        
        $contact->contact_type_display = CRM_Contact_DAO_Contact::tsEnum('contact_type', $contact->contact_type);

        return $contact;
    }

    /**
     * Get the display name and image of a contact
     *
     * @param int $id the contactId
     *
     * @return array the displayName and contactImage for this contact
     * @access public
     * @static
     */
    static function getDisplayAndImage( $id ) {
        $sql = "
SELECT    civicrm_contact.display_name as display_name,
          civicrm_contact.contact_type as contact_type,
          civicrm_email.email          as email       
FROM      civicrm_contact
LEFT JOIN civicrm_location ON (civicrm_location.entity_table = 'civicrm_contact' AND
                               civicrm_contact.id = civicrm_location.entity_id AND
                               civicrm_location.is_primary = 1)
LEFT JOIN civicrm_email ON (civicrm_location.id = civicrm_email.location_id AND civicrm_email.is_primary = 1)
WHERE     civicrm_contact.id = " . CRM_Utils_Type::escape($id, 'Integer');
        $dao =& new CRM_Core_DAO( );
        $dao->query( $sql );
        if ( $dao->fetch( ) ) {
            $config =& CRM_Core_Config::singleton( );
            $image  =  '<img src="' . $config->resourceBase . 'i/contact_';
            switch ( $dao->contact_type ) {
            case 'Individual' :
                $image .= 'ind.gif" alt="' . ts('Individual') . '" />';
                break;
            case 'Household' :
                $image .= 'house.png" alt="' . ts('Household') . '" height="16" width="16" />';
                break;
            case 'Organization' :
                $image .= 'org.gif" alt="' . ts('Organization') . '" height="16" width="18" />';
                break;
            }
            // use email if display_name is empty
            if ( empty( $dao->display_name ) ) {
                $dao->display_name = $dao->email;
            }
            return array( $dao->display_name, $image );
        }
        return null;
    }

    /**
     *
     * Get the values for pseudoconstants for name->value and reverse.
     *
     * @param array   $defaults (reference) the default values, some of which need to be resolved.
     * @param boolean $reverse  true if we want to resolve the values in the reverse direction (value -> name)
     *
     * @return none
     * @access public
     * @static
     */
    static function resolveDefaults( &$defaults, $reverse = false ) {
        // hack for birth_date
        if ( CRM_Utils_Array::value( 'birth_date', $defaults ) ) {
            if (is_array($defaults['birth_date'])) {
                $defaults['birth_date'] = CRM_Utils_Date::format( 
                                            $defaults['birth_date'], '-' 
                                        );
            }
        } 

        if ( array_key_exists( 'location', $defaults ) ) {
            $locations =& $defaults['location'];

            foreach ($locations as $index => $location) {                
                $location =& $locations[$index];
                self::lookupValue( $location, 'location_type', CRM_Core_PseudoConstant::locationType(), $reverse );

                if (array_key_exists( 'address', $location ) ) {
                    if ( ! self::lookupValue( $location['address'], 'state_province',
                                              CRM_Core_PseudoConstant::stateProvince(), $reverse ) &&
                         $reverse ) {
                        self::lookupValue( $location['address'], 'state_province', 
                                           CRM_Core_PseudoConstant::stateProvinceAbbreviation(), $reverse );
                    }
                    
                    if ( ! self::lookupValue( $location['address'], 'country',
                                              CRM_Core_PseudoConstant::country(), $reverse ) &&
                         $reverse ) {
                        self::lookupValue( $location['address'], 'country', 
                                           CRM_Core_PseudoConstant::countryIsoCode(), $reverse );
                    }
                    self::lookupValue( $location['address'], 'county'        , CRM_Core_SelectValues::county()         , $reverse );
                }

                if (array_key_exists('im', $location)) {
                    $ims =& $location['im'];
                    foreach ($ims as $innerIndex => $im) {
                        $im =& $ims[$innerIndex];
                        self::lookupValue( $im, 'provider', CRM_Core_PseudoConstant::IMProvider(), $reverse );
                        unset($im);
                    }
                }
                unset($location);
            }
        }
    }

    /**
     * This function is used to convert associative array names to values
     * and vice-versa.
     *
     * This function is used by both the web form layer and the api. Note that
     * the api needs the name => value conversion, also the view layer typically
     * requires value => name conversion
     */
    static function lookupValue( &$defaults, $property, &$lookup, $reverse ) {
        $id = $property . '_id';

        $src = $reverse ? $property : $id;
        $dst = $reverse ? $id       : $property;

        if ( ! array_key_exists( $src, $defaults ) ) {
            return false;
        }

        $look = $reverse ? array_flip( $lookup ) : $lookup;
        
        if(is_array($look)) {
            if ( ! array_key_exists( $defaults[$src], $look ) ) {
                return false;
            }
        }
        $defaults[$dst] = $look[$defaults[$src]];
        return true;
    }

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the name / value pairs
     *                        in a hierarchical manner
     * @param array $ids      (reference) the array that holds all the db ids
     *
     * @return object CRM_Contact_BAO_Contact object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults, &$ids ) {
        $contact = CRM_Contact_BAO_Contact::getValues( $params, $defaults, $ids );
        unset($params['id']);
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_BAO_" . $contact->contact_type) . ".php");
        eval( '$contact->contact_type_object =& CRM_Contact_BAO_' . $contact->contact_type . '::getValues( $params, $defaults, $ids );' );
        $locParams = $params + array('entity_id' => $params['contact_id'],
                'entity_table' => self::getTableName());
        $contact->location     =& CRM_Core_BAO_Location::getValues( $locParams, $defaults, $ids, 3 );
        $contact->notes        =& CRM_Core_BAO_Note::getValues( $params, $defaults, $ids );
        $contact->relationship =& CRM_Contact_BAO_Relationship::getValues( $params, $defaults, $ids );
        $contact->groupContact =& CRM_Contact_BAO_GroupContact::getValues( $params, $defaults, $ids );

        $activityParam         =  array('entity_id' => $params['contact_id']);
        $contact->activity     =& CRM_Core_BAO_History::getValues($activityParam, $defaults, 'Activity');

        $activityParam            =  array('contact_id' => $params['contact_id']);
        $defaults['openActivity'] = array(
                                          'data'       => self::getOpenActivities( $activityParam, 0, 3 ),
                                          'totalCount' => self::getNumOpenActivity( $params['contact_id'] ),
                                          );
        return $contact;
    }

    /**
     * Given a parameter array from CRM_Contact_BAO_Contact::retrieve() and a
     * key to search for, search recursively for that key's value.
     *
     * @param array $values     The parameter array
     * @param string $key       The key to search for
     * @return mixed            The value of the key, or null.
     * @access public
     * @static
     */
    static function retrieveValue(&$params, $key) {
        if (! is_array($params)) {
            return null;
        } else if ($value = CRM_Utils_Array::value($key, $params)) {
            return $value;
        } else {
            foreach ($params as $subParam) {
                if ($value = self::retrieveValue($subParam, $key)) {
                    return $value;
                }
            }
        }
        return null;
    }

    /**
     * function to get the display name of a contact
     *
     * @param  int    $id id of the contact
     *
     * @return null|string     display name of the contact if found
     * @static
     * @access public
     */
    static function displayName( $id ) {
        return CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'display_name' );
    }

    /**
     * function to get the email and display name of a contact
     *
     * @param  int    $id id of the contact
     *
     * @return array    tuple of display_name and email if found, or (null,null)
     * @static
     * @access public
     */
    static function getEmailDetails( $id ) {
        $sql = " SELECT    civicrm_contact.display_name, civicrm_email.email
                 FROM      civicrm_contact
                 LEFT JOIN civicrm_location ON (civicrm_location.entity_table = 'civicrm_contact' AND
                                                civicrm_contact.id = civicrm_location.entity_id AND
                                                civicrm_location.is_primary = 1)
                 LEFT JOIN civicrm_email ON (civicrm_location.id = civicrm_email.location_id AND civicrm_email.is_primary = 1)
                 WHERE     civicrm_contact.id = " . CRM_Utils_Type::escape($id, 'Integer');
        $dao =& new CRM_Core_DAO( );
        $dao->query( $sql );
        $result = $dao->getDatabaseResult();
        if ( $result ) {
            $row    = $result->fetchRow();
            if ( $row ) {
                return array( $row[0], $row[1] );
            }
        }
        return array( null, null );
    }

    /**
     * function to get the information to map a contact
     *
     * @param  array    $ids   the list of ids for which we want map info
     *
     * @return null|string     display name of the contact if found
     * @static
     * @access public
     */
    static function &getMapInfo( $ids ) {
        $idString = ' ( ' . implode( ',', $ids ) . ' ) ';
        $sql = "
SELECT
  civicrm_contact.id as contact_id,
  civicrm_contact.display_name as display_name,
  civicrm_address.street_address as street_address,
  civicrm_address.city as city,
  civicrm_address.postal_code as postal_code,
  civicrm_address.postal_code_suffix as postal_code_suffix,
  civicrm_address.geo_code_1 as latitude,
  civicrm_address.geo_code_2 as longitude,
  civicrm_state_province.abbreviation as state,
  civicrm_country.name as country,
  civicrm_location_type.name as location_type
FROM      civicrm_contact
LEFT JOIN civicrm_location ON (civicrm_location.entity_table = 'civicrm_contact' AND
                               civicrm_contact.id = civicrm_location.entity_id AND
                               civicrm_location.is_primary = 1)
LEFT JOIN civicrm_address ON civicrm_location.id = civicrm_address.location_id
LEFT JOIN civicrm_state_province ON civicrm_address.state_province_id = civicrm_state_province.id
LEFT JOIN civicrm_country ON civicrm_address.country_id = civicrm_country.id
LEFT JOIN civicrm_location_type ON civicrm_location_type.id = civicrm_location.location_type_id
WHERE     civicrm_contact.id IN $idString AND civicrm_address.geo_code_1 is not null AND civicrm_address.geo_code_2 is not null";

        $dao =& new CRM_Core_DAO( );
        $dao->query( $sql );

        $locations = array( );
        while ( $dao->fetch( ) ) {
            $location = array( );
            $location['displayName'  ] = $dao->display_name;
            $location['lat'          ] = $dao->latitude;
            $location['lng'          ] = $dao->longitude;
            $address = '';
            CRM_Utils_String::append( $address, ', ',
                                      array( $dao->street_address, $dao->city, $dao->state, $dao->postal_code, $dao->country ) );
            $location['address'      ] = $address;
            $location['url'          ] = CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $dao->contact_id );
            $location['location_type'] = $dao->location_type;
            $locations[] = $location;
        }
        return $locations;
    }

    /**
     * Delete a contact and all its associated records
     * 
     * @param  int  $id id of the contact to delete
     *
     * @return boolean true if contact deleted, false otherwise
     * @access public
     * @static
     */
    function deleteContact( $id ) {

        // make sure we have edit permission for this contact
        // before we delete
        if ( ! self::permissionedContact( $id, CRM_Core_Permission::EDIT ) ) {
            return false;
        }
            
        CRM_Core_DAO::transaction( 'BEGIN' );

        // do a top down deletion
        CRM_Mailing_Event_BAO_Subscribe::deleteContact( $id );

        CRM_Contact_BAO_GroupContact::deleteContact( $id );
        CRM_Contact_BAO_SubscriptionHistory::deleteContact($id);
        
        CRM_Contact_BAO_Relationship::deleteContact( $id );

        // cannot use this one since we need to also delete note creator contact_id
        //CRM_Core_DAO::deleteEntityContact( 'CRM_Core_DAO_Note', $id );
        CRM_Core_BAO_Note::deleteContact($id);

        CRM_Core_DAO::deleteEntityContact( 'CRM_Core_DAO_CustomValue', $id );

        CRM_Core_DAO::deleteEntityContact( 'CRM_Core_DAO_ActivityHistory', $id );

        CRM_Core_BAO_UFMatch::deleteContact( $id );
        
        // need to remove them from email, meeting and phonecall
        CRM_Core_BAO_EmailHistory::deleteContact($id);
        CRM_Core_BAO_Meeting::deleteContact($id);
        CRM_Core_BAO_Phonecall::deleteContact($id);

        // location shld be deleted after phonecall, since fields in phonecall are
        // fkeyed into location/phone.
        CRM_Core_BAO_Location::deleteContact( $id );

        // fix household and org primary contact ids
        static $misc = array( 'Household', 'Organization' );
        foreach ( $misc as $name ) {
            require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_DAO_" . $name) . ".php");
            eval( '$object =& new CRM_Contact_DAO_' . $name . '( );' );
            $object->primary_contact_id = $id;
            $object->find( );
            while ( $object->fetch( ) ) {
                // we need to set this to null explicitly
                $object->primary_contact_id = 'null';
                $object->save( );
            }
        }

        // get the contact type
        $contact =& new CRM_Contact_DAO_Contact();
        $contact->id = $id;
        if ($contact->find(true)) {
            require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_BAO_" . $contact->contact_type) . ".php");
            eval( '$object =& new CRM_Contact_BAO_' . $contact->contact_type . '( );' );
            $object->contact_id = $contact->id;
            $object->delete( );
            $contact->delete( );
        }

        //delete the contact id from recently view
        CRM_Utils_Recent::del($id);

        CRM_Core_DAO::transaction( 'COMMIT' );

        return true;
    }


    /**
     * Get contact type for a contact.
     *
     * @param int $id - id of the contact whose contact type is needed
     *
     * @return string contact_type if $id found else null ""
     *
     * @access public
     *
     * @static
     *
     */
    public static function getContactType($id)
    {
        return CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'contact_type' );
    }


    /**
     * combine all the importable fields from the lower levels object
     *
     * The ordering is important, since currently we do not have a weight
     * scheme. Adding weight is super important and should be done in the
     * next week or so, before this can be called complete.
     * @param int $contactType contact Type
     *
     * @return array array of importable Fields
     * @access public
     */
    function &importableFields( $contactType = 'Individual' ) {
        if ( ! self::$_importableFields ) {
            self::$_importableFields = array();
            
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   array('' => array( 'title' => ts('-do not import-'))) );
            
             require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_DAO_" . $contactType) . ".php");
            
            eval('self::$_importableFields = array_merge(self::$_importableFields, CRM_Contact_DAO_'.$contactType.'::import( ));');

            $locationFields = array_merge(  CRM_Core_DAO_Address::import( ),
                                            CRM_Core_DAO_Phone::import( ),
                                            CRM_Core_DAO_Email::import( ),
                                            CRM_Core_DAO_IM::import( true ));
            foreach ($locationFields as $key => $field) {
                $locationFields[$key]['hasLocationType'] = true;
            }

            self::$_importableFields = array_merge(self::$_importableFields, $locationFields);

            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Contact_DAO_Contact::import( ) );
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Core_DAO_Note::import());
            self::$_importableFields = array_merge(self::$_importableFields,
                                                   CRM_Core_BAO_CustomField::getFieldsForImport($contactType) );
        }
        return self::$_importableFields;
    }

    /**
     * Get total number of open activities
     *
     * @param  int $id id of the contact
     * @return int $numRow - total number of open activities    
     *
     * @static
     * @access public
     */
    static function getNumOpenActivity($id)
    {

        // this is not sufficient way to do.

        $query = "SELECT count(*) FROM civicrm_meeting 
                  WHERE (civicrm_meeting.target_entity_table = 'civicrm_contact' 
                  AND target_entity_id = " . CRM_Utils_Type::escape($id, 'Integer') ."
                  OR source_contact_id = " . CRM_Utils_Type::escape($id, 'Integer') .") 
                  AND status != 'Completed'";
        $rowMeeting = CRM_Core_DAO::singleValueQuery( $query );
        
        $query = "SELECT count(*) FROM civicrm_phonecall 
                  WHERE (civicrm_phonecall.target_entity_table = 'civicrm_contact' 
                  AND target_entity_id = " . CRM_Utils_Type::escape($id, 'Integer') ." 
                  OR source_contact_id = " . CRM_Utils_Type::escape($id, 'Integer') .") 
                  AND status != 'Completed'";
        $rowPhonecall = CRM_Core_DAO::singleValueQuery( $query ); 
        
        $query = "SELECT count(*) FROM civicrm_activity,civicrm_activity_type 
                  WHERE (civicrm_activity.target_entity_table = 'civicrm_contact' 
                  AND target_entity_id = " . CRM_Utils_Type::escape($id, 'Integer') ." 
                  OR source_contact_id = " . CRM_Utils_Type::escape($id, 'Integer') .") 
                  AND civicrm_activity_type.id = civicrm_activity.activity_type_id 
                  AND civicrm_activity_type.is_active = 1  AND status != 'Completed'";
        $rowActivity = CRM_Core_DAO::singleValueQuery( $query ); 

        return  $rowMeeting + $rowPhonecall + $rowActivity;
    }

    /**
     * function to get the list of open Actvities
     *
     * @param array reference $params  array of parameters 
     * @param int     $offset          which row to start from ?
     * @param int     $rowCount        how many rows to fetch
     * @param object|array  $sort      object or array describing sort order for sql query.
     * @param type    $type            type of history we're interested in
     *
     * @return array (reference)      $values the relevant data object values of open activitie
     *
     * @access public
     * @static
     */
    static function &getOpenActivities(&$params, $offset=null, $rowCount=null, $sort=null, $type='Activity')
    {
        $dao =& new CRM_Core_DAO();
        $contactId = $params['contact_id'];
        
        $query = "
( SELECT
    civicrm_phonecall.id as id,
    civicrm_phonecall.source_contact_id as source_contact_id, 
    civicrm_phonecall.target_entity_id as  target_contact_id,
    civicrm_phonecall.subject as subject,
    civicrm_phonecall.scheduled_date_time as date,
    civicrm_phonecall.status as status,
    source.display_name as sourceName,
    target.display_name as targetName,
    civicrm_activity_type.id  as activity_type_id,
    civicrm_activity_type.name  as activity_type
  FROM civicrm_activity_type, civicrm_phonecall, civicrm_contact source, civicrm_contact target
  WHERE
    civicrm_activity_type.id = 2 AND
    civicrm_phonecall.source_contact_id = source.id AND
    civicrm_phonecall.target_entity_table = 'civicrm_contact' AND
    civicrm_phonecall.target_entity_id = target.id AND
    ( civicrm_phonecall.source_contact_id = " . CRM_Utils_Type::escape($contactId, 'Integer') ." 
    OR civicrm_phonecall.target_entity_id = " . CRM_Utils_Type::escape($contactId, 'Integer') ." ) 
    AND civicrm_phonecall.status != 'Completed'
 ) UNION
( SELECT   
    civicrm_meeting.id as id,
    civicrm_meeting.source_contact_id as source_contact_id,
    civicrm_meeting.target_entity_id as  target_contact_id,
    civicrm_meeting.subject as subject,
    civicrm_meeting.scheduled_date_time as date,
    civicrm_meeting.status as status,
    source.display_name as sourceName,
    target.display_name as targetName,
    civicrm_activity_type.id  as activity_type_id,
    civicrm_activity_type.name  as activity_type
  FROM civicrm_activity_type, civicrm_meeting, civicrm_contact source, civicrm_contact target
  WHERE
    civicrm_activity_type.id = 1 AND
    civicrm_meeting.source_contact_id = source.id AND
    civicrm_meeting.target_entity_table = 'civicrm_contact' AND
    civicrm_meeting.target_entity_id = target.id AND
    ( civicrm_meeting.source_contact_id = " . CRM_Utils_Type::escape($contactId, 'Integer') ."
    OR civicrm_meeting.target_entity_id = " . CRM_Utils_Type::escape($contactId, 'Integer') ." ) 
    AND civicrm_meeting.status != 'Completed'
) UNION
( SELECT   
    civicrm_activity.id as id,
    civicrm_activity.source_contact_id as source_contact_id,
    civicrm_activity.target_entity_id as  target_contact_id,
    civicrm_activity.subject as subject,
    civicrm_activity.scheduled_date_time as date,
    civicrm_activity.status as status,
    source.display_name as sourceName,
    target.display_name as targetName,
    civicrm_activity_type.id  as activity_type_id,
    civicrm_activity_type.name  as activity_type
  FROM civicrm_activity, civicrm_contact source, civicrm_contact target ,civicrm_activity_type
  WHERE
    civicrm_activity.source_contact_id = source.id AND
    civicrm_activity.target_entity_table = 'civicrm_contact' AND
    civicrm_activity.target_entity_id = target.id AND
    ( civicrm_activity.source_contact_id = " . CRM_Utils_Type::escape($contactId, 'Integer') ." 
    OR civicrm_activity.target_entity_id = " . CRM_Utils_Type::escape($contactId, 'Integer') ." ) AND
    civicrm_activity_type.id = civicrm_activity.activity_type_id AND civicrm_activity_type.is_active = 1 AND 
    civicrm_activity.status != 'Completed'
)
";
        if ($sort) {
            $order = " ORDER BY " . $sort->orderBy(); 
        } else {
            $order = " ORDER BY date desc ";
        }
        
        if ( $rowCount > 0 ) {
            $limit = " LIMIT $offset, $rowCount ";
        }
        

        $queryString = $query . $order . $limit;
        $dao->query( $queryString );
        $values =array();
        $rowCnt = 0;
        while($dao->fetch()) {
            $values[$rowCnt]['activity_type_id'] = $dao->activity_type_id;        
            $values[$rowCnt]['activity_type'] = $dao->activity_type;
            $values[$rowCnt]['id']      = $dao->id;
            $values[$rowCnt]['subject'] = $dao->subject;
            $values[$rowCnt]['date']    = $dao->date;
            $values[$rowCnt]['status']  = $dao->status;
            $values[$rowCnt]['sourceName'] = $dao->sourceName;
            $values[$rowCnt]['targetName'] = $dao->targetName;
            $values[$rowCnt]['sourceID'] = $dao->source_contact_id;
            $values[$rowCnt]['targetID'] = $dao->target_contact_id;
            $rowCnt++;
        }
        foreach ($values as $key => $array) {
            CRM_Core_DAO_Meeting::addDisplayEnums($values[$key]);
            CRM_Core_DAO_Phonecall::addDisplayEnums($values[$key]);
        }
        return $values;
    }
    
    /**
     * Get unique contact id for input parameters.
     * Currently the parameters allowed are
     *
     * 1 - email
     * 2 - phone number
     * 3 - city
     * 4 - domain id
     *
     * @param array $param - array of input parameters
     *
     * @return $contactId|CRM_Error if unique id available
     *
     * @access public
     * @static
     */
    static function _crm_get_contact_id($params)
    {
        if (!isset($params['email']) && !isset($params['phone']) && !isset($params['city'])) {
            return _crm_error( '$params must contain either email, phone or city to obtain contact id' );
        }

        if (!isset($params['domain_id'])) {
            $config =& CRM_Core_Config::singleton( );
            $domain_id = CRM_Core_Config::domainID( );
        } else {
            $domain_id = $params['domain_id'];
        }

        $queryString = $select = $from = $where = '';
        
        $select = 'SELECT civicrm_contact.id';
        $from   = ' FROM civicrm_contact, civicrm_location';
        $andArray = array("civicrm_contact.domain_id = $domain_id");
        
        $andArray[] = "civicrm_location.entity_table = 'civicrm_contact'";
        $andArray[] = "civicrm_contact.id = civicrm_location.entity_id";
        
        if (isset($params['email'])) {// is email present ?
            $from .= ', civicrm_email';
            $andArray[] = "civicrm_location.id = civicrm_email.location_id";
            $andArray[] = "civicrm_email.email = '" . $params['email'] . "'";
        }

        if (isset($params['phone'])) { // is phone present ?
            $from .= ', civicrm_phone';
            $andArray[] = 'civicrm_location.id = civicrm_phone.location_id';
            $andArray[] = "civicrm_phone.phone = '" . $params['phone'] . "'";
        }
        
        if (isset($params['city'])) { // is city present ?
            $from .= ', civicrm_address';
            $andArray[] = 'civicrm_location.id = civicrm_address.location_id';
            $andArray[] = "civicrm_address.city = '" . $params['city'] . "'";
        }

        $where = " WHERE " . implode(" AND ", $andArray);
        
        $queryString = $select . $from . $where;
        //CRM_Core_Error::debug_var('queryString', $queryString);
        
        $dao =& new CRM_Core_DAO();
        
        $dao->query($queryString);
        $count = 0;
        while($dao->fetch()) {
            $count++;
            if ($count > 1) {
                return _crm_error( 'more than one contact id matches $params' );
            }
            
        }
    
        if ($count == 0) {
            return _crm_error( 'No contact found for given $params ' );
        }
        
        return $dao->id;
    }


    /**
     * combine all the exportable fields from the lower levels object
     * 
     * currentlty we are using importable fields as exportable fields
     *
     * @param int $contactType contact Type
     *
     * @return array array of exportable Fields
     * @access public
     */
    function &exportableFields( $contactType = 'Individual' ) {
        
        $exportableFields = array();
        
        $exportableFields = array_merge($exportableFields,
                                               array('' => array( 'title' => ts('-do not export-'))) );
        
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_DAO_" . $contactType) . ".php");
        
        eval('$exportableFields = array_merge($exportableFields, CRM_Contact_DAO_'.$contactType.'::import( ));');
        
        $locationFields = array_merge(  CRM_Core_DAO_Address::import( ),
                                        CRM_Core_DAO_Phone::import( ),
                                        CRM_Core_DAO_Email::import( ),
                                        CRM_Core_DAO_IM::import( true ));
        foreach ($locationFields as $key => $field) {
            $locationFields[$key]['hasLocationType'] = true;
        }
        
        $exportableFields = array_merge($exportableFields, $locationFields);
        
        $exportableFields = array_merge($exportableFields,
                                               CRM_Contact_DAO_Contact::import( ) );
        $exportableFields = array_merge($exportableFields,
                                               CRM_Core_DAO_Note::import());
        $exportableFields = array_merge($exportableFields,
                                               CRM_Core_BAO_CustomField::getFieldsForImport($contactType) );
        return $exportableFields;
    }


}

?>
