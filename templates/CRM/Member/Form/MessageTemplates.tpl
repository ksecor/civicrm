{* this template is used for adding/editing/deleting Message Templates *}
<div class="form-item" id=membership_status>
<fieldset><legend>{if $action eq 1}{ts}New Message Template{/ts}{elseif $action eq 2}{ts}Edit Message Template{/ts}{else}{ts}Delete Message Template{/ts}{/if}</legend>
  
   {if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>    
          {ts}Do you want to delete this message template ?{/ts}
          </dd>
       </dl>
      </div>
   {else}
        <dl>
        <dt>{$form.msg_title.label}</dt><dd class="html-adjust">{$form.msg_title.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}{/ts}</dd>
        <dt>{$form.msg_subject.label}</dt><dd class="html-adjust">{$form.msg_subject.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}{/ts}</dd>
        <dt>{$form.msg_text.label}</dt><dd class="html-adjust">{$form.msg_text.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}{/ts}</dd>
        <dt>{$form.msg_html.label}</dt><dd class="html-adjust">{$form.msg_html.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}{/ts}</dd>
    
        <dt>{$form.is_active.label}</dt><dd class="html-adjust">{$form.is_active.html}</dd>
        <dt>&nbsp;</dt><dd class="description html-adjust">{ts}Is this template enabled.{/ts}</dd>
        </dl> 
  {/if}
  <dl>   
      <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>
  </dl>
  <br clear="all" />
</fieldset>
</div>
