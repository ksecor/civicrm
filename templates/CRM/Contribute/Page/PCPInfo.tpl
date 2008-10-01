{* this template is used for displaying PCP information *}
{*border for temporary skeleton display*}
<div>
<table class="form-layout-compressed" width="40%" border=1>
<th>{$pcp.title}</th>
<tr><td>{$pcp.intro_text}</td></tr>
<tr>
<td width="20%">{$pcp.page_text}</td>
<td width="20%">
<table class="form-layout" width="10%" border=1>
<tr><td colspan="2">image part</td></tr>
<tr><td colspan="2">
{if $validDate}
  {* Show link to PCP contribution if configured for online contribution *}
     <div class="action-link">
          <strong><a href="{$contributeURL}" class="button"><span>&raquo; {$contributionText}</span></a></strong>
     </div>
{/if}
</td></tr>

<tr><td width="5%">
thermometer part
</td>
<td  width="5%">FUNDRAISING HONOR ROLL<br />
<marquee behavior="scroll" direction="up" SCROLLDELAY="200">
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