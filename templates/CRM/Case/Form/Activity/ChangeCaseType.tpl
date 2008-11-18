{* Template for "Change Case Type" activities *}
    <tr><td class="label">{$form.case_type_id.label}</td><td>{$form.case_type_id.html}</td></tr>        
    <tr><td class="label">{$form.is_reset_timeline.label}</td><td>{$form.is_reset_timeline.html}</td></tr>  
    <tr id="resetTimeline"><td class="label">{$form.reset_date_time.label}</td><td>{$form.reset_date_time.html}
	            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_case_1}
    	        {include file="CRM/common/calendar/body.tpl" dateVar=reset_date_time offset=10 trigger=trigger_case_1}</td>
    </tr>
    {if $groupTree}
        <tr>
            <td colspan="2">{include file="CRM/Custom/Form/CustomData.tpl" noPostCustomButton=1}</td>
        </tr>
    {/if}

{include file="CRM/common/showHideByFieldValue.tpl" 
trigger_field_id    ="is_reset_timeline"
trigger_value       = true
target_element_id   ="resetTimeline" 
target_element_type ="table-row"
field_type          ="radio"
invert              = 0
}
