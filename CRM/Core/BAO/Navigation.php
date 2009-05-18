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

require_once 'CRM/Core/DAO/Navigation.php';


class CRM_Core_BAO_Navigation extends CRM_Core_DAO_Navigation {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * 
     * @access public
     * @static
     */
    static function setIsActive( $id, $is_active ) {
        return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_Navigation', $id, 'is_active', $is_active );
    }

    /**
     * Function to get existing / build navigation for CiviCRM Admin Menu
     *
     * @static
     * @return array associated array
     */
    static function getMenus( ) {
        $menus = array( );
        require_once "CRM/Core/DAO/Menu.php";
        $menu  =& new CRM_Core_DAO_Menu( );
        $menu->find();
        while ( $menu->fetch() ) {
            if ( $menu->title ) {
                $menus[$menu->path] = $menu->title;
            }
        }    
        return $menus;        
    }
    
    /**
     * Function get existing Navigation from db
     *
     * @static
     * @return array associated array
     */ 
     static function getNavigationList( ) {
         $navigations = array( ); 
         require_once "CRM/Core/DAO/Navigation.php";
         $navigation  =& new CRM_Core_DAO_Navigation( );
         $navigation->find();
         while ( $navigation->fetch() ) {
            $navigations[$navigation->id] = $navigation->label;
         }    
         return $navigations;
     }
     
     /**
      * Function to add/update navigation record
      *
      * @param array associated array of submitted values
      *
      * @return object navigation object
      * @static
      */
      static function add( &$params ) {
          require_once "CRM/Core/DAO/Navigation.php";
          $navigation  =& new CRM_Core_DAO_Navigation( );
          if ( !isset( $params['id'] ) ) {
              $params['name']   = $params['label'];
              $params['weight'] = self::calculateWeight( $params['parent_id'] );
          }
                        
          $params['permission_operator'] = 'AND';
          if ( $params['CiviCRM_OP_OR'] ) {
              $params['permission_operator'] = 'OR';
          }
          
          $params['permission'] = implode( ',', $params['permission'] );

          $navigation->copyValues( $params );
          $navigation->save();
          return $navigation;
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
       * @return object CRM_Core_BAO_Navigation object on success, null otherwise
       * @access public
       * @static
       */
      static function retrieve( &$params, &$defaults ) {
          $navigation =& new CRM_Core_DAO_Navigation( );
          $navigation->copyValues( $params );
          if ( $navigation->find( true ) ) {
              CRM_Core_DAO::storeValues( $navigation, $defaults );
              return $navigation;
          }
          return null;
      }
      
      /**
       * Calculate navigation weight
       *
       * @param $parentID parent_id of a menu
       * @param $menuID  menu id
       *
       * @return $weight string
       * @static
       */
       static function calculateWeight( $parentID = null, $menuID = null ) {
           $weight = 1;
           // new weight calculation, all top level menus will have single digit weights
           // So weight convention is 1, then its child will be 1.1, if 1.1 has child it will be 1.1.1
           // and so on...
           
           // calculate max weight for top level menus, if parent id is absent
           if ( !$parentID ) {
               $query = "SELECT max(weight) as weight FROM civicrm_navigation WHERE parent_id IS NULL";
           } else {
               // if parent is passed, we need to get max weight for that particular parent
               $query = "SELECT max(weight) as weight FROM civicrm_navigation WHERE parent_id = {$parentID}";
           }
           
           $dao = CRM_Core_DAO::executeQuery( $query );
           $dao->fetch();
           return $weight = $weight + $dao->weight;
       }
}

