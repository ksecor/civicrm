<?php

abstract class CiviGroupTest extends GroupTest {


    // collects test files from given dir
    function addTestDirectory( $dir ) {

        $files = scandir( $dir );
        
        foreach ( $files as $file ) {
            // shouldn't be directory or backup file...
            if ( !is_dir($file) && $file[strlen($file)-1] !== '~' ) { 
                 $this->addTestFile( "$dir/$file" );
            }        
        }

    }
}

?>
