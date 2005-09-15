<?php

if( isset( $GLOBALS['_SERVER']['DM_SOURCEDIR'] ) ) {
    $sourceCheckoutDir = $GLOBALS['_SERVER']['DM_SOURCEDIR'];
} else {
    // backward compatibility
    $sourceCheckoutDir = $GLOBALS['_SERVER']['HOME'] . '/svn/crm';
}

if( isset( $GLOBALS['_SERVER']['DM_GENFILESDIR'] ) ) {
    $targetDir = $GLOBALS['_SERVER']['DM_GENFILESDIR'];
} else {
    // backward compatibility
    $targetDir = $GLOBALS['_SERVER']['HOME'] . '/svn/civicrm';
}

require_once "$sourceCheckoutDir/modules/config.inc.php";
require_once 'PHP/Beautifier.php';

 /**
  * This function creates destination directory
  *
  * @param $dir directory name to be created
  * @param $peram mode for that directory
  *  
  */
function createDir( $dir, $perm = 0755 ) {
    if ( ! is_dir( $dir ) ) {
        echo "Outdir: $dir\n";
        mkdir( $dir, $perm, true );
    }
}


class PHP_DownGrade {
    
    public $tokens = array();
    
    const T_SELF = 999;
    
    /**
     * This constructor creates array of tokens.
     * Here tokens form files are separated and stored in array $this->tokens
     *  
     * @param $file 
     */

    function __construct($file)
    {

        $this->constants = array( );
        $this->statics   = array( );
       
        $this->argcount  = array( );
        $this->tokens = token_get_all(file_get_contents($file));
      
        $this->staticcount=0;
        
        $this->statvar    = array( );
        $this->filenames  = array( );
        $this->classarray = array( );
        $this->filecount  = 0;
        $this->classcount = 0;

       
        foreach(array_keys($this->tokens) as $i) {
         
            if (is_string($this->tokens[$i])) {
                $this->tokens[$i] = array(ord($this->tokens[$i]) ,$this->tokens[$i] );
            }
            if (($this->tokens[$i][0] == T_STRING) && ($this->tokens[$i][1] == 'self')) {
                $this->tokens[$i][0] = self::T_SELF;
            }
        }


        //To find wich file to be need for require once.
        $flag = 0;
        for($j=0;$j<count($this->tokens);$j++)
            {
               
                if(strcmp($this->tokens[$j][1],"::")==0)
                    {
                        $back=$j;
                        while($this->tokens[$back][0]=="")
                            $back--;
                        
                        $back--;
                        for($f=0;$f<count($this->filenames);$f++)
                            {
                                if(strcmp($this->tokens[$back][1],$this->filenames[$f])==0)
                                   $flag = 1;
                            }
                        
                        if($flag==0)
                            {
                               
                                if(strcmp($this->tokens[$back][1],"parent")!=0)
                                    { 
                                        if ( $this->tokens[$back][1] != 'CRM_Core_DAO' ) {
                                            $this->filenames[$this->filecount]=$this->tokens[$back][1];
                                            $this->filecount++;
                                        }
                                    }
                            }
                        $flag = 0;
                    }
                
                
                if(strcmp($this->tokens[$j][1],"extends")==0 ||strcmp($this->tokens[$j][1],"new")==0)
                    {
                        $back=$j;
                        while($this->tokens[$back][0]!=T_STRING)
                            $back++;
                        
                       
                        for($f=0;$f<count($this->filenames);$f++)
                            {
                                if(strcmp($this->tokens[$back][1],$this->filenames[$f])==0)
                                    $flag = 1;
                            }
                        
                        if($flag==0)
                            {
                                if ( $this->tokens[$back][1] != 'CRM_Core_DAO' ) {
                                    $this->filenames[$this->filecount]=$this->tokens[$back][1];
                                    $this->filecount++;
                                }
                                
                            }
                        $flag = 0;
                    }

                
                
            }

        
    }
    
      
    /**
     * This method calls other methods in class.
     * 
     * @return  none
     */
    
    function toPHP4() {
        $this->findStart();
        $this->classdef();
        $this->funcdefs(); 
        
        $this->exceptions();
        // $this->dereferences();
        
        //$this->cloning();
        return $this->toString();
   }
    
    
    /**
     * Where is the start of the code (ignoring comments etc..)
     *
     * @return   none
     * @access   public
     */
  
