{* this template is used for displaying PCP information *} 
{if $owner}
<div class="messages status">
  <dl>
	<dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}PCPInfo{/ts}"/></dt>
	<dd><p><strong>Personal Campaign Page</strong></p></dd>
	<dd><p>This is your Personal Campaign Page (PCP). It belongs to <a href="{crmURL p='civicrm/contribute/manage' q="reset=1&action=update&id="}{$owner.id}"><strong>&raquo; {ts}Contribution Page{/ts}</strong></a>{if $owner.start_date}, which is active from <strong>{$owner.start_date|truncate:10:''|crmDate}</strong> until <strong>{$owner.end_date|truncate:10:''|crmDate}</strong>. Current status of your PCP is: <strong>{$owner.status}</strong>{/if}.</p><p><strong>You can perform following actions on your page:</strong></p></dd>
	 <dd> <table class="form-layout-compressed"> 
		{foreach from = $links key = k item = v}
	         <tr>
		   <td style="padding:0px;">
			<a href="{crmURL p=$v.url q=$v.qs|replace:'%%pcpId%%':$replace.id|replace:'%%pcpBlock%%':$replace.block}" title="{$v.title}"{if $v.extra}{$v.extra}{/if}>{$v.name}</a>
		   </td>
  		   <td style="padding:0px;">&nbsp;<cite>{$hints.$k}</cite></td>
	 	 </tr>
        	{/foreach}
  	   </table>
	</dd>
 </dl>
</div>
{/if}
<div>
  <table class="campaign" width="40%">
    <tr>
      <th colspan="2">{$pcp.intro_text}</th>
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
	    <td>
	      <table class="">
		<tr>
		  <td colspan="2" align="left"><strong>{$pcp.goal_amount|crmMoney}</strong></td><td>&nbsp;</td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td height="200px" width="50px">
		    <div class="remaining" id="remain">&nbsp;</div>
		    <div class="achieved"  id="achieve">&nbsp;</div>
		  </td>
		  <td><div vertical-align="bottom"><strong>{$achieved}%</strong></div></td>
		</tr>
		<tr>
		  <td colspan="3" align="left"><strong>{ts}Raised{/ts} {$total|crmMoney}</strong></td>
		</tr>
	      </table>
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

{/literal}
{if $remaining}
    document.getElementById("remain").style.height  = "{$remaining}%";
{else}
    document.getElementById('remain').style.display = "none";
{/if}
{if $achieved}
    document.getElementById("achieve").style.height  = "{$achieved}%";
{else}
    document.getElementById('achieve').style.display = "none";
{/if}
{literal}


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
