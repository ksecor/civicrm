<?php

class CRM_Array {

  /**
   * fill: sets the same value to all the elements in the array
   *
   * @access public
   *
   * @param list  the array to be filled
   * @param value the fill value
   * 
   * @return none
   *
   */
  static function fill( &$list, $value ) {
    foreach ( $list as $n => $v ) {
      $list[$n] = $value;
    }
  }
  
  /**
   * if the key exists in the list returns the associated value
   *
   * @access public
   *
   * @param array  $list  the array to be searched
   * @param string $key   the key value
   * 
   * @return value if exists else null
   *
   */
  static function value( $key, &$list ) {
    if ( is_array( $list ) ) {
      return array_key_exists( $key, $list ) ? $list[$key] : null;
    }
    return null;
  }

  /**
   * if the value exists in the list returns the associated key
   *
   * @access public
   *
   * @param list  the array to be searched
   * @param value the search value
   * 
   * @return key if exists else null
   *
   */
  static function key( $value, &$list ) {
    if ( is_array( $list ) ) {
      $key = array_search( $value, $list );
      return $key ? $key : null;
    }
    return null;
  }

  /**
   * takes 2 arrays and appends the second to the first
   *
   * @param array the array to be appended to
   * @param mixed the object that will be appended to the above array
   *
   * @return void
   * @access public
   *
   */
  static function append( &$orig, &$value ) {
    if ( is_array( $value ) ) {
      foreach ( $value as $v ) {
        $orig[] = $v;
      }
    } else {
      $orig[] = $value;
    }
  }
  
  /**
   *
   * takes a variable number of arrays and returns
   * the difference of the keys. Basically eliminates
   * all the keys that appear in arrays 1..n from
   * array 0
   *
   * not sure where or when we'd use this
   *
   * @param mixed  list of assoc arrays
   *
   * @return array the resultant differential array
   *
   * @access public
   *
   */
  static function assocDiffKeys( ) {
    $args = func_get_args();

    $res = $args[0];
    if ( ! is_array( $res ) ) {
      return array();
    }

    for ( $i = 1; $i < count( $args ) ; $i++ ) {
      if ( ! is_array( $args[$i] ) ) {
        continue;
      }

      foreach ( $args[$i] as $key => $data ) {
        unset( $res[$key] );
      }
    }
    return $res;
  }

  /**
   *
   * takes a variable number of arrays and returns
   * the set of keys that appear in all arrays.
   * Basically eliminates
   * all the keys that do not appear in
   * ALL the  arrays 1..n from array 0
   *
   * not sure where or when we'd use this
   *
   * @param mixed  list of assoc arrays
   *
   * @return array the resultant SAME array
   *
   * @access public
   *
   */
  static function assocSameKeys( ) {
    $args = func_get_args();
   
    $res = $args[0];
    if ( ! is_array( $res ) ) {
      return array();
    }
   
    for ( $i = 1; $i < count( $args ) ; $i++ ) {
      if ( ! is_array( $args[$i] ) ) {
        continue;
      }

      foreach ( $res as $key => $data ) {
        if ( ! array_key_exists( $key, $args[$i] ) ) {
          unset( $res[$key] );
        }
      }
    }  

    return $res;
  }

}

?>
