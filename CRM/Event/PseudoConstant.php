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

/**
 * This class holds all the Pseudo constants that are specific to Event. This avoids
 * polluting the core class and isolates the Event
 */
class CRM_Event_PseudoConstant extends CRM_Core_PseudoConstant 
{
    /**
     * Event
     *
     * @var array
     * @static
     */
    private static $event; 
    
    /**
     * Participant Status 
     *
     * @var array
     * @static
     */
    private static $participantStatus; 
    
    /**
     * Participant Role
     *
     * @var array
     * @static
     */
    private static $participantRole; 
    
    /**
     * Get all the n events
     *
     * @access public
     * @return array - array reference of all events if any
     * @static
     */
    public static function &event( $id = null )
    {
        if ( ! self::$event ) {
            CRM_Core_PseudoConstant::populate( self::$event,
                                               'CRM_Event_DAO_Event',
                                               false, 'title', 'is_active', null, null);
        }
        if ($id) {
            if (array_key_exists($id, self::$event)) {
                return self::$event[$id];
            } else {
                return null;
            }
        }
        return self::$event;
    }
    
    /**
     * Get all the n participant statuses
     *
     * @access public
     * @return array - array reference of all participant statuses if any
     * @static
     */
    public static function &participantStatus( $id = null, $all = true )
    {
        if ( ! self::$participantStatus || ! CRM_Utils_Array::value( $all, self::$participantStatus ) ) {
            self::$participantStatus[$all] = array( );
            require_once "CRM/Core/OptionGroup.php";
            $condition = null;

            if ( $all === false ) {
                $condition = 'AND filter = 1';
            } elseif ( $all === -1 ) {
                $condition = 'AND filter = 0';
            }
            self::$participantStatus[$all] = CRM_Core_OptionGroup::values("participant_status", false, false, false, $condition);
        }
        
        if ( $id ) {
            return self::$participantStatus[$all][$id];
        }
        
        return self::$participantStatus[$all];
    }
    
    /**
     * Get all the n participant roles
     *
     * @access public
     * @return array - array reference of all participant roles if any
     * @static
     */
    public static function &participantRole( $id = null )
    {
        if ( ! self::$participantRole ) {
            self::$participantRole = array( );
            require_once "CRM/Core/OptionGroup.php";
            self::$participantRole = CRM_Core_OptionGroup::values("participant_role");
        }
        
        If( $id ) {
            return self::$participantRole[$id];
        }
        
        return self::$participantRole;
    }
}

