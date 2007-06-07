{if $form.location.$index.address.country}

{literal}

<script type="text/javascript">

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

    var res = {/literal}"{crmURL p='civicrm/ajax/state' q='s=getParameters'}"{literal};
    
    var state = {/literal}"location[{$index}][address][state_province]"{literal};

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

                dojo.widget.createWidget("ComboBox", 
                    {
                      value: 'this should never be seen - it is replaced!',
                      dataUrl: res + '&node='+value,
                      id: element +'_'+node,
                      style: 'width: 300px',
                      name : state
                    }, 
                   
               dojo.byId (element+'_container_'+node));
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

{/if}


