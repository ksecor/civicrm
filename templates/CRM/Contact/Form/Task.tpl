<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}

<fieldset>
<legend>
Would you like to {$taskName} on the following:
</legend>
<div class="form-item">
<table>
  <tr class="columnheader">
    <th>Name</th>
  </tr>
{foreach from=$rows item=row}
<tr class="{cycle values="odd-row,even-row"}">
<td>{$row.displayName}</td>
</tr>
{/foreach}
</table>
</div>
</fieldset>
<p>

<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>

</form>