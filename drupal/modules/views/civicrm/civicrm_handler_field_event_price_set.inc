<?php

  /*
 * Copyright (C) 2009 James Rakich
 * Licensed to CiviCRM under the Academic Free License version 3.0.
 *
 * civicrm_handler_field_event_price_set.inc
 * Gathers Price Set Information for the event to be displayed in a list (inline, numbered or unnumbered)
 *
 */
class civicrm_handler_field_event_price_set extends views_handler_field_prerender_list {
  function construct() {
    parent::construct();
    $this->additional_fields['event_page_id'] = array('table' => 'civicrm_event_page', 'field' => 'id');
  }

    function query() {
    $this->add_additional_fields();
    $this->field_alias = $this->aliases['event_page_id'];
  }

    function pre_render ($values) {

     civicrm_initialize( );
      require_once 'CRM/Utils/Money.php';
      require_once 'CRM/Core/BAO/PriceSet.php';
      require_once 'CRM/Core/OptionGroup.php';
      require_once 'CRM/Core/BAO/Discount.php';

     $eventPageIDs = array();
     $this->items = array();

     foreach ($values as $result) {
       $eventPageIDs[] = $result->{$this->aliases['event_page_id']};
     }


     if ($eventPageIDs) {
       foreach ($eventPageIDs as $eventPageID) {
           $priceSet = array();


           if ( isset ($eventPageID ) ) {
            if ( ! CRM_Core_BAO_PriceSet::getFor( 'civicrm_event_page', $eventPageID ) ) {
                //retrieve event fees.
                $discountId = CRM_Core_BAO_Discount::findSet( $eventPageID, 'civicrm_event' );
                if ( $discountId ) {
                    CRM_Core_OptionGroup::getAssoc(
                                                   CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Discount',
                                                                                $discountId,
                                                                                'option_group_id' ),
                                                   $priceSet, 'id' );
                } else {
                    CRM_Core_OptionGroup::getAssoc( "civicrm_event_page.amount.{$eventPageID}", $priceSet );
                }
            }
        }


           for ($i = 1; $i <= count($priceSet[label]); $i++) {
             $this->items[$eventPageID][] = $priceSet[label][$i] . " " . CRM_Utils_Money::format($priceSet[value][$i]);
             }
             
       }
     }

      
    }

}
