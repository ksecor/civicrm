{if $context NEQ 'Contact Summary'} 
    {include file="CRM/pager.tpl" location="top"}
{/if}
{strip}
<table>
  <tr class="columnheader">
{if ! $single and ! $limit}
  <th>{$form.toggleSelect.html}</th> 
{/if}
  {foreach from=$columnHeaders item=header}
    <th>
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
  <tr id='rowid{$row.contact_id}' class="{cycle values="odd-row,even-row"}{if $row.cancel_date} disabled{/if}">
{if ! $single}
{if ! $limit}
    {assign var=cbName value=$row.checkbox}
    <td>{$form.$cbName.html}</td> 
{/if}
    <td>{$row.contact_type}</td>	
    <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
{/if}
    <td>{$row.total_amount}</td>
    <td>{$row.contribution_type}</td>
    <td>{$row.source}</td>
    <td>{$row.receive_date|truncate:10:''|crmDate}</td>
    <td>{$row.thankyou_date|truncate:10:''|crmDate}</td>
    <td>{$row.cancel_date|truncate:10:''|crmDate}</td>
    <td>{$row.action}</td>
  </tr>
  {/foreach}
{* Link to "View all contributions" for Contact Summary selector display *}
{if $context EQ 'Contact Summary' AND $pager->_totalItems GT $limit}
  <tr class="even-row">
    <td colspan="7"><a href="{crmURL p='civicrm/contact/view/contribution' q="reset=1&force=1&cid=$contactId"}">&raquo; {ts}View All Contributions{/ts}...</a></td></tr>
  </tr>
{/if}
</table>
{/strip}

 <script type="text/javascript">
 {* this function is called to change the color of selected row(s) *}
    var fname = "{$form.formName}";	
    on_load_init_checkboxes(fname);
 </script>

{if $context NEQ 'Contact Summary'} 
    {include file="CRM/pager.tpl" location="bottom"}
{/if}