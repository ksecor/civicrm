<?php

$GLOBALS['_CRM_CONTACT_DAO_FACTORY']['_classes'] =  array (
                            'Address'      => 'data',
                            'Contact'      => 'data',
                            'Email'        => 'data',
                            'Household'    => 'data',
                            'IM'           => 'data',
                            'Individual'   => 'data',
                            'Location'     => 'data',
                            'LocationType' => 'data',
                            'Organization' => 'data',
                            'Phone'        => 'data',
                            'Relationship' => 'data',
                            );
$GLOBALS['_CRM_CONTACT_DAO_FACTORY']['_prefix'] =  array(
                          'business'  =>  'CRM/Contact/BAO/',
                          'data'      =>  'CRM/Contact/DAO/',
                          );
$GLOBALS['_CRM_CONTACT_DAO_FACTORY']['_suffix'] =  '.php';
$GLOBALS['_CRM_CONTACT_DAO_FACTORY']['_preCall'] =  array(
                             'singleton'  =>  '',
                             'business'  =>  'new',
                             'data'      =>  'new',
                             );
$GLOBALS['_CRM_CONTACT_DAO_FACTORY']['_extCall'] =  array(
                             'singleton'  =>  '::singleton',
                             'business'  =>  '',
                             'data'      =>  '',
                             );

require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO/Factory.php';
require_once 'CRM/Core/DAO/Factory.php';

class CRM_Contact_DAO_Factory {

  

  
    
    
    
    
    
    
    

     function &create ( $className ) {
      $type = CRM_Utils_Array::value( $className, $GLOBALS['_CRM_CONTACT_DAO_FACTORY']['_classes'] );
      if ( ! $type ) {
        return CRM_Core_DAO_Factory::create( $className );
      }

      $file  = $GLOBALS['_CRM_CONTACT_DAO_FACTORY']['_prefix'][$type] . $className;
      $class = str_replace( '/', '_', $file );
                
      require_once( $file . $GLOBALS['_CRM_CONTACT_DAO_FACTORY']['_suffix'] );
                
      $newObj = eval( sprintf( "return %s %s%s();", 
                               $GLOBALS['_CRM_CONTACT_DAO_FACTORY']['_preCall'][$type],
                               $class,
                               $GLOBALS['_CRM_CONTACT_DAO_FACTORY']['_extCall'][$type] ) );
      
      return $newObj;
    }

}

?>
