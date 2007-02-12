<div class="form-item">
<fieldset>
<div id="help">
    {ts}Update field values for each participation as needed. Click <strong>Update Event Participtions</strong> below to save all your changes. To set a field to the same value for ALL rows, enter that value for the first participation and then click the <strong>Copy icon</strong> (next to the column title).{/ts}
</div>
    <legend>{$profileTitle}</legend>
         <table>
            <tr class="columnheader">
             <th>Name</th>
             <th>Event</th>   
             {foreach from=$fields item=field key=name}
                {if strpos( $field.name, '_date' ) !== false}   
                  <th><img  src="{$config->resourceBase}i/copy.png" alt="{ts 1=$field.title}Click to copy %1 from row one to all rows.{/ts}" onclick="copyValuesDate('{$field.name}')" class="action-icon" title="{ts}Click here to copy the value in row one to ALL rows.{/ts}" />{$field.title}</th>
                {else}
                  <th><img  src="{$config->resourceBase}i/copy.png" alt="{ts 1=$field.title}Click to copy %1 from row one to all rows.{/ts}" onclick="copyValues('{$field.name}')" class="action-icon" title="{ts}Click here to copy the value in row one to ALL rows.{/ts}" />{$field.title}</th>
                {/if}
             {/foreach}
            </tr>
            {foreach from=$participantIds item=pid}
             <tr class="{cycle values="odd-row,even-row"}">
              <td>{$details.$pid.name}</td> 
              <td>{$details.$pid.title}</td>   
              {foreach from=$fields item=field key=name}
                {assign var=n value=$field.name}
                <td class="compressed">{$form.field.$pid.$n.html}</td> 
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
        var pId = new Array();	
        var i = 0;{/literal}
        {foreach from=$participantIds item=field}
        {literal}pId[i++]{/literal} = {$field}
        {/foreach}
	{literal}        
	
        if ( document.getElementById("field_"+pId[0]+"_"+fieldName ) ) {
	    for ( k=0; k<pId.length; k++ ) {
                document.getElementById("field_"+pId[k]+"_"+fieldName).value = document.getElementById("field_"+pId[0]+"_"+fieldName).value;           }  
        } else if ( document.getElementsByName("field"+"["+pId[0]+"]"+"["+fieldName+"]")[0].type == "radio" ) {
	    for ( t=0; t<document.getElementsByName("field"+"["+pId[0]+"]"+"["+fieldName+"]").length; t++ ) { 
                if  (document.getElementsByName("field"+"["+pId[0]+"]"+"["+fieldName+"]")[t].checked == true ) {break}
	    }
	    if ( t == document.getElementsByName("field"+"["+pId[0]+"]"+"["+fieldName+"]").length ) {
		for ( k=0; k<pId.length; k++ ) {
		    for ( t=0; t<document.getElementsByName("field"+"["+pId[0]+"]"+"["+fieldName+"]").length; t++ ) {
			document.getElementsByName("field"+"["+pId[k]+"]"+"["+fieldName+"]")[t].checked = false;
		    }
		}
	    } else {
		for ( k=0; k<pId.length; k++ ) {
		    document.getElementsByName("field"+"["+pId[k]+"]"+"["+fieldName+"]")[t].checked = document.getElementsByName("field"+"["+pId[0]+"]"+"["+fieldName+"]")[t].checked;
		}
	    }   
	} else if ( document.getElementsByName("field"+"["+pId[0]+"]"+"["+fieldName+"]")[0].type == "checkbox" ) {
	    for ( k=0; k<pId.length; k++ ) {
		document.getElementsByName("field"+"["+pId[k]+"]"+"["+fieldName+"]")[0].checked = document.getElementsByName("field"+"["+pId[0]+"]"+"["+fieldName+"]")[0].checked;
	    }   
	}
    }

    function copyValuesDate(fieldName) 
    {
	var pId = new Array();	
	var i = 0;{/literal}
	{foreach from=$participantIds item=field}
	{literal}pId[i++]{/literal} = {$field}
	{/foreach}
	{literal}        
	
	for ( k=0; k<pId.length; k++ ) {
	    document.getElementById("field["+pId[k]+"]["+fieldName+"][Y]").value = document.getElementById("field["+pId[0]+"]["+fieldName+"][Y]").value;
	    document.getElementById("field["+pId[k]+"]["+fieldName+"][M]").value = document.getElementById("field["+pId[0]+"]["+fieldName+"][M]").value;
	    document.getElementById("field["+pId[k]+"]["+fieldName+"][d]").value = document.getElementById("field["+pId[0]+"]["+fieldName+"][d]").value;
	}
    }

</script>
{/literal}
