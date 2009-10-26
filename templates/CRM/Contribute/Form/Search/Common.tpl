<tr>
	<td>{$form.contribution_date_low.label} <br />
	{include file="CRM/common/jcalendar.tpl" elementName=contribution_date_low}</td>

	<td>{$form.contribution_date_high.label}<br />
	{include file="CRM/common/jcalendar.tpl" elementName=contribution_date_high}</td>
</tr>
<tr>
	<td><label>{ts}Contribution Amounts{/ts}</label> <br />
	{$form.contribution_amount_low.label}
	{$form.contribution_amount_low.html} &nbsp;&nbsp;
	{$form.contribution_amount_high.label}
	{$form.contribution_amount_high.html} </td>
	<td><label>{ts}Contribution Status{/ts}</label> <br />
	{$form.contribution_status_id.html} </td>
</tr>
<tr>
	<td><label>{ts}Paid By{/ts}</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	{$form.contribution_check_number.label} <br />
	{$form.contribution_payment_instrument_id.html}&nbsp;&nbsp;&nbsp;&nbsp;
	{$form.contribution_check_number.html}
	</td>
	<td>{$form.contribution_transaction_id.label} <br />
	{$form.contribution_transaction_id.html}</td>
</tr>
<tr>
	<td>
	{$form.contribution_receipt_date_isnull.html}&nbsp;{$form.contribution_receipt_date_isnull.label}<br />
	{$form.contribution_thankyou_date_isnull.html}&nbsp;{$form.contribution_thankyou_date_isnull.label}
	</td>
	<td>
	{$form.contribution_pay_later.html}&nbsp;{$form.contribution_pay_later.label}<br />
	{$form.contribution_recurring.html}&nbsp;{$form.contribution_recurring.label}<br />
	{$form.contribution_test.html}&nbsp;{$form.contribution_test.label}</td>
</tr>
<tr>
	<td><label>{ts}Contribution Type{/ts}</label> <br />
	{$form.contribution_type_id.html}</td>
	<td><label>{ts}Contribution Page{/ts}</label> <br />
	{$form.contribution_page_id.html}</td>
</tr>
<tr>
	<td>{$form.contribution_in_honor_of.label} <br />
	{$form.contribution_in_honor_of.html}</td>
	<td>{$form.contribution_source.label} <br />
	{$form.contribution_source.html}</td>
</tr>
<tr>
	<td>{$form.contribution_pcp_made_through_id.label} <br />
	{$form.contribution_pcp_made_through_id.html}</td>
	<td>{$form.contribution_pcp_display_in_roll.label}
	{$form.contribution_pcp_display_in_roll.html}&nbsp;&nbsp;<a href="javascript:unselectRadio('contribution_pcp_display_in_roll','Search')">unselect</a></td>
</tr>

{if $contributeGroupTree}
<tr>
	<td colspan="2">
	{include file="CRM/Custom/Form/Search.tpl" groupTree=$contributeGroupTree showHideLinks=false}</td>
</tr>
{/if}
