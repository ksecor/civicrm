{if $context EQ 'Search'}
    {include file="CRM/common/pager.tpl" location="top"}
{/if}

{strip}
<table class="selector">
  <tr class="columnheader">
{if !$single and $context eq 'Search' }
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
  <tr id='rowid{$row.contribution_id}' class="{cycle values="odd-row,even-row"}{if $row.cancel_date} disabled{/if}">
    {if !$single }
        {if $context eq 'Search' }       
    	    {assign var=cbName value=$row.checkbox}
    	    <td>{$form.$cbName.html}</td> 
 	{/if}
  	<td>{$row.contact_type}</td>	
    	<td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
    {/if}
    <td class="right bold nowrap">{$row.total_amount|crmMoney} {if $row.amount_level } - {$row.amount_level} {/if}
    {if $row.contribution_recur_id}
     <br /> {ts}(Recurring Contribution){/ts}
    {/if}
    </td>
    <td>{$row.contribution_type}</td>
    <td>{$row.contribution_source}</td>
    <td>{$row.receive_date|truncate:10:''|crmDate}</td>
    <td>{$row.thankyou_date|truncate:10:''|crmDate}</td>
    <td> 
        {$row.contribution_status_id}<br />
        {if $row.cancel_date}    
        {$row.cancel_date|truncate:10:''|crmDate}
        {/if}
    </td>
    <td>{$row.product_name}</td>
    <td>{$row.action|replace:'xx':$row.contribution_id}</td>
  </tr>
  {/foreach}

{* Link to "View all contributions" for Contact Summary selector display *}
{if $limit and $pager->_totalItems GT $limit }
  {if $context eq 'dashboard' } 
      <tr class="even-row">
      <td colspan="10"><a href="{crmURL p='civicrm/contribute/search' q='reset=1'}">&raquo; {ts}Find more contributions{/ts}... </a></td>
      </tr>
  {elseif $context eq 'contribution' } 
      <tr class="even-row">
      <td colspan="8"><a href="{crmURL p='civicrm/contact/view' q="reset=1&force=1&selectedChild=contribute&cid=$contactId"}">&raquo; {ts}View all contributions from this contact{/ts}... </a></td>
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
