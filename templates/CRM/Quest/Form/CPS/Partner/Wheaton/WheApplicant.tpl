{* Quest College Match: Partner: Wheaton: Applicantion section *}
{include file="CRM/Quest/Form/CPS/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">Wheaton College - {$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="optionlist">{$form.is_personal_savior.label}</td>
    <td class="optionlist">{$form.is_personal_savior.html}</td>
</tr>
<tr>
    <td class="optionlist">{$form.denomination.label}</td>
    <td class="optionlist">{$form.denomination.html}</td>
</tr>
<tr>
    <td class="optionlist">{$form.church_name.label}</td>
    <td class="optionlist">{$form.church_name.html}</td>
</tr>
<tr>
    <td class="optionlist" colspan="2">The essay is a significant component of the application process. Essays are evaluated on the basis of content, writing skill, and creativity.
    <table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
     <tr>
        <td>
            {include file="CRM/Quest/Form/CPS/Essay.tpl"}
        </td>
     </tr>
    </table>
   </td>
</tr>
</table>
