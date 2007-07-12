<script type="text/javascript">
   var stateEnabled = false;
</script>

{if $form.location.$index.address.state_province_id }
  <div class="form-item">
    <span class="labels">
    {$form.location.$index.address.state_province_id.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.state_province_id.html}
    </span>
  </div>

 <script type="text/javascript">
    stateEnabled = true;
 </script>

{/if}

{if $form.location.$index.address.country_id }
  {literal}
  <script type="text/javascript">

    var lno = {/literal}{$index}{literal};
    //data url for state
    var res = {/literal}"{$stateURL}"{literal};
    /* start of code to set defaults for state - country. Note: Code cleanup required  */

    switch ( lno ) {
       case 1:
             var countryValue1 = {/literal}"{$country_1_value}"{literal};
             var stateValue1   = {/literal}"{$state_province_1_value}"{literal};
             var countryId1    = {/literal}"{$country_1_id}"{literal};
             var stateId1      = {/literal}"{$state_province_1_id}"{literal};

             dojo.addOnLoad( function( ) 
               {
                  if ( !stateValue1 && !countryValue1 && stateEnabled ) { 
                     dojo.widget.byId( 'location_1_address_state_province_id' ).disable( );
                  } else {
                     if ( countryValue1 ) {
                       dojo.widget.byId( 'location_1_address_country_id' ).setAllValues( countryValue1, countryId1 );
                     }

                     if ( stateValue1 && stateEnabled ) {
                       dojo.widget.byId( 'location_1_address_state_province_id' ).setAllValues( stateValue1, stateId1 );
                     }
                  }
               }            
             );

             break;

       case 2:
             var countryValue2 = {/literal}"{$country_2_value}"{literal};
             var stateValue2   = {/literal}"{$state_province_2_value}"{literal};
             var countryId2    = {/literal}"{$country_2_id}"{literal};
             var stateId2      = {/literal}"{$state_province_2_id}"{literal};

             dojo.addOnLoad( function( ) 
               {
                  if ( !stateValue2 && !countryValue2 && stateEnabled ) { 
                     dojo.widget.byId( 'location_2_address_state_province_id' ).disable( );
                  } else {
                     if ( countryValue2 ) {
                       dojo.widget.byId( 'location_2_address_country_id' ).setAllValues( countryValue2, countryId2 );
                     }

                     if ( stateValue2 && stateEnabled ) {
                       dojo.widget.byId( 'location_2_address_state_province_id' ).setAllValues( stateValue2, stateId2 );
                     }
                  }
               }            
             );

             break;

       case 3:
             var countryValue3 = {/literal}"{$country_3_value}"{literal};
             var stateValue3   = {/literal}"{$state_province_3_value}"{literal};
             var countryId3    = {/literal}"{$country_3_id}"{literal};
             var stateId3      = {/literal}"{$state_province_3_id}"{literal};

             dojo.addOnLoad( function( ) 
               {
                  if ( !stateValue3 && !countryValue3 && stateEnabled ) { 
                     dojo.widget.byId( 'location_3_address_state_province_id' ).disable( );
                  } else {
                     if ( countryValue3 ) {
                       dojo.widget.byId( 'location_3_address_country_id' ).setAllValues( countryValue3, countryId3 );
                     }

                     if ( stateValue3 && stateEnabled ) {
                       dojo.widget.byId( 'location_3_address_state_province_id' ).setAllValues( stateValue3, stateId3 );
                     }
                  }
               }            
             );

             break;

       case 4:
             var countryValue4 = {/literal}"{$country_4_value}"{literal};
             var stateValue4   = {/literal}"{$state_province_4_value}"{literal};
             var countryId4    = {/literal}"{$country_4_id}"{literal};
             var stateId4      = {/literal}"{$state_province_4_id}"{literal};

             dojo.addOnLoad( function( ) 
               {
                  if ( !stateValue4 && !countryValue4 && stateEnabled ) { 
                     dojo.widget.byId( 'location_4_address_state_province_id' ).disable( );
                  } else {
                     if ( countryValue4 ) {
                       dojo.widget.byId( 'location_4_address_country_id' ).setAllValues( countryValue4, countryId4 );
                     }

                     if ( stateValue4 && stateEnabled ) {
                       dojo.widget.byId( 'location_4_address_state_province_id' ).setAllValues( stateValue4, stateId4 );
                     }
                  }
               }            
             );

             break;
    }

  /* end of code to set defaults for state - country */

  </script>
 {/literal}
{/if}