<?php

require_once "PEAR.php"; 

require_once "CRM/Error.php";

class CRM_Session {

  /**
   * Session Key
   *     $key : key under which all the session variables are stored
   *          : This enables the API to handle multiple applications
   *          : while preventing naming conflicts
   *
   */

  const RETURN_URL = 'returnUrl';

  protected $_key = 'crm';

  protected $_session;

  static private $_instance = null;

  // {{{ Constructor

  /**
   * Constructor
   *
   * Since we are now a client / module of drupal, drupal takes care
   * of initiating the php session handler session_start ().
   * All crm code should always use the session using
   * CRM_Session. we prefix stuff to avoid collisions with drupal and also
   * collisions with other crm modules!!
   * This constructor is invoked whenever any module requests an instance of
   * the session and one is not available.
   *
   * @param  string   Index for session variables
   * @return void
   */
  function __construct( $key = 'crm' ) { 
    $this->_key     = $key;
    $this->_session =& $_SESSION;
    
    $this->createSessionStore();
  }

  static function instance($key = 'crm') {
    if (self::$_instance === null ) {
      self::$_instance = new CRM_Session($key);
    }
    return self::$_instance;
  }

  // }}}}
  // {{{ createSessionStore()

  /**
   * Creates an array in the session.
   * All name/value pairs stored in this session will be stored
   * in this array.
   * This allows name conflicts between various applications assuming
   * that the top level application names are unique
   *
   * @access private
   * @return void
   */
  function createSessionStore() {
    if ( ! isset( $this->_session[$this->_key] ) ||
	 ! is_array( $this->_session[$this->_key] ) ) {
      $this->_session[$this->_key] = array();
    }
    
    return;
  }
  
  // }}}
  // {{{ resetSessionStore()

  /**
   * Resets the session store
   * All name/value pairs stored in this session will be reset / destoryed
   *
   * @access private
   * @return void
   */
  function resetSessionStore() {
    // to make certain we clear it, firs initialize it to empty
    $this->_session[$this->_key] = array();
    unset( $this->_session[$this->_key] );

    return;
  }

  /**
   * createSessionScope
   * creates a session scope if it does not exist
   *
   * @param string $prefix - session scope name
   * @access public
   * @return void
   */
  function createSessionScope( $prefix ) {
    if ( ! array_key_exists( $prefix, $this->_session[$this->_key] ) ) {
      $this->_session[$this->_key][$prefix] = array( );
    }
  }

  /**
   * resetSessionScope
   * resets the session scope
   *
   * @param string $prefix - session scope name
   * @access public
   * @return void
   */
  function resetSessionScope( $prefix ) {
    if ( array_key_exists( $prefix, $this->_session[$this->_key] ) ) {
      unset( $this->_session[$this->_key][$prefix] );
    }
  }

  // }}}
  // {{{ set()

  /**
   * Store the variable with the value in the session scope
   *
   * This function takes a name, value pair and stores this
   * in the session scope. Not sure what happens if we try
   * to store complex objects in the session. I suspect it
   * is supported but we need to verify this
   *
   * @access public
   * @param  string name  : name  of the variable
   * @param  mixed  value : value of the variable
   * @param  string  a string to prefix the keys in the session with
   * @return void
   *
   */
  function set( $name, $value, $prefix = '' ) {
    // make sure the session store is created
    $this->createSessionStore();

    if ( $prefix == '' ) {
      $this->_session[$this->_key][$name] = $value;
    } else {
      if ( ! array_key_exists( $prefix, $this->_session[$this->_key] ) ) {
        $this->_session[$this->_key][$prefix] = array( );
      }
      $this->_session[$this->_key][$prefix][$name] = $value;
    }
  }

  // }}}
  // {{{ setVars()

  /**
   * Store the variable with the value in the session scope
   *
   * This function takes an associate array of name, value pair
   * and stores this in the session scope. The associate array is
   * flattened and stored in the $_key scope of the session array
   *
   * @access   public
   * @param  mixed  vars : associative array of name / values
   * @param  string  a string to prefix the keys in the session with
   * @return void
   *
   */
  function setVars( $vars, $prefix='' ) {
    // make sure the session store is created
    $this->createSessionStore();

    if ( $prefix == '' ) {
      foreach ( $vars as $name => $value ) {
        $this->_session[$this->_key][$name] = $value;
      }
    } else {
      if ( ! array_key_exists( $prefix, $this->_session[$this->_key] ) ) {
        $this->_session[$this->_key][$prefix] = array( );
      }
      foreach( $vars as $name => $value) {
        $this->_session[$this->_key][$prefix][$name] = $value;
      }
    }
  }
  
