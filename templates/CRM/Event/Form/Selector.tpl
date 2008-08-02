{if $context EQ 'Search'}
    {include file="CRM/common/pager.tpl" location="top"}
{/if}

{strip}
<table class="selector">
  <tr class="columnheader">
{if ! $single and $context eq 'Search' }
  <th scope="col" title="Select Rows">{$form.toggleSelect.html}</th> 
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
  <tr id='rowid{$row.participant_id}' class="{cycle values="odd-row,even-row"}">
     {if ! $single }
        {if $context eq 'Search' }       
            {assign var=cbName value=$row.checkbox}
            <td>{$form.$cbName.html}</td> 
        {/if}	
	<td>{$row.contact_type}</td>
    	<td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
    {/if}

    <td><a href="{crmURL p='civicrm/event/search' q="reset=1&force=1&event=`$row.event_id`"}">{$row.event_title}</a></td>
    {assign var="participant_id" value=$row.participant_id}
    {if $lineItems.$participant_id}
    <td>
        {foreach from=$lineItems.$participant_id item=line name=lineItemsIter}
        {$line.label}: {$line.qty}
        {if ! $smarty.foreach.lineItemsIter.last}<br>{/if}
        {/foreach}
    </td>
    <td>{$row.participant_fee_amount|crmMoney}</td>
    {else}
    <td>{if !$row.paid && !$row.participant_fee_level} {ts}(no fee){/ts}{else} {$row.participant_fee_level}{/if}</td>
    <td>{$row.participant_fee_amount|crmMoney}</td>
    {/if}
    <td>{$row.event_start_date|truncate:10:''|crmDate}
        {if $row.event_end_date && $row.event_end_date|date_format:"%Y%m%d" NEQ $row.event_start_date|date_format:"%Y%m%d"}
            <br/>- {$row.event_end_date|truncate:10:''|crmDate}
        {/if}
   </td>
    <td>{$row.participant_status_id}</td>
    <td>{$row.participant_role_id}</td>
    <td>{$row.action}</td>
   </tr>
  {/foreach}
{* Link to "View all participations" for Contact Summary selector display *}
{if $limit and $pager->_totalItems GT $limit }
  {if $context EQ 'dashboard' }
    <tr class="even-row">
    <td colspan="9"><a href="{crmURL p='civicrm/event/search' q='reset=1&force=1'}">&raquo; {ts}List more Event Participants{/ts}...</a></td></tr>
    </tr>
  {elseif $context eq 'participant' }  
    <tr class="even-row">
    <td colspan="7"><a href="{crmURL p='civicrm/contact/view' q="reset=1&force=1&selectedChild=participant&cid=$contactId"}">&raquo; {ts}View all events for this contact{/ts}...</a></td></tr>
    </tr>
  {/if}
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
