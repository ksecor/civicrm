<div id="help">
  <p>{ts}This Screen allows to list all the Personal Campaign Pages set up in the system and Admin can change the statuses...{/ts}</p>
</div>

{include file="CRM/Contribute/Form/PCP/PCP.tpl"}

{if $rows}
<div id="ltype">
<p></p>
    <div class="form-item">
        {strip}
        <table cellpadding="0" cellspacing="0" border="0">
           <tr class="columnheader">
            <th>{ts}Page Title{/ts}</th>
            <th>{ts}Supporter{/ts}</th>
            <th>{ts}Contribution Page{/ts}</th>
            <th>{ts}Page Active From{/ts}</th>
            <th>{ts}Page Active Until{/ts}</th>
            <th>{ts}Status{/ts}</th>
            <th></th>
          </tr>
         {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}">
	        <td>{$row.title}</td>	
	        <td>{$row.supporter}</td>
                <td>{$row.contribution_page_id}</td>
	        <td>{$row.page_active_from|truncate:10:''|crmDate}</td>
	        <td>{$row.page_active_until|truncate:10:''|crmDate}</td>
	        <td>{$row.status_id}</td>
	        <td>{$row.action}</td>
        </tr>
        {/foreach}
         </table>
        {/strip}
    </div>
</div>
{else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
        <dd>{ts 1=$crmURL}There are no records entered for Personal Campaign Page.{/ts}</dd>
        </dl>
    </div>    
{/if}
