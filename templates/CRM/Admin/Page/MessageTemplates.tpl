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

  <div id='mainTabContainer'>
    <ul>
      <li id='tab_user'>    <a href='#user'     title='{ts}User-driven Messages{/ts}'>    {ts}User-driven Messages{/ts}    </a></li>
      <li id='tab_workflow'><a href='#workflow' title='{ts}System Workflow Messages{/ts}'>{ts}System Workflow Messages{/ts}</a></li>
    </ul>

    {* create two selector tabs, first being the ‘user’ one, the second being the ‘workflow’ one *}
    {section name='template_selector' loop=2}
      <div id='{if $smarty.section.template_selector.first}user{else}workflow{/if}' class='ui-tabs-panel ui-widget-content ui-corner-bottom'>
        <div>
          <p></p>
          <div class="form-item">
            {strip}
              {include file="CRM/common/enableDisable.tpl"}
              {include file="CRM/common/jsortable.tpl"}
              <table class="display">
                <thead>
                  <tr>
                    <th>{ts}Message Title{/ts}</th>
                    {if $smarty.section.template.first}
                      <th>{ts}Message Subject{/ts}</th>
                      <th>{ts}Enabled?{/ts}</th>
                    {/if}
                    <th></th>
                  </tr>
                </thead>
                {foreach from=$rows item=row}
                  {* we want to hide reserved rows; for the first selector show non-workflow_id templates, for the second selector show workflow_id templates *}
                  {if !$row.is_reserved and (($smarty.section.template_selector.first and !$row.workflow_id) or ($smarty.section.template_selector.last and $row.workflow_id))}
                    <tr id="row_{$row.id}" class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
                      <td>{$row.msg_title}</td>
                      {if $smarty.section.template.first}
                        <td>{$row.msg_subject}</td>
                        <td id="row_{$row.id}_status">{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
                      {/if}
                      <td>{$row.action|replace:'xx':$row.id}</td>
                    </tr>
                  {/if}
                {/foreach}
              </table>
            {/strip}

            {if $action ne 1 and $action ne 2 and $smarty.section.template_selector.first}
              <div class="action-link">
                <a href="{crmURL q="action=add&reset=1"}" id="newMessageTemplates" class="button"><span>&raquo; {ts}New Message Template{/ts}</span></a>
              </div>
            {/if}
          </div>
        </div>
      </div>
    {/section}

  <script type='text/javascript'>
    var selectedTab = 'user';
    {if $selectedChild}selectedTab = '{$selectedChild}';{/if}
    {literal}
      cj( function() {
        var tabIndex = cj('#tab_' + selectedTab).prevAll().length
        cj("#mainTabContainer").tabs( {selected: tabIndex} );
      });
    {/literal}
  </script>

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
