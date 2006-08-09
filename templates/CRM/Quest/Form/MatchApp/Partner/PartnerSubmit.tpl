{* Quest CM Recommendations: Submit Partner Supplement Form *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{ts}Submit Partner Supplement{/ts}</td>
</tr>
<tr>
<td colspan=2 class="grouplabel">
<p class="app-instruction">
{ts}If you have completed the partner supplements, please click the "Submit Partner Supplement " button below. Once you click "Submit", we will check the supplements for any errors or missing pieces of information that are required. Please be patient as the checking process may take a minute or so.
<br /><br />
Please note: if you need to make any changes to the Partner Supplements after you have submitted it, please "Submit" again so it can be checked again. Thank you.
{/ts} 
</p>
<p>
{$form.is_partner_supplement_share.html}&nbsp;{ts} I understand that my application will be shared with QuestBridge's partner(s) (college partners and scholarship partners). If I'm admitted to our partner college(s) or awarded a scholarship from our scholarship partner, I grant QuestBridge the right to use my name, address (city, state and country only), photograph, biographical and academic information for any publicity, advertising and promotional purposes, except where prohibited by law. By submitting this application, I understand I am also giving QuestBridge written permission to find out where I ultimately enroll in college. I also give QuestBridge permission to find out from its partner colleges whether I have been offered admission. I have filled out the application to the best of my knowledge and understand any deliberate misrepresentation of information will result in forfeiture of any scholarship(s) or admission received. I certify that all information in my application, including my essays, is my own work, factually true, complete and accurate, and honestly presented.{/ts} <span class="marker">*</span>
</td>
</tr>
</table>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

