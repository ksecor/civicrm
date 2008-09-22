<div class="form-item"><fieldset>
<table class="form-layout-compressed">
	<tr>
		<td class="label">{$form.page_title.label}</td>
		<td>{$form.page_title.html}</td>
	</tr>
	<tr>
		<td class="label">{$form.into_text.label}</td>
		<td>{$form.into_text.html}</td>
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
		<td>{$form.goal_amount.html}</td>
	</tr>
		<tr><td class="label">{$form.donate_button_text.label}</td>
		<td>{$form.donate_button_text.html}</td>
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
		<td class="label">{$form.uploadImageII.label}</td>
		<td>{$form.uploadImageII.html}</td>
	</tr>
	<tr>
		<td class="label">{$form.thermometer.label}</td>
		<td>{$form.thermometer.html}</td>
	</tr>
	<tr>
		<td class="label">{$form.honour_roll.label}</td>
		<td>{$form.honour_roll.html}</td>
	</tr>
</table>
<dl>    
       <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>   
</dl> 
</fieldset>
</div>