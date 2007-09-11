{* this template is used for web-based subscriptions to mailing list type groups  *}
<div class="form-item">
<fieldset>
{if $single}
<legend>{ts 1=$groupName}Subscribe to Group %1{/ts}</legend>
{else}
<legend>{ts}Mailing List Subscription{/ts}</legend>
{/if}
  <dl>
    <dt>{$form.email.label}</dt><dd>{$form.email.html}</dd>
{if ! $single}
<table summary="{ts}Group Listings.{/ts}">
<tr class="columnheader">
    <th scope="col">&nbsp;</th>
    <th scope="col">{ts}Group Name{/ts}</th>
    <th scope="col">{ts}Group Description{/ts}</th>
</tr>
{counter start=0 skip=1 print=false}
{foreach from=$rows item=row}
<tr id='rowid{$row.id}' class="{cycle values="odd-row,even-row"}">
    {assign var=cbName value=$row.checkbox}
    <td>{$form.$cbName.html}</td>
    <td>{$row.title}</td>
    <td>{$row.description}</td>
</tr>
{/foreach}  
</table>
{/if}
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
