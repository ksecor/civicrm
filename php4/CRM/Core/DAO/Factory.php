<?php

$GLOBALS['_CRM_CORE_DAO_FACTORY']['_classes'] =  array (
                              'Domain'         => 'data',

                              'Country'        => 'singleton',
                              'County'         => 'singleton',
                              'StateProvince'  => 'singleton',
                              'GeoCoord'       => 'singleton',
                              'IMProvider'     => 'singleton',
                              'MobileProvider' => 'singleton',
                             );
$GLOBALS['_CRM_CORE_DAO_FACTORY']['_prefix'] =  array(
                           'business'  =>  'CRM/Core/BAO/',
                           'data'      =>  'CRM/Core/DAO/',
                           );
$GLOBALS['_CRM_CORE_DAO_FACTORY']['_suffix'] =  '.php';
$GLOBALS['_CRM_CORE_DAO_FACTORY']['_preCall'] =  array(
                             'singleton'  =>  '',
                             'business'  =>  'new',
                             'data'      =>  'new',
                             );
$GLOBALS['_CRM_CORE_DAO_FACTORY']['_extCall'] =  array(
                             'singleton'  =>  '::singleton',
                             'business'  =>  '',
                             'data'      =>  '',
                             );


require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/Error.php';
require_once 'CRM/Core/Error.php';
require_once 'CRM/Utils/Array.php';

class CRM_Core_DAO_Factory {

    

    
    
    
    
    
    
    
    

     function &create ( $className ) {
      $type = CRM_Utils_Array::value( $className, $GLOBALS['_CRM_CORE_DAO_FACTORY']['_classes'] );
      if ( ! $type ) {
        CRM_Core_Error::fatal( "class $className not found" );
      }

      $file  = $GLOBALS['_CRM_CORE_DAO_FACTORY']['_prefix'][$type] . $className;
      $class = str_replace( '/', '_', $file );
                
      require_once( $file . $GLOBALS['_CRM_CORE_DAO_FACTORY']['_suffix'] );
                
      $newObj = eval( sprintf( "return %s %s%s();", 
                               $GLOBALS['_CRM_CORE_DAO_FACTORY']['_preCall'][$type],
                               $class,
                               $GLOBALS['_CRM_CORE_DAO_FACTORY']['_extCall'][$type] ) );
      
      return $newObj;
    }

}

?>
