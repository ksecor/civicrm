<form {$form.attributes}>

{$form.hidden}

<table>
<tr>
<td>{$form.contact_type.label}</td><td>{$form.contact_type.html}</td>
</tr>
<tr>
<td colspan=2 align="right">{$form.buttons.html}</td>
</tr>
</table>

{if $form.mode.label eq 64}
 
  {include file="CRM/Contact/Selector/Selector.tpl"}

{/if}


{*
  {include file="CRM/pager.tpl" location="top"}
  <table>
  <tr>
  {foreach from=$columnHeaders item=header}
  <th>
  {if $header.sort}
  {assign var='key' value=$header.sort}
  <a href={$sort.$key.link}>{$header.name}</a>&nbsp;{$sort.$key.direction}
  {else}
  {$header.name}
  {/if}
  </th>
  {/foreach}
  </tr>
  {foreach from=$rows item=row}
  <tr>
  <td>{$row.contact_id}</td><td>{$row.sort_name}</td><td>{$row.contact_type}</td><td>{$row.preferred_communication_method}</td>
  </tr>
  {/foreach}
  </table>
  {include file="CRM/pager.tpl" location="bottom"}
*}



</form>
