{* Quest College Match application: Household Income section *}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
  <td>
    <B>What should you list as your household income?</B> <A HREF="http://www.questbridge.org/students/household_info_faq.html" TARGET="_blank">Read more at our FAQs</A>
  </td>
</tr>
</table>

<table cellpadding="0" cellspacing="1" border="1" width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td colspan=2 class="grouplabel">
    <p></p>
    <p class="app-instruction">{ts}Household Income is the total income coming into your current, primary household.
    Please enter income information for all individuals who contribute financially to your household.{/ts}</p>
    {edit}
    {ts}
    <p class="preapp-instruction">A Household Income page will be presented for each individual whom you listed as a Parent or Guardian in the Household section.
    You can record income from people other than your Parents/Guardians by checking the "Add another income source" box on the last Parent/Guardian page.{/ts}</p>
    {/edit}
    </td>
</tr>
{edit}
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
{/edit}
<tr>
    <td rowspan=2 valign=top class="grouplabel" width="30%">
        <label>{ts}Name{/ts}</label> <span class="marker">*</span></td>
    <td class="fieldlabel" width="70%">
        {$form.first_name.html}<br />
        {edit}{$form.first_name.label}{/edit}</td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.last_name.html}<br />
        {edit}{$form.last_name.label}{/edit}</td>
</tr>
<tr><td colspan="2">
{section name=rowLoop start=1 loop=$maxIncome}
    {assign var=i value=$smarty.section.rowLoop.index}
    <div id="id_income_{$i}">
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
       <td class="fieldlabel">{$form.$amount.html}<BR>{ts}{edit}ONLY enter the US dollars amount (e.g: 10000). DO NOT enter decimal amount. For currency exchange rates <a href="http://finance.yahoo.com/currency" target="_blank">click here</a>{/edit}{/ts}</td>
    </tr>
    {if $i LT ($maxIncome-1)}
        {assign var=j value=$i+1}
        <tr><td colspan="2">
            <span id="id_income_{$j}_show">
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
<tr><td colspan="2" class="grouplabel">{$form.total_amount.label}: &nbsp; $ {$form.total_amount.html|crmReplace:class:texttolabel}<br /><br />
            <span class="font-red">If the total income is not correct, please revise the income you entered above.</span>
    </td>
</tr>
</table>

{edit}
{if $form.another_income_source.html}
    <table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
    <tr>
        <td class="grouplabel" colspan="2">
            <p class="app-instruction">{ts}Check the <strong>Add another income source</strong> box to add information for individuals who are not living with you, but who contribute to the household financially.
            For these individuals, please enter only the amount contributed to your household annually (e.g., alimony or child support), not their total income which may not go to your household.{/ts}</p>
        </td>
    </tr>

    <tr>
        <td class="grouplabel" colspan="2">{$form.another_income_source.html}</td>
    </tr>
    </table>
{/if}
{/edit}

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}
{literal}
    
    <script type="text/javascript">
    calculateIncome();
    document.getElementById("total_amount").readOnly = 1;
    function calculateIncome() 
    {
      var amount_1,amount_2,amount_3,amount_total;
      amount_1 = amount_2 = amount_3 = amount_total = 0;

      amount_1 = document.getElementById("amount_1").value;
      amount_2 = document.getElementById("amount_2").value;
      amount_3 = document.getElementById("amount_3").value;
      if ( !(parseInt(amount_1) > 0)) {amount_1 = 0} 
      if ( !(parseInt(amount_2) > 0)) {amount_2 =0} 
      if ( !(parseInt(amount_3) > 0)) {amount_3 =0} 
      document.getElementById("total_amount").value
      amount_total = parseInt(amount_1) + parseInt(amount_2) + parseInt(amount_3);
      document.getElementById("total_amount").value = formatCurrency(amount_total);
    }
    
    function formatCurrency(num) {
        num = num.toString().replace(/\$|\,/g,'');
        if(isNaN(num))
        num = "0";
        sign = (num == (num = Math.abs(num)));
        num = Math.floor(num*100+0.50000000001);
        cents = num%100;
        num = Math.floor(num/100).toString();
        if(cents<10)
        cents = "0" + cents;
        for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
        num = num.substring(0,num.length-(4*i+3))+','+
        num.substring(num.length-(4*i+3));
        return (((sign)?'':'-') + num + '.' + cents);
    }

    </script>
{/literal}
