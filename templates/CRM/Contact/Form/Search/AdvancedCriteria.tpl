{* Advanced Search Criteria Fieldset *}
{literal}
<style>
div#searchForm {
	height: 100%;
	width: 100%;
	overflow: hidden;
}

#main {
	height: 95%;
	width: 95%;
	left: 1%;
	top: 1%;
	position: relative;
}
.label-pane {
	border: 1px solid black;
	background: #232323;
	color: #fff;
	font-weight: bold;
}

.label-pane :hover {
	cursor: pointer;
}

.content-pane {
	background: #ededed;
	overflow: auto;
}
{/literal}
</style>
<fieldset>
    <legend><span id="searchForm_hide"><a href="#" onclick="hide('searchForm','searchForm_hide'); show('searchForm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a></span>
        {if $context EQ 'smog'}{ts}Find Members within this Group{/ts}
        {elseif $context EQ 'amtg'}{ts}Find Contacts to Add to this Group{/ts}
        {elseif $savedSearch}{ts 1=$savedSearch.name}%1 Smart Group Criteria{/ts}
        {else}{ts}Search Criteria{/ts}{/if}
    </legend>
 <div class="form-item">
    {strip}

<div dojoType="TitlePane" label="&raquo; Basic Criteria" labelNodeClass="label-pane" open="true" containerNodeClass="content-pane">
    {include file="CRM/Contact/Form/Search/Criteria/Basic.tpl"}
</div>

{foreach from=$allPanes key=paneName item=paneValue}
  <div id="{$paneValue.id}" dojoType="TitlePane" labelNodeClass="label-pane" href="{$paneValue.url}" label="&raquo; {$paneName}" open="{$paneValue.open}" adjustPaths="false" containerNodeClass="content-pane"></div>
{/foreach}

    <table class="form-layout">
    <tr>
    <td></td>
    <td class="label">{$form.buttons.html}</td>
    </tr>
    </table>
    {/strip}
 </div>
</fieldset>
