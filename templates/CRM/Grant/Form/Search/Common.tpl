<tr>
    <td>
        {$form.grant_report_received.label}<br />
        {$form.grant_report_received.html}
    </td>
    <td>
        {$form.grant_status_id.label}<br />
        {$form.grant_status_id.html}
    </td>
    <td>
        {$form.grant_type_id.label}<br />
        {$form.grant_type_id.html} 
    </td>
</tr>
<tr>
    <td>
        {$form.grant_amount_low.label}<br />
        {$form.grant_amount_low.html}
    </td> 
    <td colspan="2">
        {$form.grant_amount_high.label}<br />
        {$form.grant_amount_high.html}
    </td>
</tr>
<tr>
    <td>
        {$form.grant_application_received_date_low.label}<br />
        {include file="CRM/common/jcalendar.tpl" elementName=grant_application_received_date_low}
    </td>
    <td colspan="2">
        {$form.grant_application_received_date_high.label}<br />
        {include file="CRM/common/jcalendar.tpl" elementName=grant_application_received_date_high}
        &nbsp;{$form.grant_application_received_notset.html}&nbsp;&nbsp;{ts}Date is not set{/ts}
    </td>          
</tr>
<tr>
    <td>
        {$form.grant_decision_date_low.label}<br />
        {include file="CRM/common/jcalendar.tpl" elementName=grant_decision_date_low}
    </td>
    <td colspan="2">
        {$form.grant_decision_date_high.label}<br /> 
        {include file="CRM/common/jcalendar.tpl" elementName=grant_decision_date_high}
        &nbsp;{$form.grant_decision_date_notset.html}&nbsp;&nbsp;{ts}Date is not set{/ts}
    </td>          
</tr>
<tr>
    <td> 
        {$form.grant_money_transfer_date_low.label}<br /> 
        {include file="CRM/common/jcalendar.tpl" elementName=grant_money_transfer_date_low}
    </td>
    <td colspan="2">                 
        {$form.grant_money_transfer_date_high.label}<br /> 
        {include file="CRM/common/jcalendar.tpl" elementName=grant_money_transfer_date_high}
        &nbsp;{$form.grant_money_transfer_date_notset.html}&nbsp;&nbsp;{ts}Date is not set{/ts}
    </td>          
</tr>
<tr>
    <td>
        {$form.grant_due_date_low.label}<br />
        {include file="CRM/common/jcalendar.tpl" elementName=grant_due_date_low}
    </td>
    <td colspan="2">
        {$form.grant_due_date_high.label}<br />
        {include file="CRM/common/jcalendar.tpl" elementName=grant_due_date_high}
        &nbsp;{$form.grant_due_date_notset.html}&nbsp;&nbsp;{ts}Date is not set{/ts}
    </td>          
</tr>
{if $grantGroupTree}
<tr>
    <td colspan="3">
    {include file="CRM/Custom/Form/Search.tpl" groupTree=$grantGroupTree showHideLinks=false}</td>
</tr>
{/if}
