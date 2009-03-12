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
 * The core concept of the system is an action performed on an object. Typically this will be a "data model" object 
 * as specified in the API specs. We attempt to keep the number and type of actions consistent 
 * and similar across all objects (thus providing both reuse and standards)
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Utils/Array.php';

class CRM_Core_Action {

    /**
     * Different possible actions are defined here. Keep in sync with the
     * constant from CRM_Core_Form for various modes.
     *
     * @var const
     *
     * @access public
     */
    const
        NONE          =      0,
        ADD           =      1,
        UPDATE        =      2,
        VIEW          =      4,
        DELETE        =      8,
        BROWSE        =     16,
        ENABLE        =     32,
        DISABLE       =     64,
        EXPORT        =    128,
        BASIC         =    256,
        ADVANCED      =    512,
        PREVIEW       =   1024,
        FOLLOWUP      =   2048,
        MAP           =   4096,
        PROFILE       =   8192,
        COPY          =  16384,
        RENEW         =  32768,
        DETACH        =  32768,
        MAX_ACTION    =  65535;
   
  
    /**
     * map the action names to the relevant constant. We perform
     * bit manipulation operations so we can perform multiple
     * actions on the same object if needed
     *
     * @var array  _names  tupe of variable name to action constant
     *
     * @access private
     * @static
     *
     */
    static $_names = array(
                           'add'           => self::ADD,
                           'update'        => self::UPDATE,
                           'view'          => self::VIEW  ,
                           'delete'        => self::DELETE,
                           'browse'        => self::BROWSE,
                           'enable'        => self::ENABLE,
                           'disable'       => self::DISABLE,
                           'export'        => self::EXPORT,
                           'preview'       => self::PREVIEW,
                           'map'           => self::MAP,
                           'copy'          => self::COPY,
                           'profile'       => self::PROFILE,
                           'renew'         => self::RENEW,
                           'detach'        => self::DETACH
                           );

    /**
     * the flipped version of the names array, initialized when used
     * 
     * @var array
     * @static
     */
    static $_description;

    /**
     *
     * called by the request object to translate a string into a mask
     *
     * @param string $action the action to be resolved
     *
     * @return int the action mask corresponding to the input string
     * @access public
     * @static
     *
     */
    static function resolve( $str ) {
        $action = 0;
        if ( $str ) {
            $items = explode( '|', $str );
            $action = self::map( $items );
        }
        return $action;
    }

    /**
     * Given a string or an array of strings, determine the bitmask
     * for this set of actions
     *
     * @param mixed either a single string or an array of strings
     *
     * @return int the action mask corresponding to the input args
     * @access public
     * @static
     *
     */
    static function map( $item ) {
        $mask = 0;

        if ( is_array( $item ) ) {
            foreach ( $item as $it ) {
                $mask |= self::mapItem( $it );
            }
            return $mask;
        } else {
            return self::mapItem( $item );
        }
    }

    /**
     * Given a string determine the bitmask for this specific string
     *
     * @param string the input action to process
     *
     * @return int the action mask corresponding to the input string
     * @access public
     * @static
     *
     */
    static function mapItem( $item ) {
        $mask = CRM_Utils_Array::value( trim( $item ), self::$_names );
        return $mask ? $mask : 0;
    }

    /**
     *
     * Given an action mask, find the corresponding description
     *
     * @param int the action mask
     *
     * @return string the corresponding action description
     * @access public
     * @static
     *
     */
    static function description( $mask ) {
       if ( ! isset( $_description ) ) {
            self::$_description = array_flip( self::$_names );
        }
        
        return CRM_Utils_Array::value( $mask, self::$_description, 'NO DESCRIPTION SET' );
    }

    /**
     * given a set of links and a mask, return the html action string for
     * the links associated with the mask
     *
     * @param array $links  the set of link items
     * @param int   $mask   the mask to be used. a null mask means all items
     * @param array $values the array of values for parameter substitution in the link items
     *
     * @return string       the html string
     * @access public
     * @static
     */
    static function formLink( &$links, $mask, $values ) {
        $config =& CRM_Core_Config::singleton();
        if ( empty( $links ) ) {
            return null;
        }

        $url = array( );
        foreach ( $links as $m => $link ) {
            if ( ! $mask || ( $mask & $m ) ) {
                $extra = str_replace( 'onclick', 'js', CRM_Utils_Array::value( 'extra', $link, '' ) );
                $url[] = sprintf('<a href="%s" ' . $extra . '>%s</a>',
                                 CRM_Utils_System::url( self::replace( $link['url'], $values ),
                                                        self::replace( $link['qs'] , $values ), true ),
                                 $link['name'] );
            }
        }
        $result = $resultDiv = '';
        $actionLink = array_slice ( $url, 0, 2 );
        $actionDiv  = array_splice( $url, 2    );
        CRM_Utils_String::append( $resultLink, '&nbsp;|&nbsp;', $actionLink );
        if ( $actionDiv ) {
            CRM_Utils_String::append( $resultDiv, '</li><li>', $actionDiv );
            $resultDiv = "| <img src='{$config->resourceBase}i/menu-collapsed.png' title='".ts('more actions')."'/> <i>".ts('more actions')."</i><ul id='panel_xx' class='panel'><li>{$resultDiv}</li></ul>";
        }
        $result = "<span>{$resultLink} &nbsp;</span><span class='btn-slide' id=xx>{$resultDiv}</span>";
        return $result;
    }

    /**
     * given a string and an array of values, substitute the real values
     * in the placeholder in the str in the CiviCRM format
     *
     * @param string $str    the string to be replaced
     * @param array  $values the array of values for parameter substitution in the str
     *
     * @return string        the substituted string
     * @access public
     * @static
     */
    static function &replace( &$str, &$values ) {
        foreach ( $values as $n => $v ) {
            $str = str_replace( "%%$n%%", $v, $str );
        }
        return $str;
    }

    /**
     * get the mask for a permission (view, edit or null)
     *
     * @param string the permission
     *
     * @return int   the mask for the above permission
     * @static
     * @access public
     */
    static function mask( $permission ) {
        if ( $permission == CRM_Core_Permission::VIEW ) {
            return self::VIEW | self::EXPORT | self::BASIC | self::ADVANCED | self::BROWSE | self::MAP | self::PROFILE;
        } else if ( $permission == CRM_Core_Permission::EDIT ) {
            return self::MAX_ACTION;  // make sure we make this 2^(n+1) -1 if we add more actions;
        } else {
            return null;
        }
    }

}


