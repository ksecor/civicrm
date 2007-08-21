 <tr>
    <td class="label">{$form.event_title.label}</td> 
{if $event_title_value}
<script type="text/javascript">
  dojo.addOnLoad( function( ) {ldelim}
    dijit.byId( 'event_title' ).setValue( '{$event_title_value}', '{$event_title_value}' )
  {rdelim} );
</script>
{/if}
    
<td>{$form.event_title.html}
   <div dojoType="dojo.data.ItemFileReadStore" jsId="eventStore" url="{$dataURLEvent}" class ="tundra">
   </div>
</td>

    <td class="label">{$form.event_type.label}</td> 
{if $event_type_value}
<script type="text/javascript">
  dojo.addOnLoad( function( ) {ldelim}
    dijit.byId( 'event_type' ).setValue( '{$event_type_value}', '{$event_type_value}' )
  {rdelim} );
</script>
{/if}

<div dojoType="dojo.data.ItemFileReadStore" jsId="eventTypeStore" url="{$dataURLEventType}" class ="tundra">
</div>
    <td>{$form.event_type.html}</td>

 </tr>     
 <tr> 
    <td class="label"> {$form.event_start_date_low.label} </td>
    <td>
       {$form.event_start_date_low.html}&nbsp;<br />
       {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_event_1}
       {include file="CRM/common/calendar/body.tpl" dateVar=event_start_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_event_1}
    </td>
    <td colspan="2"> 
       {$form.event_end_date_high.label} {$form.event_end_date_high.html}<br />
             &nbsp; &nbsp; {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_event_2}
       {include file="CRM/common/calendar/body.tpl" dateVar=event_end_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_event_2}
    </td> 
 </tr>

 <tr>
    <td class="label">{ts}{$form.participant_status_id.label}{/ts}</td> 
    <td>{$form.participant_status_id.html}</td>
    <td class="label">{$form.participant_test.html}</td>
    <td>{$form.participant_test.label}</td>
 </tr> 
 <tr>
    <td class="label">{ts}{$form.participant_role_id.label}{/ts}</td> 
    <td colspan="3">{$form.participant_role_id.html}</td>
 </tr> 
 <tr>
    <td colspan="4">
       {include file="CRM/Custom/Form/Search.tpl" groupTree=$participantGroupTree showHideLinks=false}
    </td>
 </tr>
