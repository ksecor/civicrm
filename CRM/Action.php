<?php

require_once 'CRM/Array.class.php';

class CRM_Action {

  const
    ADD           =     1,
    BROWSE        =     2,
    COPY          =     4,
    CREATE        =     8,
    DELETE        =    16,
    DELETECONFIRM =    32,
    EDIT          =    64,
    EDIT_1        =   128,
    EDIT_2        =   256,
    EXPORT        =   512,
    MAIL          =  1024,
    RENAME        =  2048,
    SIGNUP        =  4096,
    VIEW          =  8192,
    VIEWHTML      = 16384,
    VIEWTEXT      = 32768,
    LOGOUT        = 65536;
  
  
  static $_nameToAction = array(
                                'add'           => CRM_Action::ADD   ,
                                'browse'        => CRM_Action::BROWSE,
                                'copy'          => CRM_Action::COPY,
                                'create'        => CRM_Action::CREATE,
                                'delete'        => CRM_Action::DELETE,
                                'deleteConfirm' => CRM_Action::DELETECONFIRM,
                                'edit'          => CRM_Action::EDIT  ,
                                'edit1'         => CRM_Action::EDIT_1,
                                'edit2'         => CRM_Action::EDIT_2,
                                'export'        => CRM_Action::EXPORT,
                                'mail'          => CRM_Action::MAIL,
                                'rename'        => CRM_Action::RENAME,
                                'signup'        => CRM_Action::SIGNUP,
                                'view'          => CRM_Action::VIEW,
                                'viewHtml'      => CRM_Action::VIEWHTML,
                                'viewText'      => CRM_Action::VIEWTEXT,
                                'logout'        => CRM_Action::LOGOUT,
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
    case CRM_Action::ADD          : return 'add';
    case CRM_Action::BROWSE       : return 'browse';
    case CRM_Action::COPY         : return 'copy';
    case CRM_Action::CREATE       : return 'create';
    case CRM_Action::DELETE       : return 'delete';
    case CRM_Action::DELETECONFIRM: return 'deleteconfirm';
    case CRM_Action::EDIT         : return 'edit';
    case CRM_Action::EDIT_1       : return 'edit_1';
    case CRM_Action::EDIT_2       : return 'edit_2';
    case CRM_Action::EXPORT       : return 'export';
    case CRM_Action::MAIL         : return 'mail';
    case CRM_Action::RENAME       : return 'rename';
    case CRM_Action::SIGNUP       : return 'signup';
    case CRM_Action::VIEW         : return 'view';
    case CRM_Action::VIEWHTML     : return 'viewhtml';
    case CRM_Action::VIEWTEXT     : return 'viewtext';
    case CRM_Action::LOGOUT       : return 'logout';
    }

    return '';
  }

}

?>
