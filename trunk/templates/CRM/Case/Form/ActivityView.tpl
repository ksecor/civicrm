{* View Case Activities *}
{if $cnt eq 2}
<fieldset><legend>View Activity</legend>
{/if}
<table style="width: 95%">
{foreach from=$report.fields item=row name=report}
<tr{if ! $smarty.foreach.report.last} style="border-bottom: 1px solid #F6F6F6;"{/if}>
    <td class="label">{$row.label}</td>
    {if $smarty.foreach.report.first AND ( $revisionURL OR $parentID OR $currentRevisionID OR $originalID )} {* Add a cell to first row with links to prior revision listing and Prompted by (parent) as appropriate *}
        <td class="label">{$row.value}</td>
        <td style="padding-right: 50px; text-align: right;">
            {if $revisionURL}<a href="{$revisionURL}">&raquo; {ts}Prior revisions{/ts}</a><br />{/if}
            {if $originalID}<a href="javascript:viewActivity({$originalID}, 1);">&raquo; {ts}Prior revisions{/ts}</a><br />{/if} 
            {if $currentRevisionID}<a href="javascript:viewActivity({$currentRevisionID}, 1);">&raquo; {ts}Current revision{/ts}</a><br />{/if}                   
            {if $parentID}<a href="$javascript:viewActivity({$parentID}, 1);">&raquo; {ts}Prompted by{/ts}</a>{/if}
        </td>
    {else}
        <td colspan="2"{if $smarty.foreach.report.first} class="label"{/if}>{$row.value}</td>
    {/if}
</tr>
{/foreach}
{* Display custom field data for the activity. *}
{if $report.customGroups}
    {foreach from=$report.customGroups item=customGroup key=groupTitle name=custom}
        <tr style="background-color: #F6F6F6; color: #000000; border: 1px solid #5A8FDB; font-weight: bold">
            <td colspan="3">{$groupTitle}</td>
        </tr>
        {foreach from=$customGroup item=customField name=fields}
            <tr{if ! $smarty.foreach.fields.last} style="border-bottom: 1px solid #F6F6F6;"{/if}><td class="label">{$customField.label}</td><td>{$customField.value}</td></tr>
        {/foreach}
    {/foreach}
{/if}
</table>

{if $cnt eq 2}
</fieldset>
{/if}

{if $cnt eq 2}
  <fieldset><legend>List Revisions</legend>
  {strip}
  <table>
      <tr style="background-color: #B3D1FF; color: #000000; border: 1px solid #5A8FDB;">
         <th>{ts}Created By{/ts}</th>
         <th>{ts}Created On{/ts}</th>
         <th>&nbsp;</th>
      </tr>
       {foreach from=$result item=row}
      <tr class="{cycle values="odd-row,even-row"}">
         <td>{$row.name}</td>
         <td>{$row.date}</td>
         <td><a href = "javascript:viewActivity( {$row.id} );">View</a></td>
      </tr>
       {/foreach}
  </table>
  {/strip}
  </fieldset>
{/if}


{literal}
<script>
function viewActivity( activityId,  isPrior ) {
   if ( isPrior != 1 ) {
      cj("#view-activity").show( );

      cj("#view-activity").dialog({
         title: "View Activity",
	     modal: true, 
	     width: 700,
         height: 650,
         resizable: true, 
     overlay: { 
 	       opacity: 0.5, 
	       background: "black" 
	 },
     open:function() {
 		  cj(this).parents(".ui-dialog:first").find(".ui-dialog-titlebar-close").remove();
		  cj("#activity-content").html("");
		  var cid= {/literal}"{$contactID}"{literal};
          var viewUrl = {/literal}"{crmURL p='civicrm/case/activity/view' h=0 q="snippet=4" }"{literal};
		  cj("#activity-content").load( viewUrl + "&cid="+cid + "&aid=" + activityId);
	 },
	    
	 buttons: { 
     "Done": function() { 	    
		  cj(this).dialog("close"); 
		  cj(this).dialog("destroy"); 
	   }
     } 
   });
   }

   if ( isPrior == 1 ) {
      var cid= {/literal}"{$contactID}"{literal};
      var viewUrl = {/literal}"{crmURL p='civicrm/case/activity/view' h=0 q="snippet=4" }"{literal};
  	  cj("#activity-content").load( viewUrl + "&cid="+cid + "&aid=" + activityId);
   }
}
</script>
{/literal}

<div id="view-activity">
     <div id="activity-content"></div>
</div>
