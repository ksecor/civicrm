{if $action eq 1 or $action eq 2 or $action eq 4 or $action eq 8}
    {* Add or edit Profile Group form *}
    {include file="CRM/UF/Form/Group.tpl"}
{elseif $action eq 1024}
    {* Preview Profile Group form *}	
    {include file="CRM/UF/Form/Preview.tpl"}
{elseif $action eq 8192}
    {* Display HTML Code for standalone Profile form *}
    <div id="help">
    <p>{ts}The HTML code below will display a form consisting of the active CiviCRM Profile fields. You can copy this HTML code and paste it into any block or page on ANY website where you want to collect contact information.{/ts}</p>
    <p>{ts}You can control the web page that someone is directed to AFTER completing the form by modifying the contents of the hidden <strong>postURL</strong> input field. Replace the default value with any valid complete URL prior to saving the form code to the desired page(s).{/ts}</p>
    <p>{ts}EXAMPLE:{/ts} <strong>&lt;input type="hidden" name="postURL" value="http://www.example.com/thank_you.html"&gt;</strong></p>
    </div>
    
    <h3>{ts}{$title} - Code for Stand-alone HTML Form{/ts}</h3>
    <form name="html_code">
    <textarea rows="20" cols="80" name="profile" id="profile">{$profile}</textarea>
    <br />
    <a href="#" onclick="html_code.profile.select(); return false;">Select Code</a>
    <p></p>
    <div class="action-link">
        <a href="{crmURL p='civicrm/admin/uf/group' q="reset=1"}">&raquo;  {ts}Back to Profile Listings{/ts}</a>
    </div>

{else}

    <div id="help">
    <p>{ts}CiviCRM Profile(s) allow you to aggregate groups of fields and include them in your site as input forms, contact display pages, and search and listings features.
    They provide a powerful set of tools for you to collect information from constituents and selectively share contact information.</p>
    <p>Profiles may be linked to specific modules, or used to create standalone forms and listing pages. Examples of module links include:
    <ul class="indented">
    <li><strong>User</strong> - One or several profiles can be linked to either the <strong>new user registration</strong> and/or view and edit screens for <strong>existing user accounts</strong>.
    <li><strong>CiviContribute</strong> - When you want to collect information from Contributors via online contribution pages, you can create a profile and link to to your contribution page.
    <li><strong>Profile Listings</strong> - A default profile search form and search result listings is displayed when you link users to the <site root>/civicrm/profile?reset=1 path. If you have several
    profiles which you want to use for different search and listings purposes, simply add the profile ID to the end of your query string using the 'gid' parameter. For example, the link to display a search and
    listings page for a Profile with ID = 3 would be:<br /><strong>&lt;your site root URL&gt;/civicrm/profile?reset=1&gid=3</strong>
    </ul></p>{/ts}
    {* Multi-profile standalone forms not supported for 1.3. dgg *}
    {* <p>{ts 1=$crmURL}Use the <strong>Stand-alone Form</strong> links to get the HTML code needed to add a profile form to any block or page on any website (e.g. for a signup form). You can also get the <a href="%1">HTML for
    ALL Active Profiles</a> as a single form.{/ts}</p> *}
    </div>

    {if $rows}
    <div id="notes">
    <p></p>
        <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Profile Title{/ts}</th>
            <th>{ts}ID{/ts}</th>
            <th>{ts}Used For{/ts}</th>
            <th>{ts}Status?{/ts}</th>
            <th>{ts}Weight{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>{$row.title}</td>
            <td>{$row.id}</td>
            <td>{$row.module}</td>
            <td>{if $row.is_active eq 1} {ts}Active{/ts} {else} {ts}Inactive{/ts} {/if}</td>
            <td>{$row.weight}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        
        {if NOT ($action eq 1 or $action eq 2) }
        <p></p>
        <div class="action-link">
        <a href="{crmURL p='civicrm/admin/uf/group' q="action=add&reset=1"}">&raquo; {ts}New CiviCRM Profile{/ts}</a>
        </div>
        {* <div class="action-link">
            <a href="{crmURL p='civicrm/admin/uf/group' q="reset=1&action=profile"}">&raquo;  {ts}Get HTML for All Active Profiles{/ts}</a>
        </div> *}
        {/if}

        {/strip}
        </div>
    </div>
    {else}
       {if $action ne 1} {* When we are adding an item, we should not display this message *}
       <div class="messages status">
       <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/> &nbsp;
         {capture assign=crmURL}{crmURL p='civicrm/admin/uf/group' q='action=add&reset=1'}{/capture}{ts 1=$crmURL}No CiviCRM Profiles have been created yet. You can <a href="%1">add one now</a>.{/ts}
       </div>
       {/if}
    {/if}
{/if}
