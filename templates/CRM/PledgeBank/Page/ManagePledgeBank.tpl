{capture assign=newPledgeURL}{crmURL q="action=add&reset=1"}{/capture}

<a accesskey="N" href="{$newPledgeURL}" id="newManagePledgeBank" class="button"><span>&raquo; {ts}New Pledge{/ts}</span></a>

{if $rows}
    <div class="form-item" id=pledge_id>
        {strip}
        {include file="CRM/common/pager.tpl" location="top"}
        {include file="CRM/common/pagerAToZ.tpl}    
        <table cellpadding="0" cellspacing="0" border="0">
         <tr class="columnheader">
            <th>{ts}Pledge{/ts}</th>
            <th>{ts}City{/ts}</th>
            <th>{ts}State/Province{/ts}</th>
            <th>{ts}Creates{/ts}</th>
            <th>{ts}Deadlines{/ts}</th>
	    <th>{ts}Active?{/ts}</th>
	    <th></th>
         </tr>
        {foreach from=$rows item=row}
          <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>{$row.title} ({ts}ID:{/ts} {$row.id})</td> 
            <td>{$row.city}</td>  
            <td>{$row.state_province}</td>	
    	    <td>{$row.created_date|crmDate}</td>
   	        <td>{$row.deadline|crmDate}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
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