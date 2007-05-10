<?php

require_once '../../../civicrm.config.php';
require_once 'CRM/Core/Config.php';


class APImethod {

    public $name = null;
    public $isPrivate = null;
    public $isByReference = null;
    
    
    // FIXME: remove reference mark from names
    function __construct( $rawName ) {
        $this->name = $rawName;
        $this->isPrivate = ( ( strpos($rawName, '_') === false ) ? 0 : 1 );
        $this->isByReference = ( ( strpos($rawName, '&') === false ) ? 0 : 1 );
    }
    
}


class APIAnalyser {

    private $_apiStructure = array();
    
    private $_rawAPIStructure = array();
    
    private $_apiFiles = array();

    // FIXME: make this more generic at some stage    
    private $_apiDir = '/home/mover/Work/CiviCRM/trunk/api/v2';
    
    function __construct() {
        $this->_setAPIFiles();
        foreach( $this->_apiFiles as $key => $file ) {
            $this->_rawAPIStructure[$file] = $this->_getAPIMethodsFromFile( $this->_apiDir . DIRECTORY_SEPARATOR . $file );
        }
        $this->_prepareAPIStructure();
    }

    private function _isAPIFile( $name ) { 
        return ( strtoupper( $name[0] ) === $name[0] && $name[0] !== '.');
    }

    private function _setAPIFiles() {
        foreach ( scandir( $this->_apiDir ) as $key => $file ) {
            if ( $this->_isAPIFile( $file ) ) {
                $this->_apiFiles[] = $file;
            }
        }
    }

    private function _getAPIMethodsFromFile( $fullpath ) {
        $handle = fopen( $fullpath, "r" );
        $contents = fread( $handle, filesize( $fullpath ) );
        fclose( $handle );
        preg_match_all( '@(?<=function)[ ]+[&]?[_]?civicrm_[a-z_]+@' , $contents, $functions );
        return $functions[0];
    }
                                                        

    // FIXME: strip .php from group names
    private function _prepareAPIStructure() {
        foreach ( $this->_rawAPIStructure as $key => $group ) {
            $this->_apiStructure[ $key ] = array();
            foreach ( $group as $dontCare => $method ) {
                $this->_apiStructure[ $key ][] = new APIMethod( $method );
            }
        }
    }

    function getAPI() {
      return $this->_apiStructure;
    }
    
    function getAPIGroups() {
      return array_keys($this->_apiStructure);
    }
    
    function getAPIGroupMethods() {
        // TBD
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

$a = new APIAnalyser();
print_r( $a->getAPI() );


?>







