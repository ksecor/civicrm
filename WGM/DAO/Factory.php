<?php

require_once 'WGM/Array.class.php';

class WGM_DataObject_Factory {

    static $_classes = array (
                             );

    static $_prefix = array(
                           'business'  =>  'WGM/BAO/',
                           'data'      =>  'WGM/DAO/',
                           );
    
    static $_suffix = '.class.php';
    
    static $_preCall = array(
                             'instance'  =>  '',
                             'business'  =>  'new',
                             'data'      =>  'new',
                             );
    
    static $_extCall = array(
                             'instance'  =>  '::instance',
                             'business'  =>  '',
                             'data'      =>  '',
                             );
    

    static function &create ( $className ) {
      $type = WGM_Array::value( $className, self::$_classes );
      if ( ! $type ) {
        WGM_Error::fatal( "class $className not found" );
      }

      $file  = self::$_prefix[$type] . $className;
      $class = str_replace( '/', '_', $file );
                
      require_once( $file . self::$_suffix );
                
      $newObj = eval( sprintf( "return %s %s%s();", 
                               self::$_preCall[$type],
                               $class,
                               self::$_extCall[$type] ) );
      
      return $newObj;
    }

}

?>
