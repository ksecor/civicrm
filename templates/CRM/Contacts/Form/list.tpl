{* smarty *}
 {literal}
 <script type="text/javascript" src="/js/LIST.js"></script>
 {/literal}


{$form.javascript}
<form {$form.attributes}>

{$form.contact_select.label}
{$form.contact_select.html}
{$form.change_list_view.html}

<fieldset>
<table id = "linkheader">
<tr>
<td width = "150">
	{$form.export.html}
</td>
<td>
	{$form.first.html}
	{$form.previous.html}
	{$form.page_serial.html}
	{$form.next.html}
	{$form.previouspage.html}
</td>
<td>
	{$form.page_no.label}
	{$form.page_no.html}
	{$form.gotopage.html}

</td>
</tr>
</table>
</fieldset>


<table id = "datagrid" width = "100%">
<tr>
<th>{$form.name.html}</th>
<th>{$form.email.html}</th>
<th>{$form.phone.html}</th>
<th>{$form.address.html}</th>
<th>{$form.city.html}</th>
<th>{$form.state_province.html}</th>
<th></th>
</tr>

{section name = listing start = 1 loop = 10}
{assign var =  value = $form.checkrecord.html}







</table>
</form>
