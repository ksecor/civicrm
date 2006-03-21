<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

/**
 * State machine for managing different states of the Quest process.
 *
 */
class CRM_Core_OptionGroup {
    static $_values = array( );

    static function &values( $name, $flip = false ) {

        if ( ! CRM_Utils_Array::value( $name, self::$_values ) ) {
            self::$_values[$name] = array( );

            $domainID = CRM_Core_Config::domainID( );
            $query = "
SELECT v.name as name, v.title as title ,v.id as id
FROM   civicrm_option_value v,
       civicrm_option_group g
WHERE  v.option_group_id = g.id
  AND  g.domain_id       = $domainID
  AND  g.name            = '$name'
  AND  v.is_active       = 1 
  AND  g.is_active       = 1 
ORDER BY v.grouping, v.weight;
";
            
            $dao =& CRM_Core_DAO::executeQuery( $query );
           
            while ( $dao->fetch( ) ) {
                if ( $flip ) {
                    self::$_values[$name][$dao->title] = $dao->id;
                } else {
                    self::$_values[$name][$dao->id] = $dao->title;
                }
            }
        }
        return self::$_values[$name];
    }
}

?>