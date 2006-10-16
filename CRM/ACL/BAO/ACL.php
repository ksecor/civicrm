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
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/ACL/DAO/ACL.php';

/**
 *  Access Control List
 */
class CRM_ACL_BAO_ACL extends CRM_ACL_DAO_ACL {
    static $_entityTable = null;
    static $_objectTable = null;
    static $_operation   = null;

    static $_fieldKeys   = null
;
    static function entityTable( ) {
        if ( ! self::$_entityTable ) {
            self::$_entityTable = array(
                                        'civicrm_contact'      => ts( 'Contact'       ),
                                        'civicrm_acl_group'    => ts( 'ACL Group'     ), );
        }
        return self::$_entityTable;
    }

    static function objectTable( ) {
        if ( ! self::$_objectTable ) {
            self::$_objectTable = array(
                                        'civicrm_contact'      => ts( 'Contact'       ),
                                        'civicrm_group'        => ts( 'Group'         ),
                                        'civicrm_saved_search' => ts( 'Contact Group' ),
                                        'civicrm_admin'        => ts( 'Administer'    ),
                                        'civicrm_admin'        => ts( 'Import'        ) );
        }
        return self::$_objectTable;
    }

    static function operation( ) {
        if ( ! self::$_operation ) {
            self::$_operation = array(
                                      'View'   => ts( 'View'   ),
                                      'Edit'   => ts( 'Edit'   ),
                                      'Create' => ts( 'Create' ),
                                      'Delete' => ts( 'Delete' ),
                                      'All'    => ts( 'All'    ),
                                      'Grant'  => ts( 'Grant'  ),
                                      'Revoke' => ts( 'Revoke' ),
                                      );
        }
        return self::$_operation;
    }

