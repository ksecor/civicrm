<?php

if ( ! defined( 'SMARTY_DIR' ) ) {
  define('SMARTY_DIR', '/opt/local/lib/php/Smarty/');
}

// load Smarty library files
require_once  SMARTY_DIR . 'Smarty.class.php';

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
    private static $_instance = null;

	protected $path; /// Path to the templates

	/**
	 * Constructor
	 *
	 * @param string $path the path to the templates
	 *
	 * @return void
	 */
	function __construct($path = null) {
	  $this->path = $path;
	  
	  $this->Smarty();

	  $this->template_dir = $path;
      $this->compile_dir  = '/tmp/drupal/templates_c';
	  $this->use_sub_dirs = true;
	  $this->caching      = false;
	  $this->debugging    = true;
	}

  function setPath($path) {
    $this->path = $path;
    $this->template_dir = $path;
  }

  /**
   * Static instance provider.
   *
   * Method providing static instance of SmartTemplate, as
   * in Singleton pattern.
   */
  static function instance($path = null) {
    if ( self::$_instance === NULL ) {
      self::$_instance = new SmartyTemplate($path);
    }
    self::$_instance->setPath($path);
    return self::$_instance;
  }


	/**
	 * Set the path to the template files.
	 *
	 * @param string $path path to template files
	 *
	 * @return void
	 */
	function set_path($path) {
		$this->path = $path;
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
	function fetch($file, $cache_id = null, $compile_id = null) {
	  return parent::fetch( $file );              // Return the contents
	}
}
?>
