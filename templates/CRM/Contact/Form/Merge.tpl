<div id="help">
{ts}Click <strong>Merge</strong> to move data from the Duplicate Contact on the left into the Main Contact. In addition to the contact data (address, phone, email...), you may choose to move all or some of the related activity records (groups, contributions, memberships, etc.).{/ts} {help id="intro"}
</div>
<table>
  <tr class="columnheader">
    <th>&nbsp;</th>
    <th><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$other_cid"}">{$other_name}</a> (duplicate)</th>
    <th>{ts}Mark All{/ts}<br />=={$form.toggleSelect.html} ==&gt;</th>
    <th><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$main_cid"}">{$main_name}</a></th>
  </tr>
  {foreach from=$rows item=row key=field}
    <tr class="{cycle values="odd-row,even-row"}">
      <td>{$row.title}</td><td>{$row.other}</td><td style='white-space: nowrap'>{if $form.$field}=={$form.$field.html}==&gt;{/if}</td><td>{$row.main}</td>
    </tr>
  {/foreach}
  {foreach from=$rel_tables item=params key=name}
    <tr class="{cycle values="even-row,odd-row"}">
      <th>{ts}Move related...{/ts}</th><td><a href="{$params.other_url}">{$params.title}</a></td><td style='white-space: nowrap'>=={$form.$name.html}==&gt;</td><td><a href="{$params.main_url}">{$params.title}</a></td>
    </tr>
  {/foreach}
</table>
<div class='form-item'>
  <!--<p>{$form.moveBelongings.html} {$form.moveBelongings.label}</p>-->
  <!--<p>{$form.deleteOther.html} {$form.deleteOther.label}</p>-->
</div>
<div class='form-item'>
    <p><strong>{ts}WARNING: The Duplicate Contact record WILL BE DELETED after the Merge is complete.</strong>{/ts}</strong><br />
    {$form.buttons.html}</p>
</div>
