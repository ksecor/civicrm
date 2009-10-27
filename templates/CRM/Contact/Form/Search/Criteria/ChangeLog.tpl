<div id="changelog" class="form-item">
    <table class="form-layout">
        <tr>
            <td>
                {$form.changed_by.label}<br />
                {$form.changed_by.html}
            </td>
            <td>
                {$form.modified_date_low.label}<br />
	            {include file="CRM/common/jcalendar.tpl" elementName=modified_date_low}&nbsp;
		        {$form.modified_date_high.label}&nbsp; 
                {include file="CRM/common/jcalendar.tpl" elementName=modified_date_high}
            </td>
        </tr>
    </table>
 </div>
