{* Step 1 of New Event Wizard, and Edit Event Info form. *} 
{if $cdType} 
	{include file="CRM/Custom/Form/CustomData.tpl"} 
{else} 
	{assign var=eventID value=$id}
	<div class="form-item">
        <div class="crm-submit-buttons">
            {$form.buttons.html}
        </div>
	<fieldset>
	<table class="form-layout-compressed">
		{if $form.template_title}
			<tr>
				<td class="label">{$form.template_title.label}</td>
				<td>{$form.template_title.html}</td>
			</tr>
		{/if}
		{if $form.template_id}
			<tr>
				<td class="label">{$form.template_id.label}</td>
				<td>{$form.template_id.html}</td>
			</tr>
		{/if}
		<tr>
			<td class="label">{$form.event_type_id.label}</td>
			<td>{$form.event_type_id.html}<br />
			<span class="description">{ts}After selecting an Event Type, this page will display any custom event fields for that type.{/ts}</td>
		</tr>
		<tr>
			<td class="label">{$form.default_role_id.label}</td>
			<td>{$form.default_role_id.html}<br />
			<span class="description">{ts}The Role you select here is automatically assigned to people when they register online for this event (usually the default 'Attendee' role).{/ts}
			{help id="id-participant-role"}</td>
		</tr>
		<tr>
			<td class="label">{$form.participant_listing_id.label}</td>
			<td>{$form.participant_listing_id.html}<br />
			<span class="description"> {ts}To allow users to see a listing of participants, set this field to 'Name' (list names only), or 'Name and Email' (list names and emails).{/ts} 
			{help id="id-listing"} </span></td>
		</tr>
		<tr>
			<td class="label">{$form.title.label}</td>
			<td>{$form.title.html}<br />
			<span class="description"> {ts}Please use only alphanumeric, spaces, hyphens and dashes for event names.{/ts} 
			</span></td>
		</tr>
		<tr>
			<td class="label">{$form.summary.label}</td>
			<td>{$form.summary.html}</td>
		</tr>
		<tr>
			<td class="label">{$form.description.label}</td>
			<td>{$form.description.html}</td>
		</tr>
		{if !$isTemplate}
			<tr>
				<td class="label">{$form.start_date.label}</td>
				<td>{$form.start_date.html}</br>
				<span class="description">
				{include file="CRM/common/calendar/desc.tpl" trigger=trigger_event_1 doTime=1} 
				{include file="CRM/common/calendar/body.tpl" dateVar=start_date startDate=currentYear endDate=endYear offset=10 doTime=1 trigger=trigger_event_1 ampm=1}</span><//td>
			</tr>
			<tr>
				<td class="label">{$form.end_date.label}</td>
				<td>{$form.end_date.html}</br>
				<span class="description">
				{include file="CRM/common/calendar/desc.tpl" trigger=trigger_event_2 doTime=1} 
				{include file="CRM/common/calendar/body.tpl" dateVar=end_date offset=10 doTime=1 trigger=trigger_event_2 ampm=1}
				</span></td>
			</tr>
		{/if}
		<tr>
			<td class="label">{$form.max_participants.label}</td>
			<td>{$form.max_participants.html|crmReplace:class:four} {help id="id-max_participants"}</td>
		</tr>
        <tr id="id-waitlist">
            <td class="label">{$form.has_waitlist.label}</td>
            <td>{$form.has_waitlist.html} {help id="id-has_waitlist"}</td>
        </tr>
		<tr id="id-event_full">
			<td class="label">{$form.event_full_text.label}<br />{help id="id-event_full_text"}</td>
			<td>{$form.event_full_text.html}</td>
		</tr>
		<tr id="id-waitlist-text">
			<td class="label">{$form.waitlist_text.label}<br />{help id="id-help-waitlist_text"}</td>
			<td>{$form.waitlist_text.html}</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>{$form.is_map.html} {$form.is_map.label} {help id="id-is_map"}</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>{$form.is_public.html} {$form.is_public.label}<br />
			<span class="description">{ts}Include this event in iCalendar feeds?{/ts}</span></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>{$form.is_active.html} {$form.is_active.label}</td>
		</tr>

		{if $eventID}
		<tr>
			<td>&nbsp;</td>
			<td class="description">
			{if $config->userFramework EQ 'Drupal'}
				{ts}When this Event is active, create links to the Event Information page by copying and pasting the following URL:{/ts}<br />
				<strong>{crmURL a=true p='civicrm/event/info' q="reset=1&id=`$eventID`"}</strong> 
			{elseif $config->userFramework EQ 'Joomla'}
				{ts 1=$eventID}When this Event is active, create front-end links to the Event Information page using the Menu Manager. Select <strong>Event Info Page</strong> and enter <strong>%1</strong> for the Event ID.{/ts} 
			{/if}
			</td>
		</tr>
		{/if}
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<div id="customData"></div>
	{*include custom data js file*}
	{include file="CRM/common/customData.tpl"}	
	{literal}
		<script type="text/javascript">
			cj(document).ready(function() {
				{/literal}
				buildCustomData( '{$customDataType}' );
				{if $customDataSubType}
					buildCustomData( '{$customDataType}', {$customDataSubType} );
				{/if}
				{literal}
			});
		</script>
	{/literal}
	</fieldset>     
        <div class="crm-submit-buttons">
            {$form.buttons.html}
        </div>
	</div>
    {include file="CRM/common/showHide.tpl" elemType="table-row"}
{/if}


