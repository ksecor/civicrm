<?php

require_once 'Log.php';

require_once 'CRM/Rule.php';
require_once 'CRM/Pager.php';
require_once 'CRM/Sort.php';

class CRM_Utils {
 
  function hash( $str ) {
    return strtoupper( md5( $str ) );
  }
  
  function debug( $name, &$vars, $returnOutput = false ) {
    
    $output = print_r( $vars, true );
    
    if ( $returnOutput ) {
      return $output;
    }
    else {
      echo "<p>$name<p><pre>";
      print( $output );
      echo "</pre><p>";
      flush( );
    }
  }

  function log($content, $logfile = "/var/log/crm/crm.log") {
    $conf = array('mode' => 0666, 'timeFormat' => '%X %x');
    $log  = &Log::singleton('file', $logfile, 'Apply', $conf, LOG_INFO);
    
    $log->log($content, LOG_INFO);
    
    $log->close();
  }


  function syslog($message) {
    // for config, requires that /etc/syslog.conf be tweaked with "local0.*\t\t/var/log/php.log"
    define_syslog_variables();
    openlog("CRM:", LOG_PID, LOG_LOCAL0);

    //$access = date("Y/m/d H:i:s");
    syslog(LOG_INFO, $message);
    
    closelog();
  }


  function append( &$str, $delim, $name ) {
    if ( empty( $name ) ) {
      return;
    }

    if ( is_array( $name ) ) {
      foreach ( $name as $n ) {
        if ( empty( $n ) ) {
          continue;
        }
        if ( empty( $str ) ) {
          $str = $n;
        } else {
          $str .= $delim . $n;
        }
      }
    } else {
      if ( empty( $str ) ) {
        $str = $name;
      } else {
        $str .= $delim . $name;
      }
    }
  }
   
  static function matchReferer( $names ) {
    $referer = CRM_Array::value( 'HTTP_REFERER', $_SERVER );

    // if referer is not set or name does not exist in referer return
    if ( ! $referer ) {
      return null;
    }

    foreach ( $names as $name ) {
      if ( strstr( $referer, $name ) ) {
        return $referer;
      }
    }

    return null;

  }

  // a url typically has a few additional things which make exact matching
  // not worth it.
  // currently these are: pageId, sortID, and id
  static function canonicalURL( $url ) {
    $deleteParams = array( CRM_Pager::PAGE_ID,
                           CRM_Pager::PAGE_ID_BOTTOM,
                           CRM_Sort::SORT_ID );

    foreach ( $deleteParams as $param ) {
      $url = preg_replace( "/\&$param=\d+/", '', $url );
    }

    return $url;
  }

  static function getClassName( $object ) {
    $name = get_class( $object );
    $path = explode( '_', $name );
    return $path[ count( $path ) - 1 ];
  }

  static function import( $classPath ) {
    if ( class_exists( $classPath ) ) {
      return;
    }
	
    $classPath = self::safe_identifier( $classPath);
    $classPath = str_replace( '_', '/', $classPath ) . '.php';
    require_once($classPath);
  }

  /**
   * clean a dynamic classname, or method of non word characters
   */
  static function safe_identifier($str) {
    return preg_replace('/\W/', '', $str);
  }

}

?>
