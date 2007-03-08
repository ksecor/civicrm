{* This template is used for adding/editing/deleting offlin Event Registrations *}
{if $action eq 1}
    <div id="help">
        {ts}Use this form to register contacts for an event. If this is a paid event and you are accepting payment offline - you should also enter a contribution record.{/ts}
    </div>  
{/if}
<fieldset><legend>{if $action eq 1}{ts}New Event Registration{/ts}{elseif $action eq 8}{ts}Delete Event Registration{/ts}{else}{ts}Edit Event Registration{/ts}{/if}</legend> 
    <div class="form-item">
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
        <tr><td class="label font-size12pt">{ts}Name{/ts}</td><td class="font-size12pt"><strong>{$displayName}</strong>&nbsp;</td></tr>
        <tr><td class="label">{$form.event_id.label}</td><td>{$form.event_id.html}&nbsp;        
        {if $action eq 1 && !$past }<br /><a href="{$pastURL}">&raquo; {ts}Select from past event(s) too.{/ts}</a>{/if}    
        {if $is_test}
          {ts}(test){/ts}
        {/if}
        </td></tr> 
    
        <tr><td class="label">{$form.role_id.label}</td><td>{$form.role_id.html}</td></tr>
        
        <tr><td class="label">{$form.register_date.label}</td><td>{$form.register_date.html}
    	{if $hideCalender neq true}<br />
	      {include file="CRM/common/calendar/desc.tpl" trigger=trigger_event}
    	  {include file="CRM/common/calendar/body.tpl" dateVar=register_date  offset=3 doTime=1  trigger=trigger_event}       
	    {/if}    
     	    </td>
	    </tr>

        <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td></tr>
        
        <tr><td class="label">{$form.source.label}</td><td>{$form.source.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Source for this registration (if applicable).{/ts}</td></tr>

        <tr><td class="label">{$form.amount.label}</td><td>{$form.amount.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Event Fee Level (if applicable).{/ts}</td></tr>
        <tr><td class="label" style="vertical-align:top;">{$form.note.label}</td><td>{$form.note.html}</td></tr>
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
    </table>
    </div>
</fieldset> 

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
