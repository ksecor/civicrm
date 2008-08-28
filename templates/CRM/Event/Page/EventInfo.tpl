{* this template is used for displaying event information *}

<div class="vevent">
	<h2><span class="summary">{$event.title}</span></h2>	
    <div class="display-block">
	<table class="form-layout">
      	{if $event.summary}
		<tr><td colspan="2" class="report">{$event.summary}</td></tr>
      	{/if}
      	{if $event.description}
      		<tr><td colspan="2" class="report">
		<span class="summary">{$event.description}</span></td></tr>
	{/if}
	<tr><td><label>{ts}When{/ts}</label></td>
            <td width="90%">
	    <abbr class="dtstart" title="{$event.event_start_date}">
	    	{$event.event_start_date|crmDate}</abbr>
	
	{if $event.event_end_date}
		&nbsp; {ts}through{/ts} &nbsp;
                {* Only show end time if end date = start date *}
                {if $event.event_end_date|date_format:"%Y%m%d" == $event.event_start_date|date_format:"%Y%m%d"}
			<abbr class="dtend" title="{$event.event_end_date}">
			{$event.event_end_date|crmDate:0:1}
			</abbr>        
                {else}
			<abbr class="dtend" title="{$event.event_end_date}">
			{$event.event_end_date|crmDate}
			</abbr> 	
                {/if}
            {/if}
            </td>
	</tr>
	
	{if $isShowLocation}
        	{if $location.1.name || $location.1.address}
        	    <tr><td><label>{ts}Location{/ts}</label></td>
              	        <td>{if $location.1.name}
				<span class="fn org">{$location.1.name}</span><br />{/if}
                 		{$location.1.address.display|nl2br}
                		{if ( $event.is_map && $config->mapAPIKey && ( is_numeric($location.1.address.geo_code_1)  || ( $config->mapGeoCoding && $location.1.address.city AND $location.1.address.state_province ) ) ) }
                 		<br/><a href="{$mapURL}" title="{ts}Map this Address{/ts}">{ts}Map this Location{/ts}</a>
              	  		{/if}
                 	</td>
          	    </tr>
		{/if}
      	{/if}{*End of isShowLocation condition*}  

	{if $location.1.phone.1.phone || $location.1.email.1.email}
            <tr><td><label>{ts}Contact{/ts}</label></td>
            	<td>	{* loop on any phones and emails for this event *}
               		{foreach from=$location.1.phone item=phone}
                		{if $phone.phone}
               		     		{if $phone.phone_type}{$phone.phone_type_display}:{/if} 
						<span class="tel">{$phone.phone}</span> <br />
                			{/if}
                	{/foreach}

			{foreach from=$location.1.email item=email}
        			{if $email.email}
                    			{ts}Email:{/ts} <span class="email"><a href="mailto:{$email.email}">{$email.email}</a></span>
                  		{/if}
                	{/foreach}
            	</td>
            </tr>
        {/if}
    
	{if $event.is_monetary eq 1 && $feeBlock.value} 
      	<tr><td style="vertical-align:top;"><label>{$event.fee_label}</label></td>
            <td>
            <table class="form-layout-compressed">
	         {section name=loop start=1 loop=11}
        	    {assign var=idx value=$smarty.section.loop.index}
                {if $feeBlock.value.$idx}
                	<tr><td>{$feeBlock.label.$idx}</td>
                        <td>{$feeBlock.value.$idx|crmMoney}</td>
                    </tr>
                {/if}
         	 {/section}
         	</table>
            </td>
        </tr>
        {/if}
	</table>

    {include file="CRM/Contact/Page/View/InlineCustomData.tpl" mainEditForm=1} 

    {* Show link to Event Registration page if event if configured for online reg AND we are NOT coming from Contact Dashboard (CRM-2046) *}
    {if $is_online_registration AND $context NEQ 'dashboard'}
        <div class="action-link">
            <strong><a href="{$registerURL}" title="{$registerText}">{$registerText}</a></strong>
        </div>
    {/if}
	</div>
    { if $event.is_public }
      <div class="action-link">
         {capture assign=icalFile}{crmURL p='civicrm/event/ical' q="reset=1&id=`$event.id`"}{/capture}
         {capture assign=icalFeed}{crmURL p='civicrm/event/ical' q="reset=1&page=1&id=`$event.id`"}{/capture}

         <a href="{$icalFile}">&raquo; {ts}Download iCalendar File{/ts}</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{$icalFeed}" title="{ts}iCalendar Feed{/ts}"><img src="{$config->resourceBase}i/ical_feed.gif" alt="{ts}iCalendar Feed{/ts}" /></a> 
      </div>
    {/if}
</div>
