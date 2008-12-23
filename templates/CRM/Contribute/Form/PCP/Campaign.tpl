<fieldset><legend>{ts}Personal Campaign Page Configuration Screen{/ts}</legend>
<div class="form-item">
<table class="form-layout-compressed" width="100%">
	<tr>
		<td class="label">{$form.title.label}</td>
		<td>{$form.title.html|crmReplace:class:big}
		<span class="description">{ts}{/ts}</span></td>
	</tr>
	<tr>
		<td class="label">{$form.intro_text.label}</td>
		<td>{$form.intro_text.html|crmReplace:class:big}
                <span class="description">{ts}{/ts}</span></td>
	</tr>
	<tr>
		<td class="label">{$form.goal_amount.label}</td>
		<td>{$form.goal_amount.html|crmReplace:class:six}</td>
	</tr>
	<tr>
		<td class="label">{$form.donate_link_text.label}</td>
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
		<td>{$form.is_thermometer.html}
                <span class="description">{ts}If this option is checked, a thermometer widget will be shown on your page, presenting amount already raised and your campaign goal.{/ts}</span>
                </td>
	</tr>
	<tr>
		<td class="label">{$form.is_honor_roll.label}</td>
		<td>{$form.is_honor_roll.html}
		<span class="description">{ts}If this option is checked, your page will display the roll with names or nicknames of all the people, who donated through your Personal Campaign Page.{/ts}</span></td>
	</tr>

	<tr>
		<td class="label">{$form.is_active.label}</td>
		<td>{$form.is_active.html}
                <span class="description">{ts}Is your Personal Campaign Page active? You can activate/disactivate it any time during it's lifecycle.{/ts}</span></td>
	</tr>
</table>
<dl>
	<dt></dt>
	<dd class="html-adjust">{$form.buttons.html}</dd>
</dl>
</fieldset>
</div>