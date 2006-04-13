{* Quest Pre-application: Scholarship Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
<td colspan=2 class="grouplabel">
<p class="preapp-instruction">
{ts}If you have completed the application, please click the "Submit
Application" button below. Once you click "Submit", we will check the
application for any errors or missing pieces of information that are
required. Please be patient as the checking process may take a minute
or so.{/ts}
<br><br>
{ts}Please note: if you need to make changes to the application
after you have submitted it, please "Submit" the application again so
it can be checked again. Thank you.{/ts}
</p>
<p>
{$form.approve.html}&nbsp;{ts}I understand that my application will be
shared with QuestBridge's partner colleges and may be shared with 
non-partner colleges. If I am awarded the scholarship, I grant QuestBridge
the right to use my name, address (city, state and country only), 
photograph, biographical and academic information for any publicity, 
advertising and promotional purposes, except where prohibited by law. I have 
filled out the application to the best of my knowledge and understand any 
deliberate misrepresentation of information will result in forfeiture of any 
scholarship(s) received.{/ts}
</td>
</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

