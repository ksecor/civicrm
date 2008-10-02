{* this template is used for displaying PCP information *} 
<div>
<table class="campaign" width="40%">
	<th>{$pcp.title}</th>
	<tr>
		<td colspan=2>{$pcp.intro_text}</td>
	</tr>
	<tr>
		<td width="20%">{$pcp.page_text}</td>
		<td width="20%">
		<table class="form-layout">
			<tr>
				<td colspan="3"><center>{$image}</center></td>
			</tr>
			<tr>
				<td colspan="3">
				{if $validDate} 
				{* Show link to PCP contribution if configured for online contribution *}
				<div class="action-link">
					<strong><a href="javascript:pcpPage();" class="button"><span>&raquo; {$contributionText}</span></a>
					</strong>
				</div>
				{/if}</td>
			</tr>

			<tr>
				{if $pcp.is_thermometer}<td>&nbsp;</td>
				<td><strong>MY PROGRESS</strong><br />
				<div class="pcp_progress">
				Goal
				<div class="money">{$pcp.goal_amount|crmMoney}</div>
				<div class="meter">
				<div class="remaining">&nbsp;</div>
				<div class="achieved">&nbsp;</div>
				</div>
				<div class="percentage">{$achieved}%</div>
				Achieved
				<div class="money">{$total|crmMoney}</div>

				</div>
				</td>
				{/if} 
				{if $pcp.is_honor_roll}
				<td><strong>HONOR ROLL</strong><br />
				<div class="honor_roll">
				<marquee behavior="scroll" direction="up" id="pcp_roll"	scrolldelay="200" bgcolor="#fafafa"> 
				        {foreach from = $honor item = v} 
						{$v.nickname}<br />{$v.total_amount|crmMoney}
						<br /><br /><br />
					{/foreach} 
				</marquee>
				</div><br />	
				<center>
					[<a href="javascript:roll_start_stop();" id="roll" title="Stop the Honor Roll">Stop</a>]
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
<style>
.pcp_progress {
	font-family      : arial;
	font-size        : 12px;
	margin           : 5px 20px 0 0;
    	padding          : 50px;
    	text-align       : center;
    	width            : 50px;
    	color            : black;
    	background-color : #fafafa;
    	border           : 1px solid #9d9fca;
}
.honor_roll {
	margin           : 5px 20px 0 0;
	padding          : 10px;	
    	width            : 150px;
	height           : 317px;
    	background-color : #fafafa;
    	border           : 1px solid #9d9fca;
}
.percentage {
    	padding : 3px;
}
.meter {
    	height  : 150px;
}
{/literal}{if $remaining}{literal}
.remaining {
    	height     : {/literal}{$remaining}{literal}%;
    	background : url("{/literal}{$config->resourceBase}{literal}i/contribute/pcp_remain.gif");}
{/literal}
{/if}
{if $achieved}{literal}
.achieved {
    	height     : {/literal}{$achieved}{literal}%;
    	background : url("{/literal}{$config->resourceBase}{literal}i/contribute/pcp_achieve.gif");}
{/literal}{/if}{literal}
</style>

<script language="JavaScript">
function roll_start_stop() {
      if( document.getElementById('roll').innerHTML == 'Stop') {
		document.getElementById('roll').innerHTML = 'Start';
		document.getElementById('roll').title = 'Start the Honor Roll';
		document.getElementById('pcp_roll').stop();
       } else {
		document.getElementById('roll').innerHTML = 'Stop';
		document.getElementById('roll').title = 'Stop the Honor Roll';
		document.getElementById('pcp_roll').start();
       }
}
function pcpPage() {
	var string = "{/literal}{$contributeURL}{literal}";
	window.location=  string.replace(/&amp;/g, '&');
}
</script>
{/literal}