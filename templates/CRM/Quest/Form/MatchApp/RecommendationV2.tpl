{* Quest Pre-application: Household Information section *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
  <td>
    <B>Do you have questions about who to register as a recommender or do you want to register more than three recommenders?</B> <A HREF="http://www.questbridge.org/students/school_information_faq.html" TARGET="_blank">Read more at our FAQs</A>
  </td>
</tr>
</table>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=5 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
  <td colspan="5" class="grouplabel">
    <p class="preapp-instruction">{ts}Use this form to confirm your Recommender selections. You may select an existing entry below by checking the box to the left of their name. OR you can enter information for a new recommender. You must select exactly two teachers and one guidance counselor. We will send an email to your recommenders with instructions on how to complete their recommendations. Please verify all the contact information you enter for your recommenders.{/ts}</p>
  </td>
</tr>
<tr>
    <td colspan="5" class="grouplabel">
    <strong>{ts}Teachers&nbsp;{/ts}<span class="marker">*</span></strong></td>
</tr>
<tr>
    <td class="grouplabel"><label>{ts}Selected{/ts}</label></td>
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
    <td colspan="5" class="grouplabel">
    <strong>{ts}Guidance Counselor&nbsp;{/ts}<span class="marker">*</span></strong></td>
</tr>
<tr>
    <td class="grouplabel"><label>&nbsp;</label></td>
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

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

