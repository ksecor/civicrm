<?php

$homeDir = $GLOBALS['_ENV']['HOME'];

require_once "$homeDir/svn/crm/modules/config.inc.php";
require_once 'CRM/Core/Error.php';
require_once 'PHP/Beautifier.php';

function createDir( $dir, $perm = 0755 ) {
    if ( ! is_dir( $dir ) ) {
        mkdir( $dir, $perm, true );
    }
}


class PHP_DownGrade
{
    public $tokens = array( );
    
    function __construct($file)
    {
       
        $this->tokens= token_get_all(file_get_contents($file));
        // print_r($this->tokens); 

          foreach(array_keys($this->tokens) as $i) {
         
              if (is_string($this->tokens[$i])) {
                  $this->tokens[$i] = array(ord($this->tokens[$i]) ,$this->tokens[$i] );
              }
          }
       
    }


    function convert()
    {
        $ret ="";
        for($i=0;$i<count($this->tokens);$i++){
            if($this->tokens[$i][1]=="CRM_Core_SelectValues"){
                $j=$i+1;
                $count=2;
                $str="CRM_Core_SelectValues";
                while($count>0){
                    $str.=$this->tokens[$j][1];
                    $j++;
                    $count--;
                }
               
                $pattern1 = '/(CRM_Core_SelectValues)::\$(\w+)/';
                $replacement1 = "\$GLOBALS['_CRM_CORE_SELECTVALUES']['\$2']";
                
             
                
                
                $this->tokens[$i][1]= preg_replace($pattern1, $replacement1, $str);
                $i++;
                $this->tokens[$i][1]="";
                $i++;
                $this->tokens[$i][1]="";
               
            
            }
        }
        for($i=0;$i<count($this->tokens);$i++){
         $ret.= $this->tokens[$i][1];
          }
       
       return $ret;
       
    }
    
}


$directory = array('CRM', 'modules', 'api');
//$directory = array('api');

foreach ($directory as $v) {
    $rootDir = "$homeDir/svn/crm/php4/$v";
    $destDir = "$homeDir/svn/crm/php4/$v";

    $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootDir), true);
    foreach ( $dir as $file ) {
        if ( substr( $file, -4, 4 ) == '.php' ) {
            echo str_repeat("--", $dir->getDepth()) . ' ' . $file->getPath( ) . " $file\n";
            $x    = new PHP_DownGrade($file->getPath( ) . '/' . $file);
            $php4 = $x->convert( );
            
            $php4Dir  = str_replace( $rootDir, $destDir, $file->getPath( ) );
            createDir( $php4Dir );
            $fd   = fopen( $php4Dir . '/' . $file, "w" );
            fputs( $fd, $php4 );
            fclose( $fd );
        }
    }
}




//$sam = new PHP_DownGrade($argv[1]);
//echo $sam->convert();
    //echo $sam->toPHP4();
    
?>