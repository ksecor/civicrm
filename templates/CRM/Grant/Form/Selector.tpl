{if $context EQ 'Search'}
    {include file="CRM/common/pager.tpl" location="top"}
{/if}

{if $context EQ 'Contact Summary'}
    {assign var='columnHeaders' value=$event_columnHeaders}
    {assign var='rows' value=$_rows}
   
{/if}
{strip}
<table class="selector">
  <tr class="columnheader">
{if ! $event_single and ! $limit}
  <th scope="col" title="Select Rows">{$form.toggleSelect.html}</th> 
{/if}
  {foreach from=$columnHeaders item=header}
    <th scope="col">
    {if $header.sort}
      {assign var='key' value=$header.sort}
      {if $context EQ 'Contact Summary'}	
         {$event_sort->_response.$key.link}
      {else}
         {$sort->_response.$key.link}
      {/if}  
    {else}
      {$header.name}
    {/if}
    </th>
  {/foreach}
  </tr>

  {counter start=0 skip=1 print=false}
  {foreach from=$rows item=row}
  <tr id='rowid{$row.grant_id}' class="{cycle values="odd-row,even-row"}
     
       
       <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.contact_name}</a></td> 
  
    <td>{$row.grant_status}</td>
   
    <td>{$row.grant_type}</td>
    <td>{$row.grant_amount_total}</td>
    <td>{$row.application_received_date|truncate:10:''|crmDate}
        {if $row.end_date && $row.end_date|date_format:"%Y%m%d" NEQ $row.start_date|date_format:"%Y%m%d"}
            <br/>- {$row.end_date|truncate:10:''|crmDate}
        {/if}
   </td>
    <td>{$row.status}</td>
    <td>{$row.role}</td>
    <td>{$row.action}</td>
   </tr>
  {/foreach}
{* Link to "View all participations" for Contact Summary selector display *}
{if ($context EQ 'Contact Summary') AND $event_pager->_totalItems GT $limit}
  <tr class="even-row">
    <td colspan="7"><a href="{crmURL p='civicrm/contact/view' q="reset=1&force=1&selectedChild=participant&cid=$contactId"}">&raquo; {ts}View all events for this contact{/ts}...</a></td></tr>
  </tr>
{/if}
{if ($context EQ 'DashBoard') AND $pager->_totalItems GT $limit}
  <tr class="even-row">
    <td colspan="9"><a href="{crmURL p='civicrm/event/search' q='reset=1&force=1'}">&raquo; {ts}List more Event Participants{/ts}...</a></td></tr>
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
