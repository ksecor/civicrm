{* this template is used for adding/editing/deleting case *} 
<fieldset>
{if $action eq 1}
    <legend>{ts}New Case Registration{/ts}</legend>
{elseif $action eq 2}
    <legend>{ts}Edit Case Registration{/ts}</legend>
{elseif $action eq 8 and !$context}
    <legend>{ts}Remove Case Registration{/ts}</legend>
{elseif $action eq 8 and $context}
    <legend>{ts}Detach Activity From Case{/ts}</legend>
{/if}
    <div class="form-item">
        <table class="form-layout-compressed"> 
          
    {if $action eq 8 and $context}
        <div class="status">{ts}Are you sure you want to Detach this case from Activity?{/ts}</div>
    {elseif $action eq 8 and !$context}
        <div class="status">{ts}Are you sure you want to Remove this case?{/ts}</div> 
    {else}
    	    <tr><td class="label">{$form.subject.label}</td><td>{$form.subject.html}</td></tr>
            <tr><td class="label">&nbsp;</td><td class="description">{ts}Enter the case subject {/ts}</td></tr>
            <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td></tr>   
            <tr><td class="label">&nbsp;</td><td class="description">{ts}Select the status for this case{/ts}</td></tr>
            <tr><td class="label">{$form.case_type_id.label}</td><td>{$form.case_type_id.html}</td></tr>     
            <tr><td class="label">&nbsp;</td><td class="description">{ts}Select the appropriate type of the case {/ts}</td></tr>       
            <tr><td class="label">{$form.start_date.label}</td><td>{$form.start_date.html}
	            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_case_1}
    	        {include file="CRM/common/calendar/body.tpl" dateVar=start_date offset=10 doTime=1 trigger=trigger_case_1}       
                </td>
            </tr>
            <tr><td class="label">{$form.end_date.label}</td><td>{$form.end_date.html}
                {include file="CRM/common/calendar/desc.tpl" trigger=trigger_case_2}
    	        {include file="CRM/common/calendar/body.tpl" dateVar=end_date offset=10 doTime=1  trigger=trigger_case_2}       
                </td>
            </tr>
          <tr><td class="label">{$form.details.label}</td><td>{$form.details.html|crmReplace:class:huge}</td></tr>
     {/if}
            <tr> {* <tr> for add / edit form buttons *}
      	    <td>&nbsp;</td><td>{$form.buttons.html}</td> 
    	    </tr>
       </table>
    </div>
</fieldset>
      
