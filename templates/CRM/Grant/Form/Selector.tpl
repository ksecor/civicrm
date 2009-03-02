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
  <tr id='rowid{$row.grant_id}' class="{cycle values="odd-row,even-row"}">
  {if !$single }  
     {if $context eq 'Search' }       
        {assign var=cbName value=$row.checkbox}
        <td>{$form.$cbName.html}</td> 
     {/if} 
    <td>{$row.contact_type}</td>	
    <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td> 
  {/if}
    <td>{$row.grant_status}</td>
    <td>{$row.grant_type}</td>
    <td>{$row.grant_amount_total|crmMoney}</td>
    <td>{$row.grant_amount_granted|crmMoney}</td>
    <td>{$row.grant_application_received_date|truncate:10:''|crmDate}</td>
    <td>{if $row.grant_report_received}{ts}Yes{/ts}{else}{ts}No{/ts}{/if}</td>
    <td>{$row.grant_money_transfer_date|truncate:10:''|crmDate}</td>
    <td class="btn-slide" id={$row.grant_id}>{$row.action|replace:'xx':$row.grant_id}</td>
   </tr>
  {/foreach}

{if ($context EQ 'dashboard') AND $pager->_totalItems GT $limit}
  <tr class="even-row">
    <td colspan="9"><a href="{crmURL p='civicrm/grant/search' q='reset=1&force=1'}">&raquo; {ts}List more Grants{/ts}...</a></td></tr>
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
