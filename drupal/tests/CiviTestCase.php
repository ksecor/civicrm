<?php

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

    function DBAssertNotNull(  $daoName, $id, $fieldName, $idName, $message  ) 
    {
        $value = CRM_Core_DAO::getFieldValue( $daoName, $id, $fieldName, $idName );
        $this->assertNotNull(  $value, $message );
        
        return $value;
    }

    function DBAssertNull(  $daoName, $id, $fieldName, $idName, $message  ) 
    {
        $value = CRM_Core_DAO::getFieldValue( $daoName, $id, $fieldName, $idName );
        $this->assertNull(  $value, $message );
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

    function allPermissions( ) {
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
}
