{include file="CRM/common/pager.tpl" location="top"}

{include file="CRM/common/pagerAToZ.tpl"}

<table summary="{ts}Search results listings.{/ts}" class="selector">
  <tr class="columnheader">
  <th scope="col" title="Select All Rows">{$form.toggleSelect.html}</th>
  {if $context eq 'smog'}
      <th scope="col">
        {ts}Status{/ts}
      </th>
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

  { if $id }
      {foreach from=$rows item=row}
        <tr id='rowid{$row.contact_id}' class="status-hold {cycle values="odd-row,even-row"}">
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
               {if ($key neq "checkbox") and ($key neq "action") and ($key neq "contact_type") and ($key neq "status") and ($key neq "sort_name") and ($key neq "contact_id") and ($key neq "contact_sub_type")}
                <td>
                {if $key EQ "household_income_total" }
                    {$value|crmMoney}
		{elseif strpos( $key, '_date' ) !== false }
                    {$value|crmDate}
                {else}
                    {$value}
                {/if}
                     &nbsp;
                 </td>
               {/if}
            {/foreach}
            <td class="btn-slide" id={$row.contact_id}>{$row.action|replace:'xx':$row.contact_id}</td>
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
            {if $action eq 512 or $action eq 256}
              <td>{$row.street_address|mb_truncate:22:"...":true}</td>
              <td>{$row.city}</td>
              <td>{$row.state_province}</td>
              <td>{$row.postal_code}</td>
              <td>{$row.country}</td>
              <td {if $row.on_hold}class="status-hold"{/if}>{$row.email|mb_truncate:17:"...":true}{if $row.on_hold}&nbsp;(On Hold){/if}</td>
              <td>{$row.phone}</td> 
           {else}
              {foreach from=$row item=value key=key}
                {if ($key neq "checkbox") and ($key neq "action") and ($key neq "contact_type") and ($key neq "contact_sub_type") and ($key neq "status") and ($key neq "sort_name") and ($key neq "contact_id")}
                 <td>{$value}&nbsp;</td>
                {/if}   
              {/foreach}
            {/if}
            <td class="btn-slide" id={$row.contact_id}>{$row.action|replace:'xx':$row.contact_id}</td>
         </tr>
    {/foreach}
  {/if}
</table>

 <script type="text/javascript">
 {* this function is called to change the color of selected row(s) *}
    var fname = "{$form.formName}";	
    on_load_init_checkboxes(fname);
 </script>

{include file="CRM/common/pager.tpl" location="bottom"}
