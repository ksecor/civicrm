{* Quest College Match: Essay section - Short Answers *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app" id="essay-table">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="grouplabel">{ts}Please answer the following questions in no more than three lines. (<em>You may use lists instead of sentences when appropriate</em>.){/ts}</td>
</tr>
{include file="CRM/Quest/Form/MatchApp/Essay.tpl"}

