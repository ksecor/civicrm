<?php
/*
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */



require_once 'CRM/DAO.php';
require_once 'CRM/Type.php';

class CRM_DAO_Base extends CRM_DAO {

    /*
     * auto incremented id
     * @var int
     */
    public $id;

    function __construct() {
        $this->__table = $this->getTableName();

        parent::__construct( );
    }

    function links( ) {
        return null;
    }

    function fields() {
        return array( 'id' => array( CRM_Type::T_STRING, self::NOT_NULL ) );
    }

    function table() {
        $fields =& $this->fields();
        $table = array();
        foreach ( $fields as $name => $value ) {
            $table[$name] = $value['type'];
            if ( $value['required'] ) {
                $table[$name] += self::DB_DAO_NOTNULL;
            }
        }

        // set the links
        $this->links();

        return $table;
    }

}

?>