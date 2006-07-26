{* Quest College Match: Partner: Bowdoin: Athletics section *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle} (Optional)</td>
</tr>


<tr>
    <td class="grouplabel">{$form.learn.label}&nbsp;&nbsp;
                           {$form.learn.html}</td>
</tr>

<tr>
    <td class="grouplabel">The Bowdoin College Committee on Admissions requires that all applicants submit a supplemental essay so that we may become more familiar with you. Please address the topic below with a one- or two-page essay.<br \><br \>
  Bowdoin is a liberal arts college which is unusually vibrant intellectually. Some students enter Bowdoin with a clear commitment to a particular course of study; others come considering a broader range of academic possibilities while seeking the intellectual path which most excites them. What all students will share is exposure to the breadth and depth the Bowdoin curriculum provides.<br\><br\>
</td>
</tr>

{include file="CRM/Quest/Form/MatchApp/Essay.tpl"}
</table>



{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}
