{if ! $clientName}
   <fieldset><legend>{ts}New Client{/ts}</legend>
	<table class="form-layout-compressed" border="0">
    <tr>
        <td><br />{$form.prefix_id.html}</td>
		<td>{$form.first_name.label}<br />{$form.first_name.html}</td>
		<td colspan="2">
            {$form.last_name.label}<br />{$form.last_name.html} &nbsp; {$form.suffix_id.html}
        </td>
	</tr>
	<tr>
        <td>{$form.location.1.phone.1.phone.label}<br />
            {$form.location.1.location_type_id.html}
        </td>
        <td><br />{$form.location.1.phone.1.phone_type_id.html}&nbsp;{$form.location.1.phone.1.phone.html}
        </td>
        <td colspan="2">{$form.location.2.phone.1.phone.label}<br />
            {$form.location.2.location_type_id.html}&nbsp;{$form.location.2.phone.1.phone_type_id.html}&nbsp;{$form.location.2.phone.1.phone.html}
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
        {if $onlyOneDupe}
        <td>&nbsp;&nbsp;{$form._qf_Case_next_assignExisting.html}</td>
        {/if}
    </tr>
    {/if}
    </table>
   </fieldset>
{/if}
<fieldset><legend>{ts}New Case{/ts}</legend>
    <table class="form-layout">
    {if $clientName}
        <tr><td class="label font-size12pt">{ts}Client{/ts}</td><td class="font-size12pt bold view-value">{$clientName}</td></tr>
    {/if}
    <tr><td class="label">{$form.activity_subject.label}</td><td>{$form.activity_subject.html}</td></tr>
    <tr>
        <td class="label">{$form.medium_id.label}</td>
        <td class="view-value">{$form.medium_id.html}&nbsp;&nbsp;&nbsp;{$form.activity_location.label} &nbsp;{$form.activity_location.html}</td>
    </tr> 
    <tr><td class="label">{$form.start_date.label}</td><td>{$form.start_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_case_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=start_date offset=10 trigger=trigger_case_1}       
        </td>
    </tr>
    {if $groupTree}
        <tr>
            <td colspan="2">{include file="CRM/Custom/Form/CustomData.tpl" noPostCustomButton=1}</td>
        </tr>
    {/if}
    <tr><td class="label">{$form.activity_details.label}</td><td class="view-value">{$form.activity_details.html|crmReplace:class:huge}</td></tr>
    <tr>
      <td class="label">{$form.duration.label}</td>
      <td class="view-value">
        {$form.duration.html}
         <span class="description">{ts}Total time spent on this activity (in minutes).{/ts}
      </td>
    </tr> 
    <tr><td class="label">{$form.case_type_id.label}</td><td>{$form.case_type_id.html}</td></tr>
    <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td></tr>
    <tr>
        <td>&nbsp;</td><td class="buttons">{$form.buttons.html}</td>
    </tr>
    </table>
</fieldset>
