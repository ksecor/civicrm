<?php

class CRM_Selector_Base {

  /**
   * This function gets the attribute for the action that
   * it matches.
   *
   * @param string  match      the action to match against
   * @param string  attribute  the attribute to return ( name, link, linkName, menuName )
   *
   * @return string            the attribute that matches the action if any
   *
   * @access public
   *
   */
  function getActionAttribute( $match, $attribute = 'name' ) {
    $links = $this->getLinks();

    foreach ( $link as $action => $item ) {
      if ( $match & $action ) {
        return $item[$attribute];
      }
    }
    return null;
  }

  /**
   * This is a virtual function, since the $_links array is typically
   * static, we use a virtual function to get the links array. Each 
   * inherited class must redefine this function
   */
  function &getLinks() {
    return null;
  }

}

?>