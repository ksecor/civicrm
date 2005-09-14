{if ! empty( $fields )}
<div id="crm-container"> {* wrap in crm-container div so crm styles are used *}

    {if $form.javascript}
      {$form.javascript}
    {/if}

    {if $form.hidden}
      {$form.hidden}
    {/if}

    {if count($form.errors) gt 0}
       <div class="messages error">
       {ts}Please correct the following errors in the form fields below:{/ts}
       <ul id="errorList">
       {foreach from=$form.errors key=name item=error}
          {if is_array($error)}
             <li>{$error.label} {$error.message}</li>
          {else}
             <li>{$error}</li>
          {/if}
       {/foreach}
       </ul>
       </div>
    {/if}

    <table class="form-layout-compressed">
    {foreach from=$fields item=field key=name}	
        {assign var=n value=$field.name}
	{if $field.options_per_line > 1}
	<tr>
	    <td class="label">{$form.edit.$n.label} </td>
	    <td>
		{assign var="count" value="1"}
	        <table class="form-layout">
	            {section name=rowLoop start=1 loop=$form.edit.$n}
	            {assign var=index value=$smarty.section.rowLoop.index}
	            {if $form.edit.$n.$index.html != "" }
		            {if $smarty.section.rowLoop.first}
		            <tr>
	                    {/if} 
			         <td>{$form.edit.$n.$index.html}</td>
                            {if $count == $field.options_per_line}
				</tr>
	                        <tr>
	                        {assign var="count" value="1"}
			    {else}
			        {assign var="count" value=`$count+1`}
		            {/if}
                    
			    {if $smarty.section.rowLoop.last}
				</tr>
			    {/if}
		     {/if}
		     {/section}
		</table>
	</dd>
	{else}
        <tr><td class="label">{$form.edit.$n.label}</td><td>{$form.edit.$n.html}{debug}</td></tr>
        {* Show explanatory text for field if not in 'view' mode *}
        {if $field.help_post && $action neq 4}
            <tr><td>&nbsp;</td><td class="description">{ts}{$field.help_post}{/ts}</td></tr>
        {/if}
	{/if}
    {/foreach}
    </table>
</div> {* end crm-container div *}
{/if} {* fields array is not empty *}
