{capture assign=crmURL}{crmURL p='civicrm/admin/messageTemplates' q="action=add&reset=1"}{/capture}
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/MessageTemplates.tpl"}
{elseif $action eq 4}
  <h3 class='head'>{$form.msg_subject.label}</h3>
  <p>{$form.msg_subject.html|crmReplace:class:huge}</p>

  <h3 class='head'>{ts}Text Message{/ts}</h3>
  <p>{$form.msg_text.html|crmReplace:class:huge}</p>

  <h3 class='head'>{ts}HTML Message{/ts}</h3>
  <p>{$form.msg_html.html|crmReplace:class:huge}</p>
{else}
    <div id="help">
    {ts}Message templates allow you to save and re-use messages with layouts. They are useful if you need to send similar emails to contacts on a recurring basis. You can also use them in CiviMail Mailings and they are required for CiviMember membership renewal reminders.{/ts} {help id="id-intro"}
    </div>
{/if}

{if $rows and $action ne 2}
  <div id="ltype">
    <p></p>
    <div class="form-item" id=message_status_id>
      {strip}
        {* handle enable/disable actions*}
        {include file="CRM/common/enableDisable.tpl"}
        {include file="CRM/common/jsortable.tpl"}
        <table id="options" class="display">
          <thead>
            <tr>
              <th id="sortable">{ts}Message Title{/ts}</th>
              <!-- <th>{ts}Message Subject{/ts}</th> -->
              <!-- <th>{ts}Enabled?{/ts}</th> -->
              <th></th>
            </tr>
          </thead>
          {foreach from=$rows item=row}
            {* we skip the reserved rows here rather than on the PHP side so that we can still edit the upstream templates if needed be *}
            {if !$row.is_reserved}
              <tr id="row_{$row.id}" class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
                <td>{$row.msg_title}</td>
                <!-- <td>{$row.msg_subject}</td> -->
                <!-- <td id="row_{$row.id}_status">{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td> -->
                <td>{$row.action|replace:'xx':$row.id}</td>
              </tr>
            {/if}
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
  {if $action ne 1 and $action ne 2}
    <div class="messages status">
      <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{ts 1=$crmURL}There are no Message Templates entered. You can <a href='%1'>add one</a>.{/ts}</dd>
      </dl>
    </div>
  {/if}
{/if}
