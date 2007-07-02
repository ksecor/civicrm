<?php

class Standalone_User {
  
  function Standalone_User( $identity_url, $email, $first_name = null, $last_name = null ) {
    $this->identity_url = $identity_url;
    $this->email = $email;
    $this->first_name = $first_name;
    $this->last_name = $last_name;
  }
}

?>