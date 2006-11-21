<div class="form-item">
<fieldset>
    <legend>{$profileTitle}</legend>
         <table>
            <tr class="columnheader">
             <th>Name</th>
             {foreach from=$fields item=field key=name}
                <th><img  src="{$config->resourceBase}i/copy.png" alt="{$field.title}" onClick="copyValues('{$field.name}')" ) />&nbsp;{$field.title}</th>
             {/foreach}
            </tr>
            {foreach from=$contactIds item=cid}
             <tr class="{cycle values="odd-row,even-row"}">
              <td>{$sortName.$cid}</td> 
              {foreach from=$fields item=field key=name}
                {assign var=n value=$field.name}
                <td class="compressed">{$form.field.$cid.$n.html}</td> 
              {/foreach}
             </tr>
            {/foreach}
           </tr>
         </table>
        <dl>
            <dt></dt><dd>{if $fields}{$form._qf_BatchUpdateProfile_refresh.html}{/if} &nbsp; {$form.buttons.html}</dd>
        </dl>
</fieldset>
</div>

{literal}
<script type="text/javascript">
    function copyValues(fieldName) 
    {
        var cId = new Array();	
        var i = 0;{/literal}	
        {literal}var i = 0;{/literal}
        {foreach from=$contactIds item=field}
        {literal}cId[i]{/literal} = {$field}
        {literal}i = i + 1 {/literal}    
        {/foreach}
	{literal}        
	
        for ( k=0; k<cId.length; k++ ) {
            document.getElementById("field_"+cId[k]+"_"+fieldName).value = document.getElementById("field_"+cId[0]+"_"+fieldName).value;
        }
    }  
</script>
{/literal}
