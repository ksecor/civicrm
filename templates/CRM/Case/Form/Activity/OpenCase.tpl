{if $context ne 'caseActivity'}
    <tr><td class="label">{$form.start_date.label}</td><td>{$form.start_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_case_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=start_date offset=10 trigger=trigger_case_1}       
        </td>
    </tr>
    <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td></tr>
    <tr><td class="label">{$form.case_type_id.label}</td><td>{$form.case_type_id.html}</td></tr>
{/if}