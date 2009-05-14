{* Block displays key event info for Registration Confirm and Thankyou pages *}
<table class="form-layout">
  <tr>
    <td colspan="2">
    {if $context EQ 'ThankYou'} {* Provide link back to event info page from Thank-you page *}
        <a href="{crmURL p='civicrm/event/info' q="reset=1&id=`$event.id`"}"title="{ts}View complete event information.{/ts}"><strong>{$event.event_title}</strong></a>
    {else}
        <strong>{$event.event_title}</strong>
    {/if}
    </td>
  </tr>
  <tr><td><label>{ts}When{/ts}</label></td>
      <td width="90%">
        {$event.event_start_date|crmDate}
        {if $event.event_end_date}
            &nbsp; {ts}through{/ts} &nbsp;
            {* Only show end time if end date = start date *}
            {if $event.event_end_date|date_format:"%Y%m%d" == $event.event_start_date|date_format:"%Y%m%d"}
                {$event.event_end_date|date_format:"%I:%M %p"}
            {else}
                {$event.event_end_date|crmDate}
            {/if}
        {/if}
      </td>
  </tr>

  {if $isShowLocation}
    {if $location.1.name || $location.1.address}
      <tr><td><label>{ts}Location{/ts}</label></td>
          <td>
            {if $location.1.name}{$location.1.name}<br />{/if}
              {$location.1.address.display|nl2br}
            {if ( $event.is_map && $config->mapAPIKey && ( is_numeric($location.1.address.geo_code_1)  || ( $config->mapGeoCoding && $location.1.address.city AND $location.1.address.state_province ) ) ) }
              <br/><a href="{crmURL p='civicrm/contact/map/event' q="reset=1&eid=`$event.id`"}" title="{ts}Map this Address{/ts}">{ts}Map this Location{/ts}</a>
            {/if}
          </td>
      </tr>
    {/if}
  {/if}{*End of isShowLocation condition*}
  
  {if $location.1.phone.1.phone || $location.1.email.1.email}
    <tr><td><label>{ts}Contact{/ts}</label></td>
        <td>
        {* loop on any phones and emails for this event *}
           {foreach from=$location.1.phone item=phone}
             {if $phone.phone}
                {if $phone.phone_type}{$phone.phone_type_display}{else}{ts}Phone{/ts}{/if}: {$phone.phone}
                <br />
            {/if}
           {/foreach}

           {foreach from=$location.1.email item=email}
              {if $email.email}
                {ts}Email:{/ts} <a href="mailto:{$email.email}">{$email.email}</a>
              {/if}
            {/foreach}
        </td>
    </tr>
   {/if}
</table>