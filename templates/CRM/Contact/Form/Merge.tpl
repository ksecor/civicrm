<div class='spacer'></div>
<table>
  <tr><th></th><th><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$other_cid"}">{$other_name}</a></th><th>{ts}merge?{/ts}</th><th><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$main_cid"}">{$main_name}</a></th></tr>
  {foreach from=$rows item=row key=field}
    <tr>
      <th>{$row.title}</th><td>{$row.other}</td><td style='white-space: nowrap'>—{$form.$field.html}→</td><td>{$row.main}</td>
    </tr>
  {/foreach}
</table>
<div class='form-item'>
  <p>{$form.moveBelongings.html} {$form.moveBelongings.label}</p>
  <p>{$form.deleteOther.html} {$form.deleteOther.label}</p>
</div>
<div class='form-item'>
  <p>{$form.buttons.html}</p>
</div>
