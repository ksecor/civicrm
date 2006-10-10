{if $context EQ 'Search'}
    {include file="CRM/common/pager.tpl" location="top"}
{/if}

{if $context EQ 'Contact Summary'}
    {assign var='columnHeaders' value=$member_columnHeaders}
    {assign var='rows' value=$member_rows}
    {assign var='single' value=$member_single}
    {assign var='limit' value=$member_limit}
{/if}
{strip}
<table class="selector">
  <tr class="columnheader">
{if ! $single and ! $limit}
  <th>{$form.toggleSelect.html}</th> 
{/if}
  {foreach from=$columnHeaders item=header}
    <th>
    {if $header.sort}
      {assign var='key' value=$header.sort}
      {if $context EQ 'Contact Summary'}	
         {$member_sort->_response.$key.link}
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
  <tr id='rowid{$row.membership_id}' class="{cycle values="odd-row,even-row"}{*if $row.cancel_date} disabled{/if*}">
     {if ! $single and ! $limit}
       {assign var=cbName value=$row.checkbox}
       <td>{$form.$cbName.html}</td> 
       <td>{$row.contact_type}</td>	
       <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td> 
    {/if}
    <td>{$row.membership_type}</td>
    <td>{$row.join_date|truncate:10:''|crmDate}</td>
    <td>{$row.start_date|truncate:10:''|crmDate}</td>
    <td>{$row.end_date|truncate:10:''|crmDate}</td>
    <td>{$row.source}</td>
    <td>{$row.status}</td>
    <td>{$row.action}</td>
   </tr>
  {/foreach}
{* Link to "View all memberships" for Contact Summary selector display *}
{if ($context EQ 'Contact Summary') AND $member_pager->_totalItems GT $limit}
  <tr class="even-row">
    <td colspan="7"><a href="{crmURL p='civicrm/contact/view/membership' q="reset=1&force=1&cid=$contactId"}">&raquo; {ts}View all memberships for this contact{/ts}...</a></td></tr>
  </tr>
{/if}
{* {if ($context EQ 'DashBoard') AND $pager->_totalItems GT $limit}
  <tr class="even-row">
    <td colspan="9"><a href="{crmURL p='civicrm/member/search' q='reset=1&force=1'}">&raquo; {ts}List more Memberships{/ts}...</a></td></tr>
  </tr>
{/if} *}
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
