{capture assign=crmURL}{crmURL p='civicrm/admin/messageTemplates' q="action=add&reset=1"}{/capture}
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/MessageTemplates.tpl"}
{else}
    <div id="help">
    {ts}Message templates allow you to save and re-use messages with layouts. They are useful if you need to send similar emails to contacts on a recurring basis. You can also use them in CiviMail Mailings and they are required for CiviMember membership renewal reminders.{/ts} {help id="id-intro"}
    </div>
{/if}

{if $rows}
<div id="ltype">
<p></p>
    <div class="form-item" id=message_status_id>
        {strip}
        <table cellpadding="0" cellspacing="0" border="0">
        <tr class="columnheader">
            <th>{ts}Message Title{/ts}</th>
            <th>{ts}Message Subject{/ts}</th>
            <th>{ts}Enabled?{/ts}</th>
 	        <th></th>	
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td>{$row.msg_title}</td>	
	        <td>{$row.msg_subject}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td class="btn-slide" id={$row.id}>{$row.action|replace:'xx':$row.id}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}" id="newMessageTemplates" class="button"><span>&raquo; {ts}New Message Template{/ts}</span></a>
        </div>
        {/if}
    </div>
</div>
{else}
  {if $action ne 1}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{ts 1=$crmURL}There are no Message Templates entered. You can <a href='%1'>add one</a>.{/ts}</dd>
        </dl>
    </div>    
  {/if}
{/if}
