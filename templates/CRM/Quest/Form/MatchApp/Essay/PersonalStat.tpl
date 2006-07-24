{* Quest College Match: Essay section - Short Answers *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>

{include file="CRM/Quest/Form/MatchApp/Essay.tpl"}

{include file="CRM/common/showHideByFieldValue.tpl"
    trigger_field_id    ="personalStat_quests"
    trigger_value       ="1"
    target_element_id   ="id_upload_photo"
    target_element_type ="block"
    field_type          ="radio"
    invert              = 0
}
