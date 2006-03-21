{* Quest Pre-application:  School Other Information section *}
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}
</tr>
<tr>
    <td>If you've attended any special programs at other secondary schools, colleges where you took courses for credit, etc., please list them here.</td>
</tr>
<tr>
    <td class="grouplabel"> {$form.organization_name.label} </td>
    <td> {$form.organization_name.html} </td>
</tr>
<tr>
    <td class="grouplabel"> {$form.date_of_entry.label} </td>
    <td> {$form.date_of_entry.html}  {$form.date_of_exit.html} </td>
</tr>
<tr>
    <td class="grouplabel" rowspan="4">{ts}Location{/ts}</td>
</tr>
<tr>
    <td> {$form.location.1.address.city.html} </td>
</tr>
<tr>
    <td> {$form.location.1.address.state_province_id.html} </td>
</tr>
<tr>
    <td> {$form.location.1.address.country_id.html} </td>
</tr>
<tr>
    <td> {$form.location.1.phone.1.phone.label} </td>
    <td> {$form.location.1.phone.1.phone.html}  </td>
</tr>
<tr>
    <td class="grouplabel"> {$form.note.label} </td>
    <td> {$form.note.html} </td>
</tr>

</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

