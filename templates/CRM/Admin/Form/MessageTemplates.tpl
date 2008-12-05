{* this template is used for adding/editing/deleting Message Templates *}
{capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}
{assign var="tokenDocLink" value="http://wiki.civicrm.org/confluence//x/gCY"}
<div id="help">
    {ts}Use this form to add or edit re-usable message templates.{/ts} {help id="id-msgTplIntro"}
</div>
<div class="form-item" id="message_templates">
<fieldset><legend>{if $action eq 1}{ts}New Message Template{/ts}{elseif $action eq 2}{ts}Edit Message Template{/ts}{else}{ts}Delete Message Template{/ts}{/if}</legend>
  
   {if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>    
          {ts}Do you want to delete this message template?{/ts}
          </dd>
       </dl>
      </div>
   {else}
    <table class="form-layout-compressed">
        <tr>
            <td class="label">{$form.msg_title.label}</td>
            <td>{$form.msg_title.html}
                <br /><span class="description html-adjust">{ts}Descriptive title of message - used for template selection.{/ts}
            </td>
        </tr>
        <tr>
            <td class="label">{$form.msg_subject.label}</td>
            <td>{$form.msg_subject.html}
                <br /><span class="description">{ts}Subject for email message.{/ts} {ts 1=$tokenDocLink 2=$docURLTitle}Tokens may be included (<a href='%1' target='_blank' title='%2'>token documentation</a>).{/ts}
            </td>
        </tr>
        <tr>
            <td colspan="2">{$form.msg_text.label}<br />
                            {$form.msg_text.html|crmReplace:class:huge}<br />
                            <span class="description">{ts}Text formatted message.{/ts} {ts 1=$tokenDocLink 2=$docURLTitle}Tokens may be included (<a href='%1' target='_blank' title='%2'>token documentation</a>).{/ts}
            </td>
        </tr>
        <tr>
            <td colspan="2">{$form.msg_html.label}<br />
                            {$form.msg_html.html}<br />
                            <span class="description">{ts}You may optionally create an HTML formatted version of this message. It will be sent to contacts whose Email Format preference is 'HTML' or 'Both'.{/ts} {ts 1=$tokenDocLink 2=$docURLTitle}Tokens may be included (<a href='%1' target='_blank' title='%2'>token documentation</a>).{/ts}
            </td>
        </tr>
        <tr>
            <td class="label">{$form.is_active.label}</td>
            <td>{$form.is_active.html}</td>
        </tr>
    </table> 
  {/if}
  <dl>   
      <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>
  </dl>
  <br clear="all" />
</fieldset>
</div>
