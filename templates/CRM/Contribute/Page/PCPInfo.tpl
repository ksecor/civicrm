{* this template is used for displaying PCP information *}
{*border for temporary skeleton display*}
<div>
<table class="campaign" width="40%">
<th>{$pcp.title}</th>
<tr><td colspan=2>{$pcp.intro_text}</td></tr>
<tr>
<td width="20%">{$pcp.page_text}</td>
<td width="20%">
<table class="form-layout">
<tr><td colspan="2">{$image}</td></tr>
<tr><td colspan="2">
{if $validDate}
  {* Show link to PCP contribution if configured for online contribution *}
     <div class="action-link">
          <strong><a href="{$contributeURL}" class="button"><span>&raquo; {$contributionText}</span></a></strong>
     </div>
{/if}
</td></tr>

<tr><td>
<div class="pcp_progress">
  Goal<div class="money">{$currencySymbol}{$pcp.goal_amount}</div>
  <div class="meter">
    <div class="remaining">&nbsp;</div>
    <div class="achieved">&nbsp;</div>
  </div>
  <div class="percentage">{$achieved}%</div>
  Achieved<div class="money">{$currencySymbol}{$total}</div>
  
</div>
</td>
<td>HONOR ROLL<br />
<marquee behavior="scroll" direction="up" SCROLLDELAY="200" bgcolor="#fafafa">
{foreach from=$honor key=k item=v}
{$v}<br /><br /><br />
{/foreach}
</marquee>
</td></tr>
</table>
</td>
</tr>
</table>
</div>
{literal}
<style>
.pcp_progress {
    font-family: arial;
    font-size: 12px;
    margin: 5px 20px 0 0;
    padding: 20px;
    text-align: center;
    width: 50px;
    color: black;
    background-color: #fafafa;
    border: 1px solid #9d9fca;
}
.money, .percentage, {
    padding: 3px;
}
.meter {
    height: 150px;
}
{/literal}{if $remaining}{literal}
.remaining {
    height: {/literal}{$remaining}{literal}%;
    background: url("{/literal}{$config->resourceBase}{literal}i/contribute/pcp_remain.gif");}
{/literal}
{/if}
{if $achieved}{literal}
.achieved {
    height: {/literal}{$achieved}{literal}%;
    background: url("{/literal}{$config->resourceBase}{literal}i/contribute/pcp_achieve.gif");}
{/literal}{/if}{literal}
</style>
{/literal}
