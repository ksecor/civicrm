<div class='spacer'></div>
<table>
  <tr><th></th><th>{$main_name}</th><th>{$other_name}</th></tr>
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
