<fieldset><legend>{ts}Personal Campaign Configuration Screen{/ts}</legend>
<div class="form-item">
<table class="form-layout-compressed" width="100%">
	<tr>
		<td class="label">{$form.title.label}</td>
		<td>{$form.title.html|crmReplace:class:big}</td>
	</tr>
	<tr>
		<td class="label">{$form.intro_text.label}</td>
		<td>{$form.intro_text.html|crmReplace:class:big}</td>
	</tr>
	<tr>
		<td class="label">{$form.goal_amount.label}</td>
		<td>{$form.goal_amount.html|crmReplace:class:six}</td>
	</tr>
		<tr><td class="label">{$form.donate_link_text.label}</td>
		<td>{$form.donate_link_text.html}</td>
	</tr>
	<tr>
		<td class="label" width="15%">{$form.page_text.label}</td>
		<td width="85%">{$form.page_text.html}</td>
	</tr>
</table>
      {include file="CRM/Form/attachment.tpl"}
<table class="form-layout-compressed">
	<tr>
		<td class="label">{$form.is_thermometer.label}</td>
		<td>{$form.is_thermometer.html}</td>
	</tr>
	<tr>
		<td class="label">{$form.is_honor_roll.label}</td>
		<td>{$form.is_honor_roll.html}</td>
	</tr>

	<tr>
		<td class="label">{$form.is_active.label}</td>
		<td>{$form.is_active.html}</td>
	</tr>
</table>
<dl>    
       <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>   
</dl> 
</fieldset>
</div>