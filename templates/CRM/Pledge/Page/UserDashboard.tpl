{if $context eq 'user'}
{if $pledge_rows}
{strip}
<table class="selector">
  <tr class="columnheader">
  {foreach from=$pledge_columnHeaders item=header}
    <th>{$header.name}</th>
  {/foreach}
  </tr>
  {counter start=0 skip=1 print=false}
  {foreach from=$pledge_rows item=row}
  <tr id='rowid{$row.pledge_id}' class="{cycle values="odd-row,even-row"} {if $row.pledge_status_id eq 'Overdue' } disabled{/if}">
    <td>{$row.pledge_amount|crmMoney}</td>
    <td>{$row.pledge_total_paid|crmMoney}</td>
    <td>{$row.pledge_balance_amount|crmMoney}</td>
    <td>{$row.pledge_create_date|truncate:10:''|crmDate}</td>
    <td>{$row.pledge_next_pay_date|truncate:10:''|crmDate}</td>
    <td>{$row.pledge_next_pay_amount|crmMoney}</td>
    <td>{$row.pledge_status_id}</td>
    <td>{if $row.pledge_contribution_page_id and ($row.pledge_status_id neq 'Completed')}<a href="{crmURL p='civicrm/contribute/transact' q="reset=1&id=`$row.pledge_contribution_page_id`&pledgeId=`$row.pledge_id`"}">{ts}Make Payment{/ts}</a><br/>{/if}
	<div id="{$row.pledge_id}_show">
	    <a href="#" onclick="show('paymentDetails{$row.pledge_id}', 'table-row'); buildPaymentDetails('{$row.pledge_id}','{$row.contact_id}'); hide('{$row.pledge_id}_show');show('{$row.pledge_id}_hide','table-row');return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/>{ts}Payments{/ts}</a>
	</div>
    </td>
   </tr>
   <tr id="{$row.pledge_id}_hide">
     <td colspan="11">
         <a href="#" onclick="show('{$row.pledge_id}_show', 'table-row');hide('{$row.pledge_id}_hide');return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}open section{/ts}"/>{ts}Payments{/ts}</a>
       <br/>
       <div id="paymentDetails{$row.pledge_id}"></div>
     </td>
  </tr>
 <script type="text/javascript">
     hide('{$row.pledge_id}_hide');
 </script>
  {/foreach}
</table>
{/strip}
{/if}
{*pledge row if*}

{*Display honor block*}
{if $pledgeHonor && $pledgeHonorRows}	
{strip}
<div id="help">
    <p>{ts}Pledges made in your honor.{/ts}</p>
</div>
  <table class="selector">
    <tr class="columnheader">
        <th>{ts}Pledger{/ts}</th> 
        <th>{ts}Amount{/ts}</th>
	<th>{ts}Contribution Type{/ts}</th>
        <th>{ts}Create date{/ts}</th>
        <th>{ts}Acknowledgment Sent{/ts}</th>
	 <th>{ts}Acknowledgment Date{/ts}</th>
        <th>{ts}Status{/ts}</th>
        <th></th>   
    </tr>
	{foreach from=$pledgeHonorRows item=row}
	   <tr id='rowid{$row.honorId}' class="{cycle values="odd-row,even-row"}">
	   <td><a href="{crmURL p="civicrm/contact/view" q="reset=1&cid=`$row.honorId`"}" id="view_contact">{$row.display_name}</a></td>
	   <td>{$row.amount|crmMoney}</td>
           <td>{$row.type}</td>
           <td>{$row.create_date|truncate:10:''|crmDate}</td>
           <td align="center">{if $row.acknowledge_date}{ts}Yes{/ts}{else}{ts}No{/ts}{/if}</td>
           <td>{$row.acknowledge_date|truncate:10:''|crmDate}</td>
           <td>{$row.status}</td>
	  </tr>
        {/foreach}
</table>
{/strip}
{/if} 

{* main if close*}
{/if}

{* Build pledge payment details*}
{literal}
<script type="text/javascript">

function buildPaymentDetails( pledgeId, contactId )
{
    var dataUrl = {/literal}"{crmURL p='civicrm/pledge/payment' h=0 q="action=browse&context=`$context`&snippet=4&pledgeId="}"{literal} + pledgeId + '&cid=' + contactId;
	
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
                   dojo.byId('paymentDetails' + pledgeId).innerHTML = response;
	       }
        }
     });


}
</script>
{/literal}