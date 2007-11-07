
{* This template is used for adding/editing/deleting offline Event Registrations *}

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
{if $single}
        <tr><td class="label font-size12pt">{ts}Name{/ts}</td><td class="font-size12pt"><strong>{$displayName}</strong>&nbsp;</td></tr>
{/if}
        <tr><td class="label">{$form.event_id.label}</td><td>{$form.event_id.html}&nbsp;        
        {if $action eq 1 && !$past }<br /><a href="{$pastURL}">&raquo; {ts}Include past event(s) in this select list.{/ts}</a>{/if}    
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
                {/if}
    	        {/foreach}

{else}
    {if $paidEvent}
    	<table class="form-layout-compressed">
        <tr><td class="label nowrap">{$form.amount.label}<span class="marker">*</span></td>
            <td>&nbsp;</td>
            <td>{$form.amount.html}</td>
        </tr>
    	</table>
    {/if}
	<tr><td class="label">{$form.amount.label}<td>
	{$form.amount.html}
{/if}

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
