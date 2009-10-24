{if $context ne 'caseActivity'}
    <tr><td class="label">{$form.case_type_id.label}<br />{help id="id-case_type" file="CRM/Case/Form/Case.hlp"}</td><td>{$form.case_type_id.html}</td></tr>
    <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td></tr>
    <tr>
        <td class="label">{$form.start_date.label}</td>
        <td>
            {include file="CRM/common/jcalendar.tpl" elementName=start_date}       
        </td>
    </tr>
{/if}