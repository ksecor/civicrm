{* this template is used for adding/editing/deleting grant *}
<div class="html-adjust">{$form.buttons.html}</div> 
<fieldset>
  {if $action eq 8} 
      <div class="messages status">
        <dl>
           <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
           <dd>
             <p>{ts}Are you sure you want to delete this Grant? This delete operation cannot be undone and will delete all transactions associated with these grants.{/ts}</p>
             <p>{include file="CRM/Grant/Form/Task.tpl"}</p>
           </dd>
        </dl>
      </div>
  {else}
	{if $action eq 1}
	<legend>{ts}New Grant{/ts}</legend>
	{elseif $action eq 2}
	<legend>{ts}Edit Grant{/ts}</legend>
	{/if}
	<div class="form-item">
		<table class="form-layout-compressed">  
		    {if $context eq 'standalone'}
                {include file="CRM/Contact/Form/NewContact.tpl"}
            {/if}
			<tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td></tr>   
			<tr><td class="label">{$form.grant_type_id.label}</td><td>{$form.grant_type_id.html}</td></tr>   
			<tr><td class="label">{$form.amount_total.label}</td><td>{$form.amount_total.html}</td></tr>
			<tr><td class="label">{$form.amount_requested.label}</td><td>{$form.amount_requested.html}<br />
                <span class="description">{ts}Amount requested for grant in original currency (if different).{/ts}</span></td></tr>
			<tr><td class="label">{$form.amount_granted.label}</td><td>{$form.amount_granted.html}</td></tr>

			<tr><td class="label">{$form.application_received_date.label}</td>
				<td>
    				{if $hideCalendar neq true}
                        {include file="CRM/common/jcalendar.tpl" elementName=application_received_date}
                    {else}
                        {$form.application_received_date.html|crmDate}
                    {/if}
				</td>
			</tr>
			<tr><td class="label">{$form.decision_date.label}</td>
			<td>{if $hideCalendar neq true}
                    {include file="CRM/common/jcalendar.tpl" elementName=decision_date}
                {else}
                    {$form.decision_date.html|crmDate}
                {/if}
			<br />
                <span class="description">{ts}Date on which the grant decision was finalized.{/ts}</span></td></tr>
			<tr><td class="label">{$form.money_transfer_date.label}</td>
				<td>{if $hideCalendar neq true}
                        {include file="CRM/common/jcalendar.tpl" elementName=money_transfer_date}
                    {else}
                        {$form.money_transfer_date.html|crmDate}
                    {/if}<br />
                    <span class="description">{ts}Date on which the grant money was transferred.{/ts}</span></td></tr>
			<tr><td class="label">{$form.grant_due_date.label}</td>
				<td>
				    {if $hideCalendar neq true}
                        {include file="CRM/common/jcalendar.tpl" elementName=grant_due_date}
                    {else}
                        {$form.grant_due_date.html|crmDate}
                    {/if}
				</td>
			</tr>
			<tr><td class="label">{$form.grant_report_received.label}</td><td>{$form.grant_report_received.html}</td></tr>
			<tr><td class="label">{$form.rationale.label}</td><td>{$form.rationale.html}</td></tr>
			<tr><td class="label">{$form.note.label}</td><td>{$form.note.html}</td></tr>
			<tr><td colspan=2>{include file="CRM/Custom/Form/CustomData.tpl"}</td></tr>
            <tr>
                <td colspan="2">
                    {include file="CRM/Form/attachment.tpl"}
                </td>
            </tr>
		</table>
	</div>
   {/if}
</fieldset>
<div class="html-adjust">{$form.buttons.html}</div>
