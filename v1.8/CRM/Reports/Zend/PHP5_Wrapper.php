<?
require_once('Reports/Zend/Birt.php');

/**
 * This class is a special wrapper for the BIRT object for PHP5, it wraps a real
 *  Zend_Birt object for catching and throwing exceptions
 */
class Reports_Zend_PHP5_Wrapper {
	
	/**
	 * The real Zend_Birt object that is used
	 *
	 * @var Zend_Birt
	 */
	private $_wrapped_Zend_Birt_object;
	
	/**
	 * Create an instance of the given wrapped class, wrapping the class with a try and catch block
	 * NOTE: the wrapped class must implement the getError method
	 *
	 * @todo Currently the wrapped class must have one (and only one) parameter for the constructor
	 * @param string $wrapped_class The wrapped class name, must implement the getError method
	 * @param mixed $constructor_param The constructor parameter for the wrapped class
	 * @throws Exception
	 */
	protected function instanceWrappedObject($wrapped_class,$constructor_param) {
        echo "$wrapped_class,$constructor_param<p>";
		try {
			$this->_wrapped_Zend_Birt_object = new $wrapped_class($constructor_param);
		} catch (Exception $e) {	// Exception occured during the constructor
			throw new Exception(self::_getJavaExceptionMsg($e),0);
		}
		
		// In case a fatal error occured in the constructor of PHP4, we throw exception in PHP5
		if ($this->_wrapped_Zend_Birt_object->getError()) {
			throw new Exception($this->_wrapped_Zend_Birt_object->getError(),0);
		}
	}
	
	/**
	 * Using the magic function __call to call all the Zend_Birt methods
	 *  The call to the methods is wrapped with a try and catch block
	 *
	 * @param string $methodName
	 * @param array $args
	 * @return mixed
	 * @throws Exception
	 */
	public function __call($methodName, $args) {
		try {
			if (!is_callable(array($this->_wrapped_Zend_Birt_object, $methodName))) {
				trigger_error("Call to undefined function: ".get_class($this)."::$methodName()", E_USER_ERROR);
				die();
			}
            print_r( $this->_wrapped_Zend_Birt_object );
            print_r( $methodName );
			$return_value = call_user_func_array(
						array($this->_wrapped_Zend_Birt_object, $methodName),
						$args
					);
			
			if ($return_value === false && $this->_wrapped_Zend_Birt_object->getError()) {
				throw new Exception($this->_wrapped_Zend_Birt_object->getError(), 0); 
			}
			return $return_value;
	
		} catch (Exception $e) {
            print_r( $e );
			throw new Exception(self::_getJavaExceptionMsg($e),0);
		}
	}
	
	/**
	 * Return the message of the Java exception out of a PHP exception
	 *
	 * @param Exception $exception
	 * @return string
	 */
	static private function _getJavaExceptionMsg(Exception $exception) {
		// Clean the already collected java exception.
		java_last_exception_clear();
		if (BIRT_DEBUG_MODE) {
			return $exception->getMessage();
		}
		// Get the cause of the Java exception
		if (isset($exception->javaException) && is_object($exception->javaException)) {
			$exception = $exception->javaException;
			
			while (is_object($exception->getCause())) {
				$exception = $exception->getCause();
			}
		}
		
		if ($exception->getMessage()) {
			return $exception->getMessage();
		}
		return $exception->toString();
	}
	
}

?>