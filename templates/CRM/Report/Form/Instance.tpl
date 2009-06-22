<table class="report-layout">
    <tr>
	<th>Display Settings</th>
    </tr>
</table>
<table class="report-layout">
    <tr>
        <td class="report-label" width="20%">{$form.title.label}</td>
        <td >{$form.title.html}</td>
    </tr>
    <tr>
        <td class="report-label" width="20%">{$form.description.label}</td>
        <td>{$form.description.html}</td>
    </tr>
    <tr>
        <td class="report-label" width="20%">{$form.report_header.label}</td>
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
	<th>Email Settings</th>
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
	<th>Other Settings</th>
    </tr>
</table>
<table class="report-layout">
    <tr>
        <td class="report-label" width="20%">{$form.permission.label}</td>
        <td>{$form.permission.html|crmReplace:class:huge}</td>
    </tr> 
</table>