<div class='spacer'></div>
<table>
  <tr><th></th><th><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$other_cid"}">{$other_name}</a></th><th></th><th><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$main_cid"}">{$main_name}</a></th></tr>
  <tr><th></th><th>{ts}Duplicate Contact{/ts}</th><th>{ts}merge?{/ts}</th><th>{ts}Main Contact{/ts}</th></tr>
  {foreach from=$rows item=row key=field}
    <tr>
      <th>{$row.title}</th><td>{$row.other}</td><td style='white-space: nowrap'>—{$form.$field.html}→</td><td>{$row.main}</td>
    </tr>
  {/foreach}
  {foreach from=$tables item=params key=table}
    <tr>
      <th></th><td><a href="{$params.other_url}">{$params.title}</a></td><td style='white-space: nowrap'>—{$form.$table.html}→</td><td><a href="{$params.main_url}">{$params.title}</a></td>
    </tr>
  {/foreach}
</table>
<div class='form-item'>
  <p>{$form.moveBelongings.html} {$form.moveBelongings.label}</p>
  <!--<p>{$form.deleteOther.html} {$form.deleteOther.label}</p>-->
</div>
<div class='status'>
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>{ts}WARNING: The Duplicate Contact will be deleted!{/ts}</dd>
  </dl>
</div>
<div class='form-item'>
  <p>{$form.buttons.html}</p>
</div>
