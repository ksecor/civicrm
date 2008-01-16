
{* This template is used for adding/editing/deleting offline Event Registrations *}

<fieldset><legend>{if $action eq 1}{ts}New Event Registration{/ts}{elseif $action eq 8}{ts}Delete Event Registration{/ts}{else}{ts}Edit Event Registration{/ts}{/if}</legend> 
    {if $action eq 1 AND $paid}
    <div id="help">
	{ts}If you are accepting offline payment from this participant, check <strong>Record Payment</strong>. You will be able to fill in the payment information, and optionally send a receipt.{/ts}
    </div>  
    {/if}
    
    <table class="form-layout">
    {if $action eq 8} {* If action is Delete *}
        <tr><td>
            <div class="messages status">
          	<dl>
        	<dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt> 
          	<dd> 
          	 {ts}WARNING: Deleting this registration will result in the loss of related payment records (if any).{/ts} {ts}Do you want to continue?{/ts} 
          	</dd> 
       	    </dl>
      	    </div> 
        </td></tr>
        <tr>{* <tr> for delete form button *}
    {else} {* If action is other than Delete *}
        {if $single}
        <tr><td class="right font-size12pt bold">{ts}Participant Name{/ts}&nbsp;&nbsp;</td><td class="font-size12pt"><strong>{$displayName}</strong>&nbsp;</td></tr>
        {/if}
        <tr><td class="label">{$form.event_id.label}</td><td>{$form.event_id.html}&nbsp;        
        {if $action eq 1 && !$past }<br /><a href="{$pastURL}">&raquo; {ts}Include past event(s) in this select list.{/ts}</a>{/if}    
        {if $is_test}
          {ts}(test){/ts}
        {/if}
            </td>
        </tr> 
    
        <tr><td class="label">{$form.role_id.label}</td><td>{$form.role_id.html}</td></tr>
        
        <tr><td class="label">{$form.register_date.label}</td><td>{$form.register_date.html}
    	{if $hideCalender neq true}<br />
	      {include file="CRM/common/calendar/desc.tpl" trigger=trigger_event doTime=1}
    	  {include file="CRM/common/calendar/body.tpl" dateVar=register_date  offset=10 doTime=1  trigger=trigger_event ampm=1}       
	    {/if}    
     	    </td>
	    </tr>

        <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}{if $event_is_test} {ts}(test){/ts}{/if}</td></tr>

        <tr><td class="label">{$form.source.label}</td><td>{$form.source.html}</td></tr>

        <tr><td class="label">&nbsp;</td><td class="description">{ts}Source for this registration (if applicable).{/ts}</td></tr>

        {if $priceSet}
        <tr><td class="label">{$form.amount.label}</td></tr>
   	  {foreach from=$priceSet.fields item=element key=field_id}
             {if ($element.html_type eq 'CheckBox' || $element.html_type == 'Radio') && $element.options_per_line}
                {assign var="element_name" value=price_$field_id}
                {assign var="count" value="1"}
                <tr><td class="label"> {$form.$element_name.label}</td>
                  <td>
                    <table class="form-layout-compressed">
                    {foreach name=outer key=key item=item from=$form.$element_name}
                        <tr>	
                            {if is_numeric($key) }
                                <td class="labels font-light"><td>{$form.$element_name.$key.html}</td>
                                {if $count == $element.options_per_line}
                                    {assign var="count" value="1"}
                                    </tr>
                                    <tr>			
                                {else}
                                    {assign var="count" value=`$count+1`}
                                {/if}
                            {/if}
                        </tr>
                    {/foreach}
                    {if $element.help_post}
                        <tr><td></td><td class="description">{$element.help_post}</td></tr>
                    {/if}
                    </table>
                  </td>
                </tr>
              {else}	
                {assign var="name" value=`$element.name`}
            	{assign var="element_name" value="price_"|cat:$field_id}
                <tr><td class="label"> {$form.$element_name.label}</td>
                  <td>
                    <table class="form-layout-compressed">
                        <tr>{$form.$element_name.html}</tr>
                        {if $element.help_post}
                            <tr><td class="description">{$element.help_post}</td></tr>
                        {/if}
                    </table>	
                  </td>
                </tr>
              {/if}
           {/foreach}

    {else} {* NOT Price Set *}
	{if $paid}       
	    <tr><td class="label">{$form.amount.label}<span class="marker"> *</span></td><td>{$form.amount.html}</td>
	{/if}
    {/if}

    {if $paid}
    	<tr><td class="label">&nbsp;</td><td class="description">{ts}Event Fee Level (if applicable).{/ts}</td></tr>
    {/if}

    <tr><td class="label" style="vertical-align:top;">{$form.note.label}</td><td>{$form.note.html}</td></tr>
    {if $paid}
       <tr>
           <td class="label">{$form.record_contribution.label}</td><td class="html-adjust">{$form.record_contribution.html}<br />
              <span class="description">{ts}Check this box to enter payment information. You will also be able to generate a customized receipt.{/ts}</span>
           </td>
       </tr>
    {/if}
   
    <tr>
       <td class ='html-adjust' colspan=2>
           <fieldset id="recordContribution"><legend>{ts}Registration Confirmation{if $paid} and Receipt{/if}{/ts}</legend>
             <div class="form-item">
               {if $paid}
                 <dl>	        
		   <dt class="label">{$form.contribution_type_id.label}</dt>
                   <dd>{$form.contribution_type_id.html}<br /><span class="description">{ts}Select the appropriate contribution type for this payment.{/ts}</span></dd>
		   <dt class="label">{$form.payment_instrument_id.label}</dt><dd>{$form.payment_instrument_id.html}</dd>
		   <dt class="label">{$form.contribution_status_id.label}</dt><dd>{$form.contribution_status_id.html}</dd>      
  	         </dl>
               {/if}
	       {if $email}
                 <dl>
                    <dt class="label">{ts}Send Confirmation{/ts}{if $paid}{ts} and Receipt{/ts}{/if}</dt>
                    <dd>{$form.send_receipt.html}<br />
                    <span class="description">{ts}Automatically email a confirmation {if $paid} and receipt {/if} to {$email}?{/ts}</span></dd>
                 </dl>
                 <div id='notice' class="form-item">
                    <dl>
                      <dt class="label">{$form.receipt_text.label}</dt>
                      <dd><span class="description">{ts}Enter a message you want included at the beginning of the confirmation email. EXAMPLE: "Thanks for registering for this event."{/ts}</span><br/>{$form.receipt_text.html|crmReplace:class:huge}</dd>
                   </dl>
                </div>
               {/if}
             </div>
           </fieldset>
       </td>
    </tr> 
    <tr><td colspan=2>
        {if $action eq 4} 
            {include file="CRM/Contact/Page/View/InlineCustomData.tpl"}
        {else}
            {include file="CRM/Contact/Page/View/CustomData.tpl" mainEditForm=1}
        {/if} 
        </td>
    </tr>
    <tr> {* <tr> for add / edit form buttons *}
      	<td>&nbsp;</td>
       	{/if} 
        
        <td>{$form.buttons.html}</td> 
    	</tr> 
     </table
</fieldset> 

{if $paid} {* Record contribution field only present if this is a paid event. *}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="record_contribution"
    trigger_value       =""
    target_element_id   ="recordContribution" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}
{/if}

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="send_receipt"
    trigger_value       =""
    target_element_id   ="notice" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}

<script type="text/javascript" >
 {literal}
 function reload(refresh) {
        var roleId = document.getElementById("role_id");
        var eventId = document.getElementById("event_id");    
        var url = {/literal}"{$refreshURL}"{literal}
        var post = url;

        if( eventId.value ) {
            var post = post + "&eid=" + eventId.value;
        }
        if( roleId.value ) {
            var post = post + "&rid=" + roleId.value;
        }
        if( refresh ) {
            window.location= post; 
        }
    } 
 {/literal}
</script>
