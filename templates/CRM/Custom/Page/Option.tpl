{if $action eq 1 or $action eq 2 or $action eq 4 or $action eq 8}
    {include file="CRM/Custom/Form/Option.tpl"}
{/if}

{if $customOption}
    <div id="field_page">
     <p></p>
        <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
        <th>{ts}Option Label{/ts}</th>
        <th>{ts}Option Value{/ts}</th>
	    <th>{ts}Default{/ts}</th>
        <th>{ts}Weight{/ts}</th>
	    <th>{ts}Status?{/ts}</th>
            <th>&nbsp;</th>
        </tr>
        {foreach from=$customOption item=row}
        <tr class="{cycle values="odd-row,even-row"} {if NOT $row.is_active} disabled{/if}">
            <td>{$row.label}</td>
            <td>{$row.value}</td>
            <td>{$row.default_value}</td>
            <td>{$row.weight}</td>
            <td>{if $row.is_active eq 1} {ts}Active{/ts} {else} {ts}Inactive{/ts} {/if}</td>
            <td>{$row.action}</td>
        </tr>
        {/foreach}
        </table>
        {/strip}
        
        <div class="action-link">
            <a href="{crmURL q="reset=1&action=add&fid=$fid"}">&raquo; {ts 1=$fieldTitle}New Option for "%1"{/ts}</a>
        </div>

        </div>
     </div>

{else}
    {if $action eq 16}
        <div class="messages status">
        <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{capture assign=crmURL}{crmURL p='civicrm/admin/custom/group/field/option' q="action=add&fid=$fid"}{/capture}{ts 1=$fieldTitle 2=$crmURL}There are no multiple choice options for the custom field "%1", <a href="%2">add one</a>.{/ts}</dd>
        </dl>
        </div>
    {/if}
{/if}
