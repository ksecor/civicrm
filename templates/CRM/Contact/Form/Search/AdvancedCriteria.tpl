{* Advanced Search Criteria Fieldset *}
<fieldset>
    <legend><span id="searchForm_hide"><a href="#" onclick="hide('searchForm','searchForm_hide'); show('searchForm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a></span>
        {if $context EQ 'smog'}{ts}Find Members within this Group{/ts}
        {elseif $context EQ 'amtg'}{ts}Find Contacts to Add to this Group{/ts}
        {elseif $savedSearch}{ts 1=$savedSearch.name}%1 Smart Group Criteria{/ts} &nbsp; {help id='id-advanced-smart'}
        {else}{ts}Search Criteria{/ts} &nbsp; {help id='id-advanced-intro'}{/if}
    </legend>
 <div class="form-item">
    {strip}

<div id="basicCriteria" class="content-pane">
    {include file="CRM/Contact/Form/Search/Criteria/Basic.tpl"}
</div>
<div class="tundra">
{foreach from=$allPanes key=paneName item=paneValue}
{if $paneValue.open eq 'true'}
  <div id="{$paneValue.id}" href="{$paneValue.url}" dojoType="civicrm.TitlePane"  title="{$paneName}" open="{$paneValue.open}" width="200" executeScript="true"></div>
{else}
  <div id="{$paneValue.id}" dojoType="civicrm.TitlePane"  title="{$paneName}" open="{$paneValue.open}" href ="{$paneValue.url}" executeScript="true"></div>
{/if}
{/foreach}
</div>
    <div class="spacer"></div>

    <table class="form-layout">
    <tr>
    <td class="label">{$form.buttons.html}</td>
    </tr>
    </table>
    {/strip}
 </div>
</fieldset>
