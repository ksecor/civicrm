{capture assign=crmURL}{crmURL p='civicrm/admin/messageTemplates' q="action=add&reset=1"}{/capture}
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/MessageTemplates.tpl"}
   
{elseif $action eq 4}
  {* View a system default workflow template *}

  <div id="help">
    {ts}You are viewing the system default template for this workflow. After upgrades OR if you are having issues with your customized version for this workflow, it is useful to compare your active message code to the default code shown here. You can use the 'Select' buttons below (with copy and paste commands) to copy the default code into a text editor and then compare it to your customized version.{/ts}
  </div>

  <fieldset>
  <div class="section msg_subject-section">
  <h3 class="header-dark">{$form.msg_subject.label}</h3>
    <div class="text">
      <textarea name="msg-subject" id="msg_subject" style="height: 6em; width: 45em;">{$form.msg_subject.value}</textarea>
      <div class='spacer'></div>
      <div class="section">
        <a href='#' onclick='MessageTemplates.msg_subject.select(); return false;' class='button'><span>Select Subject</span></a>
        <div class='spacer'></div>
      </div>
    </div>
  </div>
  
  <div class="section msg_txt-section">
  <h3 class="header-dark">{ts}Text Message{/ts}</h3>
    <div class="text">
      <textarea class="huge" name='msg_text' id='msg_text'>{$form.msg_text.value|htmlentities}</textarea>
      <div class='spacer'></div>
      <div class="section">
        <a href='#' onclick='MessageTemplates.msg_text.select(); return false;' class='button'><span>Select Text Message</span></a>
        <div class='spacer'></div>
      </div>
    </div>
  </div>

  <div class="section msg_html-section">
  <h3 class="header-dark">{ts}HTML Message{/ts}</h3>
    <div class='text'>
      <textarea class="huge" name='msg_html' id='msg_html'>{$form.msg_html.value|htmlentities}</textarea>
      <div class='spacer'></div>
      <div class="section">
        <a href='#' onclick='MessageTemplates.msg_html.select(); return false;' class='button'><span>Select HTML Message</span></a>
        <div class='spacer'></div>
      </div>
    </div>
  </div>
  
  <div id="crm-submit-buttons">{$form.buttons.html}</div>
  </fieldset>
  
{else}
    <div id="help">
    {ts}Message templates allow you to save and re-use messages with layouts. They are useful if you need to send similar emails to contacts on a recurring basis. You can also use them in CiviMail Mailings and they are required for CiviMember membership renewal reminders.{/ts} {help id="id-intro"}
    </div>
{/if}

{if $rows and $action ne 2 and $action ne 4}

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
                    <th id="sortable">{ts}Message Title{/ts}</th>
                    {if $smarty.section.template_selector.first}
                      <th>{ts}Message Subject{/ts}</th>
                      <th>{ts}Enabled?{/ts}</th>
                    {/if}
                    <th></th>
                  </tr>
                </thead>
                {* FIXME: the tab UI does not work if the selector is empty; we should get rid of the below line *}
                <tr><td></td><td></td><td></td><td></td></tr>
                {foreach from=$rows item=row}
                  {* we want to hide reserved rows; for the first selector show non-workflow_id templates, for the second selector show workflow_id templates *}
                  {if !$row.is_reserved and (($smarty.section.template_selector.first and !$row.workflow_id) or ($smarty.section.template_selector.last and $row.workflow_id))}
                    <tr id="row_{$row.id}" class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
                      <td>{$row.msg_title}</td>
                      {if $smarty.section.template_selector.first}
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
  </div>

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

{elseif $action ne 1 and $action ne 2 and $action ne 4}
  <div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
      <dd>{ts 1=$crmURL}There are no Message Templates entered. You can <a href='%1'>add one</a>.{/ts}</dd>
    </dl>
  </div>
{/if}
