<?php

class WGM_Error {

  const STATUS_FATAL_ERROR = 2;

  function __construct() {
  }

  static function fatal($message, $code = null, $email = null) {
    $vars = array( 'message' => $message,
		   'code'    => $code );

    theme( 'fatal_error', 'error.tpl', $vars );

    exit( WGM_Error::STATUS_FATAL_ERROR );
  }

}

?>