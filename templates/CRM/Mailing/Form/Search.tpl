<fieldset class="form-layout"><legend>{ts}Find Mailings{/ts}</legend>
<div class="form-item">
<table class="form-layout">
    <tr>
        <td>{$form.mailing_name.label}<br />
            {$form.mailing_name.html|crmReplace:class:big} {help id="id-mailing_name"}
        </td>
        <td class="nowrap">{$form.mailing_from.label}<br />{$form.mailing_from.html}<br />
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=mailing_from startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_1}
        </td>
        <td class="nowrap">{$form.mailing_to.label}<br />{$form.mailing_to.html}<br />
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=mailing_to startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_2}
        </td> 
    </tr>
    <tr> 
        <td>{$form.sort_name.label}<br />
            {$form.sort_name.html|crmReplace:class:big} {help id="id-create_sort_name"}
        </td>
    </tr>
    <tr>
        <td>{$form.buttons.html}</td><td colspan="2"></td>
    </tr>
</table>
</div>
</fieldset>
