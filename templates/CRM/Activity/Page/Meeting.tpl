{if $action eq 1 or $action eq 2}
   {include file="CRM/Activity/Form/Meeting.tpl"}
{else}
    <div id="help">
    {ts}Meetings{/ts}
    </div>
{/if}
<div id="ltype">
 <p>
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
 </p>
</div>
