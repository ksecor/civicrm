{* Form elements for displaying and running action tasks on search results *}

 <div id="search-status">
    {ts count=$pager->_totalItems plural='Found %count contributions.'}Found %count contribution.{/ts}
    {* {ts}Total Amount:{/ts} {$total_amount} *}
    {if $cancel_amount}&nbsp; &nbsp; {ts}Cancelled Contribution Amount{/ts}: {$cancel_amount}{/if}
    {if $qill}
        <ul>
        {foreach from=$qill item=criteria}
            <li>{$criteria}</li>
        {/foreach}
        </ul>
    {/if}
 </div>
{include file="CRM/Contribute/Page/ContributionTotals.tpl"}

<div class="form-item"> 
  <div> 
     {$form._qf_Search_next_print.html}&nbsp;&nbsp;
     {$form.task.html}
     {$form._qf_Search_next_action.html} 
     <br /> 
     <label>{$form.radio_ts.ts_sel.html} {ts}selected records only{/ts}</label>&nbsp; <label>{$form.radio_ts.ts_all.html} {ts count=$pager->_totalItems plural='all %count records'}the found record{/ts}</label> 
   </div>
</div>
