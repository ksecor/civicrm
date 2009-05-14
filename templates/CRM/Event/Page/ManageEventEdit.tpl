<div id="help">
    {capture assign=docLink}{docURL page="Manage Events" text="CiviEvent Administration Documentation"}{/capture}
    {ts 1=$docLink}You can update the features and content for this event from here. Refer to the %1 for more information.{/ts}
</div>
{ts}Participants{/ts}: <a href="{$findParticipants.urlCounted}" title="{ts}Find participants with counted statuses{/ts}">{$findParticipants.statusCounted}</a>, <a href="{$findParticipants.urlNotCounted}" title="{ts}Find participants with NOT counted statuses{/ts}">{$findParticipants.statusNotCounted}</a>
<table class="report"> 
<tr>
    <td class="nowrap"><a href="{crmURL q="reset=1&action=update&id=`$id`&subPage=EventInfo"}" id="idEventInformationandSettings">&raquo; {ts}Event Information and Settings{/ts}</a></td>
    <td>{ts}Set event title, type (conference, performance etc.), description, start and end dates, maximum number of participants, and activate the event. Enable the public participant listing feature.{/ts}</td>
</tr>
<tr>
    <td class="nowrap"><a href="{crmURL q="reset=1&action=update&id=`$id`&subPage=Location"}" id="idLocation">&raquo; {ts}Event Location{/ts}</a></td>
    <td>{ts}Set event location and event contact information (email and phone).{/ts}</td>
</tr>
<tr>
    <td class="nowrap"><a href="{crmURL q="reset=1&action=update&id=`$id`&subPage=Fee"}" id="idFee">&raquo; {ts}Event Fees{/ts}</a></td>
     <td>{ts}Determine if the event is free or paid. For paid events, set the payment processor, fee level(s) and discounts. Give online registrants the option to 'pay later' (e.g. mail in a check, call in a credit card, etc.).{/ts}</td>
</tr>
<tr>
    <td class="nowrap"><a href="{crmURL q="reset=1&action=update&id=`$id`&subPage=Registration"}" id="idRegistration">&raquo; {ts}Online Registration{/ts}</a></td>
    <td>{ts}Determine whether an online registration page is available. If so, configure registration, confirmation and thank you page elements and confirmation email details.{/ts}</td>
</tr>
<tr>
<td class="nowrap"><a href="{crmURL q="reset=1&action=update&id=`$id`&subPage=Friend"}" id="idFriend">&raquo; {ts}Tell a Friend{/ts}</a></td>
    <td>{ts}Make it easy for participants to spread the word about this event to friends and colleagues.{/ts}</td>
</tr>
<tr>

<tr>
{if $participantListingURL}
    <td class="nowrap"><a href="{$participantListingURL}" id="idParticipantListing">&raquo; {ts}Public Participant Listing{/ts}</a></td>
    <td>{ts 1=$participantListingURL}The following URL will display a list of registered participants for this event {if $config->userFramework EQ 'Drupal'} to users whose role includes "view event participants" permission{/if}: <a href="%1">%1</a>{/ts}</td>
{else}
    <td class="nowrap">&raquo; {ts}Public Participant Listing{/ts}</td>
    <td>{ts}Participant Listing is not enabled for this event. You can enable it from{/ts} <a href="{crmURL q="reset=1&action=update&id=`$id`&subPage=EventInfo"}">{ts}Event Information and Settings{/ts}</a>.
{/if}
</tr>

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/event/info' q="reset=1&id=`$id`"}" id="idDisplayEvent">&raquo; {ts}View Event Info{/ts}</a></td>
    <td>{ts}View the Event Information page as it will be displayed to site visitors.{/ts}</td>
</tr>

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/event/register' q="reset=1&action=preview&id=`$id`"}" id="idTest-drive">&raquo; {ts}Test-drive Registration{/ts}</a></td>
    <td>{ts}Test-drive the entire online registration process - including custom fields, confirmation, thank-you page, and receipting. Fee payment transactions will be directed to your payment processor's test server. <strong>No live financial transactions will be submitted. However, a contact record will be created or updated and participant and contribution records will be saved to the database. Use obvious test contact names so you can review and delete these records as needed.</strong>{/ts}</td>
</tr>
{if $is_active}
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/event/register' q="reset=1&id=`$id`"}" id="idLive">&raquo; {ts}Live Registration{/ts}</a></td>
    <td>{ts}Review your customized <strong>LIVE</strong> online event registration page here. Use the following URL in links and buttons on any website to send visitors to this live page{/ts}:<br />
        <strong>{crmURL a=true p='civicrm/event/register' q="reset=1&id=`$id`"}</strong>
    </td>
</tr>
{/if}

</table>