    /**
     * Construct a WHERE clause to handle permissions to $object_*
     *
     * @param array ref $tables -   Any tables that may be needed in the FROM
     * @param string $operation -   The operation being attempted
     * @param string $object_table -    The table of the object in question
     * @param int $object_id    -   The ID of the object in question
     * @param int $acl_id   -       If it's a grant/revoke operation, the ACL ID
     * @param boolean $acl_group -  For grant operations, this flag determines if we're granting a single acl (false) or an entire group.
     * @return string           -   The WHERE clause, or 0 on failure
     * @access public
     * @static
     */
    public static function permissionClause(&$tables, $operation,
                                            $object_table = null, $object_id = null, 
                                            $acl_id = null, $acl_group = false) 
    {
        $dao =& new CRM_ACL_DAO_ACL;
        
        $t = array(
            'ACL'           => self::getTableName(),
            'ACLGroup'      => 'civicrm_acl_group',
            'ACLGroupJoin'  => CRM_ACL_DAO_GroupJoin::getTableName(),
            'Contact'       => CRM_Contact_DAO_Contact::getTableName(),
            'Group'         => CRM_Contact_DAO_Group::getTableName(),
            'GroupContact'  => CRM_Contact_DAO_GroupContact::getTableName()
        );

        $session     =& CRM_Core_Session::singleton();
        $contact_id  =  $session->get('userID');
        
        $where = " {$t['ACL']}.operation = '" .
                    CRM_Utils_Type::escape($operation, 'String') ."'";

        /* Include clause if we're looking for a specific table/id permission */
        if (!empty($object_table)) {
            $where .= " AND ( {$t['ACL']}.object_table IS null
                         OR ({$t['ACL']}.object_table   = '" .
                    CRM_Utils_Type::escape($object_table, 'String') ."'";
            if (!empty($object_id)) {
                $where .= " AND ({$t['ACL']}.object_id IS null
                            OR {$t['ACL']}.object_id = " .
                    CRM_Utils_Type::escape($object_id, 'Integer') . ')';
            }
            $where .= '))';
        }
            
        /* Include clause if we're granting an ACL or ACL Group */
        if (!empty($acl_id)) {
            $where .= " AND ({$t['ACL']}.acl_id IS null 
                        OR {$t['ACL']}.acl_id   = "
                    . CRM_Utils_Type::escape($acl_id, 'Integer') . ')';
            if ($acl_group) {
                $where .= " AND {$t['ACL']}.acl_table = '{$t['ACLGroup']}'";
            } else {
                $where .= " AND {$t['ACL']}.acl_table = '{$t['ACL']}'";
            }
        }
        
        $query = array();
        
        /* Query for permissions granted to all contacts in the domain */
        $query[] = "SELECT      {$t['ACL']}.*, 0 as override
                    FROM        {$t['ACL']}
                    
                    WHERE       {$t['ACL']}.entity_table    = '{$t['Domain']}'
                            AND {$t['ACL']}.entity_id       = $domainId
                            AND ($where)";

        /* Query for permissions granted to all contacts through an ACL group */
        $query[] = "SELECT      {$t['ACL']}.*, 0 as override
                    FROM        {$t['ACL']}
                    
                    INNER JOIN  {$t['ACLGroupJoin']}
                            ON  ({$t['ACL']}.entity_table = '{$t['ACLGroup']}'
                            AND     {$t['ACL']}.entity_id = 
                                    {$t['ACLGroupJoin']}.acl_group_id)
                                    
                    INNER JOIN  {$t['ACLGroup']}
                            ON      {$t['ACL']}.entity_id = 
                                    {$t['ACLGroup']}.id
                    
                    WHERE       {$t['ACLGroupJoin']}.entity_table =
                                    '{$t['Domain']}'
                            AND {$t['ACLGroup']}.is_active      = 1
                            AND {$t['ACLGroupJoin']}.entity_id  = $domainId
                            AND ($where)";
        
        /* Query for permissions granted directly to the contact */
        $query[] = "SELECT      {$t['ACL']}.*, 1 as override
                    FROM        {$t['ACL']}
                    
                    INNER JOIN  {$t['Contact']}
                            ON  ({$t['ACL']}.entity_table = '{$t['Contact']}'
                            AND     {$t['ACL']}.entity_id = {$t['Contact']}.id)
                    
                    WHERE       {$t['Contact']}.id          = $contact_id 
                            AND ($where)";

        /* Query for permissions granted to the contact through an ACL group */
        $query[] = "SELECT      {$t['ACL']}.*, 1 as override
                    FROM        {$t['ACL']}
                    
                    INNER JOIN  {$t['ACLGroupJoin']}
                            ON  ({$t['ACL']}.entity_table = '{$t['ACLGroup']}'
                            AND     {$t['ACL']}.entity_id =
                                    {$t['ACLGroupJoin']}.acl_group_id)
                    
                    INNER JOIN  {$t['ACLGroup']}
                            ON  {$t['ACL']}.entity_id = {$t['ACLGroup']}.id
                    
                    WHERE       {$t['ACLGroupJoin']}.entity_table = 
                                    '{$t['Contact']}' 
                        AND     {$t['ACLGroup']}.is_active      = 1
                        AND     {$t['ACLGroupJoin']}.entity_id  = $contact_id
                        AND     ($where)";

        /* Query for permissions granted to the contact through a group */
        $query[] = "SELECT      {$t['ACL']}.*, 0 as override
                    FROM        {$t['ACL']}
                    
                    INNER JOIN  {$t['GroupContact']}
                            ON  ({$t['ACL']}.entity_table = '{$t['Group']}'
                            AND     {$t['ACL']}.entity_id =
                                    {$t['GroupContact']}.group_id)
                    
                    WHERE       ($where)
                        AND     {$t['GroupContact']}.contact_id = $contact_id
                        AND     {$t['GroupContact']}.status     = 'Added')";


        /* Query for permissions granted through an ACL group to a Contact
         * group */
        $query[] = "SELECT      {$t['ACL']}.*, 0 as override
                    FROM        {$t['ACL']}
                    
                    INNER JOIN  {$t['ACLGroupJoin']}
                            ON  ({$t['ACL']}.entity_table = '{$t['ACLGroup']}'
                            AND     {$t['ACL']}.entity_id = 
                                    {$t['ACLGroupJoin']}.acl_group_id)
                   
                    INNER JOIN  {$t['ACLGroup']}
                            ON  {$t['ACL']}.entity_id = {$t['ACLGroup']}.id
                   
                    INNER JOIN  {$t['GroupContact']}
                            ON  ({$t['ACLGroupJoin']}.entity_table =
                                    '{$t['Group']}'
                            AND     {$t['ACLGroupJoin']}.entity_id =
                                    {$t['GroupContact']}.group_id)
                    
                    WHERE       ($where)
                        AND     {$t['ACLGroup']}.is_active      = 1
                        AND     {$t['GroupContact']}.contact_id = $contact_id
                        AND     {$t['GroupContact']}.status     = 'Added'";
                    
        $union = '(' . implode(') UNION DISTINCT (', $query) . ')';

        $dao->query($union);
        
        $allow    = array(0);
        $deny     = array(0);
        $override = array();

        while ($dao->fetch()) {
            /* Instant bypass for the following cases:
             * 1) the rule governs all tables
             * 2) the rule governs all objects in the table in question
             * 3) the rule governs the specific object we want
             */
            if (empty($dao->object_table) || 
                ($dao->object_table == $object_table 
                    && (empty($dao->object_id) 
                        || $dao->object_id == $object_id) ) )
            {
                $clause = 1;
            } 
            else 
            {
                /* Otherwise try to generate a clause for this rule */
                $clause = self::getClause(
                    $dao->object_table, $dao->object_id, $tables);
                
                /* If the clause returned is null, then the rule is a blanket
                 * (id is null) on a table other than the one we're interested
                 * in.  So skip it. */
                if (empty($clause)) {
                    continue;
                }
            }
            
            /* Now we figure out if this is an allow or deny rule, and possibly
             * a contact-level override */
            if ($dao->deny) {
                $deny[] = $clause;
            } else {
                $allow[] = $clause;
                
                if ($dao->override) {
                    $override[] = $clause;
                }
            }
        }

        $allows = '(' . implode(' OR ', $allow) . ')';
        $denies = '(' . implode(' OR ', $deny) . ')';
        if (!empty($override)) {
            $denies = '(NOT (' . implode(' OR ', $override) .") AND $denies)";
        }

        return "($allows AND NOT $denies)";
    }

