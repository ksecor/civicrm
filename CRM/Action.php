<?php

require_once 'CRM/Array.class.php';

class CRM_Action {

  const
    CREATE        =     1,
    VIEW          =     2,
    UPDATE        =     4,
    DELETE        =     8,
    EXPORT        =    16;
  
  static $_nameToAction = array(
                                'create'        => CRM_Action::CREATE,
                                'view'          => CRM_Action::VIEW  ,
                                'update'        => CRM_Action::UPDATE,
                                'delete'        => CRM_Action::DELETE,
                                'export'        => CRM_Action::EXPORT,
                                );

  protected $_action;

  static function getAction( $kwd = 'action', $default = null ) {
    $urlVar = CRM_Array::value( $kwd, $_GET );
    if ( ! isset( $urlVar ) ) {
      $urlVar = $default;
    }

    $action = 0;
    if ( $urlVar ) {
      $items = explode( '|', $urlVar );
      $action = CRM_Action::mapActions( $items );
    }
    return $action;
  }

  static function mapActions( $action ) {
    $mask = 0;

    if ( is_array( $action ) ) {
      foreach ( $action as $act ) {
        $mask |= CRM_Action::mapAction( $act );
      }
      return $mask;
    } else {
      return CRM_Action::mapAction( $act );
    }
  }

  static function mapAction( $action ) {
    $mask = CRM_Array::value( trim( $action ), CRM_Action::$_nameToAction );
    return $mask ? $mask : 0;
  }

  static function getDescription( $action ) {

    switch ( $action ) {
    case CRM_Action::CREATE       : return 'create';
    case CRM_Action::VIEW         : return 'view';
    case CRM_Action::UPDATE       : return 'update';
    case CRM_Action::DELETE       : return 'delete';
    case CRM_Action::EXPORT       : return 'export';
    }

    return '';
  }

}

?>
