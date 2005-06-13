{if $action eq 1 or $action eq 2 or $action eq 4}
    {include file="CRM/UF/Form/Field.tpl"}
{else}
    {if $ufField}
    <div id="field_page">
     <p>
        <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}CiviCRM Field Name{/ts}</th>
            <th>{ts}Visibility{/ts}</th>
            <th>{ts}Listings Title{/ts}</th>	
            <th>{ts}Required{/ts}</th>	
            <th>{ts}Active{/ts}</th>	
            <th>{ts}View Only{/ts}</th>	
            <th>{ts}Registration{/ts}</th>	
            <th>{ts}Match{/ts}</th>	
            <th>&nbsp;</th>
        </tr>
        {foreach from=$ufField item=row}
        <tr class="{cycle values="odd-row,even-row"} {if NOT $row.is_active} disabled{/if}">
            <td>{$row.field_name}</td>
            <td>{$row.visibility}</td>
            <td>{$row.listings_title}</td>
            <td>{if $row.is_active       eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{if $row.is_required     eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{if $row.is_view         eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{if $row.is_registration eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{if $row.is_match        eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}
        
        {if $action eq 16 or $action eq 4}
            <div class="action-link">
            <a href="{crmURL q="reset=1&action=add&gid=$gid"}">&raquo; {ts}New User Framework Field{/ts}</a>
            </div>
        {/if}
        </div>
     </p>
    </div>

    {else}
        {if $action eq 16}
        <div class="message status">
        <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
        <dd>{ts 1=$groupTitle}There are no user framework fields for the group "%1",{/ts} <a href="{crmURL p='civicrm/admin/uf/group/field q="reset=1&action=add&gid=$gid"}">{ts}add one{/ts}</a>.</dd>
        </dl>
        </div>
        {/if}
    {/if}
{/if}