    /**
     * Given a table and id pair, return the filter clause
     *
     * @param string $table -   The table owning the object
     * @param int $id   -       The ID of the object
     * @param array ref $tables - Tables that will be needed in the FROM
     * @return string|null  -   WHERE-style clause to filter results, 
                                or null if $table or $id is null
     * @access public
     * @static
     */
    public static function getClause($table, $id, &$tables) {
        $table = CRM_Utils_Type::escape($table, 'String');
        $id = CRM_Utils_Type::escape($id, 'Integer');
        $whereTables = array( );

        $ssTable = CRM_Contact_BAO_SavedSearch::getTableName();

        if (empty($table)) {
            return null;
        } elseif ($table == $ssTable) {
            return CRM_Contact_BAO_SavedSearch::whereClause($id, $tables, $whereTables);
        } elseif (!empty($id)) {
            $tables[$table] = true;
            return "$table.id = $id";
        }
        return null;
    }

    /**
     * Construct an associative array of an ACL rule's properties
     *
     * @param
     * @return array    - Assoc. array of the ACL rule's properties
     * @access public
     */
    public function toArray() {
        $result = array();

        if ( ! self::$_fieldKeys ) {
            $fields =& CRM_ACL_DAO_ACL::fields( );
            self::$_fieldKeys = array_keys( $fields );
        }

        foreach ( self::$_fieldKeys as $field ) {
            $result[$field] = $this->$field;
        }
        return $result;
    }

