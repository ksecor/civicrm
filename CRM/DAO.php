<?php

require_once 'PEAR.php';
require_once 'DB/DataObject.php';

class CRM_DAO extends DB_DataObject {

  /**
   * If a you call setProperty and use NULL_PROPERTY as a value, setProperty will ignore it.
   * If you are reusing the same array to set properties for multiple data objects, you should
   * reset the array values with NULL_PROPERTY instead of null values for better db performance.
   */
  const NULL_PROPERTY = 'NULL_PROPERTY';
	
  function __construct() {
  }
	
  /**
   * initialize DB_DataObject DB connection, and debugging
   *
   * @note should also handle setting factories?
   */
  function init($dsn, $debugLvl=0) {
    $options =& PEAR::getStaticProperty('DB_DataObject','options');
    $options =  array(
                      'database'         => $dsn,
                      );
    
    if ($debugLvl) {
      DB_DataObject::DebugLevel($debugLvl);
    }
  }
	
  /**
   * Use this if you want to resue the same object for inserts and updates.
   * Not recommended for multiple fetch() calls.
   *
   *
   */
  function resetValues() {
    foreach( array_keys( $this->table() ) as $field ) {
      unset($this->$field);
    }

    // lets reset the query array manually
    $this->_query = array( );
    $this->_query['condition'] = '';

    $this->_original = null;
  }
	
  /**
   * Static function to set the factory instance for this class. Call this one time only.
   * @access public
   */
  function setFactory(&$factory) {
    global $CRM_DAO_Factory;

    if ( !isset( $CRM_DAO_Factory ) ) {
      $CRM_DAO_Factory =& $factory;
    }
  }
	
  /**
   *	Factory method to instantiate a new object from a table name.
   *   @access public
   */
  function factory($table) {
    global $CRM_DAO_Factory;

    if ( !isset($CRM_DAO_Factory) ) {
      return parent::factory($table);
    }
		
    return $CRM_DAO_Factory->create($table);
  }
	
  /**
   * Workaround to get the DB_DataObject::links() function to work. 
   * Include it in your object's table() function;
   *
   * @access protected
   */
  function setLinks() {
		
    //when this wasn't here, _database wasn't getting set for some classes
    if (!@$this->_database) {
      $this->_connect();
    }
    
    //ugly workaround to get links set up
    if ( !isset($GLOBALS['_DB_DATAOBJECT']['LINKS'][$this->_database]) ) {
      $GLOBALS['_DB_DATAOBJECT']['LINKS'][$this->_database] = array();
    }
	    
    if ( (!array_key_exists($this->__table, $GLOBALS['_DB_DATAOBJECT']['LINKS'][$this->_database])) && $this->links() ) {
      $GLOBALS['_DB_DATAOBJECT']['LINKS'][$this->_database][$this->__table] = $this->links();
    }
		
  }
	
