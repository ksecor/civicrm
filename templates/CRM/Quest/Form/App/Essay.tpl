{* Quest Pre-application:  essay section *}

<div id="help">     
    {ts}To minimize the risk of losing your work, you may wish to write your essay in another program and then paste it in this box when you are ready.{/ts}
</div> 

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}
</td>
<tr><td>List and describe the factors in your life that have most shaped you (1500 characters max). *  </td></tr>
<tr>
      <td> {$form.essay.html}</td>
</tr>    
</table>

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

