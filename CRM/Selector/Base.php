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
   *
   * links is an array of associative arrays. Each element of the array
   * has 4 fields
   *
   * name    : the name of the link
   * link    : the URI to be used for this link, along with any strings that will
   *           be replaced dynamically
   * linkName: (same as name??)
   * menuName: Sometimes the linkName and menuName differ. The linkName could be
   *           "Edit Contact", while the menuName would be the more descriptive
   *           "Edit Contact Details"
   *
   */
  function &getLinks() {
    return null;
  }

}

?>