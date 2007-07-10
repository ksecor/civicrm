<script type="text/javascript">
   var stateEnabled = false;
</script>

{if $form.location.$index.address.state_province }
  <div class="form-item">
    <span class="labels">
    {$form.location.$index.address.state_province.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.state_province.html}
    </span>
  </div>

 <script type="text/javascript">
    stateEnabled = true;
 </script>

{/if}

{if $form.location.$index.address.country }
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

             dojo.addOnLoad( function( ) 
               {
                  if ( !stateValue1 && !countryValue1 && stateEnabled ) { 
                     dojo.widget.byId( 'location_1_address_state_province' ).disable( );
                  } else {
                     if ( countryValue1 ) {
                       dojo.widget.byId( 'location_1_address_country' ).setAllValues( countryValue1, countryValue1 );
                     }

                     if ( stateValue1 && stateEnabled ) {
                       dojo.widget.byId( 'location_1_address_state_province' ).setAllValues( stateValue1, stateValue1 );
                     }
                  }
               }            
             );

             break;

       case 2:
             var countryValue2 = {/literal}"{$country_2_value}"{literal};
             var stateValue2   = {/literal}"{$state_province_2_value}"{literal};

             dojo.addOnLoad( function( ) 
               {
                  if ( !stateValue2 && !countryValue2 && stateEnabled ) { 
                     dojo.widget.byId( 'location_2_address_state_province' ).disable( );
                  } else {
                     if ( countryValue2 ) {
                       dojo.widget.byId( 'location_2_address_country' ).setAllValues( countryValue2, countryValue2 );
                     }

                     if ( stateValue2 && stateEnabled ) {
                       dojo.widget.byId( 'location_2_address_state_province' ).setAllValues( stateValue2, stateValue2 );
                     }
                  }
               }            
             );

             break;

       case 3:
             var countryValue3 = {/literal}"{$country_3_value}"{literal};
             var stateValue3   = {/literal}"{$state_province_3_value}"{literal};

             dojo.addOnLoad( function( ) 
               {
                  if ( !stateValue3 && !countryValue3 && stateEnabled ) { 
                     dojo.widget.byId( 'location_3_address_state_province' ).disable( );
                  } else {
                     if ( countryValue3 ) {
                       dojo.widget.byId( 'location_3_address_country' ).setAllValues( countryValue3, countryValue3 );
                     }

                     if ( stateValue3 && stateEnabled ) {
                       dojo.widget.byId( 'location_3_address_state_province' ).setAllValues( stateValue3, stateValue3 );
                     }
                  }
               }            
             );

             break;

       case 4:
             var countryValue4 = {/literal}"{$country_4_value}"{literal};
             var stateValue4   = {/literal}"{$state_province_4_value}"{literal};

             dojo.addOnLoad( function( ) 
               {
                  if ( !stateValue4 && !countryValue4 && stateEnabled ) { 
                     dojo.widget.byId( 'location_4_address_state_province' ).disable( );
                  } else {
                     if ( countryValue4 ) {
                       dojo.widget.byId( 'location_4_address_country' ).setAllValues( countryValue4, countryValue4 );
                     }

                     if ( stateValue4 && stateEnabled ) {
                       dojo.widget.byId( 'location_4_address_state_province' ).setAllValues( stateValue4, stateValue4 );
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