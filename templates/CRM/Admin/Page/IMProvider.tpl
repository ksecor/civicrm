{if $action eq 1 or $action eq 2}
   {include file="CRM/Admin/Form/IMProvider.tpl"}	
{/if}

<div id="improvider">
 <p>
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
        <th>Instant Messenger Service</th>
        <th>Reserved?</th>
        <th>Enabled?</th>
        <th></th>
       </tr>
       {foreach from=$rows item=row}
         <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td>{$row.name}</td>	
 	        <td>{if $row.is_reserved eq 1} Yes {else} No {/if}</td>
	       <td>{if $row.is_active eq 1} Yes {else} No {/if}</td>
            <td>{$row.action}</td>
         </tr>
       {/foreach}
       </table>
       {/strip}

       {if $action ne 1 and $action ne 2}
       <br/>
       <div class="action-link">
    	 <a href="{crmURL q="action=add&reset=1"}">New IM Service Provider</a>
       </div>
       {/if}
    </div>
 </p>
</div>
