{if $action eq 1 or $action eq 2 or $action eq 4}
   {include file="CRM/Activity/Form/Call.tpl"}
{else}
    <div id="help">
    {ts}Calls can be added for a contact{/ts}
    </div>
{/if}
<div id="ltype">
  <p>
   {if $rows}
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
        <th>{ts}Phone Call Date{/ts}</th>
        <th>{ts}Status{/ts}</th>
        <th>{ts}Priority{/ts}</th>
	<th>{ts}Next Call Date{/ts}</th>
        <th></th>
       </tr>
       {foreach from=$rows item=row}
         <tr class="{cycle values="odd-row,even-row"} ">
	   <td>{$row.phonecall_date}</td>	
           <td>{$row.status}</td>
	   <td>{$row.priority}</td>
           <td>{$row.next_phonecall_datetime}</td>
           <td>{$row.action}</td>
        </tr>	
       {/foreach}

       </table>
       {/strip}
  
       {if $action ne 1 and $action ne 2}
	<br/>
       <div class="action-link">
    	 <a href="{crmURL q="action=add"}">&raquo; {ts}New Call{/ts}</a>
       </div>
       {/if}
    </div>

  {else}
     <div class="message status">
      <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
      {capture assign=crmURL}{crmURL p='civicrm/contact/view/call' q='action=add'}{/capture}
      <dd>{ts 1=$crmURL}There are no Phone Calls for this contact. You can <a href="%1">add one</a>.{/ts}</dd>
     </div>
  {/if}   

 </p>
		
</div>
