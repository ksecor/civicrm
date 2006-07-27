{* Quest College Match: Partner: Princeton: Short Answers section *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td>
    <table border=0>
    {foreach from=$fields key=name item=dontcare}
    {assign var=title value=$fields}  
        <tr>
        <td class="grouplabel optionlist">{$form.$name.label}</td> 
        <td class="grouplabel optionlist">{$form.$name.html|crmReplace:class:medium}</td>
        </tr>
    {/foreach}
    </table>
    </td>
</tr>
{include file="CRM/Quest/Form/MatchApp/Essay.tpl"}
