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
<div class="leftpane">
<ul>
{foreach from=$activities item=activity name=activityloop}
	<div class="activity{if $smarty.foreach.activityloop.first} selected{/if}" id="civicase-audit-activity-{$smarty.foreach.activityloop.iteration}">
	<li{if $activity.completed} class="completed"{/if}>
	<a href="javascript:selectActivity({$smarty.foreach.activityloop.iteration})">
	{foreach from=$activity.leftpane item=field name=fieldloop}
		<span class="{$field.label|lower|regex_replace:'/[^a-z0-9]+/':''} {$field.datatype}">
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
		</span>
	{/foreach}
	</a>
	</li>
	</div>
{/foreach}
</ul>
</div>
<div class="rightpane">
	<div class="rightpaneheader">
	{foreach from=$activities item=activity name=activityloop}
		<div class="activityheader" id="civicase-audit-header-{$smarty.foreach.activityloop.iteration}">
		<div class="auditmenu">
			<label>{ts}Actions{/ts}</label>
			<span class="editlink"><a target="something" href="{$activity.editurl}">{ts}Edit{/ts}</a></span>
		</div>	
		{foreach from=$activity.rightpaneheader item=field name=fieldloop}
			<div class="{$field.label|lower|regex_replace:'/[^a-z0-9]+/':''}">
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
			<div class="{$field.label|lower|regex_replace:'/[^a-z0-9]+/':''}">
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
</div>
</div>