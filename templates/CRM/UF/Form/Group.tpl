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
    <dt>{$form.uf_group_type.label}</dt><dd>{$form.uf_group_type.html}&nbsp;{$otherModuleString}</dd>
    <dt>&nbsp;</dt><dd class="description">
    <table class="form-layout-compressed">
    <tr><td>{ts}Profiles can be explicitly linked to a module page.{/ts}
    <ul class="left-alignment">
   {if $config->userFramework EQ 'Drupal'}
    <li>{ts}Check <strong>User Registration</strong> if you want this Profile to be included in the New Account registration form.{/ts}</li>
    <li>{ts}Check <strong>View/Edit User Account</strong> to include it in the view and edit screens for existing user accounts.{/ts}</li>
   {/if}
    <li>{ts}Check <strong>Profile</strong> if you want it to use it for customized listings and view screens for the civicrm/profile path.{/ts}</li>
    <li>{ts}Check <strong>Search Results</strong> to use this profile to display an alternate set of results columns for CiviCRM Basic and Advanced Search.{/ts}</li>
    </ul>
    </td></tr></table></dd>
    <dt>{$form.weight.label}</dt><dd>{$form.weight.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Weight controls the order in which profiles are presented when there are more than one. Enter a positive or negative integer - lower numbers are displayed ahead of higher numbers.{/ts}</dd>
   <dt>{$form.help_pre.label}</dt><dd>{$form.help_pre.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Explanatory text displayed at the beginning of the form.{/ts}</dd>
    <dt>{$form.help_post.label}</dt><dd>{$form.help_post.html}</dd>
 
   
    <dt>&nbsp;</dt><dd class="description">{ts}Explanatory text displayed at the end of the form.{/ts}</dd>
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
