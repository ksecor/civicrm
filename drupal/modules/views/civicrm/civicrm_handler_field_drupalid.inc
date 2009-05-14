<?php

/**
 * Field handler to provide relationship to Drupal User ID
 *
 * @ingroup civicrm_field_handlers
 */
class civicrm_handler_field_drupalid extends views_handler_field {

  function construct( ) {
      civicrm_initialize( );
      require_once 'CRM/Core/BAO/UFMatch.php';
  }


   function render($values) {
     $cid =  $values->{$this->field_alias};
     $uf_id = CRM_Core_BAO_UFMatch::getUFId($cid);
     return $uf_id;
   } 

}
?>