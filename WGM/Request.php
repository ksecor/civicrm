<?php

/**
 * WGM_Request encapsulate an incoming request (GET/POST vars, session, cookies)
 * and provides accessors to determine the state of the application
 * e.g. current domain and current user
 * By default this is a public request object. Override the init() function to handle
 * authorization.
 */

class WGM_Request {

  static public $_vars;
  static protected $_valid = false;
  static private $_dateFormat = 'M/D/Y';
    
  function __construct() {
    self::$_app = $app;
    self::$_conf = $conf;
  }
	
  /**
   * Processes all the GET variables and validates it against the
   * rules associated with the variable
   *
   * If validation fails for all variable the function
   * returns false
   *
   * @return boolean - true if success, false
   * otherwise
   *
   */                                            
  function initVars($module) {
    $vars = $module->requestVars();
    
    if ($vars == null) {
      return;
    }
    
    self::addVars( $vars );
    $validVars = array();
    
    foreach ($_GET as $name => $value) {
      self::processVar($name, $value);
    }
    
    $pageID = WGM_Array::value( 'pageID', $_POST );
    if ($pageID)
      self::processVar('pageID', $pageID);
    
    self::$_valid = true;
    
    // process the reset first if exists
    if ( $this->getVar( 'reset' ) ) {
      $module->reset( );
    }
    
    self::restoreVars($module);
    
    // if we don't have all of our required variables, throw an exception
    $missingVars = self::checkRequiredVars();

    if (! empty($missingVars)) {
      WGM_Exception::throwWGMException("Missing required variables",
				       $missingVars);
    }
    
    return true;
  }
    

  function addVars( $vars ) {
    foreach ($vars as $name => $v) {
      list($type, $required, $min, $max, $storageName) = $v;
      self::addVar($name, $type, $required, $min, $max, $storageName);
    }
  }
  
  function addVar( $name, $type, $required, $min, $max, $storageName ) {
    if (self::$_vars == null) {
      self::$_vars = array();
    }
    
    $var = array(   'type'          => $type,
		    'required'      => $required,
		    'min'           => $min,
		    'max'           => $max,
		    'storageName'   => $storageName );
    self::$_vars[ $name ] = $var;
  }
  
  function processVar( $name, $value ) {
    $var = WGM_Array::value( $name, self::$_vars );
    
    if ($var === null) {
      return;
    }
        
    $type           = $var['type'       ];
    $required       = $var['required'   ];
    $min            = $var['min'        ];
    $max            = $var['max'        ];
    $storageName    = $var['storageName'];

    $validVars[ $name ] = false;

    if (! WGM_Type::valid($type, $value, self::$_dateFormat)) {
      return;
    }

    $validVars[ $name ] = self::callRule($name, $type, $min, $max, $value);

    if ($validVars[$name]) {
      $value = WGM_Type::filter($type, $value);
      $value = WGM_Type::format($type, $value);

      if ($name == 'action') {
	self::$_vars[$name]['value'] =
	  WGM_ObjectAction::getObjectAction();
      } else { 
	self::$_vars[$name]['value'] = $value;
      }
    }
  }

  function callRule($name, $type, $min, $max, $value) {
    if (! $min && ! $max) {
      return true;
    }

    $value = trim( $value );

    switch ($type) {
    case WGM_Type::TYPE_INT:
    case WGM_Type::TYPE_DOUBLE:
      return ($value >= $min && $value <= $max) ? true : false;

    case WGM_Type::TYPE_STRING:
      return WGM_Array::value( $value, $max ) ? true : false;
    }

    return true;
  }

  /*
   * retrieve variables from the session
   *
   * This helps simulate a GET and allows the application
   * access to the GET variables
   */
  function restoreVars( $module ) {
    if (! self::$_vars) {
      return;
    }

    self::storeVars( $module );

    foreach ( self::$_vars as $name => $var ) {
      $storageName = WGM_Array::value('storageName', $var);
      $value = WGM_Array::value('value', $var);

      if ($storageName && ! isset($value)) {
	self::$_vars[$name]['value'] = $module->getVar($storageName);
      }
    }
  }
   
   
  /* Store all the variables from the request into the
   * storage unit
   * The application has to explicity state which variables
   * need to be stored and the storage name
   */       
  function storeVars( $module ) {
    if (! self::$_vars) {
      return;
    }

    foreach (self::$_vars as $name => $var) {
      $storageName = WGM_Array::value('storageName', $var);
      $value = WGM_Array::value('value', $var);

      if ($storageName && isset($value)) {
	$module->setVar($storageName, $value);
      }
    }
  }
    

  static function setDateFormat($dateFormat) {
    self::$_dateFormat = $dateFormat;
  }


  /**
   * parse Request for current module
   *
   * this should parse path_info, but for right now just checking query strings
   */
  function module() {
    if (isset($_REQUEST['module']) ) {
      return $_REQUEST['module'];
    }
    else {
      return self::$_app->defaultModule();
    }
  }

  /**
   * parse Request for current action
   *
   * this should parse path_info but for right nwo just checking query strings
   */
  function action() {
    if (isset($_REQUEST['action']) ) {
      return $_REQUEST['action'];
    }
    else {
      return '';
    }
  }

  /**
   * getValue can only be called after process
   */
  function getValue($name) {
    return $this->get( $name );
  }
	
  function get( $name ) {
    if (! self::$_valid) {
      return null;
    }
    
    $var = WGM_Array::value($name, self::$_vars);
    
    if (! $var) {
      return null;
    }
    
    return WGM_Array::value('value', $var);
  }
  
  function checkRequiredVars() {
    $missingVars = array();
    foreach (self::$_vars as $name => $var) {
      if ($var['required'] && !isset($var['value'])) 
	{
	  $missingVars[] = array($name => $var['storageName']);
	}
    }
    return $missingVars;
  }

} // end WGM_Request

?>
