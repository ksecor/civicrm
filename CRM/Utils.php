<?php
/**
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 * A few utility function. Eventually we should move functionality from this
 * class to the appropritate higher class library. For now, this is a convenient
 * place
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

class CRM_Utils {
 
    static function getClassName( $object ) {
        $name = get_class( $object );
        $path = explode( '_', $name );
        return $path[ count( $path ) - 1 ];
    }

    static function import( $classPath ) {
        if ( class_exists( $classPath ) ) {
            return;
        }
        
        $classPath = CRM_String::munge( $classPath, '', 1024);
        $classPath = str_replace( '_', '/', $classPath ) . '.php';
        require_once($classPath);
    }

}

?>
