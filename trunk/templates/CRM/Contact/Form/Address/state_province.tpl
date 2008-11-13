<script type="text/javascript">
   var stateEnabled = false;
</script>

{if $form.location.$index.address.state_province_id }
  <div class="form-item">
    <span class="labels">
    {$form.location.$index.address.state_province_id.label}
    </span>
    <div class="tundra" dojoType="dojox.data.QueryReadStore" jsId="state_province_idStore" url="{$stateUrl}">
        <span class="fields">
        {$form.location.$index.address.state_province_id.html}
        </span>
    </div>
  </div>

 <script type="text/javascript">
    stateEnabled = true;
 </script>

{/if}

{if $form.location.$index.address.country_id }
  {literal}
  <script type="text/javascript">

  /* start of code to set defaults for state - country. Note: Code cleanup required  */
  {/literal}
  {if $index eq '1' } 
  {literal}
      var countryValue1 = {/literal}"{$country_1_value}"{literal};
      var stateValue1   = {/literal}"{$state_province_1_value}"{literal};

      dojo.addOnLoad( function( ) 
      {
	if ( !countryValue1 ) {
	  countryValue1 = dijit.byId( 'location_1_address_country_id' ).value;
	} 

	if ( !stateValue1 && !countryValue1 && stateEnabled ) { 
	  widget1 = dijit.byId( 'location_1_address_state_province_id' );
	  widget1.setDisabled( true );
	} else {
	  if ( countryValue1 ) {
	    var setState = true;
            if ( stateValue1 && stateEnabled ) {
	      setState = false;
	    }
	    
	    getStateProvince1( dijit.byId( 'location_1_address_country_id' ), 1, countryValue1, setState);

	    if ( stateValue1 && stateEnabled ) {
	      dijit.byId( 'location_1_address_state_province_id' ).setValue( stateValue1 );
	    }

	  }
	}
      });

  {/literal}
  {elseif $index eq '2' } 
  {literal}
      var countryValue2 = {/literal}"{$country_2_value}"{literal};
      var stateValue2   = {/literal}"{$state_province_2_value}"{literal};
      
      dojo.addOnLoad( function( ) 
      {
	 if ( !countryValue2 ) {
	   countryValue2 = dijit.byId( 'location_2_address_country_id' ).value;
	 } 

       	 if ( !stateValue2 && !countryValue2 && stateEnabled ) { 
    	   widget2 = dijit.byId( 'location_2_address_state_province_id' );
           widget2.setDisabled( true );
         } else {
	   if ( countryValue2 ) {
	     var setState = true;
	     if ( stateValue2 && stateEnabled ) {
	       setState = false;
	     }
	     
	     getStateProvince2( dijit.byId( 'location_2_address_country_id' ), 2, countryValue2, setState);
	     
	     if ( stateValue2 && stateEnabled ) {
	       dijit.byId( 'location_2_address_state_province_id' ).setValue( stateValue2 );
	     }
	   }
	 }
      });

  {/literal}
  {elseif $index eq '3' } 
  {literal}
      var countryValue3 = {/literal}"{$country_3_value}"{literal};
      var stateValue3   = {/literal}"{$state_province_3_value}"{literal};
      
      dojo.addOnLoad( function( ) 
      {
	 if ( !countryValue3 ) {
	   countryValue3 = dijit.byId( 'location_3_address_country_id' ).value;
	 } 

       	 if ( !stateValue3 && !countryValue3 && stateEnabled ) { 
    	   widget3 = dijit.byId( 'location_3_address_state_province_id' );
           widget3.setDisabled( true );
         } else {
	   if ( countryValue3 ) {
	     var setState = true;
	     if ( stateValue3 && stateEnabled ) {
	       setState = false;
	     }
	     
	     getStateProvince3( dijit.byId( 'location_3_address_country_id' ), 3, countryValue3, setState);
	     
	     if ( stateValue3 && stateEnabled ) {
	       dijit.byId( 'location_3_address_state_province_id' ).setValue( stateValue3 );
	     }
	   }
	 }
      });

  {/literal}
  { elseif $index eq '4' } 
  {literal}
      var countryValue4 = {/literal}"{$country_4_value}"{literal};
      var stateValue4   = {/literal}"{$state_province_4_value}"{literal};
      
      dojo.addOnLoad( function( ) 
      {

	if ( !countryValue4 ) {
	  countryValue4 = dijit.byId( 'location_4_address_country_id' ).value;
	} 

	if ( !stateValue4 && !countryValue4 && stateEnabled ) { 
	  widget4 = dijit.byId( 'location_4_address_state_province_id' );
	  widget4.setDisabled( true );
	} else {
	  if ( countryValue4 ) {
	    var setState = true;
	    if ( stateValue4 && stateEnabled ) {
	      setState = false;
	    }
	    
	    getStateProvince4( dijit.byId( 'location_4_address_country_id' ), 4, countryValue4, setState);
	    
	    if ( stateValue4 && stateEnabled ) {
	      dijit.byId( 'location_4_address_state_province_id' ).setValue( stateValue4 );
	    }
	  }
	}
      });

  {/literal}
  { /if } 
  {literal}

  /* end of code to set defaults for state - country */

  </script>
 {/literal}
{/if}
