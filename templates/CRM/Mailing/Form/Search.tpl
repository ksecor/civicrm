<div class="form-item">
<fieldset><legend>{ts}Find Mailings{/ts}</legend>
<table class="form-layout">
    <tr>
        <td>{$form.mailing_name.label}</td>
        <td colspan="3">{$form.mailing_name.html}
            <div class="description font-italic">
                {ts}Complete OR partial mailing name.{/ts}
            </div>
        </td>
        <td class="right">&nbsp;{$form.buttons.html}</td>
    <tr>
    </tr>
        <td>&nbsp;{$form.mailing_from.label}</td>
        <td>
            &nbsp;{$form.mailing_from.html}&nbsp;<br />
            &nbsp;{include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=mailing_from startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_1}
        </td>
        <td>{$form.mailing_to.label}</td>
        <td>
             &nbsp;{$form.mailing_to.html}<br />
             &nbsp;{include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_2}
             {include file="CRM/common/calendar/body.tpl" dateVar=mailing_to startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_2}
        </td> 
        <td></td>
    </tr>
</table>
</fieldset>
</div>
