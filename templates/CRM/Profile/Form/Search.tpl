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
	{elseif $field.options_per_line}
	<tr>
        <td class="option-label">{$form.$n.label}</td>
        <td>
	    {assign var="count" value="1"}
        {strip}
        <table class="form-layout-compressed">
        <tr>
          {* sort by fails for option per line. Added a variable to iterate through the element array*}
          {assign var="index" value="1"}
          {foreach name=outer key=key item=item from=$form.$n}
          {if $index < 10}
              {assign var="index" value=`$index+1`}
          {else}
              <td class="labels font-light">{$form.$n.$key.html}</td>
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
        {/strip}
        </td>
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
