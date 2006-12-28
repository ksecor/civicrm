{* this template is used for adding/editing/deleting event *} 
<div class="form-item"> 
  <fieldset><legend>{if $action eq 1}{ts}New Event{/ts}{elseif $action eq 8}{ts}Delete Event{/ts}{else}{ts}Edit Event{/ts}{/if}</legend> 
      	{if $action eq 8} 
            <div class="messages status"> 
          	<dl> 
          	<dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt> 
          	<dd> 
          	 {ts}WARNING: Deleting this Event will result in the loss of the associated Participation (if any).{/ts} {ts}Do you w                        	ant to continue?{/ts} 
          	</dd> 
       		</dl> 
      	     </div> 
        {else} 
      <table class="form-layout-compressed">
        <tr><td class="label font-size12pt">{ts}From{/ts}</td><td class="font-size12pt"><strong>{$displayName}</strong>&nbsp;</td></tr>
        <tr><td class="label">{$form.event_type.label}</td><td>{$form.event_type.html}&nbsp;
        {if $is_test}
          {ts}(test){/ts}
        {/if}
        </td></tr> 
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Select the appropriate Event.{/ts}</td></tr>
        <tr><td class="label">{$form.registration_date.label}</td><td>{$form.registration_date.html}
	{if $hideCalender neq true}
	  {include file="CRM/common/calendar/desc.tpl" trigger=trigger_event}
	  {include file="CRM/common/calendar/body.tpl" dateVar=registration_date startDate=currentYear endDate=endYear offset=5 trigger=trigger_event}       
	{/if}    
     	</td>
	</tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}The date this event is registered.{/ts}</td></tr>
        <tr><td class="label">{$form.fee_amount.label}</td><td>{$config->defaultCurrencySymbol}&nbsp;{$form.fee_amount.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Processing fee for the participation (if applicable).{/ts}</td></tr>
      </table>

       	{/if} 
    	<dl>    
      	<dt>&nbsp;</dt><dt>{$form.buttons.html}</dt> 
    	</dl> 
  </fieldset> 
</div> 

