{include file="CRM/pager.tpl" location="top"}

{include file="CRM/pagerAToZ.tpl"}

{strip}
<table>
  <tr class="columnheader">
  <th>{$form.toggleSelect.html}</th>
  {if $context eq 'smog'}
  <th>
    {ts}Status{/ts}
  </th>
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

  { if $id }
      {foreach from=$rows item=row}
        <tr id='rowid{$row.contact_id}' class="{cycle values="odd-row,even-row"}">
            {assign var=cbName value=$row.checkbox}
            <td>{$form.$cbName.html}</td>
            {if $context eq 'smog'}
              {if $row.status eq 'Pending'}<td class="status-pending"}>
              {elseif $row.status eq 'Removed'}<td class="status-removed">
              {else}<td>{/if}
              {$row.status}</td>
            {/if}
            <td>{$row.contact_type}</td>
            <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
            {foreach from=$row item=value key=key}
               {if ($key neq "checkbox") and ($key neq "action") and ($key neq "contact_type") and ($key neq "status") and ($key neq "sort_name") and ($key neq "contact_id")}
               <td>{$value}&nbsp;</td>
               {/if}   
            {/foreach}
            <td>{$row.action}</td>
        </tr>
     {/foreach}
  {else}
      {foreach from=$rows item=row}
         <tr id='rowid{$row.contact_id}' class="{cycle values="odd-row,even-row"}">
            {assign var=cbName value=$row.checkbox}
            <td>{$form.$cbName.html}</td>
            {if $context eq 'smog'}
                {if $row.status eq 'Pending'}<td class="status-pending"}>
                {elseif $row.status eq 'Removed'}<td class="status-removed">
                {else}<td>{/if}
                {$row.status}</td>
            {/if}
            <td>{$row.contact_type}</td>	
            <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
            <td>{$row.street_address|mb_truncate:22:"...":true}</td>
            <td>{$row.city}</td>
            <td>{$row.state_province}</td>
            <td>{$row.postal_code}</td>
            <td>{$row.country}</td>
            <td><a href="mailto:{$row.email}">{$row.email|mb_truncate:17:"...":true}</a></td>
            <td>{$row.phone}</td>
            <td>{$row.action}</td>
         </tr>
    {/foreach}


  {/if}


{*
      {foreach from=$rows item=row}
      <tr id='rowid{$row.contact_id}' class="{cycle values="odd-row,even-row"}">
        {assign var=cbName value=$row.checkbox}
        <td>{$form.$cbName.html}</td>
        {if $context eq 'smog'}
          {if $row.status eq 'Pending'}<td class="status-pending"}>
          {elseif $row.status eq 'Removed'}<td class="status-removed">
          {else}<td>{/if}
          {$row.status}</td>
        {/if}
        <td>{$row.contact_type}</td>
        <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
        {foreach from=$row item=value key=key}
           {if ($key neq "checkbox") and ($key neq "action") and ($key neq "contact_type") and ($key neq "status") and ($key neq "sort_name") and ($key neq "contact_id")}
           <td>{$value}&nbsp;</td>
           {/if}   
        {/foreach}
        <td>{$row.action}</td>
      </tr>
      {/foreach}
*}
</table>
{/strip}

 <script type="text/javascript">
 {* this function is called to change the color of selected row(s) *}
    var fname = "{$form.formName}";	
    on_load_init_checkboxes(fname);
 </script>


{include file="CRM/pager.tpl" location="bottom"}
