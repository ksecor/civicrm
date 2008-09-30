{* this template is used for displaying PCP information *}

<div class="vevent">
	<h2><span class="summary">{$pcp.title}</span></h2>	
    <div class="display-block">
	<table class="form-layout">
      	{if $pcp.intro_text}
		<tr><td colspan="2" class="report">{$pcp.intro_text}</td></tr>
      	{/if}
      	{if $pcp.page_text}
      		<tr><td colspan="2" class="report">
		<span class="summary">{$pcp.page_text}</span></td></tr>
	{/if}
	<tr><td><label>{ts}When{/ts}</label></td>
            <td width="90%">
	    <abbr class="dtstart" title="{$pcpDate.start_date}">
	    	{$pcpDate.start_date|crmDate}</abbr>
	
	{if $pcpDate.end_date}
		&nbsp; {ts}through{/ts} &nbsp;
                {* Only show end time if end date = start date *}
                {if $pcpDate.end_date|date_format:"%Y%m%d" == $pcpDate.start_date|date_format:"%Y%m%d"}
			<abbr class="dtend" title="{$pcpDate.end_date}">
			{$pcpDate.end_date|crmDate:0:1}
			</abbr>        
                {else}
			<abbr class="dtend" title="{$pcpDate.end_date}">
			{$pcpDate.end_date|crmDate}
			</abbr> 	
                {/if}
            {/if}
            </td>
	</tr>
	
	</table>

    {* Show link to PCP contribution if configured for online contribution *}
         <div class="action-link">
            <strong><a href="{$contributeURL}" class="button"><span>&raquo; {$contribtionText}</span></a></strong>
         </div>
   </div>
