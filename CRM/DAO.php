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
	
  var $_original = null; //cache of what is currently in the db
  var $_childError = null;
	
  var $_useUpdateCache = true;
	
  static $objectCount = array();
  private $fetched = false; // set to true when fetch is called.

  function __construct() {
    if ( array_key_exists(get_class($this), self::$objectCount ) ) {
      self::$objectCount[get_class($this)] = self::$objectCount[get_class($this)] + 1;
    } else {
      self::$objectCount[get_class($this)] = 1; 
    }
  }
	
  /**
   * initialize DB_DataObject DB connection, and debugging
   *
   * @note should also handle setting factories?
   */
  function init($dsn, $debugLvl=0) {
    $options = &PEAR::getStaticProperty('DB_DataObject','options');
    $options = array(
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
	
  function _copyToCache() {
    if ( $this->canUpdate() && $this->_useUpdateCache ) {
      $this->_original = null;
		 	
      //don't clone, just make a light weight object with the same db fields
      foreach( array_keys($this->table()) as $k ) {
        $this->_original->$k = $this->$k;
      }
		 	
    }
  }
	
  /***
   * Returns the db table name for this object. 
   *
   * For use when doing a selectAdd or whereAdd and you
   * don't want to hardcode the table name into the SQL text.
   *
   * @access public
   */
  function getTableName() {
    return $this->__table;
  }
	
  /**
   * Workaround to get the DB_DataObject::links() function to work. 
   * Include it in your object's table() function;
   *
   * @access protected
   */
  function setupLinks() {
		
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
   * Overrides DB_DataObject::fetch().
   *
   * @access public
   */
  function fetch() {
    $rtn = parent::fetch();
    $this->fetched = true;
    $this->_copyToCache();
		
    return $rtn;
  }

  function isFetched() {
    return $this->fetched;
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
  
  /**
   * add related objects based on the links() array
   *
   * symmetrical with getLink(), given either a CRM_DAO
   * or an array, attach a related object to this object.  if an 
   * array is passed a new object will be created, and the array 
   * will be based to setProperties()
   *
   * @param string $col  column tables are linked on (index into links array)
   * @param mixed $props GS_DataObject or array of properties
   * @return int Id on success
   *
   */
  function addLink($col, $props) {
    $links = $this->links();
    if ($links && isset($links[$col]) ) {
      list($table,$link) = explode(':', $links[$col] );
      
      if (is_object($props) && 
          is_a('CRM_DAO') && $prop->getTableName() == $table ) {
        $obj = $props;
      } else {
        $obj = self::factory($table);
        $obj->setProperties($props);
      }
      $get = 'get' . $col;
      $set = 'set' . $link;
      $obj->$set( $this->$get() );

      return $obj->save();
    }
    else {
      GS_Error::fatal("No relationship setup on $col");
    }
  }
	
  function joinAdd($obj = false, $joinType='INNER', $joinAs=false, $joinCol=false) {
		
    //patch for the bugs in the way DB_DataObject uses links()
    $this->setupLinks();
        
    // joinAdd() should clear the joinAdd field as in the parent
    if ($obj) {
      $obj->setupLinks();
    }
		
    return parent::joinAdd($obj,$joinType,$joinAs,$joinCol);
  }
	
  function getLinks($format = '_%s') {
    //patch for the bugs in the way DB_DataObject uses links()
    $this->setupLinks();
    return parent::getLinks($format);
  }
	
  function getLink($row, $table = null, $link = false) {
    //patch for the bugs in the way DB_DataObject uses links()
    $this->setupLinks();
    return parent::getLink($row,$table,$link);
  }
	
  /**
   * Overrides DB_DataObject::update().
   * 
   * @return the objects id if successful, false if the update failed. Ignores 'No affeted rows' message/error.
   * @access public
   */
  function update($dataObject = false) {
    if ( $this->_original && ($this->id != $this->_original->id) ) {
      CRM_Error::fatal('Object id does not match update cache. If you are trying to reuse this object for updates, call turnOffUpdateCache() first.');
    }
	    
    if ( !$this->canUpdate() ) {
      $className = $this->getClass();
      CRM_Error::fatal("This object cannot be updated: $className");
    }
	    
    //this should work as long as we never change primary keys outside of the db
    //and the primary key is called 'id'
    if ($dataObject == false && $this->_original) {
      if ( $this->isDirty() ) {

        //copy any insertOnly fields from the original so they are not updated
        foreach( $this->insertOnlyFields() as $insertOnlyField ) {
          $this->$insertOnlyField = $this->_original->$insertOnlyField;
        }
	    		
        $rtn = parent::update($this->_original);
      } else {
        $this->debug("NOT UPDATING:  object isn't dirty", 1);
        $rtn = $this->getId();
      }
    } else { //always update in this case
      $rtn = parent::update($dataObject);
    }
	    
    //always return the id from an update, even if we get a DB_DATAOBJECT_ERROR_NOAFFECTEDROWS error
    if ( (!$rtn) && (!$this->checkForError()) ) {
      $rtn = $this->getId();
    }
	
    $this->_copyToCache();
		
    return $rtn;
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
   * Used by {link @_checkRequired} and {link @validate()}.
   * Override if your subclass has fields that are NOT NULL and do not have a default value specified in the db.
   *
   * @access public
   */
  function requiredFields() {
    static $requiredFields;
    if ( !isset ($requiredFields) ) {
      $requiredFields = array();
    }
		
    return $requiredFields;
  }

  /**
   * used by isDirty and update() to determine if there are any fields to leave out of the query
   *
   * @access public
   * @return array of strings
   */
  function insertOnlyFields() {
    static $insertOnlyFields;
    if ( !isset ($insertOnlyFields) )
      $insertOnlyFields = array();
		
    return $insertOnlyFields;
  }

  /**
   *
   * @return false if no error, otherwise returns an error object
   * @access public
   */
  function &checkForError() {
    if ( $this->_childError ) {
      return $this->_childError;
    }
    		
    if ($this->_lastError ) {
      return $this->_lastError;//ignore no affected rows error
    }
    	
    //this sucks; the error code for "already exists" is the same as "no affected rows"
    if ( $this->_lastError && strpos( $this->_lastError->getMessage(), 'already exists') ) {
      return $this->_lastError;
    }
      
    return false;
  }
    
  /**
   * For use with compound objects and the save() function.
   * 
   * During the save process, if one of the associated child object's insert/updates fails (an address) within
   * a parent (member object) you can call setChildError on the parent to let it keep track of the error, so the caller
   * only has to call checkForError on the parent.
   * Example, $address->save();
   *			$member->setChildError($address->checkForError());
   *			if ( !$member->checkForError() ) {
   *				echo 'success';
   *			}
   * @access public
   */
  function setChildError(&$err) {
    	
    if ( $err ) {
      //only copy the error if we don't already have one.
      if ( !$this->_childError ) {
        $_childError =& $err;
      }
    }
	    
    return $err;
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
    if ( $value == GS_DataObject::NULL_PROPERTY )
      return true;//no error; ignore
	 		
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
	
  /**
   * Returns true if the object's values do not match the _original copy or the _original field is not set.
   * Otherwise returns false.
   *
   * @access protected
   */
  function isDirty() {
    if ( !$this->_useUpdateCache )
      return true;
    		
    if ( !$this->_original )
      return true;
    	
    //compare, see if anything is different
    $insertOnly = $this->insertOnlyFields();

    foreach( array_keys($this->table()) as $key) {
      if ( array_search( $key, $insertOnly) === false ) {
        if ($this->$key != $this->_original->$key) {
          return true;
        }
      }
    }
    	
    return false;
  }
	
  function hasData() {

    foreach( array_keys($this->table()) as $field) {
      if ( isset($this->$field) && $this->$field ) 
        return true;
    }
		
    return false;
  }
	
  function turnOffUpdateCache() {
    $this->_original = null;
    $this->_useUpdateCache = false;
  }
	
  // call a DB specific escape string
  function escapeString( $str ) {
    // need to add link information here
    return mysql_real_escape_string( $str );
  }

}

/**
 * these are constants are used to defined field types in the table() method
 * and build on the constants defined in DB_DataObject
 */
define('DB_DATAOBJECT_ENUM',  '2');  // treat as string

?>