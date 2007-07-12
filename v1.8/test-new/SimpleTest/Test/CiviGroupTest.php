<?php

abstract class CiviGroupTest extends GroupTest {


    // collects test files from given dir
    function addTestDirectory( $dir ) {

        $files = scandir( $dir );
        
        foreach ( $files as $file ) {
            // shouldn't be a directory or a backup file...
            if ( !is_dir($file) and substr($file, -1) != '~' and substr($file, -4) != '.swp' ) {
                 $this->addTestFile( "$dir/$file" );
            }        
        }

    }
}

?>
