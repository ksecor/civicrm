<fieldset class="collapsible">
 <table class="form-layout">  
            <tr><td class="label">{$form.activity_activitytag1_id.label}</td><td>{$form.activity_activitytag1_id.html}</td></tr>
            <tr><td class="label">{$form.activity_activitytag2_id.label}</td><td>{$form.activity_activitytag2_id.html}</td>
                <td class="label">{$form.activity_activitytag3_id.label}</td><td>{$form.activity_activitytag3_id.html}</td>             </tr>
            <tr><td class="label">{$form.activity_subject.label}</td><td>{$form.activity_subject.html}</td>
                <td class="label">{$form.activity_details.label}</td><td>{$form.activity_details.html}</td>
            </tr>            
           
            <tr><td class="label"> {$form.activity_start_date_low.label} </td>
                <td> {$form.activity_start_date_low.html}&nbsp;<br />
                {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_caseActivity_1}
                {include file="CRM/common/calendar/body.tpl" dateVar=activity_start_date_low  offset=3  doTime=1 trigger=trigger_search_caseActivity_1}
                </td>
                <td colspan="2"> {$form.activity_start_date_high.label} {$form.activity_start_date_high.html}<br /> &nbsp; &nbsp; 
                {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_caseActivity_2}
                {include file="CRM/common/calendar/body.tpl" dateVar=activity_start_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_caseActivity_2}
                </td>          
            </tr>
            
           
 </table>
</fieldset>