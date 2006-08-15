{* Quest CM Recommendations: Submit Recommendation Form *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">Complete Recommendation</td>
</tr>
<tr>
<td colspan=2 class="grouplabel">
<p class="app-instruction">
{ts}If you have completed this recommendation application, please click the "Submit Recommendation" button below.
Once you click "Submit", we will check the recommendation for any errors or missing pieces of information that are required.
Please be patient as the checking process may take a minute or so.
<br /><br />
Please note: if you need to make changes to the recommendation after you have submitted it,
please "Submit" the recommendation again so it can be checked again. Thank you.
{/ts} 
</p>
<p>
{$form.is_partner_share.html}&nbsp;{ts} I understand that my recommendation will be shared with colleges and universities that this applicant is applying to. I have filled out the recommendation to the best of my knowledge, and all the information on this recommendation is factually true and honestly presented.{/ts} <span class="marker">*</span>
</td>
</tr>
</table>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

