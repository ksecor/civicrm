<?php

require_once 'CRM/DAO/Factory.php';

class CRM_Contacts_DAO_Factory {

    static $_classes = array (
                              'Contact'              => 'data',
                              'Contact_Household'    => 'data',
                              'Contact_Individual'   => 'data',
                              'Contact_Organization' => 'data',
			      'Contact_Location'     => 'data',
			      'Relationship_Type'    => 'data',
			      'Relationship'         => 'data',
			      'Contact_Action'       => 'data',
			      'Task'                 => 'data',
			      'Note'                 => 'data',
			      'Saved_Search'         => 'data',
			      'List'                 => 'data',
			      'Contact_List'         => 'data',
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
        return CRM_DAO_Factory::create( $className );
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
