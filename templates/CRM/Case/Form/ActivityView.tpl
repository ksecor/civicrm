{* View Case Activities *} {* Uses inline styles since we have not figured out yet how to include our normal .css files. *}
{if $revs}
  <strong>{$subject}</strong> ({ts}all revisions{/ts})<br />
  {strip}
  <table style="width: 95%; border: 1px solid #CCCCCC;">
      <tr style="background-color: #F6F6F6; color: #000000; border: 1px solid #CCCCCC;">
         <th>{ts}Created By{/ts}</th>
         <th>{ts}Created On{/ts}</th>
         <th>&nbsp;</th>
      </tr>
       {foreach from=$result item=row}
      <tr {if $row.id EQ $latestRevisionID}style="font-weight: bold;"{/if}>
         <td>{$row.name}</td>
         <td>{$row.date|crmDate}</td>
         <td><a href="javascript:viewRevision( {$row.id} );" title="{ts}View this revision of the activity record.{/ts}">{if $row.id != $latestRevisionID}View Prior Revision{else}View Current Revision{/if}</a></td>
      </tr>
       {/foreach}
  </table>
  {/strip}
{else}
{if $report}
<table style="width: 95%">
{foreach from=$report.fields item=row name=report}
<tr{if ! $smarty.foreach.report.last} style="border-bottom: 1px solid #F6F6F6;"{/if}>
    <td class="label">{$row.label}</td>
    {if $smarty.foreach.report.first AND ( $activityID OR $parentID OR $latestRevisionID )} {* Add a cell to first row with links to prior revision listing and Prompted by (parent) as appropriate *}
        <td class="label">{$row.value}</td>
        <td style="padding-right: 50px; text-align: right; font-size: .9em;">
            {if $activityID}<a href="javascript:listRevisions({$activityID});">&raquo; {ts}List all revisions{/ts}</a><br />{ts}(this is the current revision){/ts}<br />{/if}
            {if $latestRevisionID}<a href="javascript:viewRevision({$latestRevisionID});">&raquo; {ts}View current revision{/ts}</a><br /><span style="color: red;">{ts}(this is not the current revision){/ts}</span><br />{/if}                   
            {if $parentID}<a href="javascript:viewRevision({$parentID});">&raquo; {ts}Prompted by{/ts}</a>{/if}
        </td>
    {else}
        <td colspan="2"{if $smarty.foreach.report.first} class="label"{/if}>{if $row.label eq 'Details'}{$row.value|nl2br}{else}{$row.value}{/if}</td>
    {/if}
</tr>
{/foreach}
{* Display custom field data for the activity. *}
{if $report.customGroups}
    {foreach from=$report.customGroups item=customGroup key=groupTitle name=custom}
        <tr style="background-color: #F6F6F6; color: #000000; border: 1px solid #CCCCCC; font-weight: bold">
            <td colspan="3">{$groupTitle}</td>
        </tr>
        {foreach from=$customGroup item=customField name=fields}
            <tr{if ! $smarty.foreach.fields.last} style="border-bottom: 1px solid #F6F6F6;"{/if}><td class="label">{$customField.label}</td><td>{$customField.value}</td></tr>
        {/foreach}
    {/foreach}
{/if}
</table>
{if $caseID}
    <div><a href="{crmURL p='civicrm/contact/view/case' q="reset=1&id=`$caseID`&cid=`$contactID`&action=view"}">{ts}&raquo; Manage Case{/ts}</a></div>
{/if}
{else}
    <div class="messages status">{ts}This activity might not be attached to any case. Please investigate.{/ts}</div>
{/if}
{/if}


{literal}
<script type="text/javascript">
function viewRevision( activityId ) {
      var cid= {/literal}"{$contactID}"{literal};
      var viewUrl = {/literal}"{crmURL p='civicrm/case/activity/view' h=0 q="snippet=4" }"{literal};
  	  cj("#activity-content").load( viewUrl + "&cid="+cid + "&aid=" + activityId);
}

function listRevisions( activityId ) {
      var cid= {/literal}"{$contactID}"{literal};
      var viewUrl = {/literal}"{crmURL p='civicrm/case/activity/view' h=0 q="snippet=4" }"{literal};
  	  cj("#activity-content").load( viewUrl + "&cid=" + cid + "&aid=" + activityId + "&revs=1" );
}
</script>
{/literal}
