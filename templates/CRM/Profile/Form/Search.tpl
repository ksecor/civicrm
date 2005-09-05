
Search for people here by entering their name (full or partial) and/or home location.

<p>
    <table class="form-layout-compressed">
    {foreach from=$fields item=field key=name}
        {assign var=n value=$field.name}
        <tr>
	<td class="label">{$form.$n.label}</td>
        <td class="description">{$form.$n.html}</td>
        </tr>
    {/foreach}
    <tr><td></td><td>{$form.buttons.html}</td></tr>
    </table>
