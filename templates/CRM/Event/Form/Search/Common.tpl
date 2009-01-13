<tr>
    <td>{$form.event_id.label}
    {if $event_id_value}
    <script type="text/javascript">
        dojo.addOnLoad( function( ) {ldelim}
        dijit.byId( 'event_id' ).setValue( "{$event_id_value}")
        {rdelim} );
    </script>
    {/if}
    <br />
    <div dojoType="dojox.data.QueryReadStore" jsId="eventStore" url="{$dataURLEvent}" class="tundra">
    {$form.event_id.html}
    </div>
    </td>
    <td>{$form.event_type.label}
    {if $event_type_value}
    <script type="text/javascript">
        dojo.addOnLoad( function( ) {ldelim}
        dijit.byId( 'event_type' ).setValue( "{$event_type_value}")
        {rdelim} );
    </script>
    {/if}
    <br>
    <div dojoType="dojox.data.QueryReadStore" jsId="eventTypeStore" url="{$dataURLEventType}" align="left" class="tundra">
    {$form.event_type.html}
    </div>
    </td>
</tr>     
 
<tr> 
    <td>  
       {$form.event_start_date_low.label}<br />{$form.event_start_date_low.html}&nbsp;
       {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_event_1}
       {include file="CRM/common/calendar/body.tpl" dateVar=event_start_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_event_1}
    </td>
    <td> 
       {$form.event_end_date_high.label}
       <br />
       {$form.event_end_date_high.html}&nbsp;
       {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_event_2}
       {include file="CRM/common/calendar/body.tpl" dateVar=event_end_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_event_2}
    </td> 
</tr>

<tr>
    <td><label>{ts}Participant Status{/ts}</label> 
    <br />
      <div class="listing-box" style="width: auto; height: 120px">
       {foreach from=$form.participant_status_id item="participant_status_val"} 
        <div class="{cycle values="odd-row,even-row"}">
       {$participant_status_val.html}
        </div>
       {/foreach}
      </div>
    </td>
    <td><label>{ts}Participant Role{/ts}</label>
    <br />
      <div class="listing-box" style="width: auto; height: 120px">
       {foreach from=$form.participant_role_id item="participant_role_id_val"}
        <div class="{cycle values="odd-row,even-row"}">
                {$participant_role_id_val.html}
        </div>
      {/foreach}
      </div><br />
    </td>
  
</tr> 
<tr>
    <td>{$form.participant_test.html}&nbsp;{$form.participant_test.label}</td> 
    <td>{$form.participant_pay_later.html}&nbsp;{$form.participant_pay_later.label}</td> 
</tr>
<tr>
    <td>{$form.participant_fee_level.label}<br /> 
   {if $participant_fee_level_value}
    <script type="text/javascript">
	dojo.addOnLoad( function( ) {ldelim}
        dijit.byId( 'participant_fee_level' ).setValue( "{$participant_fee_level_value}")
        {rdelim} );
    </script>
    {/if}

    <div dojoType="dojox.data.QueryReadStore" jsId="eventFeeStore" url="{$dataURLEventFee}" class="tundra">
    {$form.participant_fee_level.html}
    </div> 
    </td>
     <td><label>{ts}Fee Amount{/ts}</label><br />
     	{$form.participant_fee_amount_low.label} &nbsp; {$form.participant_fee_amount_low.html} &nbsp;&nbsp; 
	{$form.participant_fee_amount_high.label} &nbsp; {$form.participant_fee_amount_high.html}
     </td> 
</tr>
{if $participantGroupTree }
<tr>
    <td colspan="4">
       {include file="CRM/Custom/Form/Search.tpl" groupTree=$participantGroupTree showHideLinks=false}
    </td>
</tr>
{/if}