<?php

require_once 'CRM/Array.class.php';

class CRM_Contacts_DAO_Factory {

    static $_classes = array (
                              'Contact'              => 'data',
                              'Contact_Household'    => 'data',
                              'Contact_Individual'   => 'data',
                              'Contact_Organization' => 'data'
                             );

    static $_prefix = array(
                           'business'  =>  'CRM/Contacts/BAO/',
                           'data'      =>  'CRM/Contacts/DAO/',
                           );
    
    static $_suffix = '.php';
    
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
      $type = CRM_Array::value( $className, self::$_classes );
      if ( ! $type ) {
        CRM_Error::fatal( "class $className not found" );
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
