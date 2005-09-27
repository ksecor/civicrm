{* Displays Administer CiviCRM Control Panel *}
    {foreach from=$adminPanel key=groupName item=group}
        <fieldset><legend>{$groupName}</legend>
            <table class="control-panel">
            {assign var=i value=1}
            {foreach from=$group item=panelItem}
                {if $i eq 1 OR $i eq 4}
                    <tr>
                {/if}
                <td>
                    <a href="{crmURL p=$panelItem.path}"><img src="{$config->resourceBase}i/{$panelItem.icon}" alt="{$panelItem.title}"/></a><br >
                    <a href="{crmURL p=$panelItem.path}">{$panelItem.title|replace:" ":"<br />"}</a>
                </td>
                {if $i eq 3 OR $i eq 6}
                    </tr>
                {/if}
                {assign var="i" value="`$i+1`"}
            {/foreach}
            {if $i eq 2 or $i eq 4}<td>&nbsp;</td>&nbsp;<td></td></tr>{/if}
            {if $i eq 3 or $i eq 5}<td>&nbsp;</td></tr>{/if}
            </table>
        </fieldset>
    {/foreach}

