<div id="activity" class="form-item">
    <table class="form-layout">
        <tr>
            <td>
                {$form.activity_type_id.label}<br />
                {$form.activity_type_id.html|crmReplace:class:big}
            </td>
            <td >
                {$form.activity_date_low.label|replace:'-':'<br />'}<br/>
				{include file="CRM/common/jcalendar.tpl" elementName=activity_date_low} 
			</td>
			<td><br />
				{$form.activity_date_high.label}<br />
				{include file="CRM/common/jcalendar.tpl" elementName=activity_date_high}
            </td>
        </tr>
        <tr>
            <td>
		        {$form.activity_role.label}&nbsp;(<a href="#" title="unselect" onclick="unselectRadio('activity_role', 'Advanced'); return false;" >unselect</a>)<br />
                {$form.activity_role.html}
            </td>
            <td colspan="2"><br /><br />
				{$form.activity_target_name.html}<br />
                <span class="description font-italic">{ts}Complete OR partial Contact Name.{/ts}</span><br /><br />
				{$form.activity_test.label} &nbsp; {$form.activity_test.html} 
            </td>
        </tr>
        <tr>
             <td>
                {$form.activity_subject.label}<br />
                {$form.activity_subject.html|crmReplace:class:big} 
             </td>
	         <td colspan="2">
                {$form.activity_status.label}<br />
                {$form.activity_status.html} 
             </td>
        </tr>
        {if $activityGroupTree}
        <tr>
	         <td colspan="3">
	          {include file="CRM/Custom/Form/Search.tpl" groupTree=$activityGroupTree showHideLinks=false}
             </td>
        </tr>
        {/if}
    </table>
</div>