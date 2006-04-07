{* Quest Pre-application:  section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td colspan=2 class="grouplabel">
<p>
<p class="preapp-instruction">{ts}Household Income is the total income coming into your current, primary household. Please list all individuals and other sources of income who contribute financially to your household.</p>
<p class="preapp-instruction">An Income Source page will be presented for each individual whom you listed as a Parent or Guardian in the Household section.{/ts}</p>
    </td>
</tr>
<tr>
    <td rowspan=2 valign=top class="grouplabel" width="30%">
        <label>{ts}Name{/ts}</label> <span class="marker">*</span></td>
    <td class="fieldlabel" width="70%">
        {$form.first_name.html}<br />
        {hlp}{$form.first_name.label}{/hlp}</td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.last_name.html}<br />
        {hlp}{$form.last_name.label}{/hlp}</td>
</tr> 
{section name=rowLoop start=1 loop=3}
    {if $smarty.section.rowLoop.index GT 1}
    <tr>
        <td class="grouplabel" colspan="2">
            <p class="preapp-instruction">{ts}If this individual has more than one job or source of income, please enter information for the additional source below.{/ts}</p>
        </td>
    </tr>
    {/if}
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
       <td class="fieldlabel">{$form.$amount.html}<BR>{ts}{hlp}ONLY enter the US dollars amount (e.g: 10000). DO NOT enter decimal amount. For currency exchange rates <a href="http://finance.yahoo.com/currency" target="_blank">click here</a>{/hlp}{/ts}</td>
    </tr>
{/section}

{if $form.another_income_source.html}
    <tr>
        <td class="grouplabel" colspan="2">
            <p class="preapp-instruction">{ts}Check the <strong>Add another income source</strong> box to add information for individuals who are not living with you, but who contribute to the household financially.
            For these individuals, please enter only the amount contributed to your household annually (e.g., alimony or child support), not their total income which may not go to your household.{/ts}</p>
        </td>
    </tr>
    <tr>
        <td class="grouplabel" colspan="2">{$form.another_income_source.html}</td>
    </tr>
{/if}
    <tr>
        <td class="grouplabel" colspan="2">{$form.$deleteButtonName.html}</td>
    </tr>
</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

