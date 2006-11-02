{* Advanced Search Criteria Fieldset *}
<fieldset>
    <legend><span id="searchForm_hide"><a href="#" onclick="hide('searchForm','searchForm_hide'); show('searchForm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a></span>
        {if $context EQ 'smog'}{ts}Find Members within this Group{/ts}
        {elseif $context EQ 'amtg'}{ts}Find Contacts to Add to this Group{/ts}
        {elseif $savedSearch}{ts 1=$savedSearch.name}%1 Smart Group Criteria{/ts}
        {else}{ts}Search Criteria{/ts}{/if}
    </legend>
    <div class="form-item">
    {strip}

<div dojoType="TitlePane" label="Basic Criteria" labelNodeClass="label" containerNodeClass="content">
{include file="CRM/Contact/Form/Search/Criteria/Basic.tpl"}
</div>

{foreach from=$allPanes key=paneName item=paneValue}
  <div id="{$paneName}" dojoType="TitlePane" href="{$paneValue.url}" label="{$paneName}" open="{$paneValue.open}" style="display: none" adjustPaths="false"></div>
{/foreach}

</div>
    <table class="form-layout">
    <tr>
    <td></td>
    <td class="label">{$form.buttons.html}</td>
    </tr>
    </table>
    {/strip}
    </div>
</fieldset>
