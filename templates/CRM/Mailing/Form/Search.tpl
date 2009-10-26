<fieldset class="form-layout"><legend>{ts}Find Mailings{/ts}</legend>
<div class="form-item">
<table class="form-layout">
    <tr>
        <td>{$form.mailing_name.label}<br />
            {$form.mailing_name.html|crmReplace:class:big} {help id="id-mailing_name"}
        </td>
        <td class="nowrap">{$form.mailing_from.label}
            {include file="CRM/common/jcalendar.tpl" elementName=mailing_from}
        </td>
        <td class="nowrap">{$form.mailing_to.label}
            {include file="CRM/common/jcalendar.tpl" elementName=mailing_to}
        </td> 
    </tr>
    <tr> 
        <td colspan="3">{$form.sort_name.label}<br />
            {$form.sort_name.html|crmReplace:class:big} {help id="id-create_sort_name"}
        </td>
    </tr>
    <tr>
        <td>{$form.buttons.html}</td><td colspan="2"></td>
    </tr>
</table>
</div>
</fieldset>
