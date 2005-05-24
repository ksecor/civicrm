{if $action eq 1 or $action eq 2}
   {include file="CRM/Admin/Form/MobileProvider.tpl"}
{else}    
    <div id="help">
    {ts}When recording mobile phone numbers for contacts, it may be useful to include the Mobile Phone Service
    Provider (e.g. Cingular, Sprint, etc.). CiviCRM is installed with the most commonly encountered service providers. Administrators
    may define as many additional providers as needed.{/ts}
    </div>
{/if}

<div id="mobprovider">
 <p>
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
	     <th>{ts}Mobile Phone Provider{/ts}</th>
         <th>{ts}Reserved?{/ts}</th>
         <th>{ts}Enabled?{/ts}</th>
	     <th></th>
       </tr>
       {foreach from=$rows item=row}
         <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	       <td> {$row.name}</td>
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
    	 <a href="{crmURL q="action=add&reset=1"}">&raquo; {ts}New Mobile Phone Provider{/ts}</a>
       </div>
       {/if}
    </div>
 </p>
</div>
