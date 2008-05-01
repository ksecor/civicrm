<?php

class CiviTestCase extends DrupalTestCase {

    function __construct( ) {
        parent::__construct( );

        civicrm_initialize( );

        require_once 'CRM/Core/Config.php';
        $config =& CRM_Core_Config::singleton( );
    }

    function civiGet( $path, $params, $abort = false ) {
        $url = CRM_Utils_System::url( $path, $params );
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

    function recordAssertNotNull(  $daoName, $id, $fieldName, $idName, $message  ) 
    {
        $value = CRM_Core_DAO::getFieldValue( $daoName, $id, $fieldName, $idName );
        $this->assertNotNull(  $value, $message );
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
}
