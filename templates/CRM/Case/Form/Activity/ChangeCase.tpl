{* added onload javascript for case subject*}
{if $caseId and $context neq 'standalone' and $action neq 4}
   <script type="text/javascript">
       dojo.addOnLoad( function( ) {ldelim}
       dijit.byId( 'case_id' ).setValue( "{$caseId}" )
       {rdelim} );
   </script>
{/if}
<table class="form-layout">
   <tr><td class="label" width="30%">{$form.case_id.label}</td>
       <td> <div dojoType="dojox.data.QueryReadStore" jsId="caseStore" url="{$caseUrl}" class="tundra">
                                    {$form.case_id.html}
           </div>
       </td>
   </tr>   
   <tr><td class="label" width="30%">{$form.case_type_id.label}</td><td>{$form.case_type_id.html}</td>   
   <tr><td class="label">{$form.is_resetTimeline.label}</td><td>{$form.is_resetTimeline.html}<br />
            <span class="description">{ts}Set new timeline for this Case.{/ts}</span></td></tr>

   <tr id="TimelineDate"><td class="label">{$form.timeline_date.label}</td><td>{$form.timeline_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_case_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=timeline_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_case_1}<br />
            <span class="description">{ts}Date when new timeline fr case will be start.{/ts}</span></td></tr> 

    <tr><td class="label">{$form.is_resetCaseroles.label}</td><td>{$form.is_resetCaseroles.html}<br />
            <span class="description">{ts}Reset case roles for this Case.{/ts}</span></td></tr>

  </table>

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_resetTimeline"
    trigger_value       =""
    target_element_id   ="TimelineDate" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}
