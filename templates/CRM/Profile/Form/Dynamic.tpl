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
    {strip}
    <table class="form-layout-compressed">
    {foreach from=$fields item=field key=name}	
    {assign var=n value=$field.name}
	{if $field.options_per_line > 1}
	<tr>
	    {*<td class="label">{$form.$n.label} </td>*}
        <td>{$form.$n.label} </td>
	    <td>
		{assign var="count" value="1"}
	    <table class="form-layout">
            <tr>
            {* sort by fails for option per line. Added a variable to iterate through the element array*}
            {assign var="index" value="1"}
            {foreach name=outer key=key item=item from=$form.$element_name}
                {if $index < 10}
                    {assign var="index" value=`$index+1`}
                {else}
              	    <td class="label font-light">{$form.$element_name.$key.html}</td>
                    {if $count == $field.options_per_line}
              	        </tr>
                        <tr>
                        {assign var="count" value="1"}
           	        {else}
          		        {assign var="count" value=`$count+1`}
           	        {/if}
                {/if}
            {/foreach}
            </tr>
		</table>
	    </td>
    </tr>
	{else}
        <tr><td class="label">{$form.$n.label}</td><td>{$form.$n.html}</td></tr>
        {* Show explanatory text for field if not in 'view' mode *}
        {if $field.help_post && $action neq 4}
            <tr><td>&nbsp;</td><td class="description">{$field.help_post}</td></tr>
        {/if}
	{/if}
    {/foreach}
    </table>
    {/strip}
</div> {* end crm-container div *}
{/if} {* fields array is not empty *}
