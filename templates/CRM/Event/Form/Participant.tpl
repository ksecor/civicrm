{* this template is used for adding/editing/deleting event *} 
<div class="form-item"> 
  <fieldset><legend>{if $action eq 1}{ts}New Participation{/ts}{elseif $action eq 8}{ts}Delete Participation{/ts}{else}{ts}Edit Participation{/ts}{/if}</legend> 
      	{if $action eq 8} 
            <div class="messages status"> 
          	<dl> 
          	<dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt> 
          	<dd> 
          	 {ts}WARNING: Deleting this participation will result in the loss of the associated Participation related payments (if any).{/ts} {ts}Do you want to continue?{/ts} 
          	</dd> 
       		</dl> 
      	     </div> 
        {else} 
      <table class="form-layout-compressed">
        <tr><td class="label font-size12pt">{ts}From{/ts}</td><td class="font-size12pt"><strong>{$displayName}</strong>&nbsp;</td></tr>
        <tr><td class="label">{$form.event_id.label}</td><td>{$form.event_id.html}&nbsp;
        {if $is_test}
          {ts}(test){/ts}
        {/if}
        </td></tr> 
        <tr><td class="label"></td><td class="description">{ts}Select the appropriate Event.{/ts}</td></tr>
        <tr><td class="label">{$form.register_date.label}</td><td>{$form.register_date.html}
	{if $hideCalender neq true}
	  {include file="CRM/common/calendar/desc.tpl" trigger=trigger_event}
	  {include file="CRM/common/calendar/body.tpl" dateVar=register_date startDate=currentYear endDate=endYear offset=5 trigger=trigger_event}       
	{/if}    
     	</td>
	</tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}The date this event is registered.{/ts}</td></tr>

        <tr><td class="label">{$form.role_id.label}</td><td>{$form.role_id.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Role for the participation (if applicable).{/ts}</td></tr>


        <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Status for the participation (if applicable).{/ts}</td></tr>

        <tr><td class="label">{$form.source.label}</td><td>{$form.source.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Source for the participation (if applicable).{/ts}</td></tr>

        <tr><td class="label">{$form.event_level.label}</td><td>{$form.event_level.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Event Level for the participation (if applicable).{/ts}</td></tr>
      </table>

       	{/if} 
    	<dl>    
      	<dt>&nbsp;</dt><dt>{$form.buttons.html}</dt> 
    	</dl> 
  </fieldset> 
</div> 

