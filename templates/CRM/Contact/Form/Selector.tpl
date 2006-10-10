{include file="CRM/common/pager.tpl" location="top"}

{include file="CRM/common/pagerAToZ.tpl"}

{strip}
<table summary="{ts}Search results listings.{/ts}">
  <tr class="columnheader">
  {assign var="hdrId" value="1"}
  <th id="selHeader{$hdrId}">{$form.toggleSelect.html}</th>
  {assign var="hdrId" value=$hdrId+1}
  {if $context eq 'smog'}
      <th id="selHeader{$hdrId}">
        {ts}Status{/ts}
      </th>
      {assign var="hdrId" value=$hdrId+1}
  {/if}
  {foreach from=$columnHeaders item=header}
    <th id="selHeader{$hdrId}">
    {if $header.sort}
      {assign var='key' value=$header.sort}
      {$sort->_response.$key.link}
    {else}
      {$header.name}
    {/if}
    </th>
    {assign var="hdrId" value=$hdrId+1}
  {/foreach}
  </tr>

  {counter start=0 skip=1 print=false}

  { if $id }
      {foreach from=$rows item=row}
        <tr id='rowid{$row.contact_id}' class="{cycle values="odd-row,even-row"}">
            {assign var=cbName value=$row.checkbox}
            {assign var="hdrId" value="1"}
            <td headers="selHeader{$hdrId}">{$form.$cbName.html}</td>
            {if $context eq 'smog'}
              {if $row.status eq 'Pending'}<td headers="selHeader{$hdrId}" class="status-pending"}>
              {elseif $row.status eq 'Removed'}<td headers="selHeader{$hdrId}" class="status-removed">
              {else}<td headers="selHeader{$hdrId}">{/if}
              {$row.status}</td>
              {assign var="hdrId" value=$hdrId+1}
            {/if}
            <td headers="selHeader{$hdrId}">{$row.contact_type}</td>
            {assign var="hdrId" value=$hdrId+1}
            <td headers="selHeader{$hdrId}"><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
            {assign var="hdrId" value=$hdrId+1}
            {foreach from=$row item=value key=key} 
               {if ($key neq "checkbox") and ($key neq "action") and ($key neq "contact_type") and ($key neq "status") and ($key neq "sort_name") and ($key neq "contact_id")}
                <td headers="selHeader{$hdrId}">
                {if $key EQ "household_income_total" }
                    {$value|crmMoney}
                {else}
                    {$value}
                {/if}
                     &nbsp;
                 </td>
               {/if}
               {assign var="hdrId" value=$hdrId+1}
            {/foreach}
            <td headers="selHeader{$hdrId}">{$row.action}</td>
        </tr>
     {/foreach}
  {else}
      {foreach from=$rows item=row}
         <tr id='rowid{$row.contact_id}' class="{cycle values="odd-row,even-row"}">
            {assign var=cbName value=$row.checkbox}
            {assign var="hdrId" value="1"}
            <td headers="selHeader{$hdrId}">{$form.$cbName.html}</td>
            {assign var="hdrId" value=$hdrId+1}
            {if $context eq 'smog'}
                {if $row.status eq 'Pending'}<td class="status-pending"}>
                {elseif $row.status eq 'Removed'}<td class="status-removed">
                {else}<td>{/if}
                {$row.status}</td>
                {assign var="hdrId" value=$hdrId+1}
            {/if}
            <td headers="selHeader{$hdrId}">{$row.contact_type}</td>	
            {assign var="hdrId" value=$hdrId+1}
            <td headers="selHeader{$hdrId}"><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
            {assign var="hdrId" value=$hdrId+1}
            {if $action eq 512 or $action eq 256}
              <td headers="selHeader{$hdrId}">{$row.street_address|mb_truncate:22:"...":true}</td>
              {assign var="hdrId" value=$hdrId+1}
              <td headers="selHeader{$hdrId}">{$row.city}</td>
              {assign var="hdrId" value=$hdrId+1}
              <td headers="selHeader{$hdrId}">{$row.state_province}</td>
              {assign var="hdrId" value=$hdrId+1}
              <td headers="selHeader{$hdrId}">{$row.postal_code}</td>
              {assign var="hdrId" value=$hdrId+1}
              <td headers="selHeader{$hdrId}">{$row.country}</td>
              {assign var="hdrId" value=$hdrId+1}
              <td headers="selHeader{$hdrId}">{$row.email|mb_truncate:17:"...":true}</td>
              {assign var="hdrId" value=$hdrId+1}
              <td headers="selHeader{$hdrId}">{$row.phone}</td>
              {assign var="hdrId" value=$hdrId+1}
            {else}
              {foreach from=$row item=value key=key}
                {if ($key neq "checkbox") and ($key neq "action") and ($key neq "contact_type") and ($key neq "contact_sub_type") and ($key neq "status") and ($key neq "sort_name") and ($key neq "contact_id")}
                 <td headers="selHeader{$hdrId}">{$value}&nbsp;</td>
                 {assign var="hdrId" value=$hdrId+1}
                {/if}   
              {/foreach}
            {/if}
            <td headers="selHeader{$hdrId}">{$row.action}</td>
            {assign var="hdrId" value=$hdrId+1}
         </tr>
    {/foreach}
  {/if}
</table>
{/strip}

 <script type="text/javascript">
 {* this function is called to change the color of selected row(s) *}
    var fname = "{$form.formName}";	
    on_load_init_checkboxes(fname);
 </script>

{include file="CRM/common/pager.tpl" location="bottom"}
