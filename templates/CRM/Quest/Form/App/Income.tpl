{* Quest Pre-application:  section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}
</tr>
<tr>
    <td colspan=2>
<div id="help">     
{ts}The 'Household Information' and 'Parent/Guardian Information' sections need to be complete in order to use this section properly. (20)

Household Income is the total income coming into your current, primary household. Please list all individuals and other sources of income who contribute financially to your household. All individuals you listed in the Parent/Guardian Section are displayed. If any individual has more than one job or source of income, please list him or her multiple times for each source.

Also, please be sure to include any individuals not living with you who contribute to the household financially. For these individuals, please only enter the amount contributed to your household annually (e.g., alimony or child support), not their total income which may not go to your household.{/ts}
</div> 

    </td>
</tr>
<tr>
    <td rowspan=2 valign=top class="grouplabel" width="30%">
        <label>{ts}Name{/ts}</label> <span class="marker">*</span></td>
    <td class="fieldlabel" width="70%">
        {$form.first_name.html}<br />
        {$form.first_name.label}</td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.last_name.html}<br />
        {$form.last_name.label}</td>
</tr> 
{section name=rowLoop start=1 loop=4}
    <tr>
       {assign var=source value="type_of_income_id_"|cat:$smarty.section.rowLoop.index}  
       <td class="grouplabel">{$form.$source.label}</td>
       <td class="fieldlabel">{$form.$source.html}</td>
    </tr>
    <tr>
       {assign var=job value="job_"|cat:$smarty.section.rowLoop.index} 
       <td class="grouplabel">{$form.$job.label}</td>
       <td class="fieldlabel">{$form.$job.html}</td>
    </tr>
       {assign var=amount value="amount_"|cat:$smarty.section.rowLoop.index}
       <td class="grouplabel">{$form.$amount.label}</td>
       <td class="fieldlabel">{$form.$amount.html}</td>
    </tr>
{/section}

</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

