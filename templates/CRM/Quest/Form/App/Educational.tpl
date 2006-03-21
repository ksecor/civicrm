{* Quest Pre-application: Educational Interests  section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}
</td>
<tr>
    <td class="grouplabel">
        {$form.educational_interest.label}</td>
    <td>
        {$form.educational_interest.html}<br>
        {$form.educational_interest_other.label}{$form.educational_interest_other.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.college_type.label}</td>
    <td>
        {$form.college_type.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.college_interest.label}</td>
    <td>
        {$form.college_interest.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.college_interest_other.label}</td>
    <td>
        {$form.college_interest_other.html}</td>
</tr>
</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

    <script type="text/javascript">
	hide('educational_interest_other');
    </script>  
