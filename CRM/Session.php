<?php

require_once "PEAR.php"; 

require_once "CRM/Error.php";

class CRM_Session {

    /**
     * key is used to allow the application to have multiple top
     * level scopes rather than a single scope. (avoids naming
     * conflicts). We also extend this idea further and have local
     * scopes within a global scope. Allows us to do cool things
     * like resetting a specific area of the session code while 
     * keeping the rest intact
     *
     * @var string
     */
    protected $_key = 'crm';

    const RETURN_URL = 'returnUrl';

    /**
     * This is just a reference to the real session. Allows us to
     * debug this class a wee bit easier
     *
     * @var object
     */
    protected $_session;

    /**
     * We only need one instance of this object. So we use the singleton
     * pattern and cache the instance in this variable
     *
     * @var object
     * @static
     */
    static private $_instance = null;

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
    
        $this->create();
    }

    /**
     * singleton function used to manage this object
     *
     * @param string the key to permit session scope's
     *
     * @return object
     * @static
     *
     */
    static function instance($key = 'crm') {
        if (self::$_instance === null ) {
            self::$_instance = new CRM_Session($key);
        }
        return self::$_instance;
    }

    /**
     * Creates an array in the session. All variables now will be stored
     * under this array
     *
     * @access private
     * @return void
     */
    function create() {
        if ( ! isset( $this->_session[$this->_key] ) ||
             ! is_array( $this->_session[$this->_key] ) ) {
            $this->_session[$this->_key] = array();
        }
        return;
    }
  
    /**
     * Resets the session store
     *
     * @access public
     * @return void
     */
    function reset() {
        // to make certain we clear it, firs initialize it to empty
        $this->_session[$this->_key] = array();
        unset( $this->_session[$this->_key] );

        return;
    }

    /**
     * creates a session local scope
     *
     * @param string local scope name
     * @access public
     * @return void
     */
    function createScope( $prefix ) {
        if (! empty( $prefix ) ) {
            return;
        }

        if ( ! CRM_Array::value( $prefix, $this->_session[$this->_key] ) ) {
            $this->_session[$this->_key][$prefix] = array( );
        }
    }

    /**
     * resets the session local scope
     *
     * @param string local scope name
     * @access public
     * @return void
     */
    function resetScope( $prefix ) {
        if (! empty( $prefix ) ) {
            return;
        }

        if ( array_key_exists( $prefix, $this->_session[$this->_key] ) ) {
            unset( $this->_session[$this->_key][$prefix] );
        }
    }

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
    function set( $name, $value, $prefix = null ) {
        // create session scope
        $this->create();
        $this->createScope( $prefix );

        if ( empty( $prefix ) ) {
            $session =& $this->_session[$this->_key];
        } else {
            $session =& $this->_session[$this->_key][$prefix];
        }

        $session[$name] = $value;
    }

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
    function setVars( $vars, $prefix = null ) {
        // create session scope
        $this->create();
        $this->createScope( $prefix );

        if ( empty( $prefix ) ) {
            $session =& $this->_session[$this->_key];
        } else {
            $session =& $this->_session[$this->_key][$prefix];
        }

        foreach ( $vars as $name => $value ) {
            $session[$name] = $value;
        }
    }
  
    /**
     * Gets the value of the named variable in the session scope
     *
     * This function takes a name and retrieves the value of this 
     * variable from the session scope.
     *
     * @access public
     * @param  string name  : name  of the variable
     * @param  string prefix : adds another level of scope to the session
     * @return mixed
     *
     */
    function get( $name, $prefix = null ) {
        // create session scope
        $this->create();
        $this->createScope( $prefix );

        if ( empty( $prefix ) ) {
            $session =& $this->_session[$this->_key];
        } else {
            $session =& $this->_session[$this->_key][$prefix];
        }

        return CRM_Array::value( $name, $session );
    }

  

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
        // create session scope
        $this->create();
        $this->createScope( $prefix );

        if ( empty( $prefix ) ) {
            $session =& $this->_session[$this->_key];
        } else {
            $session =& $this->_session[$this->_key][$prefix];
        }

        foreach ($session as $name => $value) {
            $vars[$name] = $value;
        }
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

        $this->createScope( CRM_Session::RETURN_URL );

        array_push( $this->_session[$this->_key][CRM_Session::RETURN_URL], $returnUrl );
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

        $this->createScope( CRM_Session::RETURN_URL );

        array_pop ( $this->_session[$this->_key][CRM_Session::RETURN_URL] );
        array_push( $this->_session[$this->_key][CRM_Session::RETURN_URL], $returnUrl );
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
        $this->createScope( CRM_Session::RETURN_URL );

        return array_pop ( $this->_session[$this->_key][CRM_Session::RETURN_URL] );
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
        $this->createScope( CRM_Session::RETURN_URL );

        $lastElement = count( $this->_session[$this->_key][CRM_Session::RETURN_URL] ) - 1;
        return $lastElement >= 0 ? 
            $this->_session[$this->_key][CRM_Session::RETURN_URL][$lastElement] :
            null;
    }

}

?>