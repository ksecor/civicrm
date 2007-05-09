<div class='spacer'></div>
<table>
  <tr><th></th><th><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$main_cid"}">{$main_name}</a></th><th><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$other_cid"}">{$other_name}</a></th></tr>
  {foreach from=$rows item=field}
    <tr>
      <th>{$form.$field.label}</th>
      {foreach from=$form.$field.column item=column}
        <td>{$column.html}</td>
      {/foreach}
    </tr>
  {/foreach}
</table>
<div class='form-item'>
  <p>{$form.buttons.html}</p>
</div>
