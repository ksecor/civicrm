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
 *
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
            'Group'         => CRM_Contact_DAO_Group::getTableName(),
            'GroupContact'  => CRM_Contact_DAO_GroupContact::getTableName()
        );

        $session =& CRM_Core_Session::singleton();
        $contactId = $session->get('userID');
        
        $where = " AND {$t['ACL']}.operation = '" .
                    CRM_Utils_Type::escape($operation, 'String') ."'";

        /* Include clause if we're looking for a specific table/id permission */
        if (!empty($object_table)) {
            $where .= " AND {$t['ACL']}.object_table = '" .
                    CRM_Utils_Type::escape($object_table, 'String') ."'";
            if (!empty($object_id)) {
                $where .= " AND {$t['ACL']}.object_id = " .
                    CRM_Utils_Type::escape($object_id, 'Integer');
            }
        }
            
        /* Include clause if we're granting an ACL or ACL Group */
        if (!empty($acl_id)) {
            $where .= " AND {$t['ACL']}.acl_id = "
                    . CRM_Utils_Type::escape($acl_id, 'Integer');
            if ($acl_group) {
                $where .= " AND {$t['ACL']}.acl_table = '{$t['ACLGroup']}'";
            } else {
                $where .= 
                    " AND {$t['ACL']}.acl_table = '{$t['ACL']}'";
            }
        }
        
        $query = array();
        
        /* Query for permissions granted directly to the contact, or to all
         * contacts */
        $query[] = "SELECT      {$t['ACL']}.*, 1 as override
                    FROM        {$t['ACL']}
                    LEFT JOIN   {$t['Contact']}
                            ON  ({$t['ACL']}.entity_table = {$t['Contact']}
                            AND {$t['ACL']}.entity_id = {$t['Contact']}.id)
                    WHERE       ({$t['ACL']}.entity_id IS null OR
                                {$t['Contact']}.id = $contactId) 
                            AND ($where)";

        /* Query for permissions granted to the contact through an ACL group */
        $query[] = "SELECT      {$t['ACL']}.*, 1 as override
                    FROM        {$t['ACL']}
                    INNER JOIN  {$t['ACLGroupJoin']}
                            ON  ({$t['ACL']}.entity_table = '{$t['ACLGroup']}'
                            AND {$t['ACL']}.entity_id =
                                {$t['ACLGroupJoin']}.acl_group_id)
                    WHERE       {$t['ACLGroupJoin']}.entity_table = 
                                    '{$t['Contact']}' 
                        AND     ({$t['ACLGroupJoin']}.entity_id is null
                            OR  {$t['ACLGroupJoin']}.entity_id = $contactId)
                        AND     ($where)";

        /* Query for permissions granted to the contact through a group */
        $query[] = "SELECT      {$t['ACL']}.*, 0 as override
                    FROM        {$t['ACL']}
                    INNER JOIN  {$t['GroupContact']}
                            ON  ({$t['ACL']}.entity_table = {$t['Group']}
                                AND {$t['ACL']}.entity_id =
                                    {$t['GroupContact']}.group_id)
                    WHERE       ($where)
                        AND     {$t['GroupContact']}.contact_id = $contactId
                        AND     {$t['GroupContact']}.status = 'Added')";


        /* Query for permissions granted through an ACL group to a Contact
         * group */
        $query[] = "SELECT      {$t['ACL']}.*, 0 as override
                    FROM        {$t['ACL']}
                    INNER JOIN  {$t['ACLGroupJoin']}
                            ON  ({$t['ACL']}.entity_table = '{$t['ACLGroup']}'
                            AND     {$t['ACL']}.entity_id = 
                                    {$t['ACLGroupJoin']}.acl_group_id)
                    INNER JOIN  {$t['GroupContact']}
                            ON  ({$t['ACLGroupJoin']}.entity_table =
                                    '{$t['Group']}'
                            AND {$t['ACLGroupJoin']}.entity_id =
                                    {$t['GroupContact']}.group_id)
                    WHERE       ($where)
                        AND     {$t['GroupContact']}.contact_id = $contactId
                        AND     {$t['GroupContact']}.status = 'Added'";
                    

        $union = '(' . implode(') UNION DISTINCT (', $query) . ')';

        $dao->query($union);
        $allow = array(0);
        $deny = array(0);
        $override = array();

        while ($dao->fetch()) {
            $clause = self::getClause(
                    $dao->object_table, $dao->object_id, $tables);
            
            /* If we've got an exact match on the object, let it through */
            if ($dao->object_table == $object_table 
                && ($dao->object_id == $object_id 
                    || empty($dao->object_id) ) ) 
            {
                $clause = 1;
            }

            
            if ($dao->deny) {
                /* a denial */
                $deny[] = $clause;
            } else {
                /* allowance */
                $allow[] = $clause;
                
                if ($dao->override) {
                    /* contact override */
                    $override[] = $clause;
                }
            }
        }

        if (empty($allow)) {
            return '0';
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
                                or null if no $id is null
     * @access public
     * @static
     */
    public static function getClause($table, $id, &$tables) {
        $ssTable = CRM_Contact_BAO_SavedSearch::getTableName();
       
        if ($table !== $ssTable) {
            if (!empty($id)) {
                $tables[$table] = true;
                return "$table.id = $id";
            }
            return null;
        }

        return CRM_Contact_BAO_SavedSearch::whereClause($id, $tables);
    }


    /** 
     * Get all ACLs for a given contact
     *
     * @param int $contact_id   -   The contact ID
     * @param array $acl        -   Assoc array of the contact's ACLs
     * @param array $groups     -   Assoc array of the contact's ACL groups
     * @return none
     * @access public
     * @static
     */
    public static function getContactACL($contact_id, &$acl, &$groups) {

        $t = array(
            'ACL'           => self::getTableName(),
            'ACLGroup'      => CRM_Core_DAO_ACLGroup::getTableName(),
            'ACLGroupJoin'  => CRM_Core_DAO_ACLGroupJoin::getTableName(),
            'Contact'       => CRM_Contact_DAO_Contact::getTableName(),
            'Group'         => CRM_Contact_DAO_Group::getTableName(),
            'GroupContact'  => CRM_Contact_DAO_GroupContact::getTableName()
        );

        $dao =& new CRM_Core_DAO_ACL();

        $dao->entity_table = $t['Contact'];
        $dao->entity_id = $contact_id;
        $dao->find();
        
        while ($dao->fetch()) {
            $acl[] = clone($dao);
        }

        $dao->reset();

        $dao->query( "  SELECT      {$t['ACL']}.*
                        FROM        {$t['ACL']}
                        INNER JOIN  {$t['GroupContact']}
                            ON  ({$t['ACL']}.entity_table = {$t['Group']}
                            AND {$t['ACL']}.entity_id =
                                {$t['GroupContact']}.group_id)
                        INNER JOIN  {$t['Contact']}
                            ON  {$t['GroupContact']}.contact_id =
                                {$t['Contact']}.id
                        WHERE   {$t['Contact']}.id = $contact_id
                            AND {$t['GroupContact']}.status = 'Added'");

        while ($dao->fetch()) {
            $acl[] = clone($dao);
        }


        $dao->reset();

        $dao->query("SELECT     {$t['ACL']}.*
                    FROM        {$t['ACL']}
                    INNER JOIN  {$t['ACLGroupJoin']}
                            ON  ({$t['ACL']}.entity_table = '{$t['ACLGroup']}'
                            AND {$t['ACL']}.entity_id =
                                {$t['ACLGroupJoin']}.acl_group_id)
                    INNER JOIN  {$t['Contact']}
                            ON  {$t['ACLGroupJoin']}.entity_table =
                                '{$t['Contact']}'
                    WHERE       {$t['ACLGroupJoin']}.entity_id = $contact_id");

        while ($dao->fetch()) {
            $groups[] = clone($dao);
        }

        $dao->reset();
        
        $dao->query("SELECT     {$t['ACL']}.*
                    FROM        {$t['ACL']}
                    INNER JOIN  {$t['ACLGroupJoin']}
                        ON  ({$t['ACL']}.entity_table = '{$t['ACLGroup']}'
                        AND     {$t['ACL']}.entity_id = 
                                {$t['ACLGroupJoin']}.acl_group_id)
                    INNER JOIN  {$t['GroupContact']}
                        ON  ({$t['ACLGroupJoin']}.entity_table = '{$t['Group']}'
                        AND {$t['ACLGroupJoin']}.entity_id =
                             {$t['GroupContact']}.group_id)
                    WHERE       {$t['GroupContact']}.contact_id = $contact_id
                        AND     {$t['GroupContact']}.status = 'Added'");
       
        while ($dao->fetch()) {
            $groups[] = clone($dao);
        }

    }
}

?>
