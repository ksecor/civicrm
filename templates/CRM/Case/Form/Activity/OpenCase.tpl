{if ! $clientName}
   <fieldset><legend>{ts}New Client{/ts}</legend>
	<table class="form-layout-compressed">
    <tr>
		<td>{$form.prefix_id.label}</td>
		<td>{$form.first_name.label}</td>
		<td>{$form.last_name.label}</td>
		<td>{$form.suffix_id.label}</td>
	</tr>
	<tr>
		<td>{$form.prefix_id.html}</td>
		<td>{$form.first_name.html}</td>
		<td>{$form.last_name.html}</td>
		<td>{$form.suffix_id.html}</td>
	</tr>
	<tr>
        <td colspan="2">{$form.location.1.phone.1.phone.label}<br />
            {$form.location.1.location_type_id.html}&nbsp;{$form.location.1.phone.1.phone.html}
        </td>
        <td colspan="2">{$form.location.2.phone.1.phone.label}<br />
            {$form.location.2.location_type_id.html}&nbsp;{$form.location.2.phone.1.phone.html}
        </td>
    </tr>
    <tr>
        <td>{$form.location.1.email.1.email.label}</td>
        <td>{$form.location.1.email.1.email.html}</td>
        <td colspan="2"></td>
	</tr>
    {if $isDuplicate}
    <tr>
        <td>&nbsp;&nbsp;{$form._qf_Case_next_createNew.html}</td>
        <td>&nbsp;&nbsp;{$form._qf_Case_next_assignExisting.html}</td>
    </tr>
    {/if}
    </table>
   </fieldset>
{/if}
<fieldset><legend>{ts}Case Details{/ts}</legend>
    <table class="form-layout-compressed">
    {if $clientName}
        <tr><td class="label font-size12pt">{ts}Client{/ts}</td><td class="font-size12pt bold view-value">{$clientName}</td></tr>
    {/if}
    <tr><td class="label">{$form.subject.label}</td><td>{$form.subject.html}</td></tr>
    <tr>
        <td class="label">{$form.medium_id.label}</td>
        <td class="view-value">{$form.medium_id.html}&nbsp;&nbsp;&nbsp;{$form.activity_location.label} &nbsp;{$form.activity_location.html}</td>
    </tr> 
    <tr><td class="label">{$form.start_date.label}</td><td>{$form.start_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_case_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=start_date offset=10 trigger=trigger_case_1}       
        </td>
    </tr>
    <tr><td class="label">{$form.details.label}</td><td class="view-value">{$form.details.html|crmReplace:class:huge}</td></tr>
    <tr>
      <td class="label">{$form.duration.label}</td>
      <td class="view-value">
        {$form.duration.html}
         <span class="description">{ts}Total time spent on this activity (in minutes).{/ts}
      </td>
    </tr> 
    <tr><td class="label">{$form.case_type_id.label}</td><td>{$form.case_type_id.html}</td></tr>
    <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td></tr>
    </table>
</fieldset>