  // }}}
  // {{{ get()

  /**
   * Gets the value of the named variable in the session scope
   *
   * This function takes a name and retrieves the value of this 
   * variable from the session scope. If the name does not exist
   * an error object will be returned. The value is returned as a
   * reference (to avoid copying for complex objects)
   *
   * Removed return reference ToddW 10/29/04
   * 
   * @access public
   * @param  string name  : name  of the variable
   * @param  string prefix : adds another level of scope to the session
   * @return mixed
   *
   */
  function get( $name, $prefix='' ) {
    if ( $prefix == '' ) {
      if ( isset( $this->_session[$this->_key][$name] ) ) {
        return $this->_session[$this->_key][$name];
      } else {
        return null;
      }
    } else {
      if ( isset( $this->_session[$this->_key][$prefix] ) && is_array( $this->_session[$this->_key][$prefix] ) ) {
        if ( isset( $this->_session[$this->_key][$prefix][$name] ) ) {
          return $this->_session[$this->_key][$prefix][$name];
        } else {
          return null;
        }
      }
    }
    return null;
  }

  

  // }}}
  // {{{ getVars()

  /**
   * Gets all the variables in the current session scope
   * and stuffs them in an associate array
   *
   * @access public
   * @param  array  vars : associative array to store name/value pairs
   * @param  string  Strip prefix from the key before putting it in the return
   * @return void
   *
   */
  function getVars( &$vars, $prefix = '' ) {
    if ( ! isset   ( $this->_key )                  ||
	 ! isset   ( $this->_session[$this->_key] ) ||
	 ! is_array( $this->_session[$this->_key] ) )
      return;

    foreach ( $this->_session[$this->_key] as $name => $value ) {
      $vars[$name] = $value;
    }

    if ( $prefix != '' ) {
      if ( array_key_exists( $prefix, $this->_session[$this->_key] ) && 
	   is_array( $this->_session[$this->_key][$prefix] ) ) {
        foreach ($this->_session[$this->_key][$prefix] as $name => $value) {
          $vars[$name] = $value;
        }
      }
    }
    
  }

  // }}}
  // {{{ reset()
  /*
   * Resets the session scope and clears it out
   *
   * @access public
   * @return void
   *
   */
  function reset() {
    $this->resetSessionStore();
  }
  
  /**
   * maintain the returnUrl stack
   *
   */
  function createReturnUrl( ) {
    $this->createSessionStore( );

    if ( ! isset( $this->_session[CRM_Session::RETURN_URL] ) ||
         ! is_array( $this->_session[CRM_Session::RETURN_URL] ) ) {
      $this->_session[CRM_Session::RETURN_URL] = array( );
    }

    return;
  }

  function resetReturnUrl( ) {
    $this->_session[CRM_Session::RETURN_URL] = array( );
    unset( $this->_session[CRM_Session::RETURN_URL] );
  }


  /**
   * adds a returnUrl to the stack
   *
   * @param string the url to return to when done
   *
   * @return void
   *
   * @access public
   * 
   */
  function pushReturnUrl( $returnUrl ) {
    if ( empty( $returnUrl ) ) {
      return;
    }

    $this->createReturnUrl( );

    array_push( $this->_session[CRM_Session::RETURN_URL], $returnUrl );
  }

  /**
   * replace the returnURL of the stack with the passed one
   *
   * @param string the url to return to when done
   *
   * @return void
   *
   * @access public
   * 
   */
  function replaceReturnUrl( $returnUrl ) {
    if ( empty( $returnUrl ) ) {
      return;
    }

    $this->createReturnUrl( );

    array_pop( $this->_session[CRM_Session::RETURN_URL] );
    array_push( $this->_session[CRM_Session::RETURN_URL], $returnUrl );
  }

  /**
   * pops the top returnUrl stack
   *
   * @param void
   *
   * @return the top of the returnUrl stack (also pops the top element)
   *
   */
  function popReturnUrl( ) {
    $this->createReturnUrl( );

    return array_pop( $this->_session[CRM_Session::RETURN_URL] );
  }

  /**
   * reads the top returnUrl stack
   *
   * @param void
   *
   * @return the top of the returnUrl stack
   *
   */
  function readReturnUrl( ) {
    $this->createReturnUrl( );

    $lastElement = count( $this->_session[CRM_Session::RETURN_URL] ) - 1;
    return $lastElement >= 0 ? 
      $this->_session[CRM_Session::RETURN_URL][$lastElement] :
      null;
  }

}

?>