{* Quest Pre-application: Educational Interests  section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}
</td>
<tr>
    <td class="grouplabel" width="30%">
        {$form.educational_interest.label}</td>
    <td class="fieldlabel">
        {$form.educational_interest.html}&nbsp;<span id="educational-interest-other">{$form.educational_interest_other.html|crmReplace:class:big}</span>
    </td>
</tr>
<tr>
    <td class="grouplabel" width="30%">
        {$form.college_type.label}</td>
    <td class="fieldlabel">
        {$form.college_type.html}</td>
</tr>
<tr>
    <td class="grouplabel" width="30%">
        {$form.college_interest.label}</td>
    <td class="fieldlabel">
        {$form.college_interest.html}</td>
</tr>
<tr>
    <td class="grouplabel" width="30%">
        {$form.college_interest_other.label}</td>
    <td class="fieldlabel">
        {$form.college_interest_other.html}</td>
</tr>
</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

{literal}
    <script type="text/javascript">

      var selectedOther; 
	   selectedOther = document.getElementsByName("educational_interest[270]")[0].checked;
	   if (selectedOther) {
		show('educational-interest-other');
	   } else {
		hide('educational-interest-other');
	   }

   	function showTextField() {
	   selectedOther = document.getElementsByName("educational_interest[270]")[0].checked;
	   if (selectedOther) {
		show('educational-interest-other');
	   } else {
		hide('educational-interest-other');
	   }
 	}
    </script>  
{/literal}
