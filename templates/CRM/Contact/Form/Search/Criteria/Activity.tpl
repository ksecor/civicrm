<div id="activity" class="form-item">
    <table class="form-layout">
        <tr>
            <td>
                {$form.activity_type_id.label}<br />
                {$form.activity_type_id.html}
            </td>
            <td >
                {$form.activity_date_low.label|replace:'-':'<br />'}&nbsp;
	        {$form.activity_date_low.html} &nbsp;&nbsp;
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_activity_date_low}
	        {include file="CRM/common/calendar/body.tpl" dateVar=activity_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_activity_date_low}&nbsp; 

		{$form.activity_date_high.label}&nbsp;
		{$form.activity_date_high.html}&nbsp;&nbsp;
		{include file="CRM/common/calendar/desc.tpl" trigger=trigger_activity_date_high}
	        {include file="CRM/common/calendar/body.tpl" dateVar=activity_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_activity_date_high}
            </td>
        </tr>
        <tr>
            <td>
		{$form.activity_role.label}<br />
                {$form.activity_role.html}
            </td>
            <td>
		<br /><br />{$form.activity_target_name.html}<br />
                <span class="description font-italic">{ts}Complete OR partial Contact Name.{/ts}</span><br /><br />
   		{$form.test_activities.label} &nbsp; {$form.test_activities.html} 
            </td>
        </tr>
        <tr>
             <td>
                {$form.activity_subject.label}<br />
                {$form.activity_subject.html} 
             </td>
	     <td>
                {$form.activity_status.label}<br />
                {$form.activity_status.html} 
            </td>
        </tr>
       {if $activityGroupTree}
        <tr>
	    <td colspan="2">
	   {include file="CRM/Custom/Form/Search.tpl" groupTree=$activityGroupTree showHideLinks=false}</td>
        </tr>
       {/if}

    </table>
</div>
