{* View Case Activities *}
{if $revs}
  <fieldset><legend>Prior Revisions - {$subject}</legend>
  {strip}
  <table style="width: 95%">
      <tr style="background-color: #B3D1FF; color: #000000; border: 1px solid #5A8FDB;">
         <th>{ts}Created By{/ts}</th>
         <th>{ts}Created On{/ts}</th>
         <th>&nbsp;</th>
      </tr>
       {foreach from=$result item=row}
      <tr class="{cycle values="odd-row,even-row"}">
         <td>{$row.name}</td>
         <td>{$row.date}</td>
         <td><a href = "javascript:viewRevision( {$row.id} );">{if $row.id != $latestRevisionID}View{else}View Current{/if}</a></td>
      </tr>
       {/foreach}
  </table>
  {/strip}
  </fieldset>
{else}
{if $report}
<table style="width: 95%">
{foreach from=$report.fields item=row name=report}
<tr{if ! $smarty.foreach.report.last} style="border-bottom: 1px solid #F6F6F6;"{/if}>
    <td class="label">{$row.label}</td>
    {if $smarty.foreach.report.first AND ( $activityID OR $parentID OR $latestRevisionID )} {* Add a cell to first row with links to prior revision listing and Prompted by (parent) as appropriate *}
        <td class="label">{$row.value}</td>
        <td style="padding-right: 50px; text-align: right;">
            {if $activityID}<a href="javascript:ListRevisions({$activityID});">&raquo; {ts}List revisions{/ts}</a><br />{/if}
            {if $latestRevisionID}<a href="javascript:viewRevision({$latestRevisionID});">&raquo; {ts}Current revision{/ts}</a><br />{/if}                   
            {if $parentID}<a href="javascript:viewRevision({$parentID});">&raquo; {ts}Prompted by{/ts}</a>{/if}
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
{else}
    <div class="messages status">{ts}This activity is not attached to any case. Please investigate.{/ts}</div>
{/if}
{/if}


{literal}
<script type="text/javascript">
function viewRevision( activityId ) {
      var cid= {/literal}"{$contactID}"{literal};
      var viewUrl = {/literal}"{crmURL p='civicrm/case/activity/view' h=0 q="snippet=4" }"{literal};
  	  cj("#activity-content").load( viewUrl + "&cid="+cid + "&aid=" + activityId);
}

function ListRevisions( activityId ) {
      var cid= {/literal}"{$contactID}"{literal};
      var viewUrl = {/literal}"{crmURL p='civicrm/case/activity/view' h=0 q="snippet=4" }"{literal};
  	  cj("#activity-content").load( viewUrl + "&cid=" + cid + "&aid=" + activityId + "&revs=1" );
}
</script>
{/literal}
