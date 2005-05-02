<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}


{*
<fieldset>
<legend>
Print the contacts below
</legend>
<p>
{include file="CRM/Contact/Form/Task.tpl"}
</fieldset>
*}

<p>


{if $printRows } 
<div class="form-item">
<table width="30%">
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
{foreach from=$printRows item=row}
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
</div>
{/if}



<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>

</form>
