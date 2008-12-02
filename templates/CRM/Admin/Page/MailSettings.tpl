{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/MailSettings.tpl"}
{/if}

{if $rows}
<div id="mSettings">
<p></p>
    <div class="form-item">
        {strip}
        <table cellpadding="0" cellspacing="0" border="0">
        <tr class="columnheader">
            <th>{ts}Name{/ts}</th>
            <th>{ts}User Name{/ts}</th>
            <th>{ts}Email Domain{/ts}</th>
 	    <th>{ts}Return-Path{/ts}</th>
            <th>{ts}Localpart{/ts}</th>
            <th>{ts}Source{/ts}</th>
	    <th>{ts}Protocol{/ts}</th>	
            <th>{ts}Port{/ts}</th>
            <th>{ts}Use SSL?{/ts}</th>
	    <th>{ts}Default?{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
	<tr id='rowid{$row.id}' class="{cycle values="odd-row,even-row"}">
	        <td>{$row.name}</td>	
	        <td>{$row.username}</td>	
                <td>{$row.domain}</td>
		<td>{$row.return_path}</td>
		<td>{$row.localpart}</td>	
		<td>{$row.source}</td>
		<td>{$row.protocol}</td>
		<td>{$row.port}</td>
	        <td>{if $row.is_ssl eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
		<td>{if $row.is_default eq 1}<img src="{$config->resourceBase}/i/check.gif" alt="{ts}Default{/ts}" />{/if}&nbsp;</td>
	        <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}" id="newMailSettings" class="button"><span>&raquo; {ts}New Mail Settings{/ts}</span></a>
        </div>
        {/if}
    </div>
</div>
{else}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        {capture assign=crmURL}{crmURL p='civicrm/admin/mailSettings' q="action=add&reset=1"}{/capture}
        <dd>{ts 1=$crmURL}There are no Mail Settings present. You can <a href='%1'>add one</a>.{/ts}</dd>
        </dl>
    </div>    
{/if}
