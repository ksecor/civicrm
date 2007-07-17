<?php

class Standalone_User {
  
  function Standalone_User( $identityUrl, $email = null, $firstName = null, $lastName = null ) {
    $this->identity_url = $identityUrl;
    $this->email = $email;
    $this->name = $firstName . ' ' . $lastName;
  }
}

?>