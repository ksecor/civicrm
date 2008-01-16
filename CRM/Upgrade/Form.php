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

require_once 'CRM/Core/Form.php';

class CRM_Upgrade_Form extends CRM_Core_Form {

    protected $_config;

    function __construct( $state = null,
                          $action = CRM_Core_Action::NONE,
                          $method = 'post',
                          $name = null ) {
        $this->_config =& CRM_Core_Config::singleton( );

        parent::__construct( $state, $action, $method, $name );
    }

    function checkSQLConstraints( &$constraints ) {
        $pass = $fail = 0;
        foreach ( $constraints as $constraint ) {
            if ( $this->checkSQLConstraint( $constraint ) ) {
                $pass++;
            } else {
                $fail++;
            }
            return array( $pass, $fail );
        }
    }
    
    function checkSQLConstraint( $constraint ) {
        // check constraint here
        return true;
    }
    
    function source( $fileName ) {
        require_once 'CRM/Utils/File.php';
        
        //$dsn = str_replace("civicrm_cms", "civicrm", $this->_config->dsn);
        CRM_Utils_File::sourceSQLFile( $this->_config->dsn,
                                       $fileName );
    }
    
    function preProcess( ) {
        $this->verifyPreDBState( );
    }
    
    function buildQuickForm( ) {
        $this->addDefaultButtons( $this->getButtonTitle( ),
                                  'next',
                                  null,
                                  true );
    }
    
    function getTitle( ) {
        return ts( 'Title not Set' );
    }
    
    function getButtonTitle( ) {
        return ts( 'Continue' );
    }
    
    function getTemplateFileName( ) {
        $this->assign( 'title',
                       $this->getTitle( ) );
        $this->assign( 'message',
                       $this->getTemplateMessage( ) );
        return 'CRM/Upgrade/Base.tpl';
    }
    
    function postProcess( ) {
        $this->upgrade( );
        
        $this->verifyPostDBState( );
    }

    function runQuery( $query ) {
        //$dsn = str_replace("civicrm_cms", "civicrm", $this->_config->dsn);
        $db = DB::connect( $this->_config->dsn );

        if ( DB::isError( $db ) ) { 
            die( "Cannot connect to db via $dsn, " . $db->getMessage( ) ); 
        }
        
        $res = $db->query( $query );

        $db->disconnect( );

        return $res;
    }
}

?>
