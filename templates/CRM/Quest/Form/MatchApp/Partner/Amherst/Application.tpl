{* Quest College Match: Partner: Amherst: Applicant Info section *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan="2" id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td width="24%" valign="top" class="grouplabel">How did you learn about <br>
      Amherst? (check as many <br>as apply, list name(s) when possible)</td>
    <td width="76%" class="fieldlabel">
        <table width="100%" class="app">
        {foreach from=$fields key=fld item=dontcare}
            {assign var=cb value="is_"|cat:$fld}
            {assign var=div_id value="id_"|cat:$fld|cat:"_show"}
            <tr><td class="fieldlabel">
                {$form.$cb.html}
                <div id={$div_id}">{$form.$fld.label} {$form.$fld.html}</div>
                <input type="checkbox" name="checkbox" value="checkbox"></td></tr>
                {include file="CRM/common/showHideByFieldValue.tpl"
                    trigger_field_id    =$cb
                    trigger_value       ="1"
                    target_element_id   =$div_id
                    target_element_type ="block"
                    field_type          ="radio"
                    invert              = 0 }
        {/foreach}
        </table>
    </td>
</tr>
</table>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

