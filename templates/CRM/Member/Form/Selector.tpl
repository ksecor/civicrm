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
  <tr id='rowid{$row.membership_id}' class="{cycle values="odd-row,even-row"}{*if $row.cancel_date} disabled{/if*}">
     {if ! $single }
       {if $context eq 'Search' }       
          {assign var=cbName value=$row.checkbox}
          <td>{$form.$cbName.html}</td> 
       {/if}
       <td>{$row.contact_type}</td>	
       <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td> 
    {/if}
    <td>{$row.membership_type_id}</td>
    <td>{$row.join_date|truncate:10:''|crmDate}</td>
    <td>{$row.membership_start_date|truncate:10:''|crmDate}</td>
    <td>{$row.membership_end_date|truncate:10:''|crmDate}</td>
    <td>{$row.membership_source}</td>
    <td>{$row.status_id}</td>
    <td class="btn-slide" id={$row.membership_id}>{$row.action|replace:'xx':$row.membership_id}</td>
   </tr>
  {/foreach}
{* Link to "View all memberships" for Contact Summary selector display *}
{if ($context EQ 'membership') AND $pager->_totalItems GT $limit}
  <tr class="even-row">
    <td colspan="7"><a href="{crmURL p='civicrm/contact/view' q="reset=1&force=1&selectedChild=member&cid=$contactId"}">&raquo; {ts}View all memberships for this contact{/ts}...</a></td></tr>
  </tr>
{/if}
{if ($context EQ 'dashboard') AND $pager->_totalItems GT $limit}
  <tr class="even-row">
    <td colspan="9"><a href="{crmURL p='civicrm/member/search' q='reset=1'}">&raquo; {ts}Find more members{/ts}...</a></td></tr>
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
