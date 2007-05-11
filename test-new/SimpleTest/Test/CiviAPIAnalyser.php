<?php

require_once '../../../civicrm.config.php';
require_once 'CRM/Core/Config.php';


class APImethod {

    public $name = null;
    public $isPrivate = null;
    public $isByReference = null;
    
    function __construct( $rawName ) {

        $this->isByReference = ( ( strpos($rawName, '&') === false ) ? 0 : 1 );
        // trimming whitespace should have happend in _getAPIMethodsFromFile
        // but I'm not a big fan of regexps and didn't know how to do it
        $rawName = trim( $rawName );
        $this->name = ltrim( $rawName, "&" );
        // after reference marker is trimmed, we can check if underscore
        // is the first character
        $this->isPrivate = ( ( $this->name[0] !== '_' ) ? 0 : 1 );
    }
    
    public function __toString() {
        return 'CiviCRM API Method: ' . $this->name . ' (' . 
        ( ($this->isPrivate ) ? 'private' : 'public' ) . ', ' . 
        ( ($this->isByReference ) ? 'by reference' : 'direct' ) . ")\n";
    }
    
}

class APIGroup {

    public $name = null;
    public $methods = array();
    
    function __construct( $fullpath ) {
        $n = pathinfo( $fullpath );
        $this->name =  rtrim($n['basename'], '.php');
        foreach( $this->_getAPIMethodsFromFile( $fullpath ) as $dontCate => $method ) {
            $this->methods[] = new APIMethod( $method );
        }
    }
    
    private function _getAPIMethodsFromFile( $fullpath ) {
        $handle = fopen( $fullpath, "r" );
        $contents = fread( $handle, filesize( $fullpath ) );
        fclose( $handle );
        preg_match_all( '@(?<=function)[ ]+[&]?[_]?civicrm_[a-z_]+@' , $contents, $functions );
        return $functions[0];
    }
    
    public function getMethodNames() {
        $a = array();
        foreach( $this->methods as $method ) {
            $a[] = $method->name;
        }
        return $a;
    }    

    public function __toString() {
        return 'CiviCRM API Group: ' . $this->name . ' (containing ' . count($this->methods) . " methods) \n"; 
    }

}



class APIAnalyser {

    public $api = array();

    function __construct( $apiDir ) {
        foreach( $this->_getAPIFiles( $apiDir ) as $key => $file ) {
            $this->api[] = new APIGroup( $apiDir . DIRECTORY_SEPARATOR . $file );
        }
    }

    private function _isAPIFile( $name ) { 
        return ( strtoupper( $name[0] ) === $name[0] && $name[0] !== '.');
    }

    private function _getAPIFiles( $apiDir ) {
        $_apiFiles = array();
        foreach ( scandir( $apiDir ) as $key => $file ) {
            if ( $this->_isAPIFile( $file ) ) {
                $_apiFiles[] = $file;
            }
        }
        return $_apiFiles;
    }

    public function getAPI() {
        return $this->api;
    }
    
    function getAPIGroupNames() {
        $a = array();
        foreach ( $this->api as $apiGroup ) {
            $a[] = $apiGroup->name;
        }
        return $a;
    }
    
}




class APITestGenerator {

    function CiviAPIAnalyser() {
    }


    function _getAPIFiles() {
        
    }

    function getAPI() {
    }
    

}

$a = new APIAnalyser( '/home/mover/Work/CiviCRM/trunk/api/v2' );
foreach( $a->api as $group ) {
    echo $group;
    foreach( $group->methods as $method ) {
        echo $method;
    }
}


?>