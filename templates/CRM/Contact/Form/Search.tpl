<form {$form.attributes}>

{$form.hidden}

<table>
<tr><td>{$form.contact_type.label}</td><td>{$form.contact_type.html}</td></tr>
<tr><td>{$form.sort_name.label}</td><td>{$form.sort_name.html}</td></tr>
<tr><td>{$form.group_id.label}</td><td>{$form.group_id.html}</td></tr>
<tr><td>{$form.category_id.label}</td><td>{$form.category_id.html}</td></tr>
<tr><td>{$form.action_id.label}</td><td>{$form.action_id.html}</td></tr>
<tr>
<td colspan=2 align="right">{$form.buttons.html}</td>
</tr>
</table>

{include file="CRM/Contact/Selector/Selector.tpl"}

</form>