    function findStart()
    {
        for($i=0;$i<count($this->tokens);$i++) {
            
            switch ($this->tokens[$i][0]) {
            case T_COMMENT:
            case T_WHITESPACE:
            case T_OPEN_TAG:
            case T_INLINE_HTML:
            case T_DOC_COMMENT:
                continue;
            default:
                    
                $this->start = $i;
                return;
            }
        }
    }
    /**
     * This method replaces all constants by define, statics by GLOBALS,
     * removes public, private, protected keywords
     * and replaces self varialbls with proper variable names 
     *
     * @return   none
     */
    
    function classdef() 
    {
        
        for($i=0;$i<count($this->tokens);$i++) {
            // Remove the final keywaord
            if(strcmp($this->tokens[$i][1],"final")==0)
                $this->tokens[$i][1]="";

            // Remove implement keyword
            if(strcmp($this->tokens[$i][1],"implements")==0)
                while(strcmp($this->tokens[$i][1],"{")!=0)
                    {
                        $this->tokens[$i][1]="";
                        $i++;
                    }

            switch ($this->tokens[$i][0]) {
            case T_CLASS:
                $i++;
                while($this->tokens[$i][0] != T_STRING) {
                    $i++;
                }
                $class = $this->tokens[$i][1];
               
                $i++;
                break;
            
            case T_CONST:
                // look for ;
                $this->tokens[$i][1] = '';
                $ii = $i+1;
                while($this->tokens[$i][1] != ';') {
                    while($this->tokens[$i][0] != T_STRING) {
                        $i++;
                    }
                    $const = $this->tokens[$i][1];
                    $this->tokens[$i][1] = '';
                    $i++;
                    while($this->tokens[$i][1] != '=') {
                        $i++;
                    }
                    $this->tokens[$i][1] = '';
                    $i++;
                    
                    $value = '';
                    while($this->tokens[$i][0] != T_CONSTANT_ENCAPSED_STRING && $this->tokens[$i][0] != T_LNUMBER) {
                        $i++;
                    }
                    $value = $this->tokens[$i][1];
                    $this->constants[$class][$const] = $value;
                    $this->tokens[$i][1] = '';
                    $i++;

                    while($this->tokens[$i][1] != ',' && $this->tokens[$i][1] != ';') {
                        $i++;
                    }
                    if ( $this->tokens[$i][1] == ';' ) {
                        $this->tokens[$i][1] = '';
                        $i++;
                        break;
                    } else {
                        $this->tokens[$i][1] = '';
                        $i++;
                    }

                }
                $this->tokens[$i][1] = '';
                $i++;
                break;

            
            case T_PRIVATE: 
            case T_PUBLIC:
            case T_PROTECTED:
            case T_STATIC:
               
                $start = $i;
                // what can follow :
                // T_STATIC  = static var..
                // T_VARIABLE = var definition.
                // T_FUNCTION 
               
                $static = false;
                while($i) {
                    switch($this->tokens[$i][0]) {
                    case T_STATIC:
                        $static = $i;
                        if(isset($class))
                        $this->tokens[$i][1] = ''; // strip statics..
                        break;
                    case T_VARIABLE:
                        if ($static) {
                            // we need to strip it out totally and use $GLOBALS[_classname][...]
                            if(isset($class))
                                {
                                    $i= $this->convertToStatic($class,$i);
                                    for($ii = $start;$ii<$i+1;$ii++) {
                                       
                                         $this->tokens[$ii][1] = ''; 
                                  
                                      }
                                     $this->tokens[$static][1] = '';
                                }
                           
                            break 3;
                        }
                        $this->tokens[$start][1] = 'var'; // change it to var.
                        break 3;
                    case T_FUNCTION:
                        if ($static) {
                            $this->tokens[$static][1] = ''; 
                        }
                        $this->tokens[$start  ][1] = ''; // change it to var.
                        break 3;
                    }
                    // hopefully we won't loop forever!
                    $i++;
                }
                
            case self::T_SELF:
                $this->tokens[$i][1] = $class;
                $start = $i;
                $i++;
                while($this->tokens[$i][1] != '::') {
                    $i++;
                }
                while($i++) {
                    switch($this->tokens[$i][0]) {
                    case T_VARIABLE:
                        $this->tokens[$start][1] = 
                            '$GLOBALS[\'_'.strtoupper($class).'\'][\''.
                            substr($this->tokens[$i][1],1).'\']';
                        for($ii = $start+1;$ii<$i+1;$ii++) {
                            $this->tokens[$ii][1] = ''; 
                        }
                        break 3;
                    case T_STRING:
                        while($i++) {
                            switch($this->tokens[$i][0]) {
                            case ord('('):
                                // got a function!
                                break 5;
                            case T_WHITESPACE:
                                break;
                            default:
                                // got a constant:
                                for ( $ii=$i-1; $ii >= 0; $ii-- ) {
                                    if ( $this->tokens[$ii][0] != T_WHITESPACE ) {
                                        break;
                                    }
                                }

                                $this->tokens[$start][1] = 
                                    strtoupper($class).'_'.
                                    $this->tokens[$ii][1];
                                                
                                for($ii = $start+1;$ii<$i;$ii++) {
                                    $this->tokens[$ii][1] = ''; 
                                }    
                                                
                                break 5;
                            }
                        }
                    default:
                        break 2;
                    }
                }
                
                $this->tokens[$i][1];
            }
          
        }
        
    }
    
