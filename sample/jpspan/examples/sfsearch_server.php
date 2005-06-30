<?php
// $Id: sfsearch_server.php,v 1.4 2004/11/23 14:10:56 harryf Exp $
/**
* This is a remote script to call from Javascript
*/

require_once '../JPSpan.php';
require_once JPSPAN . 'Serializer.php';
require_once JPSPAN . 'Server/PostOffice.php';
//-----------------------------------------------------------------------------------
// Define a PHP class...
class ProjectList {
    var $projects;
    function ProjectList($projects = array()) {
        $this->projects = $projects;
    }
}
//-----------------------------------------------------------------------------------
// Define a class which generates Javascript when instances of ProjectList are
// encountered...
class ProjectListGenerator extends JPSpan_SerializedElement {

    function generate(& $code) {
        // Notice this generates client-side behaviour
        $func = "htmlElement.innerHTML = '';";
        $func .="var projects = new Array();";
        foreach ( $this->value->projects as $key => $project ) {
            $func .="projects[$key] = '$project';";
        }
        $func .="for (var i=0; i < projects.length; i++ ) {";
        $func .="htmlElement.innerHTML += '<a href=http://sf.net/projects/'+projects[i]+'>'+projects[i]+'</a><br>';";
        $func .="}";
        $code->append('var '.$this->tmpName.' = new Function("htmlElement","'.addslashes($func).'");');
    }
    
}

//-----------------------------------------------------------------------------------
// Register the class and it's serializer with the JPSpan_Serializer
JPSpan_Serializer::addType('ProjectList','ProjectListGenerator');

//-----------------------------------------------------------------------------------
// A class to publish for remote calls...
class SfSearch {

    function getProjects($fragment='') {
        if ( !preg_match('/^[a-z].*$/',$fragment) ) {
            $fragment = '';
        }
        $base = '/home/groups/';

        $fraglen = strlen($fragment);
        
        if ( $fraglen >= 2 ) {
            $groupDir = $base . substr($fragment,0,1).'/'.substr($fragment,0,2).'/';
        } else if ( $fraglen == 1 ) {
            $groupDir = $base . $fragment . '/'. $fragment . 'a/';
        } else {
            $groupDir = $base . 'a/aa/';
        }

        $dh = opendir($groupDir);
        
        $projects = array();
        
        while (($node = readdir($dh)) !== false) {
            if ( $node == '.' || $node == '..' ) {
                continue;
            }
            if ( is_dir($groupDir.$node) ) {
                $projects[] = $node;
            }
        }
        
        for ( $i = $fraglen; $i > 0; $i-- ) {
            $matches = preg_grep('/^'.substr($fragment,0,$i).'/i',$projects);
            if ( count($matches) > 0 ) {
                return new ProjectList(array_slice($matches,0,10));
            }
        }

        return new ProjectList();
    }

}

$S = & new JPSpan_Server_PostOffice();
$S->encoding = 'php'; // Only needs ASCII
$S->addHandler(new SfSearch());

//-----------------------------------------------------------------------------------
if (isset($_SERVER['QUERY_STRING']) && strcasecmp($_SERVER['QUERY_STRING'], 'client')==0) {

    // Compress the Javascript
    define('JPSPAN_INCLUDE_COMPRESS',TRUE);
    $S->displayClient();

} else {

    // Include error handler - PHP errors, warnings and notices serialized to JS
    require_once JPSPAN . 'ErrorHandler.php';
    $S->serve();

}
?>
