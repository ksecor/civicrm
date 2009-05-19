<tr>
    <td> {$form.event_id.label}  <br />{$form.event_id.html} </td>
    <td> {$form.event_type.label}<br />{$form.event_type.html} </td>
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
    <td>{$form.participant_fee_level.label}<br />{$form.participant_fee_level.html}</td>
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

{literal}
<script type="text/javascript"> 
var eventUrl = "{/literal}{$dataURLEvent}{literal}";
var typeUrl  = "{/literal}{$dataURLEventType}{literal}";
var feeUrl   = "{/literal}{$dataURLEventFee}{literal}";

cj('#event_id').autocomplete( eventUrl, { width : 280, selectFirst : false
                            }).result( function(event, data, formatted) { cj( "#event_name_id" ).val( data[1] );
                            }).bind( 'click', function( ) { cj( "#event_name_id" ).val(''); });

cj('#event_type').autocomplete( typeUrl, { width : 180, selectFirst : false
                               }).result(function(event, data, formatted) { cj( "#event_type_id" ).val( data[1] );
                               }).bind( 'click', function( ) { cj( "#event_type_id" ).val(''); });

cj('#participant_fee_level').autocomplete( feeUrl, { width : 180, selectFirst : false
                                         }).result(function(event, data, formatted) { cj( "#participant_fee_id" ).val( data[1] );
                                         }).bind( 'click', function( ) { cj( "#participant_fee_id" ).val(''); });
</script>
{/literal}
