{* View Case Activities *}
<table class="report">
{foreach from=$report.fields item=row name=report}
<tr>
    <td class="label">{$row.label}</td>
    {if $smarty.foreach.report.first AND ( $revisionURL OR $parentURL OR $currentURL )} {* Add a cell to first row with links to prior revision listing and Prompted by (parent) as appropriate *}
        <td>{$row.value}</td>
        <td style="padding-right: 50px; text-align: right;">
            {if $revisionURL}<a href="{$revisionURL}">&raquo; {ts}Prior revisions{/ts}</a><br />{/if}
            {if $currentURL}<a href="javascript:viewActivity({$currentURL});">&raquo; {ts}Current revision{/ts}</a><br />{/if}                   
            {if $parentURL}<a href="{$parentURL}">&raquo; {ts}Prompted by{/ts}</a>{/if}
        </td>
    {else}
        <td colspan="2">{$row.value}</td>
    {/if}
</tr>
{/foreach}
</table>
{if $cnt eq 2}
  <fieldset><legend>List Revisions</legend>
  {strip}
  <table>
      <tr class="columnheader">
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
function viewActivity( activityId ) {
    cj("#view-activity").show( );

    cj("#view-activity").dialog({
        title: "View Activity",
	    modal: true, 
	    width : 700,
            height : 450, 
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
</script>
{/literal}

<div id="view-activity">
     <div id="activity-content"></div>
</div>
