<div class="view-content">
{if $contribute_rows}
{strip}

  <table class="selector">
    <tr class="columnheader">
      <th>{ts}Total Amount{/ts}</th>
      <th>{ts}Contribution Type{/ts}</th>
      <th>{ts}Received date{/ts}</th>
      <th>{ts}Receipt Sent{/ts}</th>
      <th>{ts}Status{/ts}</th>
    </tr>

     {foreach from=$contribute_rows item=row}
       <tr id='rowid{$row.contribution_id}' class="{cycle values="odd-row,even-row"}{if $row.cancel_date} disabled{/if}">
       <td>{$row.total_amount|crmMoney} {if $row.amount_level } - {$row.amount_level} {/if}
    {if $row.contribution_recur_id}
     <br /> {ts}(Recurring Contribution){/ts}
    {/if}</td>
       <td>{$row.contribution_type}</td>
       <td>{$row.receive_date|truncate:10:''|crmDate}</td>
       <td>{$row.receipt_date|truncate:10:''|crmDate}</td>
       <td>{$row.contribution_status_id}</td>
       </tr>
      {/foreach}
  </table>
  {/strip}
{if $contributionSummary.total.count gt 12} 
{ts}Contact us for information about contributions prior to those listed above.{/ts}
{/if}
{else}
   <div class="messages status">
       <dl>
       <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
       <dd>
         {ts}There are no contributions on record for you.{/ts}
       </dd>
       </dl>
  </div>
{/if}
 

{if $honor}	
  {if $honorRows}
    {strip}
    <div id="help">
        {ts}Contributions made in your honor{/ts}:
    </div>
      <table class="selector">
        <tr class="columnheader">
            <th >{ts}Contributor{/ts}</th> 
            <th>{ts}Amount{/ts}</th>
            <th>{ts}Contribution Type{/ts}</th>
            <th>{ts}Received date{/ts}</th>
            <th>{ts}Receipt Sent{/ts}</th>
            <th>{ts}Status{/ts}</th>
        </tr>
        {foreach from=$honorRows item=row}
           <tr id='rowid{$row.honorId}' class="{cycle values="odd-row,even-row"}">
           <td><a href="{crmURL p="civicrm/contact/view" q="reset=1&cid=`$row.honorId`"}" id="view_contact">{$row.display_name}</a></td>
           <td>{$row.amount|crmMoney}</td>
               <td>{$row.type}</td>
               <td>{$row.receive_date|truncate:10:''|crmDate}</td>
               <td>{$row.receipt_date|truncate:10:''|crmDate}</td>
               <td>{$row.contribution_status}</td>
          </tr>
        {/foreach}
    </table>
    {/strip}
  {/if}   	  	
{/if} 

{if $recur}	
 {if $recurRows}
 {strip}
<div><label>{ts}Recurring Contribution(s){/ts}</label></div>
  <table class="selector">
    <tr class="columnheader">
        <th >{ts}Terms:{/ts}</th> 
        <th></th>   
     </tr>
	{foreach from=$recurRows item=row key=id}
	   <tr>
        <td> <label>{ts}{$recurRows.$id.amount}{/ts}</label>  every {$recurRows.$id.frequency_interval} {$recurRows.$id.frequency_unit} for {$recurRows.$id.installments} installments  </td>
        <td><a href="{$recurRows.$id.cancelSubscriptionUrl}">{ts}Change Recurring Contribution{/ts}</a></td>
	   </tr>
    {/foreach}
  </table>
 {/strip}
 {/if}   	  	
{/if} 

</div>