  /**
   * Assembles a query based on the object passed in, then finds associated records from one of its linked tables.
   * Example, let's say I have a subscription record that is an intersection of members with memberLists.
   * I can do this to get all of the member objects for a particular member list:
   * $subscription->setMemberListId($memberList->getId())
   * $member->findFromLinks($subscription);
   * while ( $member->fetch() ) { doSomethingCool(); }
   *
   * Sort of like getLink(), but you can use it to get multiple results.
   */
  function findFromLinks($matchOnObj) {
    $matchTable = $matchOnObj->getTableName();

    $matchOnObj->_build_condition($matchOnObj->table());
    $matchLinks = $matchOnObj->links();
    if ( count($matchLinks) == 0 ) return false;
		
    foreach( array_keys($matchLinks) as $link) {
      $primaryKey = $matchLinks[$link];
      $table = substr($primaryKey, 0, strpos($primaryKey, ':'));
      $key = substr($primaryKey, strpos($primaryKey, ':') + 1, strlen($primaryKey));
      
      //found it
      if ( ($table == $this->__table) && (array_key_exists( $key, $this->table())) ) {
        return $this->query("SELECT $table.* 
							 FROM $table INNER JOIN $matchTable
							 ON $table.$key = $matchTable.$link
							 {$matchOnObj->_query['condition']} ;" );
      }
    }
		
    return false;
    
  }
  
  function getLinks($format = '_%s') {
    //patch for the bugs in the way DB_DataObject uses links()
    $this->setLinks();
    return parent::getLinks($format);
  }
	
  function getLink($row, $table = null, $link = false) {
    //patch for the bugs in the way DB_DataObject uses links()
    $this->setLinks();
    return parent::getLink($row,$table,$link);
  }
	
  /**
   * Defines the default key as 'id'.
   * Override if your object does not use the 'id' field as a primary key.
   *
   * @access protected
   */
  function keys() {
    static $keys;
    if ( !isset ($keys) ) {
      $keys = array('id');
    }
    return $keys;
  }
    
  /**
   * Tells DB_DataObject which keys use autoincrement.
   * 'id' is autoincrementing by default.
   * 
   * @access protected
   */
  function sequenceKey() {
    static $seqKey;
    if ( !isset ($seqKey) ) {
      $seqKey = array('id', true);
    }
    return $seqKey;
  }
	
  /**
   * An accessor for fields using a non-case comparision on the field name
   *
   * @return returns the value or the string 'PROPERTY_NOT_FOUND'
   * @access public
   */
  function getProperty($name) {
    $nameLen = strlen($name);
    foreach( array_keys($this->table()) as $staticProp ) {
      if ( strncasecmp($name, $staticProp, $nameLen) == 0 ) {
        $getter = 'get'.ucfirst($staticProp);
        return $this->$getter();
      }
    }
    return 'PROPERTY_NOT_FOUND';
  }

  /**
   * A setter for fields using a non-case comparision on the field name
   *
   *
   * @return false if the property is not found
   * @access public
   */
  function setProperty($name, $value) {
    if ( $value == CRM_DAO::NULL_PROPERTY )
      return true; // no error; ignore
	 		
    $nameLen = strlen($name);
	 	
    //check static props
    foreach( array_keys($this->table()) as $staticProp ) {
      if ( strcasecmp($name, $staticProp) == 0 ) {
        $setter = 'set'.ucfirst($staticProp);
        $this->$setter($value);
        return true;
      }
    }
	 	
    return false;
  }

  /**
   * bulk set properties on a dataobject from hash
   *
   */
  function setProperties($nameValuePairs) {
    if ( !$nameValuePairs ) return;
       
    foreach ( array_keys($nameValuePairs) as $name ){
      $this->setProperty($name, $nameValuePairs[$name]);
    }
  }

  /**
   *
   * Returns a array of object values.
   * @return an associative array of name,value pairs
   *
   * @access public
   */
  function getProperties() {
		
    $rtn = array();
		
    //check static props
    //should we add null values?
    foreach( array_keys($this->table()) as $staticProp ) {
      $getter = 'get'.ucfirst($staticProp);
      $rtn[$staticProp] = $this->$getter();
    }
	 	
    return $rtn;
	 	
  }
	 
  /** 
   * PHP5 style overloaded accessors
   *
   * @note this is a temporary fix, should really be fixed in DB_DataObject
   */
   
  function __call($method,$params) {
        
    $type = strtolower(substr($method,0,3));
    $class = get_class($this);
	        
    if (($type != 'set') && ($type != 'get')) {
      CRM_Error::fatal("method does not exist: $method ");
    }
	        
    $field = substr($method,3,1);
    $field = strtolower($field);
    $field = $field.substr($method,4);
		
    if ($type == 'get' && isset($this->$field)) {
      return $this->$field;
    } else if ($type == 'get') {
      return null; //TODO; throw
    }
		   
    if ( $params == null )return;
    if ( !isset($params[0]) ) return;
		   
    //force true to 1; false to 0
    if ( $params[0] === false )
      $params[0] = 0;
    else if ( $params[0] === true )
      $params[0] = 1;
		   
    $this->$field = $params[0];
    return;
	                    
  }
	
  // call a DB specific escape string
  function escapeString( $str ) {
    // need to add link information here
    return mysql_real_escape_string( $str );
  }

}

?>