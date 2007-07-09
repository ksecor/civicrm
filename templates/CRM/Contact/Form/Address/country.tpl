{if $form.location.$index.address.country}

{literal}

<script type="text/javascript">

function checkParamChildren(value) {

    str_arr = this.widgetId.split('_');
    
    // get location no
    var lno = str_arr[1];

    //data url for state
    var res = {/literal}"{$stateURL}"{literal};
    
    //enable state province
    dojo.widget.byId('location_'+ lno + '_address_state_province').enable( );

    //check if state exist for country
    var stateExist = false;
    
    //clear state combo
    dojo.widget.byId('location_' + lno + '_address_state_province').selectedResult = '';
    dojo.widget.byId('location_' + lno + '_address_state_province').setAllValues('- select -','');

    var bindArgs = {
        url: res,
        method: 'GET',
        type: "text/json",
        load: function(type, data)
        {          
            stateExist = true;
            eval("var decoded_data = "+data);            
            if ( data.length > 2) {
                dojo.widget.byId('location_' + lno + '_address_state_province').dataProvider.searchUrl 
		  = res + '&node='+value+'&sc=child';
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
    {$form.location.$index.address.country.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.country.html}
    </span>
</div>

{/if}


