 <tr>
    <td class="label">{$form.event_title.label}</td> 
{if $event_title_value}
<script type="text/javascript">
  dojo.addOnLoad( function( ) {ldelim}
    dojo.widget.byId( 'event_title' ).setAllValues( '{$event_title_value}', '{$event_title_value}' )
  {rdelim} );
</script>
{/if}
    <td>{$form.event_title.html}</td>

    <td colspan="2">{$form.event_type.label}
{if $event_type_value}
<script type="text/javascript">
  dojo.addOnLoad( function( ) {ldelim}
    dojo.widget.byId( 'event_type' ).setAllValues( '{$event_type_value}', '{$event_type_value}' )
  {rdelim} );
</script>
{/if}
    {$form.event_type.html}</td>

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
    <td class="label"><label>{ts}Participant Status{/ts}</label></td> 
    <td>
                <div class="listing-box" style="width:180px">
                    {foreach from=$form.participant_status_id item="participant_status_val"} 
                    <div class="{cycle values="odd-row,even-row"}">
                    {$participant_status_val.html}
                    </div>
                    {/foreach}
                </div>
    </td> <td><label>{ts}Participant Role{/ts}</label></td>
    <td>
                <div class="listing-box" style="width:180px">
                    {foreach from=$form.participant_role_id item="participant_role_id_val"}                     <div class="{cycle values="odd-row,even-row"}">
                    {$participant_role_id_val.html}
                    </div>
                    {/foreach}
                </div>
    </td>
  
 </tr> 
 <tr>
    <td class="label">{ts}{$form.participant_test.label}{/ts}</td> 
    <td colspan="3">{$form.participant_test.html}</td>
 </tr>
 <tr>
    <td colspan="4">
       {include file="CRM/Custom/Form/Search.tpl" groupTree=$participantGroupTree showHideLinks=false}
    </td>
 </tr>