    /**
     * This method constructs an array of static variables, with the class names and values.
     * 
     * @param $class class name in which this static variable resides
     * @param $i     static variables position in tokens array
     */
    function convertToStatic($class,$i) 
    {

        $name  = substr($this->tokens[$i][1],1);
        
        $i++;
        while($this->tokens[$i][1] != '=') {
            if ($this->tokens[$i][1] == ';') {
                $this->statics[$class][$name] = "null";
                return $i;
            }
            $i++;
        }
        
        $i++;
        
        $value = '';
        // echo $class;
         while($this->tokens[$i][1] != ';') {
              
            
              if($this->tokens[$i+1][1] == "::") {
                 $this->tokens[$i][1] = strtoupper($this->tokens[$i][1]);  
             }
             
             if($this->tokens[$i][0] == self::T_SELF) {
                 $this->tokens[$i][1] = strtoupper($class);
             } 
             if($this->tokens[$i][1] == "::") {
                 
                  $this->tokens[$i][1] = "_";
             }
             
             $value .= $this->tokens[$i][1];
             $this->tokens[$i][1] = '';
            
             $i++;
         }
         $this->statics[$class][$name] = $value;
         $this->statvar[$this->staticcount]=$name;
         $this->staticcount++;
     
         
      
        return $i;
    
    }
    /**
     * This function handles removing of static variables, final keyword
     * constructor part converesion
     * 
     * @return  none
     */
    
