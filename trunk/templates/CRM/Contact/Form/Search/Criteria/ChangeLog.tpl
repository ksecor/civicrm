<div id="changelog" class="form-item">
    <table class="form-layout">
        <tr>
            <td>
                {$form.changed_by.label}<br />
                {$form.changed_by.html}
            </td>
            <td>
                {$form.modified_date_low.label}<br />
	        {$form.modified_date_low.html}&nbsp;&nbsp;
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_modified_date_low}
	        {include file="CRM/common/calendar/body.tpl" dateVar=modified_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_modified_date_low}&nbsp;
		
		{$form.modified_date_high.label}&nbsp; 
		{$form.modified_date_high.html}&nbsp;&nbsp;
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_modified_date_high}
	        {include file="CRM/common/calendar/body.tpl" dateVar=modified_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_modified_date_high}
            </td>
        </tr>
    </table>
 </div>
