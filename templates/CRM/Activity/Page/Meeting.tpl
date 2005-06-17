{if $action eq 1 or $action eq 2}
   {include file="CRM/Activity/Form/Meeting.tpl"}
{else}
    <div id="help">
    {ts}Meetings can be added for this contact{/ts}
    </div>
{/if}
<div id="ltype">
 <p>
  {if $rows}

    <div class="form-item">

       {strip}
       <table>
       <tr class="columnheader">
        <th>{ts}Date{/ts}</th>
        <th>{ts}Title{/ts}</th>
        <th>{ts}Location{/ts}</th>
        <th></th>
       </tr>
       {foreach from=$rows item=row}
         <tr class="{cycle values="odd-row,even-row"} ">
           <td>{$row.meeting_date}</td>	
           <td> {$row.title} </td>
           <td> {$row.location} </td>
	   <td>{$row.action}</td>
        </tr>
       {/foreach}
       </table>
       {/strip}

       {if $action ne 1 and $action ne 2}
	<br/>
       <div class="action-link">
    	 <a href="{crmURL q="action=add"}">&raquo; {ts}New Meeting{/ts}</a>
       </div>
       {/if}
    </div>

  {else}
     <div class="message status">
      <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
      {capture assign=crmURL}{crmURL p='civicrm/contact/view/meeting' q='action=add'}{/capture}
      <dd>{ts 1=$crmURL}There are no Meetings for this contact. You can <a href="%1">add one</a>.{/ts}</dd>
     </div>
  {/if}   

 </p>
</div>