    function funcdefs() {

        for($i=0;$i<count($this->tokens);$i++) {

            //to remove static keyword in front of function
            if(strcmp($this->tokens[$i][1],"static")==0)
                {
                    
                    $j=$i+1;
                    while($this->tokens[$j][0] ==T_WHITESPACE) 
                        $j++;
                   
                   
                    
                    if(strcmp($this->tokens[$j][1],"function")==0)
                        $this->tokens[$i][1]="";
                        
                
                }
            //to remove final keyword.
            if(strcmp($this->tokens[$i][1],"final")==0)
                $this->tokens[$i][1]="";
                
            switch ($this->tokens[$i][0]) {
            case T_CLASS:
                $i++;
                while($this->tokens[$i][0] != T_STRING) {
                    $i++;
                }
                $class = $this->tokens[$i][1];
                $this->classarray[$class] = 1;
                $i++;
                break;

                case 373:
                // make sure the previous and next tokens are strings
                if ( $this->tokens[$i-1][0] == T_STRING && $this->tokens[$i+1][0] == T_STRING ) {
                    // make sure the following token are not open paran and hence a function call
                    $func = false;
                    $flag = 0;
                    for ( $ii = $i+2; $ii < $i + 4; $ii++ ) {
                        if ( $this->tokens[$ii][1] == '(' ) {
                            $func = true;
                            break;
                        } else if ( $this->tokens[$ii][1] == ')' || $this->tokens[$ii][1] == ',' ) {
                            break;
                        }
                    }
                    if ( $func ) {
                        break;
                    }
                    if($this->tokens[$i-1][1]!=""){
                        if($this->tokens[$i-1][1] =="parent"){
                            // $this->tokens[$i-1][1] = strtoupper($this->tokens[$i-1][1]) . '_' . $this->tokens[$i+1][1];
                            $flag=1;
                        }else{
                            $this->tokens[$i-1][1] = strtoupper($this->tokens[$i-1][1]) . '_' . $this->tokens[$i+1][1];
                        }
                    }
                     if($flag!=1)
                         {
                             $this->tokens[$i  ][1] = '';
                             $this->tokens[$i+1][1] = '';
                             $flag=0;
                         }
                }
                case T_FUNCTION:
                  $i++;
                while($this->tokens[$i][1]==T_WHITESPACE)
                {
                    
                    $i++;
                }
                $func = $this->tokens[$i+1][1];
                if ($func == '__construct') {
                    $this->tokens[$i+1][1] = $class;
                }
                // mmh what about __destruct ! = ignore!!
                /* while($this->tokens[$i][1] != '(') {
                    $i++;
                }
                while($i++) {
                    switch($this->tokens[$i][0]) {
                        case ord(')');
                        break 3;
                    case T_VARIABLE:
                        while($this->tokens[$i][1] != ',') {
                            if ($this->tokens[$i][1] == ')') {
                                break 4;
                            }
                            $i++;
                        }
                        $i++;
                    case T_STRING:
                        // $this->tokens[$i][1] = '';
                        break;
                    }
                }*/
            }
        }
                
            
    
    }
    
    
    /**
     * Function that takes care of Exception conversion
     *
     * @return none
     */
    function exceptions() 
    {

        for($i=0;$i<count($this->tokens);$i++) {
            switch ($this->tokens[$i][0]) {
            case T_THROW:
                $start = $i;
                $i++;
                while($this->tokens[$i][0] != T_NEW) {
                    $i++;
                }
                $i++;
                while($this->tokens[$i][0] != T_STRING) {
                    $i++;
                }
                $i++;
                $value = '';
                // not really very safe!
                while($this->tokens[$i][1] != ')') {
                    $value .= $this->tokens[$i][1];
                    $i++;
                }
                for($ii = $start+1;$ii<$i;$ii++) {
                    $this->tokens[$ii][1] = ''; 
                }  
                    
                    
                    
                $this->tokens[$start][1] = "require_once 'PEAR.php'; ".
                    "return PEAR::raiseError{$value},null,PEAR_ERROR_RETURN";
                break;
            }
        }
                    
    }
    
    
    /**
     * Function to add static, constant variables at starting of the program.
     * it also adds which files are needed at top by require_once
     *
     */
    function toString() 
    {
        global $sourceCheckoutDir;
        
        $classNames = array('CRM_Core_SelectValues', 'CRM_Core_Custom_Field','CRM_Contact_Task','CRM_Core_BAO_CustomField');
        //To Replace SelectValues
         for($i=0; $i < count($this->tokens); $i++) {
             foreach($classNames as $string) {
                 if($this->tokens[$i][1] == $string ) {
                     $j = $i+1;
                     $count = 2;
                     $str = $string;
                     while($count>0) {
                         $str.= $this->tokens[$j][1];
                         $j++;
                         $count--;
                     }
                     $pattern = '/('. $string .')::\$(\w+)/e';
                     $replacement = "'$' . 'GLOBALS[\'_' .    strtoupper(\\1)  . '\'][\'' . \\2 .'\']'";
                     $this->tokens[$i][1]= @preg_replace($pattern, $replacement, $str);
                     $i++;
                     $this->tokens[$i][1]="";
                     $i++;
                     $this->tokens[$i][1]="";

                 }
             }
         }
         
  
        //To remove abstact keyword     
        for($j=0;$j<count($this->tokens);$j++)
            {
                if(strcmp($this->tokens[$j][1],"abstract")==0)
                    {
                        $next = $j;
                        $next++;
                          while($this->tokens[$next][0]==T_WHITESPACE)
                            $next++;
                          
                          //echo "\n".$this->tokens[$next][1];
                          
                          if($this->tokens[$next][1]=="function")
                              {
                                  $this->tokens[$j][1]="// ".$this->tokens[$j][1];
                              }
                          else
                              {
                                  $this->tokens[$j][1]="";
                              }
                    }
            }

       
       
       
       
        //To remove interface definition.
        for($j=0;$j<count($this->tokens);$j++)
            {
                if(strcmp($this->tokens[$j][1],"interface")==0)
                    {
                        do
                            {
                                $this->tokens[$j][1]="";
                                $j++;
                            }while(strcmp($this->tokens[$j][1],"}")!=0);
                        $this->tokens[$j][1]="";
                    }
                
            }
       
        //TO CHANGE THE NAMES OF STATIC VARIABLES outside of class

        foreach($this->statvar as $name)
                 for($j=0;$j<count($this->tokens);$j++)
                {
                      if(strcmp($this->tokens[$j][1],"$".$name)==0 ||strcmp($this->tokens[$j][1],$name)==0)
                          {
                              $k=$j;       
                              $k--;
                              $l=$k+1;
                              $flag=0; 
                              do
                                  {
                                      
                                      if((strcmp($this->tokens[$l][1],"::")==0))
                                      {
                                         $flag=1;
                                      }
                                    
                                      $l--;
                                  }while($this->tokens[$l][0] != T_STRING);
                              if($flag==1)
                                  {
                                      $count = 2;
                                      while($count >0)
                                          {   
                                              if($this->tokens[$k][0]!=T_WHITESPACE)
                                                  { 
                                                      // echo "enternig into if"; 
                                                      $this->tokens[$k][1]="";
                                                      $count--;
                                                  }
                                              $k--;
                                                     
                                          }  
                                  }  
                          }                            
                }
            
        foreach( $this->statics as $class => $consts) 
                 {
                     foreach($consts as $name =>$val) 
                         {
                             for($j=0;$j<count($this->tokens);$j++)
                                 {
                                     if(strcmp($this->tokens[$j][1],"$".$name)==0) {
                                         $this->classarray[$class] = 1;
                                         $this->tokens[$j][1]=  "\$GLOBALS['_".strtoupper($class) . "']['{$name}']";
                                     }
                                 }
                         }
                 }


        //To change calls of parent constructor and constant variable
        for($j=0;$j<count($this->tokens);$j++)
            {
                if(strcmp($this->tokens[$j][1],"parent")==0)
                    {
                        
                        $back=$j;
                        while(strcmp($this->tokens[$back][1],"extends")!=0)
                            $back--;

                        $back++;
                        while($this->tokens[$back][0] ==T_WHITESPACE) 
                             $back++;
                        
                        $classname=$this->tokens[$back][1];
                        
                        $k=$j;
                        while(strcmp($this->tokens[$k][1],";")!=0) 
                            { 
                                if($this->tokens[$k][1]=="::")
                                    {
                                        if(strcmp($this->tokens[$k+1][1],"__construct")==0){
                                            $this->tokens[$k+1][1]=$classname;
                                        }else{
                                            $flag = false;
                                            $f = $k;
                                            while($this->tokens[$f][1]!=';'  && $this->tokens[$f][1]!=',')
                                                {
                                                 
                                                    if($this->tokens[$f][1]=='('){
                                                      
                                                        $flag = true; 
                                                    }
                                                    $f++;
                                                }
                                            $f = $k;

                                            /* while($this->tokens[$f][1]!=';' && $this->tokens[$f][1]!=',')
                                                {
                                                    
                                                    if($this->tokens[$f][1]==T_VARIABLE){
                                                        echo "Entering";
                                                        $flag = true; 
                                                        break;
                                                    }
                                                    
                                                    $f++;
                                                }*/
                                            if($flag == false){
                                                $this->tokens[$k-1][1]=strtoupper($classname);
                                                $this->tokens[$k][1]="_";
                                                $flag = false;
                                            }
                                        }

                                    }
                                $k++;
                            }
                        
                    }
         }


        //======================================================================================= 

        $ret = '';
        
        for($i=0;$i<count($this->tokens);$i++) {
            if ($i == $this->start) {
                foreach( $this->constants as $class => $consts) {
                    $ret ."\n";
                    foreach($consts as $name =>$val) {
                        // echo $consts;
                        $ret .= "define( '".strtoupper($class) . "_{$name}',$val);\n";
                    }
                }
                foreach( $this->statics as $class => $consts) {
                    $ret ."\n";
                    foreach($consts as $name =>$val) {
                        {
                            
                            $ret .= "\$GLOBALS['_".strtoupper($class) . "']['{$name}'] = $val;\n";
                        }
                    }
                }
                $ret.="\n";
                
                if($this->tokens[$i][1]=="include_once")
                    {
                        
                        do
                            {
                                $ret .= $this->tokens[$i][1];
                                $i++;
                            } while($this->tokens[$i][1]!=";");
                        $ret .=";";
                        $this->tokens[$i][1]="";
                    }
                
                $ret .="\n";
                foreach($this->filenames as $file)
                    {
                        
                        if(count($this->classarray)!=0)
                            {
                                foreach($this->classarray as $class => $dontcare)
                                    {
                                        $flag = 0;
                                        if($class!=$file)
                                            {
                                                $file=str_replace("_","/",$file);
                                                $strFile = $file . ".php"; 
                                                $file="require_once '".$file.".php"."';";
                                                for($j=0;$j<count($this->tokens);$j++) {
                                                   
                                                    if($this->tokens[$j][1]=="require_once") {
                                                        $reqfile =$this->tokens[$j][1];
                                                        while($this->tokens[$j][1]!=";") {
                                                            $j++;
                                                            $reqfile.=$this->tokens[$j][1];
                                                        }
                                                        
                                                        if($reqfile==$file) {
                                                            $flag = 1;
                                                            }
                                                    } 
                                                }
                                                if($flag==0)
                                                    {
                                                        if(file_exists($sourceCheckoutDir.'/'.$strFile)) {
                                                            
                                                            $ret.=$file."\n"; 
                                                        }
                                                    }
                                            }
                                    }
                            }
                        else
                            {
                                $flag = 0;
                                $file=str_replace("_","/",$file);
                                $strFile = $file . ".php"; 
                                $file="require_once '".$file.".php"."';";
                                for($j=0;$j<count($this->tokens);$j++) {
                                    if($this->tokens[$j][1]=="require_once") {
                                        $reqfile =$this->tokens[$j][1];
                                        while($this->tokens[$j][1]!=";") {
                                            $j++;
                                            $reqfile.=$this->tokens[$j][1];
                                        }
                                        if($reqfile==$file) {
                                            $flag = 1;
                                        }
                                        
                                    } 
                                }
                                                
                                if($flag==0)
                                    {
                                        if(file_exists($sourceCheckoutDir.'/'.$strFile)) {
                                            
                                            $ret.=$file."\n"; 
                                        }
                                    }
                              
                            }
                    }


            }
        
         
           
            
            $ret .= $this->tokens[$i][1];
            
        }
        return $ret;
    }


    
    

