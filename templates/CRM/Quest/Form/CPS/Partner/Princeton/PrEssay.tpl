{* Quest College Match: Partner: Princeton: Essay section *}
{include file="CRM/Quest/Form/CPS/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
   <td colspan="2" class="grouplabel">
    <label>In addition to the essays you have written for the College Match Application, please select one of the following themes and write an essay of about 300 words in response. Please do not repeat, in full or in part, the essay you wrote for the College Match Application. * (300 words max)
    </label><br /><br />
  
         <label>Using one of the quotes below (or your own favorite quotation) as a jumping off point, tell us about an event or experience that helped you define one of your values. </strong>. </label> <br /><br />
      {$form.essay_theme.html}&nbsp;&nbsp; {$form.princeton_essay.label}      
    {include file="CRM/Quest/Form/CPS/Essay.tpl"} </td>
    </tr>
</table>
