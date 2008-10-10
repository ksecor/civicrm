{* this template is used for displaying PCP information *} 
<div>
<table class="campaign" width="40%">
	<tr>
		<th colspan=2>{$pcp.intro_text}</th>
	</tr>
	<tr>
		<td width="20%">{$pcp.page_text}</td>
		<td width="20%">
		<table class="form-layout">
			<tr>
				<td colspan="3"><center>{$image}</center></td>
			</tr>
			{if $validDate} 
			<tr>
				<td colspan="3">
				{* Show link to PCP contribution if configured for online contribution *}
				<div class="action-link">
                     		<a href={$contributeURL} class="button"><span>&raquo; <strong>{$contributionText}</strong></span></a>
				</div>
                		</td>
			</tr>
			{/if}
			<tr>
				{if $pcp.is_thermometer}
                		<td>&nbsp;</td>
				<td><strong>{ts}MY PROGRESS{/ts}</strong><br />
                		Thermometer HERE
				</td>
				{/if} 
				{if $pcp.is_honor_roll}
				<td><strong>{ts}HONOR ROLL{/ts}</strong><br />
				<div class="honor_roll">
				<marquee behavior="scroll" direction="up" id="pcp_roll"	scrolldelay="200" bgcolor="#fafafa"> 
				        {foreach from = $honor item = v} 
						{$v.nickname}<br />{$v.total_amount|crmMoney}
						<br /><br /><br />
					{/foreach} 
				</marquee>
				</div><br />	
				<center>
					[<a href="javascript:roll_start_stop();" id="roll" title="Stop the Honor Roll">{ts}Stop{/ts}</a>]
				</center>
				</td>
				{/if}
			</tr>
		</table>
		</td>
	</tr>
</table>
</div>

{literal}
<script language="JavaScript">
var start=true;
function roll_start_stop( ) {
	if ( start ) {
		document.getElementById('roll').innerHTML = "{/literal}{ts}Start{/ts}{literal}";
		document.getElementById('roll').title = "{/literal}{ts}Start the Honor Roll{/ts}{literal}";
		document.getElementById('pcp_roll').stop();
		start=false;
      	 } else {
		document.getElementById('roll').innerHTML = "{/literal}{ts}Stop{/ts}{literal}";
		document.getElementById('roll').title = "{/literal}{ts}Stop the Honor Roll{/ts}{literal}";
		document.getElementById('pcp_roll').start();
		start=true;
       	}
}
</script>
{/literal}