    function dump() {
        foreach(array_keys($this->tokens) as $i) {
            // echo token_name($this->tokens[$i][0]) .':' . $this->tokens[$i][0]  . ':' . $this->tokens[$i][1] . "\n";
        }
    }
    
    function vars() 
    {
        $this->dump();
    }
    
    
}


if (isset($argv[1])) {
    // use this code if single file has to be converted from php5 to php4  and comment the above block
    $sam =& new PHP_DownGrade($argv[1]);
    echo $sam->toPHP4();
  
} else {

    // start of code to convert files recursively ---
    // this code is to convert the whole directory from php5 to php4
    
    $directory = array('CRM', 'api','modules');
    
    foreach ($directory as $v) {
        $rootDir = "$sourceCheckoutDir/$v";
        $destDir = "$targetDir/$v";
        
        $dir =& new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootDir), true);
        foreach ( $dir as $file ) {
            if ( substr( $file, -4, 4 ) == '.php' ) {
                // skip config.inc.php
                if ( trim($file) == 'config.inc.php' ) {
                    continue;
                }

                str_repeat("--", $dir->getDepth()) . ' ' . $file->getPath( ) . " $file\n";
                $x    =& new PHP_DownGrade($file->getPath( ) . '/' . $file);
                $php4 = $x->toPHP4( );
                
                $php4Dir  = str_replace( $rootDir, $destDir, $file->getPath( ) );
                createDir( $php4Dir );
                $fd   = fopen( $php4Dir . '/' . $file, "w" );
                fputs( $fd, $php4 );
                print "Converting: $file\n";
                fclose( $fd );
            }
        }
    }

    // end of code to convert files recursively --
   
}

?>