    /**
     * Retrieve ACLs for a contact or group.  Note that including a contact id
     * without a group id will return those ACL rules which are granted
     * directly to the contact, but not those granted to the contact through
     * any/all of his group memberships.
     *
     * @param int $contact_id       -   ID of a contact to search for
     * @param int $group_id         -   ID of a group to search for
     * @param boolean $aclGroups    -   Should we include ACL Groups
     * @return array                -   Array of assoc. arrays of ACL rules 
     * @access public
     * @static
     */
    public static function &getACLs($contact_id = null, $group_id = null, $aclGroups = false) {
        $contact_id = CRM_Utils_Type::escape($contact_id, 'Integer');
        if ( $group_id ) {
            $group_id   = CRM_Utils_Type::escape($group_id, 'Integer');
        }
        
        $rule       =& new CRM_ACL_BAO_ACL();

        require_once 'CRM/Contact/BAO/Group.php';

        $acl        = self::getTableName();
        $contact    = CRM_Contact_BAO_Contact::getTableName();
        $c2g        = CRM_Contact_BAO_GroupContact::getTableName();
        $group      = CRM_Contact_BAO_Group::getTableName();
        
        $query      = " SELECT      $acl.*
                        FROM        $acl ";
        
        if (!empty($group_id)) {
            $query .= " INNER JOIN  $c2g
                            ON      $acl.entity_id      = $c2g.group_id
                        WHERE       $acl.entity_table   = '$group'
                            AND     $acl.is_active      = 1
                            AND     $c2g.group_id       = $group_id";
                        
            if (!empty($contact_id)) {
                $query .= " AND     $c2g.contact_id     = $contact_id
                            AND     $c2g.status         = 'Added'";
            }
            
        } else {
            if (!empty($contact_id)) {
                $query .= " WHERE   $acl.entity_table   = '$contact'
                            AND     $acl.entity_id      = $contact_id";
            
            }
        }

        $rule->query($query);
        
        $results = array();
        while ($rule->fetch()) {
            $results[$rule->id] = $rule->toArray( );
        }

        if ($aclGroups) {
            $results += self::getACLGroups($contact_id, $group_id);
        }

        return $results;
    }
    
    /**
     * Get all of the ACLs through ACL groups
     *
     * @param int $contact_id   -   ID of a contact to search for
     * @param int $group_id     -   ID of a group to search for
     * @return array            -   Array of assoc. arrays of ACL rules
     * @access public
     * @static
     */
    public static function &getACLGroups($contact_id = null, $group_id = null) {
        $contact_id = CRM_Utils_Type::escape($contact_id, 'Integer');
        if ( $group_id ) {
            $group_id   = CRM_Utils_Type::escape($group_id, 'Integer');
        }

        $rule       =& new CRM_ACL_BAO_ACL();

        require_once 'CRM/ACL/DAO/GroupJoin.php';
        $acl           = self::getTableName();
        $aclGroup      = 'civicrm_acl_group';
        $aclGroupJoin  = CRM_ACL_DAO_GroupJoin::getTableName();
        $contact       = CRM_Contact_BAO_Contact::getTableName();
        $c2g           = CRM_Contact_BAO_GroupContact::getTableName();
        $group         = CRM_Contact_BAO_Group::getTableName();
        
        $query =    "   SELECT          $acl.* 
                        FROM            $acl
                        INNER JOIN      civicrm_option_group og
                                ON      og.name = 'acl_group'
                        INNER JOIN      civicrm_option_value ov
                                ON      $acl.entity_table   = '$aclGroup'
                                AND     ov.option_group_id  = og.id
                                AND     $acl.entity_id      = ov.value";
                                
        if (!empty($group_id)) {
            $query .= " INNER JOIN  $c2g
                            ON      $acl.entity_id     = $c2g.group_id
                        WHERE       $acl.entity_table  = '$group'
                            AND     $acl.is_active     = 1
                            AND     $c2g.group_id           = $group_id";
                        
            if (!empty($contact_id)) {
                $query .= " AND     $c2g.contact_id = $contact_id
                            AND     $c2g.status = 'Added'";
            }
            
        } else {
            if (!empty($contact_id)) {
                $query .= " WHERE   $acl.entity_table  = '$contact'
                                AND $acl.is_active     = 1
                                AND $acl.entity_id     = $contact_id";
            
            }
        }

        $results = array();
        
        $rule->query($query);
        
        while ($rule->fetch()) {
            $results[$rule->id] =& $rule->toArray();
        }
        
        return $results;
    }


