{include file="CRM/pager.tpl" location="top"}

{include file="CRM/pagerAToZ.tpl"}

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
  <tr id='rowid{$row.contact_id}' class="{cycle values="odd-row,even-row"}">
    {assign var=cbName value=$row.checkbox}
    <td>{$form.$cbName.html}</td>
    <td>{$row.contact_type}</td>	
    <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
    <td>{$row.street_address|mb_truncate:22:"...":true}</td>
    <td>{$row.city}</td>
    <td>{$row.state}</td>
    <td>{$row.postal_code}</td>
    <td>{$row.country}</td>
    <td>{$row.email|mb_truncate:17:"...":true}</td>
    <td>{$row.phone}</td>
    <td>{$row.action} | <a href="{crmURL p='civicrm/contact/search/basic' q="map=1&cid=`$row.contact_id`"}">Map</a></td>
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
