<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 * This file has its origins in my conversation with David Strauss over IRC and the transaction
 * function in DAO.php
 *
 * Strauss went on and abstraced this into a class which can be used in PHP5 (since destructors
 * are called automagically at the end of the script. I've modified the code and used CiviCRM
 * coding standards. You can search for 'pressflow_transaction' on drupal modules for Strauss's
 * code
 *
 */

class CRM_Core_Transaction {

    /**
     * Keep track of the number of opens and close
     *
     * @var int
     */
    private static $_count = 0;

    /**
     * Keep track if we need to commit or rollback
     *
     * @var boolean
     */
    private static $_doCommit = true;

    /**
     * hold a dao singleton for query operations
     *
     * @var object
     */
    private static $_dao = null;

    function __construct( ) {
        if ( ! self::$_dao ) {
            self::$_dao = new CRM_Core_DAO( );
        }

        if ( self::$_count == 0 ) {
            self::$_dao->query( 'BEGIN' );
        }

        self::$_count++;
    }

    function __destruct( ) {
        self::$_count--;

        if ( self::$_count == 0 ) {
            if ( self::$_doCommit ) {
                self::$_dao->query( 'COMMIT' );
            } else {
                self::$_dao->query( 'ROLLBACK' );
            }
            // this transaction is complete, so reset doCommit flag
            self::$_doCommit = true;
        }
    }

    static public function rollbackIfFalse( $flag ) {
        if ( $flag === false ) {
            self::$_doCommit = false;
        }
    }

    static public function rollback( ) {
        self::$_doCommit = false;
    }
    
    static public function willCommit( ) {
        return self::$_doCommit;
    }

}

?>
