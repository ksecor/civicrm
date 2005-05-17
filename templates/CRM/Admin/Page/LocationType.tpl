{if $action eq 1 or $action eq 2}
   {include file="CRM/Admin/Form/LocationType.tpl"}
{else}
    <div id="help">
    {ts}Viewing Lcation Type. You can create Location Type as per your need.{/ts}
    </div>
{/if}
<div id="ltype">
 <p>
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
        <th>{ts}Name{/ts}</th>
        <th>{ts}Description{/ts}</th>
        <th>{ts}Reserved?{/ts}</th>
        <th>{ts}Enabled?{/ts}</th>
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

       {if $action ne 1 and $action ne 2}
	<br/>
       <div class="action-link">
    	 <a href="{crmURL q="action=add&reset=1"}">&raquo; {ts}New Location Type{/ts}</a>
       </div>
       {/if}
    </div>
 </p>
</div>
