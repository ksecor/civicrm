<table class="report-header">
    <tr>
	<td><strong>{ts}Display Settings{/ts}</strong></td>
    </tr>
</table>
<table class="form-layout">
    <tr>
        <td class="label" width="20%">{$form.title.label}</td>
        <td>{$form.title.html}</td>
    </tr>
    <tr>
        <td class="label">{$form.description.label}</td>
        <td>{$form.description.html}</td>
    </tr>
    <tr>
        <td class="label">{$form.report_header.label}</td>
        <td>{$form.report_header.html}</td>
    </tr>
    <tr>
        <td class="label">{$form.report_footer.label}</td>
        <td>{$form.report_footer.html}</td>
    </tr>
</table>
<br/>

<table class="report-header">
    <tr>
	<td><strong>{ts}Email Settings{/ts}</strong></td>
    </tr>
</table>
<table class="form-layout">
    <tr>
        <td class="label" width="20%">{$form.email_subject.label}</td>
        <td>{$form.email_subject.html|crmReplace:class:huge}</td>
    </tr>
    <tr>
        <td class="label">{$form.email_to.label}</td>
        <td>{$form.email_to.html|crmReplace:class:huge}</td>
    </tr>
    <tr>
        <td class="label">{$form.email_cc.label}</td>
        <td>{$form.email_cc.html|crmReplace:class:huge}</td>
    </tr> 
</table>
<br/>

<table class="report-header">
    <tr>
	<td><strong>{ts}Other Settings{/ts}</strong></td>
    </tr>
</table>
<table class="form-layout">
    <tr>
        <td class="label" width="20%">{$form.permission.label}</td>
        <td>{$form.permission.html|crmReplace:class:huge}</td>
    </tr> 
</table>