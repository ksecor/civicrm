{if $form.location.$index.address.country}

{literal}

<script type="text/javascript">

var active_levels = Array();

function checkParamChildren(value) {
    checkChildren(this, 'country', value, checkParamChildren);
}

function checkChildren(obj, element, value, src_func) {

    // first we delete existing elements
    str_arr = obj.widgetId.split('_');

    element = str_arr;

    if (!active_levels[element]) {
        active_levels[element] = Array();
    }        
    
    // first we delete existing elements
    str_arr = obj.widgetId.split('_');

    if (str_arr[1]) {
        element_id =  str_arr.pop();
        removeChildren(element, element_id);
        active_levels[element] = element_id;
    }
    else {
        active_levels[element][value] = Array();
        removeChildren(element, 0);
        active_levels[element] = 0;
    }        

 
    var cname = String(element);

    var lno = cname.substring(cname.length, 7 );

    var res = {/literal}"{$stateURL}"{literal};
    
    var state = 'location[' + lno + '][address][state_province]';

    var bindArgs = {
        url: res,
        method: 'GET',
        type: "text/json",
        load: function(type, data)
        {          
            eval("var decoded_data = "+data);            
            if ( data.length > 2) {
                tmp = active_levels[element];
                tmp++;
                active_levels[element] = tmp;
                
                node = active_levels[element];

                container = document.createElement('span');
                container.setAttribute('id',element+'_container_'+node);
                dojo.byId(element+'_children').appendChild(container);

                dojo.widget.createWidget("ComboBox", 
                    {
                      value: 'this should never be seen - it is replaced!',
                      dataUrl: res + '&node='+value+'&sc=child',
                      id: element +'_'+node,
                      style: 'width: 300px',
                      name : state,
                      mode: 'remote'
                    }, 

               dojo.byId (element+'_container_'+node));

               /* start of code to set defaults for state. Note: Code cleanup required  */

               if ( lno == 1) {
                 {/literal}{if $country1_state_value } {literal}
                 var stateValue1 = {/literal}"{$country1_state_value}"{literal};
                 dojo.addOnLoad( function( ) 
                   {
                      dojo.widget.byId( 'country1_1' ).setAllValues( stateValue1, stateValue1 );
                   }            
                 );
                 {/literal}{/if }{literal}

              } else if ( lno == 2) {
                 {/literal}{if $country2_state_value } {literal}
                 var stateValue2 = {/literal}"{$country2_state_value}"{literal};
                 dojo.addOnLoad( function( ) 
                   {
                      dojo.widget.byId( 'country2_1' ).setAllValues( stateValue2, stateValue2 );
                   }            
                 );
                 {/literal}{/if }{literal}

              } else if ( lno == 3) {
                 {/literal}{if $country3_state_value } {literal}
                 var stateValue3 = {/literal}"{$country3_state_value}"{literal};
                 dojo.addOnLoad( function( ) 
                   {
                      dojo.widget.byId( 'country3_1' ).setAllValues( stateValue3, stateValue3 );
                   }            
                 );
                 {/literal}{/if }{literal}

              } else if ( lno == 4) {
                 {/literal}{if $country4_state_value } {literal}
                 var stateValue4 = {/literal}"{$country4_state_value}"{literal};
                 dojo.addOnLoad( function( ) 
                   {
                      dojo.widget.byId( 'country4_1' ).setAllValues( stateValue4, stateValue4 );
                   }            
                 );
                 {/literal}{/if }{literal}
              }
   
              /* end of code to set defaults for state */
           }
        }            
    };            
            
    bindArgs.content = { node: value };
             
    // Get all preparations
    dojo.io.bind(bindArgs);        
}

function removeChildren(element, value) {
    for (var i=active_levels[element]; i>value; i--) {
        if (dojo.widget.byId(element+'_'+i)) {
            dojo.widget.byId(element+'_'+i).selectedResult = '';
            dojo.widget.byId(element+'_'+i).setAllValues('','');
            dojo.widget.byId(element+'_'+i).dataProvider.searchUrl = null;
            dojo.widget.byId(element+'_'+i).destroy();
        }
    }
}
</script>
{/literal}

<div id="select_root" class="form-item">
    <span class="labels">
    {$form.location.$index.address.country.label}
    </span>
    <span class="fields">
    {$form.location.$index.address.country.html}
    </span>
</div>


{literal}
<script type="text/javascript">

    /* start of code to set defaults for country. Note: Code cleanup required */
    var lno = {/literal}{$index}{literal};

    switch ( lno ) {
       case 1:
             {/literal}{if $country1_value } {literal}
             var countryValue1 = {/literal}"{$country1_value}"{literal};
             dojo.addOnLoad( function( ) 
               {
                  dojo.widget.byId( 'country1' ).setAllValues( countryValue1, countryValue1 );
               }            
             );

             break;
             {/literal}{/if }{literal}
       case 2:
             {/literal} {if $country2_value } {literal}
             var countryValue2 = {/literal}"{$country2_value}"{literal};
             dojo.addOnLoad( function( ) 
               {
                  dojo.widget.byId( 'country2' ).setAllValues( countryValue2, countryValue2 );            
               }          
             );

             break;
             {/literal}{/if }{literal}
       case 3: 
             {/literal} {if $country3_value } {literal}
             var countryValue3 = {/literal}"{$country3_value}"{literal};

             dojo.addOnLoad( function( ) 
               {
                  dojo.widget.byId( 'country3' ).setAllValues( countryValue3, countryValue3 );            
               }          
             );

             break;
             {/literal}{/if }{literal}
       case 4: 
             {/literal} {if $country4_value } {literal}
             var countryValue4 = {/literal}"{$country4_value}"{literal};

             dojo.addOnLoad( function( ) 
               {
                  dojo.widget.byId( 'country4' ).setAllValues( countryValue4, countryValue4 );            
               }          
             );

             break;
             {/literal}{/if }{literal}
    }

    /* end of code to set defaults for country*/

</script>

{/literal}

{/if}


