{capture assign=newPledgeURL}{crmURL q="action=add&reset=1"}{/capture}

<a accesskey="N" href="{$newPledgeURL}" id="newManagePledgeBank" class="button"><span>&raquo; {ts}New Pledge{/ts}</span></a>

{if $rows}
    <div class="form-item" id=pledge_id>
        {strip}
        {include file="CRM/common/pager.tpl" location="top"}
        {include file="CRM/common/pagerAToZ.tpl}    
        <table cellpadding="0" cellspacing="0" border="0">
         <tr class="columnheader">
            <th>{ts}Title{/ts}</th>
            <th>{ts}Id{/ts}</th>
            <th>{ts}Signers limit{/ts}</th>
            <th>{ts}Deadline{/ts}</th>
            <th>{ts}Creator{/ts}</th>
            <th>{ts}Status{/ts}</th>
	    <th>{ts}Active?{/ts}</th>
	    <th></th>
         </tr>
        {foreach from=$rows item=row key=id}
          <tr class="{cycle values="odd-row,even-row"}{if NOT $row.isActive} disabled{/if}">
            <td>{$row.title}</td>
            <td>{$id}</td>  
            <td>{$row.signersLimit}</td>	
    	    <td>{$row.deadline|crmDate}</td>
   	    <td>{$row.displayName}</td>
            <td>{$row.status}</td>
	    <td>{if $row.isActive eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	    <td>{$row.action}</td>
          </tr>
        {/foreach}    
        </table>
        {include file="CRM/common/pager.tpl" location="bottom"}
        {/strip}
      
    </div>
{else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
        <dd>{ts 1=$newPledgeURL}There are no pledge created yet. You can <a href='%1'>add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}