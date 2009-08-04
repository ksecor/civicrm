{* this template is used for adding/editing activities for a case. *}
{if $cdType }
   {include file="CRM/Custom/Form/CustomData.tpl"}
{else}
    {if $action neq 8 and $action  neq 32768 }

{* added onload javascript for source contact*}
{literal}
<script type="text/javascript">
var assignee_contact = '';

{/literal}

{if $assigneeContactCount}
{foreach from=$assignee_contact key=id item=name}
     {literal} assignee_contact += '{"name":"'+{/literal}"{$name}"{literal}+'","id":"'+{/literal}"{$id}"{literal}+'"},';{/literal}
{/foreach}
{literal} eval( 'assignee_contact = [' + assignee_contact + ']'); {/literal}
{/if}
{literal}

cj(document).ready( function( ) {
{/literal}
{if $source_contact and $admin and $action neq 4} 
{literal} cj( '#source_contact_id' ).val( "{/literal}{$source_contact}{literal}");{/literal}
{/if}
{literal}

eval( 'tokenClass = { tokenList: "token-input-list-facebook", token: "token-input-token-facebook", tokenDelete: "token-input-delete-token-facebook", selectedToken: "token-input-selected-token-facebook", highlightedToken: "token-input-highlighted-token-facebook", dropdown: "token-input-dropdown-facebook", dropdownItem: "token-input-dropdown-item-facebook", dropdownItem2: "token-input-dropdown-item2-facebook", selectedDropdownItem: "token-input-selected-dropdown-item-facebook", inputToken: "token-input-input-token-facebook" } ');

var sourceDataUrl = "{/literal}{$dataUrl}{literal}";

cj( "#assignee_contact_id").tokenInput( sourceDataUrl, { prePopulate: assignee_contact, classes: tokenClass });

cj( "#source_contact_id").autocomplete( sourceDataUrl, { width : 180, selectFirst : false
                            }).result( function(event, data, formatted) { cj( "#source_contact_qid" ).val( data[1] );
                            }).bind( 'click', function( ) { cj( "#source_contact_qid" ).val(''); });
});
</script>
{/literal}

    {/if}

    <fieldset>
        <legend>
           {if $action eq 8}
              {ts}Delete{/ts}
           {elseif $action eq 4}
              {ts}View{/ts}
           {elseif $action eq 32768}
              {ts}Restore{/ts}
           {/if}
           {$activityTypeName}
        </legend>
        <table class="form-layout">
           {if $action eq 8 or $action eq 32768 }
            <div class="messages status"> 
              <dl> 
                 <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt> 
                 <dd> 
                 {if $action eq 8}
                    {ts 1=$activityTypeName}Click Delete to move this &quot;%1&quot; activity to the Trash.{/ts}
                 {else}
                    {ts 1=$activityTypeName}Click Restore to retrieve this &quot;%1&quot; activity from the Trash.{/ts}
                 {/if}  
                 </dd> 
              </dl> 
            </div> 
           {else}
            {if $activityTypeDescription }
           <tr>
              <div id="help">{$activityTypeDescription}</div>
           </tr>
            {/if}
           <tr>
              <td class="label font-size12pt">{ts}Client{/ts}</td>
              <td class="view-value font-size12pt bold">{$client_name|escape}</td>
           </tr>
           <tr>
              <td class="label">{ts}Activity Type{/ts}</td>
              <td class="view-value bold">{$activityTypeName|escape}</td>
           </tr>
           <tr>
              <td class="label">{$form.source_contact_id.label}</td>
              <td class="view-value"> {if $admin}{$form.source_contact_id.html}{/if}</td>
            </tr>
            <tr>
                <td class="label">{ts}Assigned To {/ts}</td>
                <td>{$form.assignee_contact_id.html}                   
                    {edit}<span class="description">
                           {ts}You can optionally assign this activity to someone.{/ts}<br />
                           {ts}A copy of this activity will be emailed to each Assignee.{/ts}</span>
                    {/edit}
                </td>
            </tr>

            {* Include special processing fields if any are defined for this activity type (e.g. Change Case Status / Change Case Type). *}
            {if $activityTypeFile}
                {include file="CRM/Case/Form/Activity/$activityTypeFile.tpl"}
            {/if}
	    {if $activityTypeFile neq 'ChangeCaseStartDate'}
            <tr>
              <td class="label">{$form.subject.label}</td><td class="view-value">{$form.subject.html}</td>
            </tr>
	    {/if}
           <tr>
              <td class="label">{$form.medium_id.label}</td>
              <td class="view-value">{$form.medium_id.html}&nbsp;&nbsp;&nbsp;{$form.location.label} &nbsp;{$form.location.html}</td>
           </tr> 
           <tr>
              <td class="label">{$form.activity_date_time.label}</td>
              <td class="view-value">{$form.activity_date_time.html | crmDate }
                  {if $action neq 4}
                        {include file="CRM/common/calendar/desc.tpl" trigger=trigger_activity_1 doTime=1}
                        {include file="CRM/common/calendar/body.tpl" dateVar=activity_date_time startDate=currentYear 
                                endDate=endYear offset=10 doTime=1 trigger=trigger_activity_1 ampm=1}
                  {/if}  
              </td>
           </tr>
           <tr>
              <td colspan="2"><div id="customData"></div></td>
           </tr>
           <tr>
              <td class="label">{$form.details.label}</td><td class="view-value">{$form.details.html|crmReplace:class:huge}</td>
           </tr>
           <tr>
              <td colspan="2">{include file="CRM/Form/attachment.tpl"}</td>
           </tr>
           {if $searchRows} {* We've got case role rows to display for "Send Copy To" feature *}
            <tr>
                <td colspan="2">
                    <div id="sendcopy_show" class="section-hidden section-hidden-border">
                        <a href="#" onclick="hide('sendcopy_show'); show('sendcopy'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open section"/></a><label>{ts}Send a Copy{/ts}</label><br />
                    </div>

                    <div id="sendcopy" class="section-shown">
                    <fieldset><legend><a href="#" onclick="hide('sendcopy'); show('sendcopy_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close section"/></a>{ts}Send a Copy{/ts}</legend>
                    <div class="description">{ts}Email a complete copy of this activity record to other people involved with the case. Click the top left box to select all.{/ts}</div>
                   {strip}
                   <table>
                      <tr class="columnheader">
                          <th>{$form.toggleSelect.html}&nbsp;</th>
                          <th>{ts}Case Role{/ts}</th>
                          <th>{ts}Name{/ts}</th>
                          <th>{ts}Email{/ts}</th>
                       </tr>
                       {foreach from=$searchRows item=row key=id}
                       <tr class="{cycle values="odd-row,even-row"}">
                           <td>{$form.contact_check[$id].html}</td>
                           <td>{$row.role}</td>
                           <td>{$row.display_name}</td>
                           <td>{$row.email}</td>
                       </tr>
                       {/foreach}
                   </table>
                   {/strip}
                  </fieldset>
                  </div>
                </td>
            </tr>
            {/if}
           <tr>
              <td colspan="2">
                <div id="follow-up_show" class="section-hidden section-hidden-border">
                 <a href="#" onclick="hide('follow-up_show'); show('follow-up'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open section"/></a><label>{ts}Schedule Follow-up{/ts}</label><br />
                </div>

                <div id="follow-up" class="section-shown">
                <fieldset><legend><a href="#" onclick="hide('follow-up'); show('follow-up_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close section"/></a>{ts}Schedule Follow-up{/ts}</legend>
                    <table class="form-layout-compressed">
                        <tr><td class="label">{ts}Schedule Follow-up Activity{/ts}</td>
                            <td>{$form.followup_activity_type_id.html}&nbsp;{$form.interval.label}&nbsp;{$form.interval.html}&nbsp;{$form.interval_unit.html}</td>
                        </tr>
                        <tr>
                           <td class="label">{$form.followup_activity_subject.label}</td>
                           <td>{$form.followup_activity_subject.html}</td>
                        </tr>
                    </table>
                </fieldset>
                </div>
              </td>
           </tr>
           <tr>
              <td class="label">{$form.duration.label}</td>
              <td class="view-value">
                {$form.duration.html}
                 <span class="description">{ts}Total time spent on this activity (in minutes).{/ts}
              </td>
           </tr> 
           <tr>
              <td class="label">{$form.status_id.label}</td><td class="view-value">{$form.status_id.html}</td>
           </tr>
	   <tr>
              <td class="label">{$form.priority_id.label}</td><td class="view-value">{$form.priority_id.html}</td>
           </tr>
           {/if}
           <tr>
              <td>&nbsp;</td><td class="buttons">{$form.buttons.html}</td>
            </tr>
        </table>
    </fieldset>

    {if $action eq 1 or $action eq 2}
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
    {/if}

    {if $action neq 8 and $action neq 32768} 
        <script type="text/javascript">
            {if $searchRows}
                hide('sendcopy');
                show('sendcopy_show');
            {/if}

            hide('follow-up');
            show('follow-up_show');

        </script>
    {/if}
{/if } {* end of main if block*}

</script>
   {literal}
    <script type="text/javascript">
     function verify( ) {

         var d = new Date();
         var currentDateTime = d.getTime();
         d.setFullYear(cj("select#activity_date_time\\[Y\\]").val());
         d.setMonth(cj("select#activity_date_time\\[M\\]").val() - 1);
         d.setDate(cj("select#activity_date_time\\[d\\]").val());
         var hours = cj("select#activity_date_time\\[h\\]").val();
         var ampm = cj("select#activity_date_time\\[A\\]").val();
         if (ampm == "PM") {
             // force arithmetic instead of string concatenation
             hours = hours*1 + 12;
         }
         d.setHours(hours);
         d.setMinutes(cj("select#activity_date_time\\[i\\]").val());

         var activity_date_time = d.getTime();

	  var activityStatusId = cj('#status_id').val();

	  if ( activityStatusId == 2 && currentDateTime < activity_date_time ) {
	       var okMessage = confirm( 'Are you sure? This is a COMPLETED activity with the DATE in the FUTURE. Click Cancel to change the date / status. Otherwise, click OK to save.' );    
               if (!okMessage ) {
                    return false;
	       }
	  } else if ( activity_date_time && activityStatusId == 1 && currentDateTime >= activity_date_time ) {
	       var ok = confirm( 'Are you sure? This is a SCHEDULED activity with the DATE in the PAST. Click Cancel to change the date / status. Otherwise, click OK to save.' );    
               if (!ok ) {
                    return false;
	       }
          }
     }
</script>
{/literal}