<!--table>
        <tr> 
            <td class="label">{$form.sort_name.label}</td>
            <td class="nowrap">{$form.sort_name.html}{help id='sort_name'}</td>
        </tr>
        <tr> 
            <td class="label">{$form.state_province.label}</td> 
            <td class="nowrap">{$form.state_province.html}{help id='state_province'}</td>
        </tr>
        <tr> 
            <td class="label">{$form.country.label}</td> 
            <td>{$form.country.html}{help id='country'}</td>
        </tr>
        <tr> 
            <td colspan=2>{$form.buttons.html}</td>
        </tr>

</table-->
<br/><br/>
{if $rows}
<table>
<tr><th>{ts}Contact ID{/ts}</th><th>{ts}Sort Name{/ts}</th></tr>
{foreach from=$rows key=id item=name}
<tr><td>{$id}</td><td>{$name}</td></tr>
{/foreach}
</table>
{/if}

{literal}

<script type="text/javascript">

dojo.require("dojo.widget.ComboBox");
dojo.require("dojo.io.*");

var active_levels = Array();

function checkParamChildren(value) {
    checkChildren(this, 'wizCardDefGroupId', value, checkParamChildren);
}

function checkChildren(obj, element, value, src_func) {
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

    var res = {/literal}"{crmURL p='civicrm/ajax/state' h=0 q='s=getParameters'}"{literal};
    
    var bindArgs = {
        url: res,
        method: 'GET',
        type: "text/json",
        load: function(type, data)
        {          
            eval("var decoded_data = "+data);            
            if(data.length > 2) {
                tmp = active_levels[element];
                tmp++;
                active_levels[element] = tmp;
                
                node = active_levels[element];
                
                container = document.createElement('span');
                container.setAttribute('id',element+'_container_'+node);
                dojo.byId(element+'_children').appendChild(container);

                if ( value ) {  
                    dojo.widget.createWidget("ComboBox", 
                    {
                      value: 'this should never be seen - it is replaced!',
                      dataUrl: res + '&node='+value,
                      id: element +'_'+node,
                      style: 'width: 300px'
                    }, 
                   
                    dojo.byId (element+'_container_'+node));
                } 
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

<div id="select_root">
 {$form.country.label}{$form.country.html}{help id='country'}
</div>

 
<div id="wizCardDefGroupId_children"></div>    
