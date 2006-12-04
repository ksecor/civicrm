{* Quest Pre-application: Household Information section *}

{include file="CRM/Quest/Form/CPS/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
  <td>
    <B>Do you have questions about who to register as a recommender or do you want to register more than three recommenders?</B> <A HREF="http://www.questbridge.org/students/school_information_faq.html" TARGET="_blank">Read more at our FAQs</A>
  </td>
</tr>
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=6 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
  <td colspan="6" class="grouplabel">
    <p class="preapp-instruction">{ts}Use this form to verify or update your recommenders. You must select exactly two teachers and one guidance counselor.<br /><br />To select a recommender, you need to check the 'Selected' check-box next to the recommender's name. If a recommender has already completed the recommendation, that recommender will be pre-selected.<br /><br />If you want to select a recommender that isn't listed, please enter the recommender's first name, last name, email address, and school. Also, check the 'Selected' box next to the recommender's name.<br /><br />If you see multiple entries for the same recommender, please select the recommender with the 'In Progress' status. If all entries for the recommender are 'Not Started', then it doesn't matter which entry to select.<br /><br />Please verify the email address you have entered for the recommender. If you have entered an incorrect email address, please re-enter the recommender's information.{/ts}</p>
  </td>
</tr>
<tr>
    <td colspan="6" class="grouplabel">
    <strong>{ts}Teachers&nbsp;{/ts}<span class="marker">*</span></strong></td>
</tr>
<tr>
    <td class="grouplabel"><label>{ts}Selected{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Status{/ts}</label></td>
    <td class="grouplabel"><label>{ts}First Name{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Last Name{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Email address{/ts}</label></td>
    <td class="grouplabel"><label>{ts}School{/ts}</label></td>
</tr>
{section name=teacherLoop start=1 loop=$teacherCount}
{assign var=index value=$smarty.section.teacherLoop.index}
<tr>
    {assign var="element_name" value="mark_cb_"|cat:$index}
    <td class="fieldlabel">{$form.$element_name.html}</td>
    {assign var="element_name" value="status_"|cat:$index}
    <td class="fieldlabel">{$form.$element_name.html}</td>
    {assign var="element_name" value="first_name_"|cat:$index}
    <td class="fieldlabel">{$form.$element_name.html|crmReplace:class:eight}</td>
    {assign var="element_name" value="last_name_"|cat:$index}
    <td class="fieldlabel">{$form.$element_name.html|crmReplace:class:eight}</td>
    {assign var="element_name" value="email_"|cat:$index}
    <td class="fieldlabel">{$form.$element_name.html|crmReplace:class:eight}</td>
    {assign var="element_name" value="school_id_"|cat:$index}
    <td class="fieldlabel">{$form.$element_name.html}</td>
</tr>
{/section}
<tr>
    <td colspan="6" class="grouplabel">
    <strong>{ts}Guidance Counselor&nbsp;{/ts}<span class="marker">*</span></strong></td>
</tr>
<tr>
    <td class="grouplabel"><label>&nbsp;</label></td>
    <td class="grouplabel"><label>{ts}Status{/ts}</label></td>
    <td class="grouplabel"><label>{ts}First Name{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Last Name{/ts}</label></td>
    <td class="grouplabel"><label>{ts}Email address{/ts}</label></td>
    <td class="grouplabel"><label>{ts}School{/ts}</label></td>
</tr>
{section name=counselorLoop start=$counselorStart loop=$counselorCount}
{assign var=index value=$smarty.section.counselorLoop.index}
<tr>
    {assign var="element_name" value="mark_cb_"|cat:$index}
    <td class="fieldlabel">{$form.$element_name.html}</td>
    {assign var="element_name" value="status_"|cat:$index}
    <td class="fieldlabel">{$form.$element_name.html}</td>
    {assign var="element_name" value="first_name_"|cat:$index}
    <td class="fieldlabel">{$form.$element_name.html|crmReplace:class:eight}</td>
    {assign var="element_name" value="last_name_"|cat:$index}
    <td class="fieldlabel">{$form.$element_name.html|crmReplace:class:eight}</td>
    {assign var="element_name" value="email_"|cat:$index}
    <td class="fieldlabel">{$form.$element_name.html|crmReplace:class:eight}</td>
    {assign var="element_name" value="school_id_"|cat:$index}
    <td class="fieldlabel">{$form.$element_name.html}</td>
</tr>
{/section}
</table>

{include file="CRM/Quest/Form/CPS/AppContainer.tpl" context="end"}