    /** 
     * Get all ACLs granted to a contact through all group memberships
     *
     * @param int $contact_id       -   The contact's ID
     * @param boolean $aclGroups    -   Include ACL Groups?
     * @return array                -   Assoc array of ACL rules
     * @access public
     * @static
     */
    public static function &getGroupACLs($contact_id, $aclGroups = false) {
        $contact_id = CRM_Utils_Type::escape($contact_id, 'Integer');

        $rule       =& new CRM_ACL_BAO_ACL();
        
        $acl        = self::getTableName();
        $c2g        = CRM_Contact_BAO_GroupContact::getTableName();
        $group      = CRM_Contact_BAO_Group::getTableName();
        
        $query      = " SELECT      $acl.*
                        FROM        $acl 
                        INNER JOIN  $c2g
                            ON      $acl.entity_id      = $c2g.group_id
                        WHERE       $acl.entity_table   = '$group'
                            AND     $c2g.contact_id     = $contact_id
                            AND     $c2g.status         = 'Added'";

        $rule->query($query);
        
        $results = array();
        while ($rule->fetch()) {
            $results[$acl->id] =& $rule->toArray();
        }

        if ($aclGroups) {
            $results += self::getGroupACLGroups($contact_id);
        }
        
        return $results;
    }

    /**
    * Get all of the ACLs for a contact through ACL groups owned by Contact
    * groups.
    *
    * @param int $contact_id   -   ID of a contact to search for
    * @return array            -   Array of assoc. arrays of ACL rules
    * @access public
    * @static
    */
    public static function &getGroupACLGroups($contact_id) {
        $contact_id = CRM_Utils_Type::escape($contact_id, 'Integer');
        
        $rule       =& new CRM_ACL_BAO_ACL();
                                                                                
        $acl        = self::getTableName();
        $aclGroup   = 'civicrm_acl_group';
        $c2g        = CRM_Contact_BAO_GroupContact::getTableName();
        $group      = CRM_Contact_BAO_Group::getTableName();
     
        $query =    "   SELECT          $acl.* 
                        FROM            $acl
                        INNER JOIN      civicrm_option_group og
                                ON      og.name = 'acl_group'
                        INNER JOIN      civicrm_option_value ov
                                ON      $acl.entity_table   = '$aclGroup'
                                AND     ov.option_group_id  = og.id
                                AND     $acl.entity_id      = ov.value
                        INNER JOIN  $c2g
                                ON      $acl.entity_id      = $c2g.group_id
                        WHERE       $acl.entity_table       = '$group'
                            AND     $acl.is_active          = 1
                            AND     $c2g.contact_id         = $contact_id
                            AND     $c2g.status             = 'Added'";
            
        $results = array();
        
        $rule->query($query);
        
        while ($rule->fetch()) {
            $results[$acl->id] =& $rule->toArray();
        }
        
        return $results;
    }


    /**
     * Get all ACLs owned by a given contact, including domain and group-level.
     *
     * @param int $contact_id   -   The contact ID
     * @return array            -   Assoc array of ACL rules
     * @access public
     * @static
     */
    public static function &getAllByContact($contact_id) {
        $result = array();

        /* First, the contact-specific ACLs, including ACL Groups */
        $result += self::getACLs($contact_id, null, true);

        /* Then, all ACLs granted through group membership */
        $result += self::getGroupACLs($contact_id, true);
        
        /* Finally, get the domain-level ACL rules */
        // $result += self::getACLs(null, null, true);

        return $result;
    }

    static function create( &$params ) {
        $dao =& new CRM_ACL_DAO_ACL( );
        $dao->copyValues( $params );
        $dao->domain_id = CRM_Core_Config::domainID( );

        $dao->save( );
    }

