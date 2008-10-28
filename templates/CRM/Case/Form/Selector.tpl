{literal}
<style>
#crm-container a.sort-ascending, #crm-container a.sort-descending, #crm-container a.sort-none 
{
white-space:normal;
}
</style>
{/literal}

{if $context EQ 'Search'}
    {include file="CRM/common/pager.tpl" location="top"}
{/if}

{capture assign=iconURL}<img src="{$config->resourceBase}i/TreePlus.gif" alt="{ts}open section{/ts}"/>{/capture}
{ts 1=$iconURL}Click %1 to view case details.{/ts}
{strip}
<table class="selector">
  <tr class="columnheader">
{if ! $single and $context eq 'Search' }
  <th scope="col" title="Select Rows">{$form.toggleSelect.html}</th>
{/if}
{if ! $single}
  <th></th>
{/if}
  {foreach from=$columnHeaders item=header}
    <th scope="col">
    {if $header.sort}
      {assign var='key' value=$header.sort}
      {$sort->_response.$key.link}
    {else}
      {$header.name}
    {/if}
    </th>
  {/foreach}
  </tr>
  {counter start=0 skip=1 print=false}
  {foreach from=$rows item=row}
  {cycle values="odd-row,even-row" assign=rowClass}
  <tr id='rowid{$row.case_id}' class='{$rowClass} {if $row.case_status_id eq 'Resolved' } disabled{/if}'>
    {if $context eq 'Search' }
        {assign var=cbName value=$row.checkbox}
        <td>{$form.$cbName.html}</td> 
    {/if}
	<td>
    {if ! $single }	
        &nbsp;{$row.contact_type}<br/>
    {/if}
	<span id="{$row.case_id}_show">
	    <a href="#" onclick="show('caseDetails$row.case_id}', 'table-row'); 
                             buildCaseDetails('{$row.case_id}','{$row.contact_id}'); 
                             hide('{$row.case_id}_show');
                             show('minus{$row.case_id}_hide');
                             show('{$row.case_id}_hide','table-row');
                             return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a>
	</span>
	<span id="minus{$row.case_id}_hide">
	    <a href="#" onclick="hide('caseDetails$row.case_id}'); 
                             show('{$row.case_id}_show', 'table-row');
                             hide('{$row.case_id}_hide');
                             hide('minus{$row.case_id}_hide');
                             return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a>
	</td>
    {if ! $single }	
    	<td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
    {/if}

    <td class="right">{$row.case_status}</td>
    <td class="right">{$row.case_type}</td>
    <td></td>
    <td class="right">{$row.case_recent_activity_date|crmDate}</td>
    <td>{$row.case_recent_activity_type}</td>
    <td class="right">{$row.case_scheduled_activity_date|crmDate}</td>
    <td class="right">{$row.case_scheduled_activity_type}</td>
    <td>{$row.action}</td>
   </tr>
   <tr id="{$row.case_id}_hide" class='{$rowClass}'>
     <td style="border-right: none;">
     </td>
{if $context EQ 'Search'}
     <td colspan="10">
{else}
     <td colspan="9">
{/if}
        <div id="caseDetails$row.case_id}"></div>
     </td>
   </tr>
 <script type="text/javascript">
     hide('{$row.case_id}_hide');
     hide('minus{$row.case_id}_hide');
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

{if $context EQ 'Search'}
 <script type="text/javascript">
 {* this function is called to change the color of selected row(s) *}
    var fname = "{$form.formName}";	
    on_load_init_checkboxes(fname);
 </script>
{/if}

{if $context EQ 'Search'}
    {include file="CRM/common/pager.tpl" location="bottom"}
{/if}

{* Build case details*}
{literal}
<script type="text/javascript">

function buildCaseDetails( caseId, contactId )
{
    var dataUrl = {/literal}"{crmURL p='civicrm/case/details' h=0 q="action=browse&snippet=4&context=`$context`&caseId="}"{literal} + caseId + '&cid=' + contactId;
	
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
                   dojo.byId('caseDetails' + caseId).innerHTML = response;
	       }
        }
     });


}
</script>

{/literal}	