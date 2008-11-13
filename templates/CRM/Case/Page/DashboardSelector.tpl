{literal}
<style>
  #crm-container a.sort-ascending, #crm-container a.sort-descending, #crm-container a.sort-none 
  {
    white-space:normal;
  }
</style>
{/literal}

{capture assign=expandIconURL}<img src="{$config->resourceBase}i/TreePlus.gif" alt="{ts}open section{/ts}"/>{/capture}
{ts 1=$expandIconURL}Click %1 to view case details.{/ts}

{strip}
<table class="selector">
  <tr class="columnheader">
    <th></th>
    <th>{ts}Client{/ts}</th>
    <th>{ts}Status{/ts}</th>
    <th>{ts}Type{/ts}</th>
    <th>{ts}My Role{/ts}</th>

    <th>Activity Date</th>
    <th>Activity Type</th>

    <th></th>
  </tr>

  {counter start=0 skip=1 print=false}
  {foreach from=$rows item=row}
  {cycle values="odd-row,even-row" assign=rowClass}

  <tr id='{$list}Rowid{$row.case_id}' class='{$rowClass} {if $row.case_status_id eq 'Resolved' } disabled{/if}'>
	<td>
        &nbsp;{$row.contact_type_icon}<br />
        <span id="{$list}{$row.case_id}_show">
	    <a href="#" onclick="show('{$list}CaseDetails{$row.case_id}', 'table-row');
                             {$list}CaseDetails('{$row.case_id}','{$row.contact_id}'); 
                             hide('{$list}{$row.case_id}_show');
                             show('minus{$list}{$row.case_id}_hide');
                             show('{$list}{$row.case_id}_hide','table-row');
                             return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a>
	</span>
	<span id="minus{$list}{$row.case_id}_hide">
	    <a href="#" onclick="hide('{$list}CaseDetails{$row.case_id}');
                             show('{$list}{$row.case_id}_show', 'table-row');
                             hide('{$list}{$row.case_id}_hide');
                             hide('minus{$list}{$row.case_id}_hide');
                             return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a>
	</td>

    <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
    <td>{$row.case_status}</td>
    <td>{$row.case_type}</td>
    <td>{$row.case_role}</td>

    {if $list eq 'upcoming'}
    <td class="right">{$row.case_scheduled_activity_date|crmDate}</td>
    <td>{$row.case_scheduled_activity_type}</td>
    {elseif $list eq 'recent'}
    <td class="right">{$row.case_recent_activity_date|crmDate}</td>
    <td>{$row.case_recent_activity_type}</td>
    {/if}

    <td>{$row.action}</td>
   </tr>
   <tr id="{$list}{$row.case_id}_hide" class='{$rowClass}'>
     <td>
     </td>
     <td colspan="7" width="99%" class="enclosingNested">
        <div id="{$list}CaseDetails{$row.case_id}"></div>
     </td>
   </tr>
 <script type="text/javascript">
     hide('{$list}{$row.case_id}_hide');
     hide('minus{$list}{$row.case_id}_hide');
 </script>
  {/foreach}

    {* Dashboard only lists 10 most recent casess. *}
    {if $context EQ 'dashboard' and $limit and $pager->_totalItems GT $limit }
      <tr class="even-row">
        <td colspan="10"><a href="{crmURL p='civicrm/case/search' q='reset=1'}">&raquo; {ts}Find more cases{/ts}... </a></td>
      </tr>
    {/if}

</table>
{/strip}

{* Build case details*}
{literal}
<script type="text/javascript">

function {/literal}{$list}{literal}CaseDetails( caseId, contactId )
{

  var dataUrl = {/literal}"{crmURL p='civicrm/case/details' h=0 q='snippet=4&caseId='}{literal}" + caseId;

  dataUrl = dataUrl + '&cid=' + contactId;

    var result = dojo.xhrGet({
        url: dataUrl,
        handleAs: "text",
        timeout: 5000, //Time in milliseconds
        handle: function(response, ioArgs){
                if(response instanceof Error){
                        if(response.dojoType == "cancel"){
                                //The request was canceled by some other JavaScript code.
                                console.debug("Request canceled.");
                        }else if(response.dojoType == "timeout"){
                                //The request took over 5 seconds to complete.
                                console.debug("Request timed out.");
                        }else{
                                //Some other error happened.
                                console.error(response);
                        }
                } else {
		   // on success
                   dojo.byId( '{/literal}{$list}{literal}CaseDetails' + caseId).innerHTML = response;
	       }
        }
     });


}
</script>
{/literal}	