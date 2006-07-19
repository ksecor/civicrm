{* Quest Pre-application: Household Information section *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=4 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
  <td colspan="4" class="grouplabel">
    <p class="preapp-instruction">{ts}List two teachers and a guidance counselor that you would like to use as recommenders. We will send an email to your recommenders with instructions on how they can complete the recommendation. Please verify all the contact information you enter about the recommenders.{/ts}</p>
  </td>
</tr>
<tr>
    <td colspan="4" class="grouplabel">
    <strong>{ts}Teachers&nbsp;{/ts}<span class="marker">*</span></strong></td>
</tr>
<tr>
    <td class="grouplabel"><label>{ts}First Name{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Last Name{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Email address{/ts}</label></td>
    <td class="grouplabel"><label>{ts}School{/ts}</label></td>
</tr>
<tr>
    <td class="fieldlabel">{$form.first_name_1.html}</td>
    <td class="fieldlabel">{$form.last_name_1.html}</td>
    <td class="fieldlabel">{$form.email_1.html}</td>
    <td class="fieldlabel">{$form.school_id_1.html}</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.first_name_2.html}</td>
    <td class="fieldlabel">{$form.last_name_2.html}</td>
    <td class="fieldlabel">{$form.email_2.html}</td>
    <td class="fieldlabel">{$form.school_id_2.html}</td>
</tr>
<tr>
    <td colspan="4" class="grouplabel">
    <strong>{ts}Guidance Counselor&nbsp;{/ts}<span class="marker">*</span></strong></td>
</tr>
<tr>
    <td class="grouplabel"><label>{ts}First Name{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Last Name{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Email address{/ts}</label></td>
    <td class="grouplabel"><label>{ts}School{/ts}</label></td>
</tr>
<tr>
    <td class="fieldlabel">{$form.first_name_3.html}</td>
    <td class="fieldlabel">{$form.last_name_3.html}</td>
    <td class="fieldlabel">{$form.email_3.html}</td>
    <td class="fieldlabel">{$form.school_id_3.html}</td>
</tr>
</table>

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

