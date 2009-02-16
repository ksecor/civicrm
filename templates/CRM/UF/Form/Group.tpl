{* add/update/view CiviCRM Profile *}       

<div class="form-item">  
 <fieldset>
  {if $action eq 8 or $action eq 64}
    {if $action eq 8}
      <legend>{ts}Delete CiviCRM Profile{/ts}</legend>
    {else}
      <legend>{ts}Disable CiviCRM Profile{/ts}</legend>
    {/if}
    <div class="messages status">
    <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>    
    {$message}
    </dd>
    </dl>
    </div>
   {else}
    <legend>{ts}CiviCRM Profile{/ts}</legend>
    <dl>
    <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    <dt>{$form.uf_group_type.label} {help id='id-used_for' file="CRM/UF/Form/Group.hlp"}</dt><dd>{$form.uf_group_type.html}&nbsp;{$otherModuleString}</dd>
    <dt>{$form.weight.label}{if $config->userFramework EQ 'Drupal'} {help id='id-profile_weight' file="CRM/UF/Form/Group.hlp"}{/if}</dt><dd>{$form.weight.html}</dd>
    <dt>{$form.help_pre.label} {help id='id-help_pre' file="CRM/UF/Form/Group.hlp"}</dt><dd>{$form.help_pre.html}</dd>
    <dt>{$form.help_post.label} {help id='id-help_post' file="CRM/UF/Form/Group.hlp"}</dt><dd>{$form.help_post.html}</dd>
    </dl>
    <dl>	
    <dt></dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
    </dl>
    
  
    <div class="tundra">
      {foreach from=$allPanes key=paneName item=paneValue}
        {if $paneValue.open eq 'true'}
           <div id="{$paneValue.id}" href="{$paneValue.url}" dojoType="civicrm.TitlePane"  title="{$paneName}" open="{$paneValue.open}" width="200" executeScript="true"></div>
        {else}
           <div id="{$paneValue.id}" dojoType="civicrm.TitlePane"  title="{$paneName}" open="{$paneValue.open}" href ="{$paneValue.url}" executeScript="true"></div>
        {/if}
      {/foreach}
   </div>

    {/if}
    {if $action ne 4}
      <dl>  <dt></dt>
        <dd>
        <div id="crm-submit-buttons">{$form.buttons.html}</div>
        </dd>
	<dt></dt> <dd></dd></dl>
    {else}
        <div id="crm-done-button">
        <dt></dt><dd>{$form.done.html}</dd>
        </div>
    {/if} {* $action ne view *}
  		
    </fieldset>
</div>
  
{if $action eq 2 or $action eq 4 } {* Update or View*}
    <p></p>
    <div class="action-link">
    <a href="{crmURL p='civicrm/admin/uf/group/field' q="action=browse&reset=1&gid=$gid"}" class="button"><span>&raquo; {ts}View or Edit Fields for this Profile{/ts}</a></span>
    </div>
{/if}

{include file="CRM/common/showHide.tpl"}
