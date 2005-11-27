{include file="CRM/pager.tpl" location="top"}
{strip}
<table>
  <tr class="columnheader">
  <th>{$form.toggleSelect.html}</th> 
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
    {assign var=cbName value=$row.checkbox}
    <td>{$form.$cbName.html}</td> 
    <td>{$row.contact_type}</td>	
    <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
    <td>{$row.total_amount}</td>
    <td>{$row.contribution_type}</td>
    <td>{$row.contribution_source}</td>
    <td>{$row.receive_date|crmDate:"%b %e, %Y"}</td>
    <td>{$row.thankyou_date|crmDate:"%b %e, %Y"}</td>
    <td>{$row.cancel_date|crmDate:"%b %e, %Y"}</td>
    <td>{$row.action}</td>
  </tr>
  {/foreach}
</table>
{/strip}

 <script type="text/javascript">
 {* this function is called to change the color of selected row(s) *}
    var fname = "{$form.formName}";	
    on_load_init_checkboxes(fname);
 </script>

{include file="CRM/pager.tpl" location="bottom"}
