<div class="form-item">
<fieldset>
<div id="help">
    {ts}Update field values for each contribution as needed. Click <strong>Update Contributions</strong> below to save all your changes. To set a field to the same value for ALL rows, enter that value for the first contribution and then click the <strong>Copy icon</strong> (next to the column title).{/ts}
</div>
    <legend>{$profileTitle}</legend>
         <table>
            <tr class="columnheader">
             <th>Name</th>
             {foreach from=$fields item=field key=name}
                {if strpos( $field.name, '_date' ) !== false}
                  <th><img  src="{$config->resourceBase}i/copy.png" alt="{ts 1=$field.title}Click to copy %1 from row one to all rows.{/ts}" onclick="copyValuesDate('{$field.name}')" class="action-icon" title="{ts}Click here to copy the value in row one to ALL rows.{/ts}" />{$field.title}</th>
                {else}
                  <th><img  src="{$config->resourceBase}i/copy.png" alt="{ts 1=$field.title}Click to copy %1 from row one to all rows.{/ts}" onclick="copyValues('{$field.name}')" class="action-icon" title="{ts}Click here to copy the value in row one to ALL rows.{/ts}" />{$field.title}</th>
                 {/if}
             {/foreach}
            </tr>
            {foreach from=$contributionIds item=cid}
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
            <dt></dt><dd>{if $fields}{$form._qf_Batch_refresh.html}{/if} &nbsp; {$form.buttons.html}</dd>
        </dl>
</fieldset>
</div>

{literal}
<script type="text/javascript">
    function copyValues(fieldName) 
    {
        var cId = new Array();	
        var i = 0;{/literal}
        {foreach from=$contributionIds item=field}
        {literal}cId[i++]{/literal} = {$field}
        {/foreach}
	{literal}        
	
        for ( k=0; k<cId.length; k++ ) {
            document.getElementById("field_"+cId[k]+"_"+fieldName).value = document.getElementById("field_"+cId[0]+"_"+fieldName).value;
        }
    }  
    function copyValuesDate(fieldName) 
    {
        var cId = new Array();	
        var i = 0;{/literal}
        {foreach from=$contributionIds item=field}
        {literal}cId[i++]{/literal} = {$field}
        {/foreach}
	{literal}        
	
        for ( k=0; k<cId.length; k++ ) {
            document.getElementById("field["+cId[k]+"]["+fieldName+"][Y]").value = document.getElementById("field["+cId[0]+"]["+fieldName+"][Y]").value;
            document.getElementById("field["+cId[k]+"]["+fieldName+"][M]").value = document.getElementById("field["+cId[0]+"]["+fieldName+"][M]").value;
            document.getElementById("field["+cId[k]+"]["+fieldName+"][d]").value = document.getElementById("field["+cId[0]+"]["+fieldName+"][d]").value;
        }
    }  
</script>
{/literal}
