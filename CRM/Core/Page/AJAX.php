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

require_once 'CRM/Core/Page.php';

/**
 * This class contains all the function that are called using AJAX (dojo)
 */
class CRM_Core_Page_AJAX extends CRM_Core_Page 
{
    /**
     * Run the page
     */
    function run( &$args ) 
    {
        $this->invoke( $args );
        exit( );
    }
    
    /**
     * Invoke function that redirects to respective functions
     */
    function invoke( &$args ) 
    {
        // intialize the system
        $config =& CRM_Core_Config::singleton( );
        
        if ( $args[0] != 'civicrm' && $args[1] != 'ajax' ) {
            exit( );
        }
        
        switch ( $args[2] ) {


        // civicrm/ajax/           
        case 'mapper':
            require_once 'CRM/Core/Page/AJAX/Mapper.php';
            $method = array( 'CRM_Core_Page_AJAX_Mapper',
                             $args[3] );

            if ( is_callable( $method ) ) {
                return eval( "return CRM_Core_Page_AJAX_Mapper::{$args[3]}( " . ' $config ); ' );
            }
            exit( );

        default:
	  return;
	}
    }




}
