<table class="form-layout">
   <tr><td class="label" width="30%">{$form.case_id.label}</td>
       <td><div dojoType="dojox.data.QueryReadStore" jsId="caseStore" url="{$caseUrl}" class="tundra">
                                    {$form.case_id.html}
           </div>
       </td>
   </tr>        
   <tr><td class="label">{$form.case_type_id.label}</td><td>{$form.case_type_id.html}</td></tr>        
   <tr><td class="label">{$form.is_reset_timeline.label}</td><td>{$form.is_reset_timeline.html}</td></tr>  
   <tr id="resetTimeline"><td class="label">{$form.start_date.label}</td><td>{$form.start_date.html}
	            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_case_1}
    	        {include file="CRM/common/calendar/body.tpl" dateVar=start_date offset=10 trigger=trigger_case_1}</td>
   </tr>
</table>

{include file="CRM/common/showHide.tpl"}
{include file="CRM/common/showHideByFieldValue.tpl" 
trigger_field_id    ="is_reset_timeline"
trigger_value       =""
target_element_id   ="resetTimeline" 
target_element_type ="table-row"
field_type          ="radio"
invert              = 0
}

{literal}
<script type="text/javascript">
window.onload = function() {
document.getElementsByName("is_reset_timeline")[0].checked = true;
showHideByValue('is_reset_timeline','','resetTimeline','table-row','radio',false);
}
</script>
{/literal}
