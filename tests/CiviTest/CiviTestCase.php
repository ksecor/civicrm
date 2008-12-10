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

class CiviTestCase extends DrupalTestCase 
{

    function __construct( ) 
    {
        parent::__construct( );

        civicrm_initialize( );

        require_once 'CRM/Core/Config.php';
        $config =& CRM_Core_Config::singleton( );
    }

    function civiGet( $path, $params, $abort = false ) {
        $url = CRM_Utils_System::url( $path, $params, true, null, false );

        return $this->civiGetURL( $url, $abort );
    }

    function civiGetURL( $url, $abort = false ) {
        $html = $this->_browser->get($url);
        
        if ($this->drupalCheckAuth(true)) {
            $html .= $this->drupalCheckAuth();
        }

        $this->_content = $this->_browser->getContent();

        if ( $abort ) {
            echo $html;
            exit( );
        }

        return $html;
    }

    // Request a record from the DB by seachColumn+searchValue. Success if a record is found. 
    function assertDBNotNull(  $daoName, $searchValue, $returnColumn, $searchColumn, $message  ) 
    {
        $value = CRM_Core_DAO::getFieldValue( $daoName, $searchValue, $returnColumn, $searchColumn );
        $this->assertNotNull(  $value, $message );
        
        return $value;
    }

    // Request a record from the DB by seachColumn+searchValue. Success if NO record is found. 
    function assertDBNull(  $daoName, $searchValue, $returnColumn, $searchColumn, $message  ) 
    {
        $value = CRM_Core_DAO::getFieldValue( $daoName, $searchValue, $returnColumn, $searchColumn );
        $this->assertNull(  $value, $message );
    }

    // Compare a single column value in a retrieved DB record to an expected value
    function assertDBCompareValue(  $daoName, $searchValue, $returnColumn, $searchColumn,
                                    $expectedValue, $message  ) 
    {
        $value = CRM_Core_DAO::getFieldValue( $daoName, $searchValue, $returnColumn, $searchColumn );
        $this->assertEqual(  $value, $expectedValue, $message );
    }

    // Compare all values in a single retrieved DB record to an array of expected values
    function assertDBCompareValues( $daoName, $searchParams, $expectedValues )  
    {
        //get the values from db 
        $dbValues = array( );
        CRM_Core_DAO::commonRetrieve( $daoName, $searchParams, $dbValues );
        
        // compare db values with expected values
        self::assertAttributesEqual( $expectedValues, $dbValues);
    }
    
    function assertAttributesEqual( &$expectedValues, &$actualValues ) 
    {
        foreach( $expectedValues as $paramName => $paramValue ) {
            if ( isset( $actualValues[$paramName] ) ) {
                $this->assertEqual( $paramValue, $actualValues[$paramName] );
            } else {
                $this->fail( "Attribute $paramName not present in actual array." );
            }
        }        
    }
    
    function assertArrayKeyExists( $key, &$list ) {
        $result = isset( $list[$key] ) ? true : false;
        $this->assertTrue( $result, ts( "%1 element exists?",
                                        array( 1 => $key ) ) );
    }

    function assertArrayValueNotNull( $key, &$list ) {
        $this->assertArrayKeyExists( $key, $list );

        $value = isset( $list[$key] ) ? $list[$key] : null;
        $this->assertTrue( $value,
                           ts( "%1 element not null?",
                               array( 1 => $key ) ) );
    }

    function getUrlsByLabel($label, $fuzzy = false) {
        if ( ! $fuzzy ) {
            return $this->_browser->_page->getUrlsByLabel( $label );
        }

        $matches = array();
        foreach ($this->_browser->_page->_links as $link) {
            $text = $link->getText();
            if ( $text == $label ||
                 strpos( $text, $label ) !== false ) {
                $matches[] = $this->_browser->_page->_getUrlFromLink($link);
            }
        }
        return $matches;
    }

    function isCiviURL( $url, $ignoreVariations = true ) {
        static $config = null;
        if ( ! $config ) {
            $config =& CRM_Core_Config::singleton( );
        }
        
        if ( strpos( $url,
                     $config->userFrameworkBaseURL . 'civicrm/' ) === false ) {
            return false;
        }
        
        // ignore all urls with snippet, force, crmSID
        if ( $ignoreVariations &&
             ( strpos( $url, 'snippet=' ) ||
               strpos( $url, 'force='   ) ||
               strpos( $url, 'crmSID='  ) ) ) {
            return false;
        }
        
        return true;
    }

    function getUrlsByToken( $token, $path = null ) {
        $matches = array();
        foreach ($this->_browser->_page->_links as $link) {
            $text = $link->getText();
            $url  = $this->_browser->_page->_getUrlFromLink($link)->asString( );
            if ( $this->isCiviURL( $url ) &&
                 ( strpos( $url, $token ) !== false ) ) {
                if ( ! $path ||
                     strpos( $url, $path ) !== false ) {
                    $matches[$text] = $url;
                }
            }
        }
        return $matches;
    }

    function clickLink($label, $index = 0, $fuzzy = false) {
        if ( ! $fuzzy ) {
            return parent::clickLink( $label, $index );
        } 

        $url_before = str_replace('%', '%%', $this->getUrl());
        $urls = $this->getUrlsByLabel($label, true);
        if (count($urls) < $index + 1) {
            $url_target = 'URL NOT FOUND!';
        } else {
            $url_target = str_replace('%', '%%', $urls[$index]->asString());
        }

        $this->_browser->_load( $urls[$index], new SimpleGetEncoding( ) );
        $ret = $this->_failOnError( $this->_browser->getContent( ) );
        
        $this->assertTrue($ret, ' [browser] clicked link '. t($label) . " ($url_target) from $url_before");
        
        return $ret;
    }

    function allPermissions( ) 
    {
        return array(
                     1 => 'add contacts'               ,
                     2 => 'view all contacts'          ,
                     3 => 'edit all contacts'          ,
                     4 => 'import contacts'            ,
                     5 => 'edit groups'                ,
                     6 => 'administer CiviCRM'         ,
                     7 => 'access uploaded files'      ,
                     8 => 'profile listings and forms' ,
                     9 => 'access all custom data'     ,
                     10 => 'view all activities'        ,
                     11 => 'access CiviCRM'             ,
                     12 => 'access Contact Dashboard'   ,
                     );
    }

    function errorPage( &$ret, &$url ) {
        // check if there is a civicrm error or warning message on the page
        // at a later stage, we should also check for CMS based errors
        $this->assertTrue($ret, ts(' [browser] GET %1"', array('%1' => $url)));

        $this->assertNoText( 'Sorry. A non-recoverable error has occurred', '[browser] fatal error page?' );
        $this->assertNoText( 'The requested page could not be found', '[browser] page not found?' );
        $this->assertNoText( 'You are not authorized to access this page', '[browser] permission denied?' );
        
        return;
    }

}
