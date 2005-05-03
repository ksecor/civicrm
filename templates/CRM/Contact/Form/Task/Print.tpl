<form {$form.attributes}>
{include file="CRM/formCommon.tpl"}
<p>

{if $rows } 
<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>
<div class="spacer"></div>
<br />
<p>
<table>
  <tr class="columnheader">
    <th>Name</th>
    <th>Address</th>
    <th>City</th>
    <th>State</th>
    <th>Postal</th>
    <th>Country</th>
    <th>Email</th>
    <th>Phone</th>
  </tr>
{foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.sort_name}</td>
        <td>{$row.street_address}</td>
        <td>{$row.city}</td>
        <td>{$row.state}</td>
        <td>{$row.postal_code}</td>
        <td>{$row.country}</td>
        <td>{$row.email}</td>
        <td>{$row.phone}</td>
    </tr>
{/foreach}
</table>

<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>

{else}
   <div class="message status">
    <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="status"></dt>
    <dd>
        There are no records selected for Print.
    </dd>
    </dl>
   </div>
{/if}


</form>
