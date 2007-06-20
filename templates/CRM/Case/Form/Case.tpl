{* this template is used for adding/editing/deleting case *} 
<fieldset>
{if $action eq 1}
    <legend>{ts}New Case Registration{/ts}</legend>
{/if}
{if $action eq 2}
    <legend>{ts}Edit Case Registration{/ts}</legend>
{/if}
    <div class="form-item">
        <table class="form-layout">  
            <tr><td class="label">{$form.subject.label}</td><td>{$form.subject.html}</td></tr>
            <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td></tr>   
            <tr><td class="label">{$form.casetag1_id.label}</td><td>{$form.casetag1_id.html}</td></tr>            
            <tr><td class="label">{$form.casetag2_id.label}</td><td>{$form.casetag2_id.html}</td></tr>            
            <tr><td class="label">{$form.casetag3_id.label}</td><td>{$form.casetag3_id.html}</td></tr>            
            
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
            
            <tr><td class="label">{$form.details.label}</td><td>{$form.details.html}</td></tr>
            <tr> {* <tr> for add / edit form buttons *}
      	    <td>&nbsp;</td><td>{$form.buttons.html}</td> 
    	    </tr> 
         </table>
    </div>
</fieldset>
        
    