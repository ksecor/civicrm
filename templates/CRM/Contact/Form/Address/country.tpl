{if $form.location.$index.address.country_id}

{literal}

<script type="text/javascript">

function getStateProvince{/literal}{$index}{literal}( obj, lno, value, setState ) {

    // if value of country is not send get it from widget
    if ( ! value ) {
      var value = obj.getValue( );
    }
    
    //load state province only for valid country value

    //get state province id
    var widget = dijit.byId('location_' + lno + '_address_state_province_id');

    if ( !isNaN(value) ) {

      //enable state province only if country value exists
       widget.setDisabled( false );

       //set state province combo if it is not set
       if ( setState ) {
	  //translate select
          var sel = '&id=' + {/literal}"{ts}- type first letter(s) -{/ts}"{literal}; 
       } 

       //data url for state
       var res = {/literal}"{$stateUrl}"{literal};

       var queryUrl = res + '&node=' + value;

       if ( sel ) {
	   queryUrl = queryUrl + sel;
       }

       var queryStore = new dojox.data.QueryReadStore({url: queryUrl } );
       widget.store   = queryStore;
   } else {
       widget.setDisabled( true );
       var sel = {/literal}"{ts}- type first letter(s) -{/ts}"{literal}; 
       widget.setDisplayedValue( sel );
   }
}

</script>
{/literal}

<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.country_id.label}
    </span>
    <div class ="tundra" dojoType="dojox.data.QueryReadStore" jsId="country_idStore" url="{$countryUrl}">
    <span class="fields">
        {$form.location.$index.address.country_id.html}
        <br class="spacer"/>
        <span class="description font-italic">
            {ts}Type in the first few letters of the country and then select from the drop-down. After selecting a country, the State / Province field provides a choice of states or provinces in that country.{/ts}
        </span>
    </span>
    </div>
</div>

{/if}


