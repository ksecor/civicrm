<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 *  Access Control List
 */
class CRM_Core_BAO_ACL extends CRM_Core_DAO_ACL {

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
        $dao =& new CRM_Core_DAO_ACL;
        
        $t = array(
            'ACL'           => self::getTableName(),
            'ACLGroup'      => CRM_Core_DAO_ACLGroup::getTableName(),
            'ACLGroupJoin'  => CRM_Core_DAO_ACLGroupJoin::getTableName(),
            'Contact'       => CRM_Contact_DAO_Contact::getTableName(),
            'Domain'        => CRM_Core_DAO_Domain::getTableName(),
            'Group'         => CRM_Contact_DAO_Group::getTableName(),
            'GroupContact'  => CRM_Contact_DAO_GroupContact::getTableName()
        );

        $session    =& CRM_Core_Session::singleton();
        $contact_id  = $session->get('userID');
        $domainId   = $session->get('domainID');
        
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
        
        $allow = array(0);
        $deny = array(0);
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
    public function &toArray() {
        $result = array();
        
        foreach (array('id', 'deny', 'entity_table', 'entity_id', 
            'object_table', 'object_id', 'acl_table', 'acl_id') as $field) 
        {
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
        $group_id   = CRM_Utils_Type::escape($group_id, 'Integer');
        
        $rule       =& new CRM_Core_BAO_ACL();
        
        $acl        = self::getTableName();
        $contact    = CRM_Contact_BAO_Contact::getTableName();
        $c2g        = CRM_Contact_BAO_GroupContact::getTableName();
        $domain     = CRM_Core_BAO_Domain::getTableName();
        $group      = CRM_Contact_BAO_Group::getTableName();
        
        $session    =& CRM_Core_Session::singleton();
        $domainId   = $session->get('domainID');
        
        $query      = " SELECT      $acl.*
                        FROM        $acl ";
        
        if (!empty($group_id)) {
            
            $query .= " INNER JOIN  $c2g
                            ON      $acl.entity_id      = $c2g.group_id
                        WHERE       $acl.entity_table   = '$group'
                            AND     $c2g.group_id       = $group_id";
                        
            if (!empty($contact_id)) {
                $query .= " AND     $c2g.contact_id     = $contact_id
                            AND     $c2g.status         = 'Added'";
            }
            
        } else {
            
            if (!empty($contact_id)) {
                $query .= " WHERE   $acl.entity_table   = '$contact'
                            AND     $acl.entity_id      = $contact_id";
            
            } else {
                $query .= " WHERE   $acl.entity_table   = '$domain'
                            AND     $acl.entity_id      = $domainId";
                
            }
        }

        $rule->query($query);
        
        $results = array();
        while ($rule->fetch()) {
            $results[] =& $rule->toArray();
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
        $group_id   = CRM_Utils_Type::escape($group_id, 'Integer');

        $rule       =& new CRM_Core_BAO_ACL();

        $acl        = self::getTableName();
        $aclGroup   = CRM_Core_BAO_ACLGroup::getTableName();
        $contact    = CRM_Contact_BAO_Contact::getTableName();
        $domain     = CRM_Core_DAO_Domain::getTableName();
        $c2g        = CRM_Contact_BAO_GroupContact::getTableName();
        $group      = CRM_Contact_BAO_Group::getTableName();
        
        
        $session    =& CRM_Core_Session::singleton();
        $domainId   = $session->get('domainID');
        
        $query =    "   SELECT          $acl.* 
                        FROM            $acl
                        INNER JOIN      $aclGroup
                                ON      $acl.entity_table   = '$aclGroup'
                                AND     $acl.entity_id      = $aclGroup.id";
                                
        if (!empty($group_id)) {
            
            $query .= " INNER JOIN  $c2g
                            ON      $aclGroup.entity_id     = $c2g.group_id
                        WHERE       $aclGroup.entity_table  = '$group'
                            AND     $aclGroup.is_active     = 1
                            AND     $c2g.group_id           = $group_id";
                        
            if (!empty($contact_id)) {
                $query .= " AND     $c2g.contact_id = $contact_id
                            AND     $c2g.status = 'Added'";
            }
            
        } else {
            
            if (!empty($contact_id)) {
                $query .= " WHERE   $aclGroup.entity_table  = '$contact'
                                AND $aclGroup.is_active     = 1
                                AND $aclGroup.entity_id     = $contact_id";
            
            } else {
                $query .= " WHERE   $aclGroup.entity_table  = '$domain'
                                AND $aclGroup.is_active     = 1
                                AND $aclGroup.entity_id     = $domain_id";
            }
        }

        $results = array();
        
        $rule->query($query);
        
        while ($rule->fetch()) {
            $results[] =& $rule->toArray();
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
        $group_id   = CRM_Utils_Type::escape($group_id, 'Integer');

        $rule       =& new CRM_Core_BAO_ACL();
        
        $acl        = self::getTableName();
        $c2g        = CRM_Contact_BAO_GroupContact::getTableName();
        $group      = CRM_Contact_BAO_Group::getTableName();
        
        $session    =& CRM_Core_Session::singleton();
        $domainId   = $session->get('domainID');
        
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
            $results[] =& $rule->toArray();
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
        
        $rule       =& new CRM_Core_BAO_ACL();
                                                                                
        $acl        = self::getTableName();
        $aclGroup   = CRM_Core_BAO_ACLGroup::getTableName();
        $c2g        = CRM_Contact_BAO_GroupContact::getTableName();
        $group      = CRM_Contact_BAO_Group::getTableName();
     
        $query =    "   SELECT          $acl.* 
                        FROM            $acl
                        INNER JOIN      $aclGroup
                                ON      $acl.entity_table   = '$aclGroup'
                                AND     $acl.entity_id      = $aclGroup.id
                            
                        INNER JOIN  $c2g
                            ON      $aclGroup.entity_id     = $c2g.group_id
                            
                        WHERE       $aclGroup.entity_table  = '$group'
                            AND     $aclGroup.is_active     = 1
                            AND     $c2g.contact_id         = $contact_id
                            AND     $c2g.status             = 'Added'";
            
        $results = array();
        
        $rule->query($query);
        
        while ($rule->fetch()) {
            $results[] =& $rule->toArray();
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
        $result += self::getACLs(null, null, true);

        return $result;
    }
}

?>
