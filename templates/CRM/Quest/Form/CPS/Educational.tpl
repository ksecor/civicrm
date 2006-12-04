{* Quest Match-application: Educational Interests  section *}

{include file="CRM/Quest/Form/CPS/AppContainer.tpl" context="begin"}
{strip}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="grouplabel" width="30%">
        {$form.educational_interest.label}</td>
    <td>
        <table>
        {assign var="countEI" value=0}
        {foreach from=$form.educational_interest item=type key=key}
            {assign var="countEI" value=`$countEI+1`}
            {if $countEI gt 9 }
                {if $countEI is not odd}
                <tr>
                {/if}
                <td class="optionlist">{$form.educational_interest.$key.html}</td>
                {if $countEI is not even}
                </tr>
                {/if}
            {/if}
        {/foreach}
        {if ($countEI gt 9) and ($countEI is not odd) }
        <td class="optionlist"></td></tr>
        {/if}
        <tr id="educational_interest_other">
        <td colspan=2 class="optionlist">{$form.educational_interest_other.html|crmReplace:class:large}</td>
        </tr>
        </table>
    </td>
</tr>
<tr>
    <td class="grouplabel" width="30%">
        {$form.college_type.label}</td>
    <td>
        <table>
        {assign var="countCT" value=0}
        {foreach from=$form.college_type item=type key=key}
            {assign var="countCT" value=`$countCT+1`}
            {if $countCT gt 9 }
                {if $countCT is not odd}
                <tr>
                {/if}
                <td class="optionlist">{$form.college_type.$key.html}</td>
                {if $countCT is not even}
                </tr>
                {/if}
            {/if}
        {/foreach}
        {if ($countCT gt 9) and ($countCT is not odd) }
        <td class="optionlist"></td></tr>
        {/if}
        </table>
    </td>
</tr>


</table>
{/strip}
{include file="CRM/Quest/Form/CPS/AppContainer.tpl" context="end"}

{include file="CRM/common/showHideByFieldValue.tpl"
    trigger_field_id    ="educational_interest[245]"
    trigger_value       ="1"
    target_element_id   ="educational_interest_other"
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}
