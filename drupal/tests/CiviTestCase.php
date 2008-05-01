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

}
