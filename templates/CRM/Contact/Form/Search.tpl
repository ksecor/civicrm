<form {$form.attributes}>

{$form.hidden}

<table>
<tr>
<td>{$form.contact_type.label}</td><td>{$form.contact_type.html}</td>
</tr>
<tr>
<td colspan=2 align="right">{$form.buttons.html|qfReplace:test:class}</td>
</tr>
</table>

{include file="CRM/Contact/Selector/Selector.tpl"}

</form>
