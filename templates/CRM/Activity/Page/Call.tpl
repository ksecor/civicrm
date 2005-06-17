{if $action eq 1 or $action eq 2}
   {include file="CRM/Activity/Form/Call.tpl"}
{else}
    <div id="help">
    {ts}Calls{/ts}
    </div>
{/if}
<div id="ltype">
{if $rows}
 <p>
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
        <th>{ts}Phone Call Date{/ts}</th>
        <th>{ts}Status{/ts}</th>
        <th>{ts}Call Log{/ts}</th>
        <th>{ts}Priority{/ts}</th>
	<th>{ts}Next Call Date{/ts}</th>
        <th></th>
       </tr>
       {foreach from=$rows item=row}
         <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	       <td>{$row.name}</td>	
           <td>
            {$row.description}
           </td>
	       <td>{if $row.is_reserved eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	       <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	       <td>{$row.action}</td>
        </tr>	
       {/foreach}

       </table>
       {/strip}
{/if}
       {if $action ne 1 and $action ne 2}
	<br/>
       <div class="action-link">
    	 <a href="{crmURL q="action=add"}">&raquo; {ts}New Call{/ts}</a>
       </div>
       {/if}
    </div>
 </p>
		
</div>
