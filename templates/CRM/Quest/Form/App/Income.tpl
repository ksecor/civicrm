{* Quest Pre-application:  section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td colspan=2 class="grouplabel">
<p></p>
<p class="preapp-instruction">{ts}Household Income is the total income coming into your current, primary household.
Please enter income information for all individuals who contribute financially to your household.</p>
<p class="preapp-instruction">A Household Income page will be presented for each individual whom you listed as a Parent or Guardian in the Household section.
    You can record income from people other than your Parents/Guardians by checking the "Add another income source" box on the last Parent/Guardian page.{/ts}</p>
    </td>
</tr>
{if $form.$deleteButtonName.html}
    <tr>
        <td class="grouplabel" colspan="2">
	    <table cellpadding=2 cellspacing=0 border=1 width="100%" class="app">
                <tr>
                    <td class="grouplabel">{ts}If this person did not contribute to your household income, click on the delete button{/ts}</td>
                    <td>{$form.$deleteButtonName.html}</td>
                </tr>
             </table>
	</td>
    </tr>
{/if}
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
<tr><td colspan=2>
{section name=rowLoop start=1 loop=$maxIncome}
    {assign var=i value=$smarty.section.rowLoop.index}
    <div id="income_{$i}">
    <table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
    <tr>
       {assign var=source value="type_of_income_id_"|cat:$i}  
       <td class="grouplabel">{$form.$source.label}</td>
       <td class="fieldlabel">{$form.$source.html}</td>
    </tr>
    <tr>
       {assign var=job value="job_"|cat:$i} 
       <td class="grouplabel">{$form.$job.label}</td>
       <td class="fieldlabel">{$form.$job.html}</td>
    </tr>
       {assign var=amount value="amount_"|cat:$i}
       <td class="grouplabel">{$form.$amount.label}</td>
       <td class="fieldlabel">{$form.$amount.html}<BR>{ts}{hlp}ONLY enter the US dollars amount (e.g: 10000). DO NOT enter decimal amount. For currency exchange rates <a href="http://finance.yahoo.com/currency" target="_blank">click here</a>{/hlp}{/ts}</td>
    </tr>
    {if $i LT ($maxIncome-1)}
        {assign var=j value=$i+1}
        <tr><td colspan="2">
            <span id="income_{$j}[show]">
                {$income.$j.show}<br />
                {ts}Click here to enter additional information if this individual has more than one job or type of income.{/ts}
            </span>
            </td>
        </tr>
    {/if}
    </table>
    </div>
{/section}
</td></tr>
</table>

{if $form.another_income_source.html}
    <table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
    <tr>
        <td class="grouplabel" colspan="2">
            <p class="preapp-instruction">{ts}Check the <strong>Add another income source</strong> box to add information for individuals who are not living with you, but who contribute to the household financially.
            For these individuals, please enter only the amount contributed to your household annually (e.g., alimony or child support), not their total income which may not go to your household.{/ts}</p>
        </td>
    </tr>
    <tr>
        <td class="grouplabel" colspan="2">{$form.another_income_source.html}</td>
    </tr>
    </table>
{/if}
</table>

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

