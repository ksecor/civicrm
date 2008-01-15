{if $action eq 1 or $action eq 2 or $action eq 8} {* add, update or view *}            
    {include file="CRM/Contribute/Form/Contribution.tpl"}
{elseif $action eq 4}
    {include file="CRM/Contribute/Form/ContributionView.tpl"}
{/if}
{if $honorRows}
 <div class="form-item">
        {strip}
        <table cellpadding="0" cellspacing="0" border="0">
        <tr class="columnheader">
	    <th>{ts}Contributor{/ts}</th> 
        <th>{ts}Amount{/ts}</th>
	    <th>{ts}Type{/ts}</th>
        <th>{ts}Source{/ts}</th>
        <th>{ts}Received{/ts}</th>
        <th>{ts}Status{/ts}</th>
        <th>&nbsp;</th>   
        </tr>
       	{foreach from=$honorRows item=row}
	   <tr id='rowid{$row.honorId}' class="{cycle values="odd-row,even-row"}">
	       <td><a href="{crmURL p="civicrm/contact/view" q="reset=1&cid=`$row.honorId`"}" id="view_contact">{$row.display_name}</a></td>
	       <td>{$row.amount|crmMoney}</td>
           <td>{$row.type}</td>
           <td>{$row.source}</td>
           <td>{$row.receive_date|truncate:10:''|crmDate}</td>
           <td>{$row.contribution_status}</td>
	       <td>{$row.action}</td>
	  </tr>
        {/foreach}
       	</table>
	    {/strip}
 </div>
{/if}

