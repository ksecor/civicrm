<table class="form-layout">
   <tr><td class="label" width="30%">{$form.case_id.label}</td>
       <td><div dojoType="dojox.data.QueryReadStore" jsId="caseStore" url="{$caseUrl}" class="tundra">
                                    {$form.case_id.html}
           </div>
       </td>
   </tr>        
   <tr><td class="label">{$form.case_type_id.label}</td><td>{$form.case_type_id.html}</td>        
   <tr><td class="label">{$form.is_reset_timeline.label}</td><td>{$form.is_reset_timeline.html}</td>        
   <tr><td class="label">{$form.start_date.label}</td><td>{$form.start_date.html}
	            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_case_1}
    	        {include file="CRM/common/calendar/body.tpl" dateVar=start_date offset=10 trigger=trigger_case_1}</td>
   <tr><td">&nbsp;</td><td>{$form.buttons.html}</td></tr>
</table>