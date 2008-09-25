<div class="form-item"><fieldset>
<table class="form-layout-compressed">
	<tr>
		<td class="label">{$form.title.label}</td>
		<td>{$form.title.html|crmReplace:class:big}</td>
	</tr>
	<tr>
		<td class="label">{$form.into_text.label}</td>
		<td>{$form.into_text.html|crmReplace:class:big}</td>
	</tr>
	<tr>	
		<td class="label">{$form.page_active_from.label}</td>
		<td>{$form.page_active_from.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_active_from}
		{include file="CRM/common/calendar/body.tpl" dateVar=page_active_from startDate=currentYear endDate=endYear offset=10 trigger=trigger_active_from}</td>
	</tr>
	<tr>
		<td class="label">{$form.page_active_until.label}</td>
		<td>{$form.page_active_until.html}
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_active_until}
		{include file="CRM/common/calendar/body.tpl" dateVar=page_active_until startDate=currentYear endDate=endYear offset=10 trigger=trigger_active_until}</td>
	</tr>
	<tr>
		<td class="label">{$form.goal_amount.label}</td>
		<td>{$form.goal_amount.html|crmReplace:class:six}</td>
	</tr>
		<tr><td class="label">{$form.donate_link_text.label}</td>
		<td>{$form.donate_link_text.html}</td>
	</tr>
	<tr>
		<td class="label">{$form.page_text.label}</td>
		<td>{$form.page_text.html}</td>
	</tr>
	<tr>
		<td class="label">{$form.uploadImageI.label}</td>
		<td>{$form.uploadImageI.html}</td>
	</tr>
	<tr>
		<td class="label"></td>
		<td><br/>{$form.uploadImageII.html}</td>
	</tr>
	<tr>
		<td class="label">{$form.is_thermometer.label}</td>
		<td>{$form.is_thermometer.html}</td>
	</tr>
	<tr>
		<td class="label">{$form.is_honor_roll.label}</td>
		<td>{$form.is_honor_roll.html}</td>
	</tr>
</table>
<dl>    
       <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>   
</dl> 
</fieldset>
</div>