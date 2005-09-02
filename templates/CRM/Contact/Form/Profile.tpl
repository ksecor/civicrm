
Search for people here by entering their name (full or partial) and/or home location.

<p>
    <table class="form-layout-compressed">
    {assign var=count value=0}
    {foreach from=$fields item=field key=name}
        {if ! $count % 2}<tr>{/if}
        {assign var=n value=$field.name}
	<td class="label">{$form.$n.label}</td>
        <td class="description">
           {$form.$n.html}
        </td>
        {if $count % 2}</tr>{/if}
        {if $count eq 6}
           </tr><tr><td colspan=4><p>Search by current location.</td></tr>
           {assign var=count value=$count+1}
        {/if}
        {assign var=count value=$count+1}
    {/foreach}
    <tr><td></td><td>{$form.buttons.html}</td></tr>
    </table>
