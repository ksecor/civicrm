<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/DAO/UFJoin.php';

/**
 *
 */
class CRM_Core_BAO_UFJoin extends CRM_Core_DAO_UFJoin {

    /**
     * takes an associative array and creates a uf join object
     *
     * @param array $params (reference) an assoc array of name/value pairs
     *
     * @return object CRM_Core_DAO_UFJoin object 
     * @access public
     * @static
     */
    public static function &create($params) {
        // see if a record exists with the same weight
        $id = self::findJoinEntryId( $params );
        if ( $id ) {
            $params['id'] = $id;
        }

        $dao =& new CRM_Core_DAO_UFJoin( ); 
        $dao->copyValues( $params ); 
        if ( $params['uf_group_id'] ) {
            $dao->save( ); 
        } else { 
            $dao->delete( );
        }

        return $dao; 
    } 

    /**
     * Given an assoc list of params, find if there is a record
     * for this set of params
     *
     * @param array $params (reference) an assoc array of name/value pairs 
     * 
     * @return int or null
     * @access public
     * @static
     */
    public static function findJoinEntryId(&$params) {
        if ( CRM_Utils_Array::value( 'id', $params ) ) {
            return $params['id'];
        }

        $dao =& new CRM_Core_DAO_UFJoin( );
        
        $dao->entity_table = CRM_Utils_Array::value( 'entity_table', $params );
        $dao->entity_id    = CRM_Utils_Array::value( 'entity_id'   , $params );
        $dao->weight       = CRM_Utils_Array::value( 'weight'      , $params );
        if ( $dao->find( true ) ) {
            return $dao->id;
        }
        return null;
    }

    /**
     * Given an assoc list of params, find if there is a record
     * for this set of params and return the group id
     *
     * @param array $params (reference) an assoc array of name/value pairs 
     * 
     * @return int or null
     * @access public
     * @static
     */
    public static function findUFGroupId(&$params) { 
    
        $dao =& new CRM_Core_DAO_UFJoin( ); 
         
        $dao->entity_table = CRM_Utils_Array::value( 'entity_table', $params );
        $dao->entity_id    = CRM_Utils_Array::value( 'entity_id'   , $params );
        $dao->weight       = CRM_Utils_Array::value( 'weight'      , $params );
        if ( $dao->find( true ) ) { 
            return $dao->uf_group_id; 
        } 
        return null; 
    } 

}

?>