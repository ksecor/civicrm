{capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}
{capture assign=crmURL}{crmURL p='civicrm/admin/messageTemplates' q="action=add&reset=1"}{/capture}
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/MessageTemplates.tpl"}
{else}
    <div id="help">
    {ts}Message templates allow you to save and re-use messages with layouts. They are useful if you need to send similar emails to
    contacts on a recurring basis. You can also use them in CiviMail Mailings and they are required for CiviMember membership renewal reminders.{/ts} {help id="id-intro"}
    </div>
{/if}

{if $rows}
<div id="ltype">
<p></p>
    <div class="form-item" id=message_status_id>
        {strip}
        <table enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
        <thead>
        <tr class="columnheader">
            <th field="Message Title" dataType="String">{ts}Message Title{/ts}</th>
            <th field="Message Subject" dataType="String">{ts}Message Subject{/ts}</th>
            <th field="Enabled"  dataType="String" >{ts}Enabled?{/ts}</th>
 	    <th datatype="html"></th>	
        </tr>
	</thead>
        <tbody>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td>{$row.msg_title}</td>	
	        <td>{$row.msg_subject}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{$row.action}</td>
        </tr>
        </tbody>
        {/foreach}
        </table>
        {/strip}

        {if $action ne 1 and $action ne 2}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&reset=1"}" id="newMessageTemplates">&raquo; {ts}New Message Templates{/ts}</a>
        </div>
        {/if}
    </div>
</div>
{else}
  {if $action ne 1}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{ts 1=$crmURL}There are no Message Templates  entered. You can <a href="%1">add one</a>.{/ts}</dd>
        </dl>
    </div>    
  {/if}
{/if}
