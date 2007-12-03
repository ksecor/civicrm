{* this template is used for adding/editing other (custom) activities. *}


{* added onload javascript for source contact*}
{if $source_contact_value and $admin }
   <script type="text/javascript">
       dojo.addOnLoad( function( ) {ldelim}
       dijit.byId( 'source_contact' ).setValue( "{$source_contact_value}", "{$source_contact_value}" )
       {rdelim} );
   </script>
{/if}

{* added onload javascript for target contact*}
{if $target_contact_value and $standalone }
   <script type="text/javascript">
       dojo.addOnLoad( function( ) {ldelim}
       dijit.byId( 'target_contact' ).setValue( "{$target_contact_value}", "{$target_contact_value}" )
       {rdelim} );
   </script>
{/if}

{* added onload javascript for assignee contact*}
{if $assignee_contact_value}
   <script type="text/javascript">
       dojo.addOnLoad( function( ) {ldelim}
       dijit.byId( 'assignee_contact' ).setValue( "{$assignee_contact_value}", "{$assignee_contact_value}" )
       {rdelim} );
   </script>
{/if}

{* added onload javascript for case subject*}
{if $subject_value}
   <script type="text/javascript">
       dojo.addOnLoad( function( ) {ldelim}
       dijit.byId( 'case_subject' ).setValue( "{$subject_value}", "{$subject_value}" )
       {rdelim} );
   </script>
{/if}

<div id="help">{$activityTypeDescription}</div>
<table class="no-border" >
  <tr>
     <td>
        <fieldset>
          <legend>
           {if $action eq 2}
              {ts}Edit{/ts} 
           {elseif $action eq 8}
              {ts}Delete{/ts}
           {elseif $action eq 4}
              {ts}View{/ts}
           {/if}
           {$activityTypeName}
          </legend>
         <table class="no-border">
           {if $action eq 1 or $action eq 2  or $action eq 4 }
             {if $standalone }
		<tr>
                   <td class="label">{$form.activity_type_id.label}</td><td>{$form.activity_type_id.html}</td>
                </tr>
             {/if}
             <tr>
                <td class="label">{$form.source_contact.label}</td>
		<td>
                   <div dojoType="dojo.data.ItemFileReadStore" jsId="contactStore" url="{$dataUrl}" class="tundra">
                       {if $admin }{$form.source_contact.html} {else} {$source_contact_value} {/if}
                   </div>
                </td>
             </tr>
             <tr>
                <td class="label">{$form.target_contact.label}</td>
		<td>
                   <div dojoType="dojo.data.ItemFileReadStore" jsId="contactStore" url="{$dataUrl}" class="tundra">
                       {if $standalone } {$form.target_contact.html} {else} {$target_contact_value} {/if}
                   </div>
                </td>
             </tr>
             <tr>
                <td class="label">{$form.assignee_contact.label}</td>
		<td>
                   <div dojoType="dojo.data.ItemFileReadStore" jsId="contactStore" url="{$dataUrl}" class="tundra">
                       {$form.assignee_contact.html}
                   </div>
                </td>
             </tr>
             <tr>
                <td class="label">{$form.case_subject.label}</td>
		<td>
                   <div dojoType="dojo.data.ItemFileReadStore" jsId="contactStore" url="{$dataUrl}" class="tundra">
                       {$form.case_subject.html}
                   </div>
                </td>
             </tr>
             <tr>
                <td class="label">{$form.subject.label}</td><td>{$form.subject.html}</td>
             </tr> 
             <tr>
                <td class="label">{$form.location.label}</td><td>{$form.location.html}</td>
             </tr> 
             <tr>
                <td class="label">{$form.activity_date_time.label}</td>
                <td>{$form.activity_date_time.html | crmDate }</br>
                    {if $action neq 4}
                      <span class="description">
                      {include file="CRM/common/calendar/desc.tpl" trigger=trigger_activity}
                      {include file="CRM/common/calendar/body.tpl" dateVar=activity_date_time startDate=currentYear 
                                      endDate=endYear offset=3 doTime=1 trigger=trigger_activity}
                      </span>
                   {/if}  
                </td>
             </tr> 
             <tr>
                <td class="label">{$form.duration_hours.label}</td><td>{$form.duration_hours.html},&nbsp;{$form.duration_minutes.html}</td>
             </tr> 
             <tr>
                <td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td>
             </tr> 
             <tr>
                <td class="label">{$form.details.label}</td><td>{$form.details.html|crmReplace:class:huge}</td>
             </tr> 
             <tr>
                <td colspan="2">
	           {if $action eq 4} 
                       {include file="CRM/Contact/Page/View/InlineCustomData.tpl"}
                   {else}
                       {include file="CRM/Contact/Page/View/CustomData.tpl" mainEditForm=1}
                   {/if} 
                </td>
             </tr> 
             <tr>
                <td colspan="2">&nbsp;</td>
             </tr> 
           {elseif $action eq 8}
             <tr>
                <td colspan="2">
                    <div class="status">{ts 1=$delName}Are you sure you want to delete "%1"?{/ts}</div>
                </td>
             </tr>  
           {/if}
             <tr>
                <td>&nbsp;</td><td>{$form.buttons.html}</td>
             </tr> 
         </table>   
      </fieldset> 
     </td> 
  </tr>
</table>

{if $action eq 4 }
  <div class="form-item">
  {if $subject_value}  
    <a href="{crmURL p='civicrm/contact/view/case'
     q="activity_id=`$activityID`&action=delete&reset=1&id=`$id`&cid=`$contactId`&context=`$context`&subType=`$activityID`&caseid=`$caseid`"}" ">{ts}  Detach Activity from Case {/ts}</a>
  {/if}

{/if }   