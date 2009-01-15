
{if $action eq 1 or $action eq 2 or $action eq 4 or $action eq 8 or $action eq 64 or $action eq 16384}
    {* Add or edit Profile Group form *}
    {include file="CRM/UF/Form/Group.tpl"}
{elseif $action eq 1024}
    {* Preview Profile Group form *}	
    {include file="CRM/UF/Form/Preview.tpl"}
{elseif $action eq 8192}
    {* Display HTML Code for standalone Profile form *}
    <div id="help">
    <p>{ts}The HTML code below will display a form consisting of the active CiviCRM Profile fields. You can copy this HTML code and paste it into any block or page on ANY website where you want to collect contact information.{/ts} {help id='standalone'}</p>
    </div>
   
    <h3>{$title} - {ts}Code for Stand-alone HTML Form{/ts}</h3>
    <form name="html_code" action="{crmURL p="civicrm/admin/uf/group" q="action=profile&gid=$gid"}">
    <div id="standalone-form">
        <textarea rows="20" cols="80" name="profile" id="profile">{$profile}</textarea>
        <div class="spacer"></div>    
        <a href="#" onclick="html_code.profile.select(); return false;">Select Code</a> 
    </div>
    <div class="action-link">
        <a href="{crmURL p='civicrm/admin/uf/group' q="reset=1"}">&raquo;  {ts}Back to Profile Listings{/ts}</a>
    </div>
    </form>

{else}
    <div id="help">
        {ts}CiviCRM Profile(s) allow you to aggregate groups of fields and include them in your site as input forms, contact display pages, and search and listings features. They provide a powerful set of tools for you to collect information from constituents and selectively share contact information.{/ts} {help id='profile_overview'}
    </div>

    {if $rows}
    <div id="uf_profile">
    <p></p>
        <div class="form-item">
        {strip}
      <table class="selector">
         <tr class="columnheader">
            <th>{ts}Profile Title{/ts}</th>
            <th>{ts}Type{/ts}</th>
            <th>{ts}ID{/ts}</th>
            <th>{ts}Used For{/ts}</th>
            <th>{ts}Status?{/ts}</th>
            <th></th>
         </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}
        {if NOT $row.is_active}disabled{/if}">
            <td>{$row.title}</td>
            <td>{$row.group_type}</td>
            <td>{$row.id}</td>
            <td>{$row.module}</td>
            <td>{if $row.is_active eq 1} {ts}Active{/ts} {else} {ts}Inactive{/ts} {/if}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        
        {if NOT ($action eq 1 or $action eq 2)}
        <p></p>
        <div class="action-link">
        <a href="{crmURL p='civicrm/admin/uf/group' q="action=add&reset=1"}" id="newCiviCRMProfile" class="button"><span>&raquo; {ts}New CiviCRM Profile{/ts}</span></a>
        </div>
        {/if}
         {/strip}
        </div>
    </div>
    {else}
    {if $action ne 1} {* When we are adding an item, we should not display this message *}
       <div class="messages status">
       <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/> &nbsp;
         {capture assign=crmURL}{crmURL p='civicrm/admin/uf/group' q='action=add&reset=1'}{/capture}{ts 1=$crmURL}No CiviCRM Profiles have been created yet. You can <a href='%1'>add one now</a>.{/ts}
       </div>
    {/if}
    {/if}
{/if}
