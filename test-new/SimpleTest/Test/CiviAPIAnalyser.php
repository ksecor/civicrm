<?php

require_once '../../../civicrm.config.php';
require_once 'CRM/Core/Config.php';


class APImethod {

    public $name = null;
    public $isPrivate = null;
    public $isByReference = null;
    
    function __construct( $rawName ) {

        $this->isByReference = ( ( strpos($rawName, '&') === FALSE ) ? 0 : 1 );
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

    function getAPIGroups() {
        $a = array();
        foreach( $this->api as $apiGroup ) {
            $a[] = $apiGroup;
        }
        return $a;
    }
    
    function getAllAPIMethods() {
        $m = array();
        foreach( $this->getAPIGroups() as $group ) {
            foreach( $group->methods as $method ) {
            $m[] = $method;
            }
        }
        return $m;
    }

}

class APITestGenerator {

    private $_analyser = null;
    private $_targetDir = null;

    public function __construct( $analyser) {
        $this->_analyser = $analyser;
        $this->_targetDir = '/tmp';
    }

    private function _getTestcaseName( $method ) {
                $testcaseName = '';

                // private methods get different prefix
                // leading underscore will disappear on
                // explode a few lines below
                if( $method->isPrivate ) {
                    $testcaseName .= "testPrivate";
                } else {
                    $testcaseName .= "testPublic";
                }

                // uppercase words in function name
                $n = explode( '_', $method->name);
                foreach( $n as $key => $namePart ) {
                    $n[$key] = ucwords( $namePart);
                }

                // merge
                $testcaseName .= implode( $n );

                return $testcaseName;
    }
    
    
    private function _createTestFile( $testcaseName ) {
        $testFilename = $testcaseName . '.php';
        $fullpath = $this->_targetDir . DIRECTORY_SEPARATOR . $testFilename;
        if( file_exists( $fullpath ) === TRUE ) {
            return FALSE;
        } else {
            if( ( $handle = fopen( $fullpath, "x" ) ) === FALSE ) {
                trigger_error("Cannot open file for writing: $fullpath ", E_USER_ERROR );
            } else {
                fwrite( $handle, $this->_getTestcaseTemplate( $testcaseName ) );
                fclose( $handle );
                return TRUE;
            }
        }

    }


    private function _getTestcaseTemplate( $testcaseName ) {
        return "TBD - doing $testcaseName";
    }

    public function setTargetDir( $dir ) {
        if ( file_exists( $dir ) === TRUE ) {
            $this->_targetDir = $dir;
        } else {
            trigger_error("Target directory ($dir) doesn't exist", E_USER_ERROR );
        }
    }

    public function getTargetDir() {
        return $this->_targetDir;
    }

    public function buildTests() {
        foreach( $this->_analyser->getAllAPIMethods() as $method ) {
            $tn = $this->_getTestcaseName( $method );
            if ( $this->_createTestFile( $tn ) ) {
                echo "CREATED $tn.php\n";                
            } else {
                echo "Skipping $tn.php - file exists\n";
            }
        }
    }

}

$a = new APIAnalyser( '/home/mover/Work/CiviCRM/trunk/api/v2' );
$t = new APITestGenerator( $a );
$t->setTargetDir('/tmp/tests/');
$t->buildTests();


?>