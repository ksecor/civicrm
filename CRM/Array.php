<?php

class CRM_Array {

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

}

?>
