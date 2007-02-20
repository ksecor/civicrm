<div class="view-content">
<div id="help">
<p>{ts 1=$displayName}This page lists all contributions received from %1 since inception.{/ts} 
</p>
</div>

{if $contribute_rows}
    {strip}
 <div><label>{ts}Contributions{/ts}</label></div>
  <table class="selector">
    <tr class="columnheader">
      <th>{ts}Total Amount{/ts}</th>
      <th>{ts}Contribution Type{/ts}</th>
      <th>{ts}Received date{/ts}</th>
      <th>{ts}Receipt Sent{/ts}</th>
      <th>{ts}Status{/ts}</th>
      <th></th>
    </tr>

     {counter start=0 skip=1 print=false}
     {foreach from=$contribute_rows item=row}
       <tr>
       <td>{$row.total_amount|crmMoney} {if $row.amount_level } - {$row.amount_level} {/if}
    {if $row.contribution_recur_id}
     <br /> {ts}(Recurring Contribution){/ts}
    {/if}

    </td>
    <td>{$row.contribution_type}</td>
    <td>{$row.receive_date|truncate:10:''|crmDate}</td>
    <td>{$row.receipt_date|truncate:10:''|crmDate}</td>
    <td>{$row.contrib_status}</td>
       </tr>
      {/foreach}
  </table>
  {/strip}
{else}
   <div class="messages status">
       <dl>
       <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
       <dd>
            {if $permission EQ 'edit'}
                {ts 1=$newContribURL}There are no contributions recorded for this contact. You can <a href="%1">enter one now</a>.{/ts}
            {else}
                {ts}There are no contributions recorded for this contact.{/ts}
            {/if}
       </dd>
       </dl>
  </div>
{/if}
 

{if $honor}	
 {if $honorRows}
{strip}
<div id="help">
<p>{ts 1=$displayName}Contributions made in honor of %1.{/ts} 
</p>
</div>
  <table class="selector">
    <tr class="columnheader">
        <th >{ts}Contributor{/ts}</th> 
        <th>{ts}Amount{/ts}</th>
	    <th>{ts}Contribution Type{/ts}</th>
        <th>{ts}Received date{/ts}</th>
        <th>{ts}Receipt Sent{/ts}</th>
        <th>{ts}Status{/ts}</th>
        <th></th>   
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
	{foreach from=$recurRows item=row}
	   <tr>
        <td> <label>{ts}{$recurRows.0.amount}{/ts}</label>  every {$recurRows.0.frequency_interval} {$recurRows.0.frequency_unit} for {$recurRows.0.installments} installments  </td>
        <td><a href="{$cancelSubscriptionUrl}">{ts}Change Recurring Contribution{/ts}</a></td>
	   </tr>
    {/foreach}
  </table>
 {/strip}
 {/if}   	  	
{/if} 

</div>
