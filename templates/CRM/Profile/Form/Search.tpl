{if ! empty( $fields )}
<p>
    <table class="form-layout-compressed">
    {foreach from=$fields item=field key=name}
        {assign var=n value=$field.name}
	{if $field.is_search_range}
	   {assign var=from value=$field.name|cat:'_from'}
	   {assign var=to value=$field.name|cat:'_to'}
	        <tr>
        	    <td class="label">{$form.$from.label}</td>
	            <td class="description">{$form.$from.html}</td>
	            <td class="label">{$form.$to.label}</td>
        	    <td class="description">{$form.$to.html}</td>
	        </tr>
	{else}
	        <tr>
        	    <td class="label">{$form.$n.label}</td>
	            <td class="description">{$form.$n.html}</td>
        	</tr>
	{/if}
    {/foreach}
    <tr><td></td><td>{$form.buttons.html}</td></tr>
    </table>
</p>
{/if}
