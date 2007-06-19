{* this template is used for adding/editing/deleting case *} 
<fieldset>
    <legend>{ts}New Case Registration{/ts}</legend>
    <div class="form-item">
        <table class="form-layout">  
            <tr><td class="label">{$form.subject.label}</td><td>{$form.subject.html}</td></tr>
            <tr><td class="label">{$form.status.label}</td><td>{$form.status.html}</td></tr>   
            <tr><td class="label">{$form.case_type_id.label}</td><td>{$form.case_type_id.html}</td></tr>            
            <tr><td class="label">{$form.case_sub_type_id.label}</td><td>{$form.case_sub_type_id.html}</td></tr>            
            <tr><td class="label">{$form.case_violation_type_id.label}</td><td>{$form.case_violation_type_id.html}</td></tr>            
            
            <tr><td class="label">{$form.start_date.label}</td><td>{$form.start_date.html}
	        {if $hideCalender neq true}<br />
	            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_case1}
    	        {include file="CRM/common/calendar/body.tpl" dateVar=start_date  offset=3 doTime=1  trigger=trigger_case1}       
            {/if}</td></tr>
           
            <tr><td class="label">{$form.end_date.label}</td><td>{$form.end_date.html}
             {if $hideCalender neq true}<br />
	            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_case2}
    	        {include file="CRM/common/calendar/body.tpl" dateVar=end_date  offset=3 doTime=1  trigger=trigger_case2}       
            {/if}</td></tr>
            
            <tr><td class="label">{$form.description.label}</td><td>{$form.description.html}</td></tr>
            <tr> {* <tr> for add / edit form buttons *}
      	    <td>&nbsp;</td><td>{$form.buttons.html}</td> 
    	    </tr> 
         </table>
    </div>
</fieldset>
        
    