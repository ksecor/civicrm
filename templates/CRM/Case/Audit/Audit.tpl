{*
Notes:
- Any id's should be prefixed with civicase-audit to avoid name collisions.
- The idea behind the regex_replace is that for a css selector on a field, we can make it simple by saying the convention is to use the field label, but convert to lower case and strip out all except a-z and 0-9.
There's the potential for collisions (two different labels having the same shortened version), but it would be odd for the user to configure fields that way, and at most affects styling as opposed to crashing or something. 
- Note the whole output gets contained within a <form> with name="Report".
*}

<script src="{$config->resourceBase}js/Audit/audit.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="{$config->resourceBase}css/Audit/style.css" />
<input type="hidden" name="currentSelection" value="1" />
<div id="civicase-audit">
<table><tr><td class="leftpane">
<table class="report">
<tr class="columnheader-dark">
<th>&nbsp;</th>
<th>{ts}Description{/ts}</th>
</tr>
{foreach from=$activities item=activity name=activityloop}
<tr class="activity{if $smarty.foreach.activityloop.first} selected{/if}" id="civicase-audit-activity-{$smarty.foreach.activityloop.iteration}">
	<td class="indicator">
		{if $activity.completed}
		<img src="{$config->resourceBase}i/spacer.gif" width="20" height="20">
		{else}
		<a href="javascript:selectActivity({$smarty.foreach.activityloop.iteration})">
		<img src="{$config->resourceBase}i/contribute/incomplete.gif" width="20" height="20" alt="{ts}Incomplete{/ts}" title="{ts}Incomplete{/ts}">
		</a>
		{/if}
	</td>
	<td>
	<a href="javascript:selectActivity({$smarty.foreach.activityloop.iteration})">
	{foreach from=$activity.leftpane item=field name=fieldloop}
		<span class="civicase-audit-{$field.label|lower|regex_replace:'/[^a-z0-9]+/':''} {$field.datatype}">
		{if $field.datatype == 'File'}<a href="{$field.value|escape}">{/if}
		{if $field.datatype == 'Date'}
			{if $field.includeTime}
				{$field.value|escape|replace:'T':' '|crmdate}
			{else}
				{$field.value|escape|truncate:10:'':true|crmdate}
			{/if}
		{else}
			{$field.value|escape}
		{/if}
		{if $field.datatype == 'File'}</a>{/if}
		</span><br>
	{/foreach}
	</a>
	</td>
</tr>
{/foreach}
</table>
</td>
<td class="separator">&nbsp;</td>
<td class="rightpane">
	<div class="rightpaneheader">
	{foreach from=$activities item=activity name=activityloop}
		<div class="activityheader" id="civicase-audit-header-{$smarty.foreach.activityloop.iteration}">
		<div class="auditmenu">
			<label>{ts}Actions{/ts}</label>
			<span class="editlink"><a target="editauditwin" href="{$activity.editurl}">{ts}Edit{/ts}</a></span>
		</div>	
		{foreach from=$activity.rightpaneheader item=field name=fieldloop}
			<div class="civicase-audit-{$field.label|lower|regex_replace:'/[^a-z0-9]+/':''}">
			<label>{$field.label|escape}</label>
			<span class="{$field.datatype}">{if $field.datatype == 'File'}<a href="{$field.value|escape}">{/if}
			{if $field.datatype == 'Date'}
				{if $field.includeTime}
					{$field.value|escape|replace:'T':' '|crmdate}
				{else}
					{$field.value|escape|truncate:10:'':true|crmdate}
				{/if}
			{else}
				{$field.value|escape}
			{/if}
			{if $field.datatype == 'File'}</a>{/if}
			</span>
			</div>
		{/foreach}
		</div>
	{/foreach}
	</div>
	<div class="rightpanebody">
	{foreach from=$activities item=activity name=activityloop}
		<div class="activitybody" id="civicase-audit-body-{$smarty.foreach.activityloop.iteration}">
		{foreach from=$activity.rightpanebody item=field name=fieldloop}
			<div class="civicase-audit-{$field.label|lower|regex_replace:'/[^a-z0-9]+/':''}">
			<label>{$field.label|escape}</label>
			<span class="{$field.datatype}">{if $field.datatype == 'File'}<a href="{$field.value|escape}">{/if}
			{if $field.datatype == 'Date'}
				{if $field.includeTime}
					{$field.value|escape|replace:'T':' '|crmdate}
				{else}
					{$field.value|escape|truncate:10:'':true|crmdate}
				{/if}
			{else}
				{$field.value|escape}
			{/if}
			{if $field.datatype == 'File'}</a>{/if}
			</span>
			</div>
		{/foreach}
		</div>
	{/foreach}
	</div>
</td></tr></table>
</div>