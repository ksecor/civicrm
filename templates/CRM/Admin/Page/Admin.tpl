{* Displays Administer CiviCRM Control Panel *}
{* Set cells per row value for control panel icons *}
{assign var=itemsPerRow value=4}

{if $newVersion}
    <div class="message status">
        <p>{ts 1=$newVersion 2=$localVersion}A new version of CiviCRM is avaliable: %1 (this site is currently running %2).{/ts}</p>
        <p>{ts 1='http://openngo.org/' 2='http://downloads.openngo.org/civicrm/'}Read about the new version on the <a href="%1">OpenNGO pages</a> and <a href="%2">download it</a>.{/ts}</p>
    </div>
{/if}

{foreach from=$adminPanel key=groupName item=group}
    <fieldset><legend>{$groupName}</legend>
        <table class="control-panel">
        {assign var=i value=1}
        {foreach from=$group item=panelItem  name=groupLoop}
            {if $i eq 1 OR ($i % $itemsPerRow eq 1)}
                <tr>
            {/if}
            <td>
                <a href="{$panelItem.url}"{if $panelItem.extra} {$panelItem.extra}{/if}><img src="{$config->resourceBase}i/{$panelItem.icon}" alt="{$panelItem.title}"/></a><br >
                <a href="{$panelItem.url}"{if $panelItem.extra} {$panelItem.extra}{/if}>{$panelItem.title|replace:" ":"<br />"}</a>
            </td>
            {if $i % $itemsPerRow eq 0}
                </tr>
            {/if}
            {if $smarty.foreach.groupLoop.last eq false}
                {assign var="i" value="`$i+1`"}
            {/if}
        {/foreach}
        
        {* See if we need to fill out and close last row in group. NOTE: do not separate operands from operator with spaces else modulus op will not work.*}
        {assign var="j" value="`$i%$itemsPerRow`"}
        {if $j gt 0}
            {section name=moreCells start=0 loop=$itemsPerRow-$j}
                <td>&nbsp;</td>
            {/section}
            </tr>
        {/if}
        </table>
    </fieldset>
{/foreach}

