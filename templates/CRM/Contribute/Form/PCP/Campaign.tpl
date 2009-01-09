<div id="help">
    {ts}Personalize the contents and appearance of your fundraising page here. You will be able to return to this page and make changes at any time.{/ts}
</div>
<fieldset>
<div class="form-item">
<table class="form-layout-compressed" width="100%">
	<tr>
		<td class="label">{$form.title.label}</td>
		<td>{$form.title.html|crmReplace:class:big}</td>
	</tr>
	<tr>
		<td class="label">{$form.intro_text.label}</td>
		<td>{$form.intro_text.html|crmReplace:class:big}
            <span class="description">{ts}{/ts}</span>
        </td>
	</tr>
	<tr>
		<td class="label">{$form.goal_amount.label}</td>
		<td>{$form.goal_amount.html|crmReplace:class:six}
            <span class="description">{ts}Total amount you would like to raise for this campaign.{/ts}</span>
		</td>
	</tr>
	<tr>
		<td class="label">{$form.donate_link_text.label}</td>
		<td>{$form.donate_link_text.html}
		<span class="description">{ts}The text for the contribute button.{/ts}</span>
		</td>
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
            <span class="description">{ts}If this option is checked, a "thermometer" showing progress toward your goal will be included on the page.{/ts}</span>
        </td>
	</tr>
	<tr>
		<td class="label">{$form.is_honor_roll.label}</td>
		<td>{$form.is_honor_roll.html}
		<span class="description">{ts}If this option is checked, an "honor roll" will be displayed with the names (or nicknames) of the people who donated through your fundraising page. (Donors will have the option to remain anonymous. Their names will NOT be listed.){/ts}</span></td>
	</tr>

	<tr>
		<td class="label">{$form.is_active.label}</td>
		<td>{$form.is_active.html}
            <span class="description">{ts}Is your Personal Campaign Page active? You can activate/de-activate it any time during it's lifecycle.{/ts}</span></td>
	</tr>
</table>
<dl>
	<dt></dt>
	<dd class="html-adjust">{$form.buttons.html}</dd>
</dl>
</fieldset>
</div>