{* Quest Pre-application:  School Other Information section *}
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td colspan=2 class="grouplabel"><p>{ts}If you've attended any special programs at other secondary schools, colleges where you took courses for credit, etc., please list them here.{/ts}</p></td>
</tr>
<tr>
    <td class="grouplabel">{$form.organization_name.label} </td>
    <td class="fieldlabel"> {$form.organization_name.html} </td>
</tr>
<tr>
    <td class="grouplabel">{$form.date_of_entry.label} </td>
    <td class="fieldlabel">{$form.date_of_entry.html}  {$form.date_of_exit.html} </td>
</tr>
<tr>
    <td class="grouplabel" rowspan="4"><label>{ts}Location{/ts}</label></td>
</tr>
<tr>
    <td class="fieldlabel">{$form.location.1.address.city.html}<br>{ts}City{/ts}</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.location.1.address.state_province_id.html}<br>{ts}State or Province{/ts}</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.location.1.address.country_id.html}<br>{ts}Country{/ts}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.location.1.phone.1.phone.label} </td>
    <td class="fieldlabel">{$form.location.1.phone.1.phone.html}  </td>
</tr>
<tr>
    <td class="grouplabel">{$form.note.label}</td>
    <td class="fieldlabel">{$form.note.html}</td>
</tr>

</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}
