{capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}
{capture assign=crmURL}{crmURL p='civicrm/admin/messageTemplates' q="action=add&reset=1"}{/capture}
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/MessageTemplates.tpl"}
{else}
    <div id="help">
    {ts 1="http://wiki.civicrm.org/confluence//x/gCY" 2=$docURLTitle}Message templates allow you to save and re-use messages with layouts. You can use them when sending email to
    one or more contacts. If you are using the CiviMember component, you can also use a message template to send Membership Renewal Reminders.
    You may include tokens to represent fields (like a contact's "first name") in the message subject and body. These will be replaced with the actual value of the corresponding
    field in the outgoing message EXAMPLE: Dear {ldelim}contact.first_name{rdelim} (<a href="%1" target="_blank" title="%2">read more...</a>).{/ts}
    <p class="font-italic description">{ts}NOTE: This template feature is not yet integrated with the CiviMail component. CiviMail message templates must be saved as local text files and uploaded.{/ts}</p>
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
