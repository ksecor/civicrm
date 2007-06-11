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

<div id="basicCriteria" class="content-pane">
    {include file="CRM/Contact/Form/Search/Criteria/Basic.tpl"}
</div>

{foreach from=$allPanes key=paneName item=paneValue}
{if $paneValue.open eq 'true'}
  <div id="{$paneValue.id}" href="{$paneValue.url}" dojoType="TitlePane" labelNodeClass="label-pane" label="&raquo; {$paneName}" open="{$paneValue.open}" adjustPaths="false" containerNodeClass="content-pane"></div>
{else}
  <div id="{$paneValue.id}" onDownloadEnd= "this.setLabel('&raquo; {$paneName}'); this.containerNode.style.height = 'auto'" onDownloadStart = "this.setLabel('&raquo; Loading');" onLabelClick="if (this.href != '{$paneValue.url}') this.setUrl('{$paneValue.url}');; " dojoType="TitlePane" labelNodeClass="label-pane" label="&raquo; {$paneName}" open="{$paneValue.open}" adjustPaths="false" containerNodeClass="content-pane"></div>
{/if}
{/foreach}

    <div class="spacer"></div>

    <table class="form-layout">
    <tr>
    <td></td>
    <td class="label">{$form.buttons.html}</td>
    </tr>
    </table>
    {/strip}
 </div>
</fieldset>
