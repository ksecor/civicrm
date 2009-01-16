{if $showBestResult }
    <span class="font-red">{ts}For best results, the Country field should precede the State-Province field in your Profile form. You can use the up and down arrows on field listing page for this profile to change the order of these fields or manually edit weight for Country/State-Province Field.{/ts}</span>
{/if}    

{if $action eq 1 or $action eq 2 or $action eq 4 or $action eq 8 }
    {include file="CRM/UF/Form/Field.tpl"}
{elseif $action eq 1024 }
    {include file="CRM/UF/Form/Preview.tpl"}
{else}
    {if $ufField}
        <div id="field_page">
         <p></p>
        {strip}
        <table class="selector">
            <tr class="columnheader">
                <th>{ts}CiviCRM Field Name{/ts}</th>
                <th>{ts}Visibility{/ts}</th>
                <th>{ts}Searchable?{/ts}</th>
                <th>{ts}In Selector?{/ts}</th>
                <th>{ts}Order{/ts}</th>
                <th>{ts}Active{/ts}</th>	
                <th>{ts}Required{/ts}</th>	
                <th>{ts}View Only{/ts}</th>	
                <th></th>
            </tr>
            {foreach from=$ufField item=row}
            <tr class="{cycle values="odd-row,even-row"} {$row.class}
                {if NOT $row.is_active}disabled{/if}">
                <td>{$row.label}<br/>({$row.field_type})</td>
                <td>{$row.visibility_display}</td>
                <td>{if $row.is_searchable   eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
                <td>{if $row.in_selector     eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
                <td class="nowrap">{$row.weight}</td>
                <td>{if $row.is_active       eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
                <td>{if $row.is_required     eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
                <td>{if $row.is_view         eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
                <td>{$row.action}</td>
            </tr>
            {/foreach}
        </table>
        {/strip}
            
        {if not ($action eq 2 or $action eq 1)}
            <table class="form-layout-compressed">
            <tr>
                <td><a href="{crmURL p="civicrm/admin/uf/group/field" q="reset=1&action=add&gid=$gid"}" class="button"><span>&raquo; {ts}New CiviCRM Profile Field{/ts}</span></a></td>
                <td><a href="{crmURL p="civicrm/admin/uf/group" q="action=update&id=`$gid`&reset=1"}" class="button"><span>&raquo; {ts}Edit Profile Settings{/ts}</span></a></td>
                <td style="vertical-align: top"><a href="{crmURL p="civicrm/admin/uf/group" q="action=preview&id=`$gid`&reset=1&field=0"}">&raquo; {ts}Preview this profile (all fields){/ts}</a></td>
            </tr>
            </table>
        {/if}
        </div>
    {else}
        {if $action eq 16}
        {capture assign=crmURL}{crmURL p="civicrm/admin/uf/group/field" q="reset=1&action=add&gid=$gid"}{/capture}
        <div class="messages status">
        <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{ts 1=$groupTitle 2=$crmURL}There are no CiviCRM Profile Fields for '%1', you can <a href='%2'>add one now</a>.{/ts}</dd>
        </dl>
        </div>
        {/if}
    {/if}
{/if}
