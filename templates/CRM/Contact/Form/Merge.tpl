<div id="help">
{ts}Click <strong>Merge</strong> to move data from the Duplicate Contact on the left into the Main Contact. In addition to the contact data (address, phone, email...), you may choose to move all or some of the related activity records (groups, contributions, memberships, etc.).{/ts} {help id="intro"}
</div>

<div class="action-link">
    	<a href="{crmURL q="reset=1&cid=$other_cid&oid=$main_cid"}">&raquo; {ts}Flip between original and duplicate contacts.{/ts}</a>
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
      <td>{$row.title}</td><td>{$row.other}</td><td style='white-space: nowrap'>{if $form.$field}=={$form.$field.html}==&gt;{/if}</td><td>{if $row.title|substr:-5 == "email" OR $row.title|substr:-7 == "address" OR $row.title|substr:-2 == "im" OR $row.title|substr:-6 == "openid" OR $row.title|substr:-5 == "phone"}{assign var=locId value=$field|substr:-1}
{if $row.title|substr:-5 == "email"}
{assign var=locType value="email"}
{/if}
{if $row.title|substr:-5 == "phone"}
{assign var=locType value="phone"}
{/if}
{if $row.title|substr:-2 == "im"}
{assign var=locType value="im"}
{/if}
{if $row.title|substr:-6 == "openid"}
{assign var=locType value="openid"}
{/if}
{if $row.title|substr:-7 == "address"}
{assign var=locType value="address"}
{/if}
{$form.location.$locType.$locId.html}&nbsp;<span id="{$field}_overwrite_label">{if $row.main}(overwrite){else}(add){/if}</span><br/>{/if}<span id="{$field}_main_label">{$row.main}</span></td>
  </tr>
  {/foreach}
  {foreach from=$rel_tables item=params key=paramName}
    <tr class="{cycle values="even-row,odd-row"}">
      <th>{ts}Move related...{/ts}</th><td><a href="{$params.other_url}">{$params.title}</a></td><td style='white-space: nowrap'>=={$form.$paramName.html}==&gt;</td><td><a href="{$params.main_url}">{$params.title}</a></td>
    </tr>
  {/foreach}
</table>
<div class='form-item'>
  <!--<p>{$form.moveBelongings.html} {$form.moveBelongings.label}</p>-->
  <!--<p>{$form.deleteOther.html} {$form.deleteOther.label}</p>-->
</div>
<div class='form-item'>
    <p><strong>{ts}WARNING: The duplicate contact record WILL BE DELETED after the merge is complete.{/ts}</strong></strong><br />
    {$form.buttons.html}</p>
</div>

{literal}
<script type="text/javascript">
     function displayMainLoc( element, fldType, defaultLocTypeId ) {
          var status = "";
          var rows = {/literal}{$main_loc}{literal};
          var label = eval("rows." + 'main_' + element.value + "." + fldType);
          if ( label ) {
              document.getElementById( 'move_' + element.id + '_main_label').innerHTML = label;
          } else {
              document.getElementById( 'move_' + element.id + '_main_label').innerHTML = "";
          }

          if ( fldType == 'address' ) {
              if ( label ) {
                  status = '(overwrite)';
              } else {
                  status = '(add)';
              }
          } else {
              if ( defaultLocTypeId != element.value ) {
                  status = '(add)';
              } else if ( label ) {
                  status = '(overwrite)';
              } else {
                  status = '(add)';
              }
          }
          document.getElementById( 'move_' + element.id + '_overwrite_label').innerHTML = status;
     }
</script>
{/literal}
