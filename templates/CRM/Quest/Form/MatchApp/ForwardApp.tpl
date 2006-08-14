{* Quest college Match application:Forward Application  section *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
{strip}
<table cellpadding=0 cellspacing=1 width="90%" class="app">
<tr>
    <td id="category" class="grouplabel"> {$wizard.currentStepRootTitle}{$wizard.currentStepTitle}for Regular Admissions</td>
</tr>

<tr>
    <td class="grouplabel">
        {ts}Most of our partner colleges accept the QuestBridge application for regular admissions process, and all of our partner colleges will waive their application fees for qualified low-income QuestBridge applicants. In the event you are not awarded a College Match scholarship, allowing us to forward your application will make you a candidate for regular admissions at our partner colleges.<br/><br/>{/ts}
        {ts}By selecting a college below, QuestBridge will forward your application to that college. Although you will need to contact the college or university to have your application be considered for regular admissions. You might also need to complete other documents, as required by the college or university.<br/><br/>{/ts} 
        {ts}Select the colleges to which you would like QuestBridge to forward your application for regular admissions. <span class="marker" title="This field is required.">*</span>{/ts} 
    </td>
</tr>

<tr><td class="grouplabel">
    <table cellpadding=0 cellspacing=1  width="90%" class="app">
    {assign var=count value=0}
    {foreach from=$partner item=type key=k1}
    {assign var=count value=$count+1}
    {assign var=regular_admission value="regular_addmission_"|cat:$k1}
        {if $count is odd }
            <tr>
        {/if}
            <td class="optionlist">{$form.$regular_admission.html} {$form.$regular_admission.label} &nbsp;<a href={$url_link.$k1}>(<u>learn more</u>)</a></td>
        {if $count is even }
            </tr>
        {/if}
    {/foreach}
    
    {if $count is odd }
        <td class="optionlist"></td></tr>
        {assign var=count value=$count-1}
    {/if}
    </table>
</td></tr>

</table>

<table cellpadding=0 cellspacing=1  width="90%" class="app">
<tr>
    <td id="category" class="grouplabel">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}for Scholarships</td>
</tr>
<tr>
    <td class="grouplabel">
    QuestBridge is forming partnerships by which select scholarships have the opportunity to receive information about our applicants. Please check any scholarships you want QuestBridge to forward your information to. (You will still need to fill out the scholarship's regular application if you are contacted by the scholarship provider).
    <br/><br/>
    <table cellpadding=0 cellspacing=1  width="90%" class="app">
    {foreach from=$partner_s item=type key=k2}
    {assign var=count value=$count+1}
    {assign var=regular_admission_s value="scholarship_addmission_"|cat:$k2}
        {if $count is odd}
            <tr>
        {/if}
            <td class="optionlist">{$form.$regular_admission_s.html} {$form.$regular_admission_s.label} &nbsp;<a href="{$scholarshipUrl_link.$k2}">(<u>learn more</u>)</a></td>
        {if $count is even}
            </tr>
        {/if}
    {/foreach}
    
    {if $count is odd}
            <td class="optionlist"></td><tr>
    {/if}
    </table>
   </td>
 </tr> 
</table>
{/strip} 
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}
