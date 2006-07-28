{* Quest College Match: Partner: Wellesley: Applicant Info section *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan="2" id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
<td class="grouplabel">
    {ts}Please indicate no more than two areas of academic interest.*{/ts}
</td>
<td>
    <table>
        <tr>
            <td colspan=2 class="optionlist">
             {$form.undecided.html}{$form.undecided.label}
            </td>
        </tr>
        <tr>
            <td colspan=2 class="optionlist" id="bold-table-header">{$form.departmental_majors.label}</td>
        </tr>
        {assign var="count" value=1}
        {foreach from=$form.departmental_majors item=dnc1 key=id1}
            {if $count lt 10}
                {assign var="count" value=`$count+1`}
            {else}
                {if $id1 is odd}
                <tr>
                {/if}
                <td class="optionlist">{$form.departmental_majors.$id1.html}</td>
                {if $id1 is even}
                </tr>
                {/if}
            {/if}
        {/foreach}
        {if $id1 is odd }
        <td class="optionlist"></td></tr>
        {/if}
        <tr>
            <td colspan=2 class="optionlist" id="bold-table-header">{$form.interdepartmental_major.label}</td>
        </tr>
        {assign var="count" value=1}
        {foreach from=$form.interdepartmental_major item=name key=id2}
            {if $count lt 10}
                {assign var="count" value=`$count+1`}
            {else}
                {if $id2 is odd}
                <tr>
                {/if}
                <td class="optionlist">{$form.interdepartmental_major.$id2.html}</td>
                {if $id2 is even}
                </tr>
                {/if}
            {/if}
        {/foreach}
        {if $id2 is odd }
        <td class="optionlist"></td></tr>
        {/if}
        <tr>
            <td colspan=2 class="optionlist" id="bold-table-header">{$form.preprofessional_interest.label}</td>
        </tr>
        {assign var="count" value=1}
        {foreach from=$form.preprofessional_interest item=name key=id3}
            {if $count lt 10}
                {assign var="count" value=`$count+1`}
            {else}
                {if $id3 is odd}
                <tr>
                {/if}
                <td class="optionlist">{$form.preprofessional_interest.$id3.html}
                {if $id3 eq 3}
                    <div id="preprofessional_interest_other">{$form.preprofessional_interest_other.html}</div>
                {/if}
                </td>
                {if $id3 is even}
                </tr>
                {/if}
            {/if}
        {/foreach}
        {if $id3 is odd }
        <td class="optionlist"></td></tr>
        {/if}
    </table>
</td>
</tr>
</table>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

{include file="CRM/common/showHideByFieldValue.tpl"
    trigger_field_id    ="preprofessional_interest[3]"
    trigger_value       ="1"
    target_element_id   ="preprofessional_interest_other"
    target_element_type =""
    field_type          ="radio"
    invert              = 0
}
