<?php

if ( ! defined( 'CRM_SMARTYDIR' ) ) {
  define( 'CRM_SMARTYDIR', '/opt/local/lib/php/Smarty/' );
}

if ( ! defined( 'CRM_TEMPLATE_COMPILEDIR' ) ) {
  define( 'CRM_TEMPLATE_COMPILEDIR', '/tmp/templates_c/' );
}


// load Smarty library files
require_once  CRM_SMARTYDIR . 'Smarty.class.php';

/**
 * Copyright (c) 2004 Donald A. Lobo (lobo at yahoo dot com)
 *
 * Derived from the phptemplate version by Brian E. Lozier (brian@massassi.net)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

class SmartyTemplate extends Smarty {
  private static $_singleton = null;

  /**
   * Constructor
   *
   * @return void
   */
  function __construct($compileDir = null, $templateDir = CRM_TEMPLATE_COMPILEDIR) {
    $this->Smarty();

    $this->template_dir = $compileDir;
    $this->compile_dir  = $templateDir;
    $this->use_sub_dirs = true;
    $this->caching      = true;
    $this->debugging    = false;
    $this->plugins_dir  = array ( CRM_SMARTYDIR . 'plugins', 'crm/CRM/Smarty/plugins' );
  }

  /**
   * Static instance provider.
   *
   * Method providing static instance of SmartTemplate, as
   * in Singleton pattern.
   */
  static function singleton($templateDir = null, $compileDir = CRM_TEMPLATE_COMPILEDIR) {
    if ( self::$_singleton === NULL ) {
      self::$_singleton = new SmartyTemplate($compileDir, $templateDir);
    }
    self::$_singleton->template_dir = $templateDir;
    self::$_singleton->compile_dir  = $compileDir;
    return self::$_singleton;
  }


  /**
   * Set a template variable.
   *
   * @param string $name name of the variable to set
   * @param mixed $value the value of the variable
   *
   * @return void
   */
  function set($name, $value) {
    $this->assign($name, $value);
  }

  /**
   * Set a bunch of variables at once using an associative array.
   *
   * @param array $vars array of vars to set
   * @param bool $clear whether to completely overwrite the existing vars
   *
   * @return void
   */
  function set_vars($vars, $clear = false) {
    if($clear) {
      $this->clear_all_assign( );
    }
    foreach ( $vars as $name => $value ) {
      $this->assign( $name, $value );
    }
  }

  /**
   * Open, parse, and return the template file.
   *
   * @param string string the template file name
   *
   * @return string
   */
  function fetch($file, $templateDir = null, $cache_id = null, $compile_id = null) {
    if ( $templateDir !== null ) {
      $this->template_dir = $templateDir;
    }
    return parent::fetch( $file );              // Return the contents
  }
}

?>
