<div class="form-item">
<fieldset>
    <legend>{ts}Update Contribution Status{/ts}</legend>
    <dl>
        <dt>{$form.contribution_status_id.label}</dt><dd>{$form.contribution_status_id.html}</dd>
<table>
<tr>
<th>Name</th>
<th>Amount</th>
<th>Transaction ID</th>
<th>Fee Amount</th>
<th>Transaction Date</th>
</tr>
{foreach from=$rows item=row}
<tr>
<td>{$row.display_name}</td>
<td>{$row.amount}</td>
{assign var="element_name" value="trxn_id_"|cat:$row.contribution_id}
<td>{$form.$element_name.html}</td>
{assign var="element_name" value="fee_amount_"|cat:$row.contribution_id}
<td>{$form.$element_name.html}</td>
{assign var="element_name" value="trxn_date_"|cat:$row.contribution_id}
<td>{$form.$element_name.html}</td>
</tr>
{/foreach}
</table>
        <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>
