{* smarty *}
{literal}
<script type="text/javascript" src="/js/ORG.js"></script>
{/literal}

{$form.javascript}

<form {$form.attributes}>

<table border="0" width="100%" cellpadding="2" cellspacing="2">

<tr><td>
	{if $form.hidden}
	{$form.hidden}{/if}

	{if count($form.errors) gt 0}
	<table width="100%" cellpadding="1" cellspacing="0" border="0" bgcolor="#ff9900"><tr><td>
	<table width="100%" cellpadding="10" cellspacing="0" border="0" bgcolor="#FFFFCC"><tr><td align="center">
	<span class="error" style="font-size: 13px;">Please correct the errors below.</span>
	</td></tr></table>
	</td></tr></table>
	</p>
	{/if}
</td></tr>

<tr><td>
<div id="core">
<fieldset><legend>Organization</legend>
<table border = "0" cellpadding="2" cellspacing="2">
	<tr>
		<td class="form-item"><label>{$form.organization_name.label}</label></td>
		<td>{$form.organization_name.html}</td>
	</tr>
	<tr>
		<td class="form-item"><label>{$form.legal_name.label}</label></td>
		<td>{$form.legal_name.html}</td>
	</tr>
	<tr>
		<td class="form-item"><label>{$form.nick_name.label}</label></td>
		<td>{$form.nick_name.html}</td>
	</tr>
	<tr>
		<td class="form-item"><label>{$form.primary_contact_id.label}</label></td>
		<td>{$form.primary_contact_id.html}</td>
	</tr>
	<tr>
		<td class="form-item"><label>{$form.sic_code.label}</label></td>
		<td>{$form.sic_code.html}</td>
	</tr>

</table>
</fieldset>
</td></tr>

<tr><td>
 {include file="CRM/Contact/Form/Contact.tpl"} 
</td></tr>

 {* STARTING UNIT gx3 LOCATION ENGINE *}
<tr><td>
 {include file="CRM/Contact/Form/Location.tpl" locloop = 2 phoneloop = 4 emailloop = 4 imloop = 4} 
</td></tr> 
{* ENDING UNIT gx3 LOCATION ENGINE } */
{******************************** ENDIND THE DIV SECTION **************************************}
{******************************** ENDIND THE DIV SECTION **************************************}

</div> <!-- end 'core' section of contact form -->
</table>

<div id = "buttons">
<table cellpadding="2" cellspacing="2">
<tr>
	<td class="form-item">
	{$form.buttons.html}</td>
	
</tr>
</table>
</div>

 {$form.my_script.label}
</form>
	
{literal}
<script type="text/javascript">
on_load_execute(frm.name);
</script>
{/literal}

{if count($form.errors) gt 0}
{literal}
<script type="text/javascript">
on_error_execute(frm.name);
</script>
{/literal}
{/if}

