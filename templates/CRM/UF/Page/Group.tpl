{if $action eq 1 or $action eq 2 or $action eq 4 or $action eq 8}
    {* Add or edit Profile Group form *}
    {include file="CRM/UF/Form/Group.tpl"}

{elseif $action eq 1024}
    {* Display HTML Code for standalone Profile form *}
    <div id="help">
    {ts}
    <p>The HTML code below will display a form consisting of the selected Profile fields. You can copy this HTML
    code and paste it into any block or page on ANY web site where you want to collect contact information.</p>
    <p>You can control the web page that someone is directed to AFTER completing the form by modifying the
    contents of the hidden <strong>postURL</strong> input field. Replace the default value with any valid complete
    URL prior to saving the form code to the desired page(s).</p>
    <p>EXAMPLE: <strong>&lt;input type="hidden" name="postURL" value="http://www.example.com/thank_you.html"&gt;</strong></p>
    {/ts}
    </div>
    
    <h3>{$rows.1.title} - {ts}HTML Form Code{/ts}</h3>
    <form name="html_code">
    <textarea rows="20" cols="80" name="preview" id="preview">{$preview}</textarea>
    <br />
    <a href="#" onclick="html_code.preview.select(); return false;">Select Code</a>
    <p></p>
    <div class="action-link">
        <a href="{crmURL p='civicrm/admin/uf/group' q="reset=1"}">&raquo;  {ts}Back to Profile Listings{/ts}</a>
    </div>

{else}

    <div id="help">
    {ts}<p>By configuring 'CiviCRM Profile(s)', you can allow end-users to edit and/or view specific fields from their own contact information. Additionally, 'CiviCRM Profile' fields control which data is used to match a contact record to a user. You can also mark 'CiviCRM Profile' fields as viewable by other users and site visitors.</p>
    <p>Each 'CiviCRM Profile' is presented as a separate form when new users register for an account, as well as when they edit an existing account.</p>{/ts}
    </div>

    {if $rows}
    <div id="notes">
    <p></p>
        <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Profile Title{/ts}</th>
            <th>{ts}Status?{/ts}</th>
            <th>{ts}Weight{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
            <td>{$row.title}</td>
            <td>{if $row.is_active eq 1} {ts}Active{/ts} {else} {ts}Inactive{/ts} {/if}</td>
            <td>{$row.weight}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        
        {if NOT ($action eq 1 or $action eq 2) }
        <p></p>
        <div class="action-link">
        <a href="{crmURL p='civicrm/admin/uf/group' q="action=add&reset=1"}">&raquo;  {ts}New CiviCRM Profile{/ts}</a>
        </div>
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
