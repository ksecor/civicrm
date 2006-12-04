{* Quest College Match: Partner: Columbia: Interests section *}
{include file="CRM/Quest/Form/CPS/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan="2" id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="grouplabel" colspan="2">Please answer the following questions.</td>	
</tr>
{foreach from=$essays item=essay}
{assign var=name value=$essay.name}
<tr>
    <td class="grouplabel">
        {$form.essay.$name.label}<br />
        {$form.essay.$name.html} &nbsp;<br /><br />
        {$form.word_count.$name.label} &nbsp;&nbsp;{$form.word_count.$name.html}
    </td> 
</tr>
{/foreach}

</table>
{include file="CRM/Quest/Form/CPS/AppContainer.tpl" context="end"}

