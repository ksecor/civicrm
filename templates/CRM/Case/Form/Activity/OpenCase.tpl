<table class="form-layout">
   <tr><td class="label" width="30%">{$form.case_type_id.label}</td><td>{$form.case_type_id.html}</td>        
   <tr><td class="label">{$form.subject.label}</td><td>{$form.subject.html}</td>        
   <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td></tr>
   <tr><td class="label">{$form.start_date.label}</td><td>{$form.start_date.html}
	            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_case_1}
    	        {include file="CRM/common/calendar/body.tpl" dateVar=start_date offset=10 trigger=trigger_case_1}       
                </td>
   </tr>   
{if $clientName}
   <tr><td class="label">{ts}Client{/ts}</td><td><strong>{$clientName}</strong></td>
{else}
   <tr><td colspan=2>
   <fieldset><legend><strong>Client Details</strong></legend>
	<table class="form-layout-compressed">
    <tr>
		<td>{$form.prefix_id.label}</td>
		<td>{$form.first_name.label}</td>
		<td>{$form.last_name.label}</td>
	</tr>
	<tr>
		<td>{$form.prefix_id.html}</td>
		<td>{$form.first_name.html}</td>
		<td>{$form.last_name.html}</td>
	</tr>
    <tr><td>&nbsp;</td></tr>
	<tr>
        <td>{$form.location.1.phone.1.phone.label}</td>
        <td>{$form.location.1.phone.1.phone.html}</td>
	</tr>
	<tr>
        <td>{$form.location.1.email.1.email.label}</td>
        <td>{$form.location.1.email.1.email.html}</td>
	</tr>
    </table>
   </fieldset>
   </td></tr> 
{/if}        
 </table>
