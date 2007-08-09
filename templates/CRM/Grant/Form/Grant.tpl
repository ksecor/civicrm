{* this template is used for adding/editing/deleting case *} 
<fieldset>
{if $action eq 1}
    <legend>{ts}New Grant{/ts}</legend>
{elseif $action eq 2}
    <legend>{ts}Edit Grant{/ts}</legend>
{/if}
    <div class="form-item">
        <table class="form-layout-compressed">  
            <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td></tr>   
            <tr><td class="label">&nbsp;</td><td class="description">{ts}Select the status for this grant{/ts}</td></tr>
            <tr><td class="label">{$form.grant_type_id.label}</td><td>{$form.grant_type_id.html}</td></tr>   
             <tr><td class="label">&nbsp;</td><td class="description">{ts}Select the appropriate type of the Grant {/ts}</td></tr>
             <tr><td class="label">{$form.amount_requested.label}</td><td>{$form.amount_requested.html}</td></tr>
            <tr><td class="label">&nbsp;</td><td class="description">{ts}Amount requested for grant  {/ts}
                </td>
            </tr>
            <tr><td class="label">{$form.amount_granted.label}</td><td>{$form.amount_granted.html}</td></tr>
             <tr><td class="label">&nbsp;</td><td class="description">{ts}Actual amount granted  {/ts}
                </td>
            </tr>
            <tr><td class="label">{$form.amount_total.label}</td><td>{$form.amount_total.html}</td></tr>
             <tr><td class="label">&nbsp;</td><td class="description">{ts}Total amount of the grant  {/ts}
                </td>
            </tr>
            
            <tr><td class="label">{$form.application_received_date.label}</td>
                <td>{$form.application_received_date.html}
	        {if $hideCalender neq true}<br />
	            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_case1}
    	        {include file="CRM/common/calendar/body.tpl" dateVar=application_received_date offset=3 doTime=1 trigger=trigger_case1}
            {/if}
                </td>
            </tr>
            <tr><td class="label">&nbsp;</td><td class="description">{ts}Date on which application for the Grant recieved {/ts}
                </td>
            </tr>
            <tr><td class="label">{$form.decision_date.label}</td>
                <td>{$form.decision_date.html }<br />
	        {if $hideCalender neq true}
	            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_case1}
    	        {include file="CRM/common/calendar/body.tpl" dateVar=decision_date offset=3 doTime=1 trigger=trigger_case1}
            {/if}
                 </td>
            </tr>
            <tr><td class="label">&nbsp;</td><td class="description">{ts}Date on which the Grant decided {/ts}  
                </td>
            </tr>
            <tr><td class="label">{$form.money_transfer_date.label}</td>
                <td>{$form.money_transfer_date.html}
	        {if $hideCalender neq true }<br />
	            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_case1}
    	            {include file="CRM/common/calendar/body.tpl" 
                             dateVar=money_transfer_date offset=3 doTime=1 trigger=trigger_case1}
                 {/if}
                </td>
            </tr>
            <tr><td class="label">&nbsp;</td><td class="description">{ts}Date on which the Grant money transferred {/ts}
                </td>
            </tr>
            <tr><td class="label">{$form.grant_due_date.label}</td>
                <td>{$form.grant_due_date.html}
	        {if $hideCalender neq true}<br />
	            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_case1}
    	        {include file="CRM/common/calendar/body.tpl" dateVar=grant_due_date offset=3 doTime=1 trigger=trigger_case1}
            {/if}
                </td>
            </tr>
            <tr><td class="label">{$form.rationale.label}</td><td>{$form.rationale.html}</td></tr>
            <tr><td class="label">{$form.note.label}</td><td>{$form.note.html}</td></tr>

            <tr> {* <tr> for add / edit form buttons *}
      	    <td>&nbsp;</td><td>{$form.buttons.html}</td> 
    	    </tr> 
         </table>
    </div>
</fieldset>
        
    