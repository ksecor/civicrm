{if $form.location.$index.address.country_id}

{literal}

<script type="text/javascript">

function getStateProvince{/literal}{$index}{literal}( obj, lno ) {

    // get the typed value
    var value = obj.getValue( );

    //data url for state
    var res = {/literal}"{$stateURL}"{literal};
    
    //get state province id
    var widget = dojo.widget.byId('location_' + lno + '_address_state_province_id');
    
    //enable state province
    widget.enable( );
    //with (widget.downArrowNode.style) { width = "15px";	height = "15px";}

    //check if state exist for country
    var stateExist = false;
    
    //translate select
    var sel = {/literal}"{ts} - type first letter(s) - {/ts}"{literal};

    //set state province combo if it is not set
    if ( !widget.getValue( ) ) {
        widget.setAllValues( sel,'' );
    }

    //clear state province combo list
    widget._clearResultList();

    var bindArgs = {
        url: res,
        method: 'GET',
        type: "text/json",
        load: function(type, data)
        {          
            stateExist = true;
            eval("var decoded_data = "+data);            
            if ( data.length > 2) {
               widget.dataProvider.searchUrl = res + '&node=' + value + '&sc=child';
            }
        }            
    };            
  
    //if state exits then add data to combo
    if ( stateExist ) {
       bindArgs.content = { node: value };
    } 

    // Get all preparations
    dojo.io.bind(bindArgs);        
}

</script>
{/literal}

<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.country_id.label}
    </span>
    <span class="fields">
        {$form.location.$index.address.country_id.html}
        <br class="spacer"/>
        <span class="description font-italic">
            {ts}Type in the first few letters of the country and then select from the drop-down. After selecting a country, the State / Province field provides a choice of states or provinces in that country.{/ts}
        </span>
    </span>
</div>

{/if}