    static function retrieve( &$params, &$defaults ) {
        CRM_Core_DAO::commonRetrieve( 'CRM_ACL_DAO_ACL', $params, $defaults );
    }    

    static function check( $str, $contactID ) {
        require_once 'CRM/ACL/BAO/Cache.php';
        
        $acls =& CRM_ACL_BAO_Cache::build( $contactID );

        $aclKeys = array_keys( $acls );
        $aclKeys = implode( ',', $aclKeys );

        $params  = array( 1 => array( $str, 'String' ) );

        $query = "
SELECT count( id )
  FROM civicrm_acl_cache c, civicrm_acl a
 WHERE c.acl_id    =  a.id
   AND a.is_active =  1
   AND a.name      =  %1
   AND a.id        IN ( $aclKeys )
";
        $count =& CRM_Core_DAO::singleValueQuery( $query, $params );
        return ( $count ) ? true : false;
    }

    public static function whereClause( $type, &$tables, &$whereTables, $contactID = null ) {
        require_once 'CRM/ACL/BAO/Cache.php';

        $acls =& CRM_ACL_BAO_Cache::build( $contactID );
        if ( empty( $acls ) ) {
            return ' ( 0 ) ';
        }
        
        $aclKeys = array_keys( $acls );
        $aclKeys = implode( ',', $aclKeys );

        $query = "
SELECT   a.operation, a.object_id
  FROM   civicrm_acl_cache c, civicrm_acl a
 WHERE   c.acl_id       =  a.id
   AND   a.is_active    =  1
   AND   a.object_table = 'civicrm_saved_search'
   AND   a.id        IN ( $aclKeys )
ORDER BY a.object_id
";
        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        
        // do an or of all the where clauses u see
        $ids = array( );
        while ( $dao->fetch( ) ) {
            if ( ! $dao->object_id ) {
                return ' ( 1 ) ';
            }
            
            $ids[] = $dao->object_id;
        }

        if ( empty( $ids ) ) {
            return ' ( 0 ) ';
        }

        $ids = implode( ',', $ids );
        $query = "
SELECT s.where_clause, s.select_tables, s.where_tables
  FROM civicrm_saved_search s, civicrm_group g
 WHERE s.id = g.saved_search_id
   AND g.id IN ( $ids )
";
        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        $clauses = array( );
        while ( $dao->fetch( ) ) {
            // make sure operation matches the type TODO
            // currently operation is restrcited to VIEW/EDIT
            if ( $dao->where_clause ) {
                $clauses[] = $dao->where_clause;
                if ( $dao->select_tables ) {
                    $tables = array_merge( $tables,
                                           unserialize( $dao->select_tables ) );
                }
                if ( $dao->where_tables ) {
                    $whereTables = array_merge( $whereTables,
                                                unserialize( $dao->where_tables ) );
                }
            }
        }

        if ( ! empty( $clauses ) ) {
            return ' ( ' . implode( ' OR ', $clauses ) . ' ) ';
        } else {
            return ' ( 0 ) ';
        }
    }

    public static function group( $type, $contactID = null ) {
        require_once 'CRM/ACL/BAO/Cache.php';

        $acls =& CRM_ACL_BAO_Cache::build( $contactID );
        $ids  = array( );
        if ( empty( $acls ) ) {
	  return $ids;
        }
        
        $aclKeys = array_keys( $acls );
        $aclKeys = implode( ',', $aclKeys );

        $query = "
SELECT   a.operation, a.object_id
  FROM   civicrm_acl_cache c, civicrm_acl a
 WHERE   c.acl_id       =  a.id
   AND   a.is_active    =  1
   AND   a.object_table = 'civicrm_saved_search'
   AND   a.id        IN ( $aclKeys )
ORDER BY a.object_id
";
        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );


        // do an or of all the where clauses u see
        while ( $dao->fetch( ) ) {
	  if ( $dao->object_id ) {
            $ids[] = $dao->object_id;
	  }
        }

	return $ids;
    }

}

?>
