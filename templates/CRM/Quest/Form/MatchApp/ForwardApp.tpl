{* Quest Pre-application:Forward Application  section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}
{strip}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>

<tr>
    <td colspan=2 class="grouplabel">
        {ts}Most of our partner colleges allow the QuestBridge application to serve as a free application for regular admission to their college. A majority of the QuestBridge applicants go through the College Match Program, although many of the applicants do not get matched. In the event you do not get matched, allowing us to forward your application will make you a candidate for regular admissions at our partner colleges. <br/><br/>{/ts}
{ts}Select the colleges to which you would like QuestBridge to forward your application for regular admissions.{/ts} 
     </td>
</tr>   
 

<tr>
        <table  class="app">
        
        {assign var="countEI" value=0}
        {foreach from=$partner item=type key=key}
        {assign var=regular_addmission value="regular_addmission_"|cat:$key}
        <td class="optionlist">{$form.$regular_addmission.html}{$form.$regular_addmission.label}</td>
        {/foreach}
        {if ($countEI gt 9) and ($countEI is not odd) }
        <td class="optionlist"></td>
        {/if}       
        </table>
</tr>

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">

<tr>
    <td colspan=2 class="grouplabel">
{ts}QuestBridge is forming partnerships by which select scholarships have the opportunity to search our growing pool for applicants. Please check any scholarships you want QuestBridge to forward your information to. (You will still need to fill out the scholarship's regular application if you are recruited as a referral student).
<br/><br/>{/ts}
{ts}Select the scholarship you would like QuestBridge to forward your information to.{/ts} 
    </td>
</tr> 
    
<tr>
        <table cellpadding=0 cellspacing=3 border=1 width="90%" class="app">
        
        {assign var="countEI" value=0}
        {foreach from=$partner item=type key=key}
         {assign var=regular_addmission_s value="regular_addmission_s_"|cat:$key}
             <td class="optionlist">{$form.$regular_addmission_s.html}{$form.$regular_addmission_s.label}</td>
        {/foreach}
        {if ($countEI gt 9) and ($countEI is not odd) }
        <td class="optionlist"></td>
        {/if}       
        </table>   
</tr>


   
</table>


{/strip}

