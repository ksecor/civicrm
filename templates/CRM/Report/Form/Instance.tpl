<table class="report-layout">
    <tr>
	<th>{ts}General Settings{/ts}</th>
    </tr>
</table>
<table class="report-layout">
    <tr>
        <td class="report-label" width="20%">{$form.title.label} {help id="id-report_title" file="CRM/Report/Form/Settings.hlp"}</td>
        <td >{$form.title.html}</td>
    </tr>
    <tr>
        <td class="report-label" width="20%">{$form.description.label}</td>
        <td>{$form.description.html}</td>
    </tr>
    <tr>
        <td class="report-label" width="20%">{$form.report_header.label}<br /> {help id="id-report_header" file="CRM/Report/Form/Settings.hlp"}</td>
        <td>{$form.report_header.html}</td>
    </tr>
    <tr>
        <td class="report-label" width="20%">{$form.report_footer.label}</td>
        <td>{$form.report_footer.html}</td>
    </tr>
</table>
<br/>

<table class="report-layout">
    <tr>
	<th>{ts}Email Delivery Settings{/ts} {help id="id-email_settings" file="CRM/Report/Form/Settings.hlp"}</th>
    </tr>
</table>
<table class="report-layout">
    <tr>
        <td class="report-label" width="20%">{$form.email_subject.label}</td>
        <td>{$form.email_subject.html|crmReplace:class:huge}</td>
    </tr>
    <tr>
        <td class="report-label">{$form.email_to.label}</td>
        <td>{$form.email_to.html|crmReplace:class:huge}</td>
    </tr>
    <tr>
        <td class="report-label">{$form.email_cc.label}</td>
        <td>{$form.email_cc.html|crmReplace:class:huge}</td>
    </tr> 
</table>
<br/>

<table class="report-layout">
    <tr>
	<th>{ts}Other Settings{/ts}</th>
    </tr>
</table>
<table class="report-layout">
    <tr>
        <td class="report-label" width="20%">{$form.permission.label} {help id="id-report_perms" file="CRM/Report/Form/Settings.hlp"}</td>
        <td>{$form.permission.html|crmReplace:class:huge}</td>
    </tr> 
</table>