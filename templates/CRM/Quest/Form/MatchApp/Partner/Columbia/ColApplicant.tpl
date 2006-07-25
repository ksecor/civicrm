{* Quest College Match: Partner: Columbia: Applicant Info section *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan="2" id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td class="grouplabel" width="30%">
        {$form.columbia_career.label}</td>
    <td>
        <table class="app">
        {assign var="countEI" value=0}
        {foreach from=$form.columbia_career item=type key=key}
            {assign var="countEI" value=`$countEI+1`}
            {if $countEI gt 9 }
                {if $countEI is not odd}
                <tr>
                {/if}
                <td class="optionlist">{$form.columbia_career.$key.html}</td>
                {if $countEI is not even}
                </tr>
                {/if}
            {/if}
        {/foreach}
        {if ($countEI gt 9) and ($countEI is not odd) }
        <td class="optionlist"></td></tr>
        {/if}
        <tr id="career_other">
        <td colspan=2 class="optionlist">{$form.career_other.html|crmReplace:class:large}</td>
        </tr>
        </table>
    </td>
</tr>
<tr>
    <td class="grouplabel"> {$form.is_reside_campus.label}</td>
    <td class="fieldlabel"> {$form.is_reside_campus.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.essay.representative.label}</td>
    <td class="fieldlabel"> 
        {$form.essay.representative.html} &nbsp;<br /><br />
        {$form.word_count.representative.label} &nbsp;&nbsp;{$form.word_count.representative.html}
    </td> 
</tr>
<tr>
    <td class="grouplabel"> {$form.is_parent_fulltime.label}</td>
    <td class="fieldlabel"> {$form.is_parent_fulltime.html}</td>
</tr>
<tr>
    <td class="grouplabel"> {$form.is_financial_aid.label}</td>
    <td class="fieldlabel"> {$form.is_financial_aid.html}</td>
</tr>
<tr>
    <td class="grouplabel"> {$form.is_visited_campus.label}</td>
    <td class="fieldlabel"> {$form.is_visited_campus.html}</td>
</tr>
<tr>
    <td class="grouplabel" width="30%">
        {$form.columbia_interest.label}</td>
    <td>
        <table class="app">
        {assign var="countEI" value=0}
        {foreach from=$form.columbia_interest item=type key=key}
            {assign var="countEI" value=`$countEI+1`}
            {if $countEI gt 9 }
                {if $countEI is not odd}
                <tr>
                {/if}
                <td class="optionlist">{$form.columbia_interest.$key.html}</td>
                {if $countEI is not even}
                </tr>
                {/if}
            {/if}
        {/foreach}
        {if ($countEI gt 9) and ($countEI is not odd) }
        <td class="optionlist"></td></tr>
        {/if}
        <tr id="interest_other">
        <td colspan=2 class="optionlist">{$form.interest_other.html|crmReplace:class:large}</td>
        </tr>
        </table>
    </td>
</tr>

</table>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

{include file="CRM/common/showHideByFieldValue.tpl"
    trigger_field_id    ="columbia_interest[14]"
    trigger_value       ="1"
    target_element_id   ="interest_other"
    target_element_type =""
    field_type          ="radio"
    invert              = 0
}

