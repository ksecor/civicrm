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
    <p>{ts}If the form is submitted with errors (i.e. required field not completed...) - the default behavior is to display the errors within the "built-in" profile form. You can override this behavior - specifying your own error page - by adding a hidden <strong>errorURL</strong> input field{/ts} (<a href="http://wiki.civicrm.org/confluence/display/CRM/Configure+CiviCRM+Profile" target="_blank">{ts}more info{/ts}...</a>).</p>
    <p><strong>{ts}Make sure the CAPTCHA feature is NOT enabled for this profile when you are grabbing the HTML code for a stand-alone form. CAPTCHA requires dynamic page generation. Submitting a stand-alone form with CAPTCHA included will always result in a CAPTCHA validation error.{/ts}</strong></p>
    </div>
   
    <h3>{ts}{$title} - Code for Stand-alone HTML Form{/ts}</h3>
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
    <p>{ts}CiviCRM Profile(s) allow you to aggregate groups of fields and include them in your site as input forms, contact display pages, and search and listings features. They provide a powerful set of tools for you to collect information from constituents and selectively share contact information.{/ts}</p>
    <p>{ts}Profiles may be linked to specific modules, accessed via built-in CiviCRM URLs, or used as standalone forms on any web page. Examples include:{/ts}</p>
    <ul class="indented">
    {if $config->userFramework EQ 'Drupal'}
        <li>{ts}<strong>User Screens</strong> - One or several profiles can be linked to either the <strong>new user registration</strong> and/or view and edit screens for <strong>existing user accounts</strong>.{/ts}</li>
    {/if}
    {capture assign=configContribURL}{crmURL p='civicrm/admin/contribute' q='reset=1'}{/capture}
    <li>{ts 1=$configContribURL}<strong>CiviContribute</strong> - When you want to collect information from Contributors via online contribution pages, you can create a profile and link it to to your contribution page as a "custom page element" (<a href="%1">Configure Online Contribution Pages</a>).{/ts}</li>
    {capture assign=siteRoot}&lt;{ts}site root{/ts}&gt;{/capture}
    <li>{ts 1=$siteRoot 2='civicrm/profile?reset=1'}<strong>Contact Search and Listings</strong> - A default profile search form and search result listings is displayed when you link users to the <em>%1/%2</em> path. If you have several profiles which you want to use for different search and listings purposes, simply add the profile ID to the end of your query string using the 'gid' parameter. For example, the link to display a search and listings page for a Profile with ID = 3 would be:{/ts} <em>{$siteRoot}/civicrm/profile?reset=1&amp;gid=3</em></li>
    <li>{ts 1=$siteRoot 2='civicrm/profile/create?reset=1&amp;gid=3'}<strong>Contact Signup Forms (built-in pages)</strong> - Create link(s) to "new contact" input form(s) for your Profiles using the following path: <em>%1/%2</em>. (This example links to an input form for Profile ID 3.){/ts}</li>
    <li>{ts}<strong>Standalone Forms</strong> - If you want more control over form layout, or want to add Profile input forms to non-CiviCRM blocks, pages and/or sites...click the *Standalone Form* action link for a Profile below - and copy and paste the HTML form code into any web page.{/ts}</li>
    </ul>
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
        <tr class="{cycle values="odd-row,even-row"} {$row.class}
        {if NOT $row.is_active}disabled{/if}">
            <td>{$row.title}</td>
            <td>{$row.id}</td>
            <td>{$row.module}</td>
            <td>{if $row.is_active eq 1} {ts}Active{/ts} {else} {ts}Inactive{/ts} {/if}</td>
            <td>{$row.weight}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        
        {if NOT ($action eq 1 or $action eq 2)}
        <p></p>
        <div class="action-link">
        <a href="{crmURL p='civicrm/admin/uf/group' q="action=add&reset=1"}" id="newCiviCRMProfile">&raquo; {ts}New CiviCRM Profile{/ts}</a>
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
